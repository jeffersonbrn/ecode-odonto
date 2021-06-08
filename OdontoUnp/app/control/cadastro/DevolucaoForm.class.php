<?php
/**
 * DevolucaoForm
 *
 * @version    1.0
 * @package    control
 * @subpackage cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class DevolucaoForm extends TPage
{
    private $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DevolucaoForm');
        $this->form->setClientValidation(true);
        $this->form->setFormTitle('Devolução de Instrumentos');
        
        // create the form fields
        $id       = new TEntry('id');
        $nomealuno     = new TEntry('nomealuno');
        $matricula     = new TEntry('matricula');
        $escaninho     = new TEntry('escaninho');
        $curso     = new TEntry('curso');
        $instrumento     = new TEntry('nomeinstrumento');
        $dataentrada     = new TEntry('dataentrada');
        
        $id->setEditable(FALSE);
        $nomealuno->setEditable(FALSE);
        $matricula->setEditable(FALSE);
        $escaninho->setEditable(FALSE);
        $curso->setEditable(FALSE);
        $instrumento->setEditable(FALSE);
        $dataentrada->setEditable(FALSE);

        $dataentrada->setMask('dd/mm/yyyy');

        // add the form fields
        $this->form->addFields( [new TLabel('ID')],    [$id] );
        $this->form->addFields( [new TLabel('Nome Aluno', 'red')],  [$nomealuno] );
        $this->form->addFields( [new TLabel('Matricula', 'red')],  [$matricula] );
        $this->form->addFields( [new TLabel('Curso', 'red')],  [$curso] ); 
        $this->form->addFields( [new TLabel('Escaninho')], [$escaninho] );
        $this->form->addFields( [new TLabel('Instrumento')], [$instrumento] );
        $this->form->addFields( [new TLabel('Data Entrada')], [$dataentrada] );
                       
        //$nomealuno->addValidation('Nome do Aluno', new TRequiredValidator);
        //$curso_id->addValidation('Curso', new TRequiredValidator);
        //$matricula_id->addValidation('Matricula', new TRequiredValidator);
        
        // define the form action
        $this->form->addAction('Devolver', new TAction([$this, 'onSave']), 'fa:save green');
        //$this->form->addActionLink('Limpar',  new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addActionLink('Voltar',  new TAction(['DevolucaoList', 'onReload']), 'fa:table blue');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', 'AlunoList'));
        $vbox->add($this->form);
        parent::add($vbox);
    }
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            // open a transaction with database 'database'
            TTransaction::open('database');
            
            $this->form->validate(); // run form validation
            
            $data = $this->form->getData(); // get form data as array
            
            $object = new ItemMovimentacao($data->id);  // Recupera o item da movimentacao
            $object->datadevolucao = date("Y-m-d"); 
            $object->funcionario_devolucao_id = TSession::getValue('userid');
            
            $object->store(); // save the object
            
            // fill the form with the active record data
            $this->form->setData($object);
            
            TTransaction::close();  // close the transaction
            
            // shows the success message
            new TMessage('info', 'Registro Salvo');
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form
     */
    public function onClear()
    {
        $this->form->clear( TRUE );
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['id']))
            {
                $key = $param['id'];  // get the parameter
                TTransaction::open('database');   // open a transaction with database 'database'
                $object = new Vw_Movimentacao($key);        // instantiates object Item Movimentaçao
                $object->dataentrada = TDate::date2br($object->dataentrada);
                $this->form->setData($object);   // fill the form with the active record data
                TTransaction::close();           // close the transaction
            }
            else
            {
                $this->form->clear( true );
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}