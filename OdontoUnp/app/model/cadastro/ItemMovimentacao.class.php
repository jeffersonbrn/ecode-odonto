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
    
    private $movimentacao;
    private $instrumento;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('movimentacao_id');
        parent::addAttribute('instrumento_id');
        parent::addAttribute('observacao');
    }

    public function set_movimentacao(movimentacao $object) {
       $this->movimentacao = $object;
       $this->movimentacao_id = $object->id;
    }

    public function get_movimentacao() {
        if (empty($this->movimentacao)){
            $this->movimentacao = new movimentacao($this->movimentacao_id);
            return $this->movimentacao;
        }
    }

    public function set_instrumento(instrumento $object) {
        $this->instrumento = $object;
        $this->instrumento_id = $object->id;
     }

     public function get_instrumento() {
        if (empty($this->instrumento)){
            $this->instrumentoo = new movimentacao($this->instrumento_id);
            return $this->instrumento;
        }
    }
}
