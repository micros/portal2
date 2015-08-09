<?php
/**
 * Archivo que contiene la clase usuario
 * @package lib-core
 */

/**
 * La Clase usuario recupera todos los valores asociados a un idusuario en
 * particular y devuelve tanto los valores directos y la pertenencia a grupos.
 * @author Leonardo Forero Sandoval <gerencia@micrositios.net>
 * @version 1.0
 * @package lib-core
 */
class usuario {
	/**
     * Idusuario que se esta analizando. Siempre debe ser un número entero positivo
     * @access public
     * @var integer
     */
	public $idusuario;
	/**
     * Username del usuario que se esta analizando.
     * @access public
     * @var string
     */
	public $username;
	public $idzona;
	public $activo;
	public $eliminado;

	public $grupos;
	public $categorias;

	public $dbh;

	/**
	 * Hace la precarga de los valores iniciales del usuario
	 * @param object $dbh objeto de conexión PDO
	 * @param int $idusuario entero con el id del usuario a analizar 
	 */
	public function __construct($dbh, $idusuario){

		$this->dbh = $dbh;

		try {
			$query_general = $this->dbh->prepare(sprintf('SELECT 
				idusuario,
				username,
				idzona,
				activo,
				eliminado
				from por_usuarios_gelxml where idusuario = %s',$idusuario));

			$query_general->execute();

			if ($query_general->rowCount() <> 0) {
				foreach ($query_general as $row_general){

					$this->idusuario 		= (int) 	$row_general['idusuario'];
					$this->username 		= (string) 	$row_general['username'];
					$this->idzona 			= (int) 	$row_general['idzona'];
					$this->activo 			= (int) 	$row_general['activo'];
					$this->eliminado 		= (int) 	$row_general['eliminado'];
				}
			} else {
					$this->idusuario 		= 0;
			}

			$query_grupos = $this->dbh->prepare(sprintf('SELECT 
				idlista, 
				idusuario 
				from detallelista where idusuario = %s',$this->idusuario));
			
			$query_grupos->execute();

			if ($query_grupos->rowCount() <> 0) {
				foreach ($query_grupos as $row_grupos){
					$this->grupos[] 		= (int) $row_grupos['idlista'];
				}
			}

			$query_categorias = $this->dbh->prepare(sprintf('SELECT 
				idcategoria, 
				idusuario 
				from editores where idusuario = %s',$this->idusuario));
			
			$query_categorias->execute();

			if ($query_categorias->rowCount() <> 0) {
				foreach ($query_categorias as $row_categorias){
					$this->categorias[] 		= (int) $row_categorias['idcategoria'];
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		} catch (Exception $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		$dbh = null;

	}
}
