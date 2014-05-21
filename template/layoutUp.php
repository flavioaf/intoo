<?php
	session_start();
	
	require_once("./engine/funcoes.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="author" content="Flávio de França">
		<meta name="description" content="Projeto Intoo">
		<meta name="keywords" content="Intoo, Processos, Tribunal Federal">
		<?php
			//Título da página
			$frase = "Intoo - Consulta de Processos";
		?>	
		<script type="text/javascript" src="./engine/funcoes.js"></script>
		<script type="text/javascript" src="./engine/jquery-1.2.6.js"></script>
		<script type="text/javascript" src="./engine/jquery.maskedinput-1.1.4.js"></script>
		<script type="text/javascript" src="./engine/ajax.js"></script>
		<link rel="stylesheet" type="text/css" href="./estilo/index.css"/>		
		<link rel="stylesheet" type="text/css" href="./estilo/formulario.css"/>		
		
		<title><?php echo $frase; ?></title>
	</head>
	<body>
