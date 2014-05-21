<?php	
	require_once("./engine/funcoes.php");
	
	$cnpj = $_GET['cnpj'];
	$uf   = $_GET['uf'];
	$estado = $_GET['estado'];
	
	//Inicializando o iMacros
	$iim1 = new COM("imacros");
	$s = $iim1->iimInit("-runner");	
	
	//Consultando os tribunais
	consultaTribunalFederal($cnpj, $iim1, $uf, $estado);

	$s = $iim1->iimExit();
?>