<?php
/**
 * InstrumentoForm
 *
 * @version    1.0
 * @package    control
 * @subpackage cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class InstrumentoForm extends TStandardForm
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        $ini  = AdiantiApplicationConfig::get();
        
        $this->setDatabase('database');              // defines the database
        $this->setActiveRecord('Instrumento');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Instrumento');
        $this->form->setFormTitle('Instrumento');
        
        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        
        // add the fields
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Nome Instrumento')], [$nome] );

        $id->setEditable(FALSE);
        $id->setSize('30%');
        $nome->setSize('70%');
        $nome->addValidation('Nome Instrumento', new TRequiredValidator );
        
        // create the form actions
        $btn = $this->form->addAction('salvar', new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Limpar',  new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addActionLink('Voltar',new TAction(array('InstrumentoList','onReload')),'far:arrow-alt-circle-left blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'InstrumentoList'));
        $container->add($this->form);
        
        parent::add($container);
    }
}
