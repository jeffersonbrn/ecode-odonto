<?php
/**
 * Movimentacao
 *
 * @version    1.0
 * @package    model
 * @subpackage Cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class Movimentacao extends TRecord
{
    const TABLENAME = 'movimentacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('aluno_id');
        parent::addAttribute('dataentrada');
        parent::addAttribute('horaentrada');
        parent::addAttribute('datasaida');
        parent::addAttribute('horasaida');
        parent::addAttribute('escaninho');
        parent::addAttribute('observacao');
        parent::addAttribute('funcionario_entrada_id');
        parent::addAttribute('funcionario_saida_id');
    }
}
