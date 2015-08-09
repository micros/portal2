<?php
class fabrica {
	
	private function __construct(){}

	public static function makeNodo($dbh, $id, $obj_user, $obj_url){
		$a = new nodo($dbh,$id);
		echo $a->idcategoria."<br>";
		

		if (!$obj_user->idusuario){
		
		// INICIO condicional sesion SIN Usuario.
		// Este es el caso mas frecuente y debe estar optimizado
			if($a->activa && !$a->eliminado && is_null($a->grupos)){
				// Nodo SIN problemas, entonces lo devolvemos
				echo "Nodo sin problemas, caso mas frecuente en modo normal<br>";
				$a->setModo("ver");
			} else {
				// Nodo CON problemas de activa, eliminado o grupos entonces verificamos el papa
				$id = $a->idpadre;
				// Si llega a home, mostramos home
				if (0 == $id) {
					echo "Vista forzada de home en modo normal<br>";
					$a->setModo("ver");
				} else {
					// La destruimos para que no quede en memoria e intentamos con el padre
					unset($a);
					return self::makeNodo($dbh, $id, $obj_user, $obj_url);
				}
			}
		// FIN condicional sesiones SIN Usuario

		} else {

		// INICIO condicional sesion CON Usuario
			
			// INICIO condicional Administradores del portal
			if (9 == $obj_user->idzona){

				// INICIO condicional Administradores del portal en modo EDICION
				if ($obj_url->getEd()){
					echo "Administrador en modo edicion<br>";
					$a->setModo("editar");
				// FIN

				// INICIO condicional Administradores del portal en modo VER
				} else {
					echo "Administrador en modo normal<br>";
					$a->setModo("ver");
				}
				// FIN

			// FIN condicional Administradores del portal

			// INICIO condicional usuarios en sesion pero NO Administradores
			} else {

				// INICIO condicional editor de esta categoría
				if(!is_null($obj_user->categorias) && 
					array_intersect($a->control, $obj_user->categorias) && 
					$obj_user->idzona > 1) {
				
					// INICIO condicional Editor de esta categoria en modo EDICION
					
					if(!$a->eliminado){

						if ($obj_url->getEd()){
							echo "Editor en modo edicion<br>";
							$a->setModo("editar");
						// FIN

						// INICIO condicional Editor de esta categoria en modo VER
						} else {
							echo "Editor en modo normal<br>";
							$a->setModo("ver");
						}

					} else {

							// Nodo eliminado entonces intentaremos con el padre
							// Los editores NO pueden ver nodos eliminados
							$id = $a->idpadre;

							// Si llega a home, mostramos home forzado
							if (0 == $id) {
								echo "Vista forzada de home en modo normal<br>";
								$a->setModo("ver");

							// Si no hemos llegado a home intentamos con el padre
							} else {
								unset($a);
								return self::makeNodo($dbh, $id, $obj_user, $obj_url);
							}

					}

				// FIN

				// INICIO condicional usuarios en sesion pero NO Administradores y NO Editor de esta categoria
				} else {

					// INICIO condicional usuarios en sesion que pertenece a grupo autorizado
					if(is_array($obj_user->grupos) && is_array($a->grupos) && 
						array_intersect($a->grupos, $obj_user->grupos)) {

						// Como solo puede ver debemos verificar que activa y no eliminada
						if($a->activa && !$a->eliminado){
							echo "Pertenece al grupo y la pagina no esta borrada ni inactiva, la puede ver en modo normal<br>";
							$a->setModo("ver");
						} else {

							// Nodo CON problemas de activa, eliminado o grupos entonces verificamos el papa
							$id = $a->idpadre;

							// Si llega a home, mostramos home
							if (0 == $id) {
								echo "Vista forzada de home en modo normal<br>";
								$a->setModo("ver");

							// Si no hemos llegado a home intentamos con el padre
							} else {
								unset($a);
								return self::makeNodo($dbh, $id, $obj_user, $obj_url);
							}
						}	

					// INICIO condicional usuarios en sesion que NO pertenece a grupo autorizado
					// Es como si no estuviera en sesion
					} else {
						if($a->activa && !$a->eliminado && is_null($a->grupos)){
							// Nodo SIN problemas, entonces lo devolvemos
							echo "Modo normal, pero llego aqui despues de validar su sesion<br>";
							$a->setModo("ver");
						} else {
							// Nodo CON problemas de activa, eliminado o grupos entonces verificamos el papa
							$id = $a->idpadre;
							// Si llega a home, mostramos home
							if (0 == $id) {
								echo "Vista forzada de home en modo normal<br>";
								$a->setModo("ver");
							} else {
								// La destruimos para que no quede en memoria e intentamos con el padre
								unset($a);
								return self::makeNodo($dbh, $id, $obj_user, $obj_url);
							}
						}
					}
				}					
			}
		}
		return $a;
	}
}

