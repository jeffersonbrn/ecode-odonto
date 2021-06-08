<?php
/**
 * Vw_Movimentacao
 *
 * @version    1.0
 * @package    model
 * @subpackage Cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class Vw_Movimentacao extends TRecord
{
    const TABLENAME = 'vw_movimentacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
}
