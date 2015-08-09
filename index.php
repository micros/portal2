<?php
require_once 'config.inc.php';

spl_autoload_register(function ($class) {
	include 'lib/micros/class_' . $class . '.php';
});

$obj_url = new url_limpia($_GET);

$start = microtime(true);

try {

	if (!is_null($obj_url->getId()) && $obj_url->getId() <> 0) {
		$id = $obj_url->getId();
	} else {
		$id = 1;
	}


	if (!is_null($obj_url->getUser()) && $obj_url->getUser() <> 0) {
		$user = $obj_url->getUser();
	} else {
		$user = 0;

	}

	$obj_user = new usuario($dbh,$user);


	$a = fabrica::makeNodo($dbh,$id,$obj_user,$obj_url);

	var_dump($obj_url);

	var_dump($obj_user);

	var_dump($a);


} catch (Exception $e) {
	print "Mensaje de error: " . $e->getMessage() . "<br/>";
	die();
}



$total = microtime(true) - $start;
echo "<br><font color='red'><b>---Tiempo total de calculo: ".$total." ---</b></font></br>";