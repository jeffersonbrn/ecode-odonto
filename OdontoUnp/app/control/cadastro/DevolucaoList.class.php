<?php
/**
 * DevolucaoList
 *
 * @version    1.0
 * @package    control
 * @subpackage cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class DevolucaoList extends TPage
{
    private $form, $datagrid, $pageNavigation, $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_DevolucaoList');
        $this->form->setFormTitle('Devoluções');
        
        $name = new TEntry('name');
        $matricula = new TEntry('matricula');
        $this->form->addFields( [new TLabel('Nome:')], [$name] );
        $this->form->addFields( [new TLabel('Matricula:')], [$matricula] );
        
        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search blue');
        //$this->form->addActionLink('Novo',  new TAction(['DevolucaoForm', 'onClear']), 'fa:plus-circle green');
        // keep the form filled with the search data
        $name->setValue( TSession::getValue( 'Nome_Devolucao_Filter' ) );
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        
        // creates the datagrid columns
        $col_matriculaid    = new TDataGridColumn('matricula', 'Matricula', 'right', '10%');
        $col_nomealuno  = new TDataGridColumn('nomealuno', 'Nome do Aluno', 'left', '30%');
        $col_escaninho = new TDataGridColumn('escaninho', 'Escaninho', 'left', '5%');
        $col_instrumento = new TDataGridColumn('nomeinstrumento', 'Instrumento', 'left', '15%');
        $col_datanetrada = new TDataGridColumn('dataentrada', 'Entrada', 'left', '10%');
        $col_curso = new TDataGridColumn('curso', 'Curso', 'left');
        
        // assign the ordering actions
        $col_matriculaid->setAction(new TAction([$this, 'onReload']), ['order' => 'matricula']);
        $col_nomealuno->setAction(new TAction([$this, 'onReload']), ['order' => 'nomealuno']);
        $col_escaninho->setAction(new TAction([$this, 'onReload']), ['order' => 'escaninho']);
        $col_instrumento->setAction(new TAction([$this, 'onReload']), ['order' => 'nomeinstrumento']);
        $col_datanetrada->setAction(new TAction([$this, 'onReload']), ['order' => 'dataentrada']);
        $col_curso->setAction(new TAction([$this, 'onReload']), ['order' => 'curso']);

        // add the columns to the DataGrid
        $this->datagrid->addColumn($col_matriculaid);
        $this->datagrid->addColumn($col_nomealuno);
        $this->datagrid->addColumn($col_escaninho);
        $this->datagrid->addColumn($col_instrumento);
        $this->datagrid->addColumn($col_datanetrada);
        $this->datagrid->addColumn($col_curso);
        
        $action1 = new TDataGridAction(['DevolucaoForm', 'onEdit'],   ['key' => '{id}'] );
        
        $this->datagrid->addAction($action1, 'Devolver',   'far:edit blue');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        // creates the page structure using a table
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form); // add a row to the form
        $vbox->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation)); // add a row for page navigation
        
        // add the table inside the page
        parent::add($vbox);
    }
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // check if the user has filled the form
        if (isset($data->name) or (isset($data->matricula)))
        {
            if(isset($data->name)) {
                // creates a filter using what the user has typed
                $filter1 = new TFilter('nomealuno', 'like', "%{$data->name}%");
                // stores the filter in the session
                TSession::setValue('DevolucaoList_filter1', $filter1);
            } 
            if(isset($data->matricula)) {
                // creates a filter using what the user has typed
                $filter2 = new TFilter('matricula_id', 'like', "%{$data->matricula}%");
                // stores the filter in the session
                TSession::setValue('DevolucaoList_filter2', $filter2);
            } 
            
            TSession::setValue('Nome_Devolucao_Filter',   $data->name);
            
            // fill the form with data again
            $this->form->setData($data);
        }
        
        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'database'
            TTransaction::open('database');
            
            // creates a repository for Devolucao
            $repository = new TRepository('Vw_movimentacao');
            $limit = 20;
            
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'dataentrada';
                $param['direction'] = 'asc';
            }
            
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            $filternaodevolvido = new TFilter('datadevolucao', 'IS', NULL);
            $criteria->add($filternaodevolvido);
            
            if (TSession::getValue('DevolucaoList_filter1'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('DevolucaoList_filter1'));
            }

            if (TSession::getValue('DevolucaoList_filter2'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue('DevolucaoList_filter2'));
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $object->dataentrada = TDate::date2br($object->dataentrada);
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count = $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    

    
    /**
     * method show()
     * Shows the page
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded)
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
}