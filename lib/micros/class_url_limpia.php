<?php
/**
 * Archivo que contiene la clase url_limpia
 * @package lib-core
 */

/**
 * La Clase url_limpia debe sanitizar cada uno de los parámetros recibidos
 * por GET o POST y devuelve un valor seguro para usar en las instrucciones SQL.
 * @package lib-core
 */
class url_limpia
{
	private $data_insegura;
	private $id;
	private $user;
	private $ed;

	function __construct($data) {
		$this->data_insegura 	= $data;
		$this->id   			= isset($data['id'])	?	$this->valida_entero($data['id'])  :null;
		$this->user   			= isset($data['user'])	?	$this->valida_entero($data['user']):null;
		$this->ed   			= isset($data['ed'])	?	1:0;
	}
	function valida_entero($numero){
		$numero = trim($numero);
		$numero = (string) (intval($numero)) === $numero ? (int) intval($numero) : null;
		return $numero;
	}
	function getId(){
		return $this->id;
	}
	function getUser(){
		return $this->user;
	}
	function getEd(){
		return $this->ed;
	}

}