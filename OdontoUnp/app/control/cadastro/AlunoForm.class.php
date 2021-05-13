<?php
/**
 * AlunoForm
 *
 * @version    1.0
 * @package    control
 * @subpackage cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class AlunoForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_AlunoForm');
        $this->form->setClientValidation(true);
        $this->form->setFormTitle('Aluno');
        
        // create the form fields
        $id       = new TEntry('id');
        $nomealuno     = new TEntry('nomealuno');
        $matricula_id     = new TEntry('matricula_id');
        $escaninho     = new TEntry('escaninho');
        $curso_id = new TDBCombo('curso_id', 'database', 'Curso', 'id', 'nome');
        $id->setEditable(FALSE);
        
        // add the form fields
        $this->form->addFields( [new TLabel('ID')],    [$id] );
        $this->form->addFields( [new TLabel('nome Aluno', 'red')],  [$nomealuno] );
        $this->form->addFields( [new TLabel('Matricula', 'red')],  [$matricula_id] );
        $this->form->addFields( [new TLabel('Curso', 'red')],  [$curso_id] ); 
        $this->form->addFields( [new TLabel('Escaninho')], [$escaninho] );
                       
        $nomealuno->addValidation('Nome do Aluno', new TRequiredValidator);
        $curso_id->addValidation('Curso', new TRequiredValidator);
        $matricula_id->addValidation('Matricula', new TRequiredValidator);
        
        // define the form action
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addActionLink('Limpar',  new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addActionLink('Voltar',  new TAction(['AlunoList', 'onReload']), 'fa:table blue');
        
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
            
            $object = new Aluno;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
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
                $object = new Aluno($key);        // instantiates object Aluno
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