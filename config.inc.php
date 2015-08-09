<?php
$user = 'root';
$pass = 'lmKuZfSr';
$base = 'base2';
try {
	$dbh = new PDO(sprintf('mysql:charset=utf8mb4;mysql:host=localhost;dbname=%s',$base), $user, $pass);
} catch (PDOException $e) {
	print "Error de conexion a la base de datos. Revise parametros<br/>";
	print "Mensaje de error: " . $e->getMessage() . "<br/>";
	die();
}