<?php
	require_once("classes/NadaConsta.php");	
	extract($_GET);
	
	$nadaConsta = new NadaConsta();
	$nadaConsta->setUp($uf, $cnpj, $numero, $nome);	
?>