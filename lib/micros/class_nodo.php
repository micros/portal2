<?php
/**
 * Archivo que contiene la clase nodo
 * @package lib-core
 */

/**
 * La Clase nodo recupera todos los valores asociados a un idcategoria en
 * particular y devuelve tanto los valores directos como los heredados.
 * @author Leonardo Forero Sandoval <gerencia@micrositios.net>
 * @version 1.0
 * @package lib-core
 */
class nodo {
	/**
     * Idcategoria que se esta analizando. Siempre debe ser un número entero positivo
     * @access public
     * @var integer
     */
	public $idcategoria;
	private $modo;
	/**
     * Idcategoria del padre del nodo que se esta analizando. Siempre debe ser un número entero positivo
     * @access public
     * @var integer
     */
	public $idpadre;
	public $nombre;
	public $descripcion;
	public $activa;
	public $template;
	public $es_root;
	public $orden;
	public $orden_sub;
	public $asc_sub;
	public $paginas_sub;
	public $iddisplay;
	public $iddisplay_sub;
	public $varsubsitio;
	public $fecha1;
	public $fecha2;
	public $fecha3;
	public $antetitulo;
	public $subtitulo;
	public $entradilla;
	public $imagen;
	public $cuenta;
	public $idioma;
	public $en_mapa;
	public $en_buscador;
	public $eliminado;
	public $idbusqueda;
	public $es_rss;
	public $indexacion;

	public $mi_root;
	public $grupos;
	public $migas;
	public $control;
	public $idpadre_tmp;
	public $dbh;

	/**
	 * Hace la precarga de los valores iniciales del nodo
	 * @param object $dbh objeto de conexión PDO
	 * @param int $idcategoria entero con el id de categoria a analizar 
	 */
	public function __construct($dbh, $idcategoria){

		$this->dbh = $dbh;

		try {
			$query_txt = 'SELECT 
			idcategoria,
			idpadre,
			nombre,
			descripcion,
			activa,
			template,
			es_root,
			orden,
			orden_sub,
			asc_sub,
			paginas_sub,
			iddisplay,
			iddisplay_sub,
			varsubsitio,
			fecha1,
			fecha2,
			fecha3,
			antetitulo,
			subtitulo,
			entradilla,
			imagen,
			cuenta,
			idioma,
			en_mapa,
			en_buscador,
			eliminado,
			idbusqueda,
			es_rss,
			indexacion
			from categoria where idcategoria = %s';

			$query = $dbh->prepare(sprintf($query_txt, $idcategoria));
			$query->execute();

			if ($query->rowCount() <> 0) {
				foreach ($query as $row){

					$this->idcategoria 		= (int) 	$row['idcategoria'];
					$this->idpadre 			= (int) 	$row['idpadre'];
					$this->nombre 			= (string) 	$row['nombre'];
					$this->descripcion 		= (string) 	$row['descripcion'];
					$this->activa 			= (int) 	$row['activa'];
					$this->template			= (string) 	$row['template'];
					$this->es_root 			= (int) 	$row['es_root'];
					$this->orden 			= (int) 	$row['orden'];
					$this->orden_sub 		= (string) 	$row['orden_sub'];
					$this->asc_sub 			= (int) 	$row['asc_sub'];
					$this->paginas_sub 		= (int) 	$row['paginas_sub'];
					$this->iddisplay 		= (int) 	$row['iddisplay'];
					$this->iddisplay_sub 	= (int) 	$row['iddisplay_sub'];
					$this->varsubsitio 		= (string) 	$row['varsubsitio'];
					$this->fecha1			= (string)	$row['fecha1'];
					$this->fecha2			= (string)	$row['fecha2'];
					$this->fecha3			= (string)	$row['fecha3'];
					$this->antetitulo		= (string)	$row['antetitulo'];
					$this->subtitulo		= (string)	$row['subtitulo'];
					$this->entradilla		= (string)	$row['entradilla'];
					$this->imagen			= (string)	$row['imagen'];
					$this->cuenta			= (int)		$row['cuenta'];
					$this->idioma			= (string)	$row['idioma'];
					$this->en_mapa			= (int)		$row['en_mapa'];
					$this->en_buscador		= (int)		$row['en_buscador'];
					$this->eliminado		= (int)		$row['eliminado'];
					$this->idbusqueda		= (int)		$row['idbusqueda'];
					$this->es_rss			= (int)		$row['es_rss'];
					$this->indexacion		= (int)		$row['indexacion'];

					$this->control[] 		= (int) 	$row['idcategoria'];
					$this->idpadre_tmp		= (int) 	$row['idpadre'];
					$this->mi_root   		= $row['es_root'] ? (int) $row['idcategoria']:0;
					
					$this->rectificar();
					
				}

				$this->buscargrupos($row['idcategoria']);

				eval('$this->varsubsitio = (array) '.$this->varsubsitio.';');

			} else {
				$this->idcategoria 	= (int) 0;
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
	/**
    * Sobreescribe los valores heredados. No toma ningún parámetro
    */
	private function rectificar(){
		if($this->idpadre_tmp <> 0){
			try {
				$query_txt = 'SELECT 
				idcategoria,
				idpadre,
				nombre,
				activa,
				template,
				es_root,
				orden_sub,
				asc_sub,
				paginas_sub,
				iddisplay_sub,
				varsubsitio,
				idioma,
				eliminado
				from categoria where idcategoria = %s';

				$query = $this->dbh->prepare(sprintf($query_txt, $this->idpadre_tmp));
				$query->execute();
				if ($query->rowCount() <> 0) {
					foreach ($query as $row){
						if (!in_array($row['idpadre'], $this->control)){

							$this->idpadre_tmp 	= (int) $row['idpadre'];
							$this->activa 		= ($this->activa   <> 0 )	 									? (int)   $row['activa']		: (int) $this->activa;
							$this->template 	= ($this->template == "" 	|| is_null($this->template))	 	? (string) $row['template'] 	: (string) $this->template;
							$this->orden_sub 	= ($this->orden_sub == "" 	|| is_null($this->orden_sub))	 	? (string) $row['orden_sub'] 	: (string) $this->orden_sub;
							$this->asc_sub 		= ($this->asc_sub == 0 		|| is_null($this->asc_sub))		 	? (int) $row['asc_sub'] 		: (int) $this->asc_sub;
							$this->paginas_sub 	= ($this->paginas_sub == 0 	|| is_null($this->paginas_sub))	 	? (int) $row['paginas_sub'] 	: (int) $this->paginas_sub;
							$this->iddisplay_sub = ($this->iddisplay_sub == 0 	|| is_null($this->paginas_sub)) ? (int) $row['iddisplay_sub'] 	: (int) $this->iddisplay_sub;
							$this->idioma 		= ($this->idioma == "" 	|| is_null($this->idioma) || $this->idioma == "0") ? (string) $row['idioma'] 		: (string) $this->idioma;
							$this->eliminado 	= ($this->eliminado   == 0 )	 								? (int)  $row['eliminado']		: (int) $this->eliminado;

							if(!$this->mi_root){
								$this->migas[] = array( (int) $row['idcategoria'] => $row['nombre']);
							}							
							
							$this->control[]    = (int) $row['idcategoria'];
							
							if ($this->mi_root  == 0 && $row['es_root'] == 1){
								$this->mi_root = (int) $row['idcategoria'];
								$this->varsubsitio = $row['varsubsitio'];
							}

							if ($this->idpadre_tmp <> 0){
								$this->rectificar();
							}

						} else {
							throw new Exception("Referencia Circular!");
						}

					}

				} else {
					throw new Exception("Nodos huerfanos!");
				}
			} catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
				die();
			} catch (Exception $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
				die();

			}

		}

	}
	/**
    * Busca los grupos asociados a este idcategoria y a sus ancestros.
    * Inicialmente se hace una busqueda general para determinar si alguno de sus
    * ancestros tiene restricción de grupo, si esto ocurre se hace una búsqueda nodo por nodo
    * de lo contrario no se hace ninguna búsqueda. No recibe parámetros.
    */
	private function buscargrupos(){
		try {
				$query_general = $this->dbh->prepare(sprintf('SELECT 
					idlista, 
					idcategoria 
					from acceso where idcategoria in (%s)',implode(",", $this->control)));

				$query_txt = ('SELECT 
					idlista, 
					idcategoria 
					from acceso where idcategoria = %s');

				$query_general->execute();

				if ($query_general->rowCount() <> 0) {
					foreach ($this->control as $id) {

						if (empty($this->grupos)) {
							$query_especifico = $this->dbh->prepare(sprintf($query_txt,$id));
							$query_especifico->execute();

							if ($query_especifico->rowCount() <> 0) {
								foreach ($query_especifico as $row) {
									$this->grupos[] = (int) $row['idlista'];
								}
							}
						}
					}
				}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}
	public function setModo($modo){
		if ("ver" === $modo || "editar" === $modo)
		{
			$this->modo = $modo;
		} else {
			$this->modo = null;
		}
	}	
	public function getModo(){
		return $this->modo;
	}	
	
}
