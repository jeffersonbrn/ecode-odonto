<?php
/**
 * Aluno
 *
 * @version    1.0
 * @package    model
 * @subpackage Cadastro
 * @author     Bruno Reis
 * @copyright  Copyright (c) 2021
 * @license    http://www.adianti.com.br/framework-license
 */
class Aluno extends TRecord
{
    const TABLENAME = 'aluno';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nomealuno');
        parent::addAttribute('matricula_id');
        parent::addAttribute('escaninho');
        parent::addAttribute('curso_id');
    }

    /**
     * Returns the curso
     */
    public function get_curso()
    {
        return Curso::find($this->curso_id);
    }
}
