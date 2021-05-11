<?php
/**
 * CursoForm
 *
 * @version    1.0
 * @package    control
 * @subpackage cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class CursoForm extends TStandardForm
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
        $this->setActiveRecord('Curso');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Curso');
        $this->form->setFormTitle('Curso');
        
        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $sigla = new TEntry('sigla');
        
        // add the fields
        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel('Nome Curso')], [$nome] );
        $this->form->addFields( [new TLabel('Sigla Curso')], [$sigla] );

        $id->setEditable(FALSE);
        $id->setSize('30%');
        $nome->setSize('70%');
        $nome->addValidation('Nome Curso', new TRequiredValidator );
        $sigla->setSize('50%');
        $sigla->addValidation('Sigla Curso', new TRequiredValidator );
        
        // create the form actions
        $btn = $this->form->addAction('salvar', new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink('Limpar',  new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addActionLink('Voltar',new TAction(array('CursoList','onReload')),'far:arrow-alt-circle-left blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CursoList'));
        $container->add($this->form);
        
        parent::add($container);
    }
}
