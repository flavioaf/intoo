<?php
	//Atribuindo valores aos dados de conexão
	$strURLBD	  = "localhost";
	$strUsuarioBD = "root";
	$strSenhaBD	  = "";
	$strNomeBD	  = "ecotele";

	$conn = mysql_connect($strURLBD , $strUsuarioBD , $strSenhaBD) or die("Não foi possível realizar a conexão com o Banco de Dados.");
	mysql_select_db($strNomeBD , $conn) or die("Não foi possível selecionar o Banco de Dados.");
?>