<?php
/**
 * ItemMovimentacao
 *
 * @version    1.0
 * @package    model
 * @subpackage Cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class ItemMovimentacao extends TRecord
{
    const TABLENAME = 'itemmovimentacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('movimentacao_id');
        parent::addAttribute('instrumento_id');
        parent::addAttribute('observacao');
        parent::addAttribute('datadevolucao');
        parent::addAttribute('funcionario_devolucao_id');
    }
}
