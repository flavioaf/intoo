<?php
	//Atribuindo valores aos dados de conex�o
	$strURLBD	  = "localhost";
	$strUsuarioBD = "root";
	$strSenhaBD	  = "";
	$strNomeBD	  = "ecotele";

	$conn = mysql_connect($strURLBD , $strUsuarioBD , $strSenhaBD) or die("N�o foi poss�vel realizar a conex�o com o Banco de Dados.");
	mysql_select_db($strNomeBD , $conn) or die("N�o foi poss�vel selecionar o Banco de Dados.");
?>