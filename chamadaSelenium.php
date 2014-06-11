<?php
	require_once("classes/ConsultaTribunal.php");	
	extract($_GET);
	
	$consultaTribunal = new ConsultaTribunal();
	$consultaTribunal->setUp($uf, $cnpj, $numero);	
?>