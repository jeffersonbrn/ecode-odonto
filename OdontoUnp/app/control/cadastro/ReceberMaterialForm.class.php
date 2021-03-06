<?php
/**
 * ReceberMaterialForm 
 * @version    1.0
 * @package    control
 * @subpackage cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class ReceberMaterialForm extends TPage
{
    protected $form; // form
    protected $instrumento_list;
    protected $movimentacao_items;
    protected $instrumento_id;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct($param)
    {
        parent::__construct($param);
        
        // creates the form
        $this->form   = new BootstrapFormBuilder('form_ReceberMaterial');
        $this->form->setFormTitle('Receber Material');
        
        // master fields
        $id             = new TEntry('id');
        $aluno_id    = new TDBSeekButton('aluno_id', 'database', $this->form->getName(), 'Aluno', 'nomealuno', 'aluno_id', 'nome_aluno');
        $nome_aluno = new TEntry('nome_aluno');
        $escaninho  = new TEntry('escaninho');
        $observacao = new TText('observacao');
        
        $id->setSize(40);
        $id->setEditable(false);
        $aluno_id->setSize(50);
        $nome_aluno->setSize('calc(100% - 200px)');
        $nome_aluno->setEditable(false);
        $escaninho->setSize(50);
        $observacao->setSize('100%',50);
                
        $escaninho->addValidation('Escaninho', new TRequiredValidator);
        $aluno_id->addValidation('Aluno', new TRequiredValidator);
        
        $label_escaninho     = new TLabel('Escaninho (*)');
        $label_aluno = new TLabel('Aluno (*)');
        
        $this->form->addFields( [new TLabel('ID')], [$id] );
        $this->form->addFields( [$label_aluno], [$aluno_id, $nome_aluno] );
        $this->form->addFields( [$label_escaninho], [$escaninho] );
        $this->form->addFields( [new TLabel('Observação')], [$observacao] );
        
        $label_escaninho->setFontColor('#FF0000');
        $label_aluno->setFontColor('#FF0000');

        // create detail fields Campo para seleção dos instrumentos
        $instrumento_id = new TDBUniqueSearch('instrumento_id[]', 'database', 'Instrumento', 'id', 'nome');
        $instrumento_id->setMinLength(0);
        $instrumento_id->setSize('100%');
        $instrumento_id->setMask('{nome} ({id})');
        // $instrumento_id->setChangeAction(new TAction(array($this, 'onChangeProduct')));
    
        //Criação do campo de observações
        $observacao_item = new TEntry('observacao_item');
        $observacao_item->style = 'text-align: right';

        $this->form->addField($instrumento_id);
        $this->form->addField($observacao_item);
        
        // detail
        $this->instrumento_list = new TFieldList;
        $this->instrumento_list->addField( '<b>Instrumento</b>', $instrumento_id,     ['width' => '50%']);
        $this->instrumento_list->addField( '<b>Observação</b>', $observacao_item,     ['width' => '50%']);
        $this->instrumento_list-> width = '100%';
        $this->instrumento_list->enableSorting();
        
        $this->form->addFields( [new TFormSeparator('Seleção de Instrumentos') ] );
        $this->form->addFields( [$this->instrumento_list] );
        $this->form->addFields( [$this->observacao_item] );
        
        $this->form->addAction( 'Salvar',  new TAction( [$this, 'onSave'] ),  'fa:save green' );
        $this->form->addAction( 'Limpar', new TAction( [$this, 'onClear'] ), 'fa:eraser red' );
        
        // update total when remove item row
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $this->onClear($param);
        parent::add($container);
    }
    
    /**
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            TTransaction::open('database');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $movimentacao = new Movimentacao($key);
                $this->form->setData($movimentacao);
                
                $movimentacao_items = ItemMovimentacao::where('movimentacao_id', '=', $movimentacao->id)->load();
                
                $this->instrumento_list->addHeader();
                if ($movimentacao_items)
                {
                    foreach($movimentacao_items  as $item )
                    {
                        $this->instrumento_list->addDetail($item);
                    }
                    $this->instrumento_list->addCloneAction();
                }
                else
                {
                    $this->onClear($param);
                }
                
                TTransaction::close(); // close transaction
                new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
   
    /**
     * Clear form
     */
    public function onClear($param)
    {
        $this->instrumento_list->addHeader();
        $this->instrumento_list->addDetail( new stdClass );
        $this->instrumento_list->addCloneAction();
    }
    
    /**
     * Save the sale and the sale items
     */
    public static function onSave($param)
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('database');
            
            $id = (int) $param['id'];
            $movimentacao = new Movimentacao($id);
            $movimentacao->aluno_id = $param['aluno_id'];
            $movimentacao->escaninho = $param['escaninho'];
            $movimentacao->observacao = $param['observacao'];
            $movimentacao->funcionario_entrada_id = TSession::getValue('userid');
            $movimentacao->store();
            
            $movimentacao_items = ItemMovimentacao::where('movimentacao_id', '=', $movimentacao->id)->delete();
            
            if( !empty($param['instrumento_id']) AND is_array($param['instrumento_id']) )
            {
                foreach( $param['instrumento_id'] as $row => $instrumento_id)
                {
                    if ($instrumento_id)
                    {
                        $item = new ItemMovimentacao;
                        $item->instrumento_id  = $instrumento_id;
                        $item->movimentacao_id = $movimentacao->id;
                        $item->observacao = $param['observacao_item'];
                        $item->store();
                    }
                }
            }
            
            $data = new stdClass;
            $data->id = $movimentacao->id;
            TForm::sendData('form_ReceberMaterial', $data);
            TTransaction::close(); // close the transaction
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
