<?php
	require("consultaTribunal.php");	
	extract($_GET);
	
	//Função para criar diretórios
	function criarDiretorios($diretorio_atual)
	{
		if(!file_exists($diretorio_atual . "\captchas"))
		{
			mkdir($diretorio_atual . "\captchas", 0700) or die("Falha ao criar diretório.");
		}

		if(!file_exists($diretorio_atual . "\screenshots"))
		{
			mkdir($diretorio_atual . "\screenshots", 0700) or die("Falha ao criar diretório.");
		}		
	}		
	
	//Criando os diretórios
	$diretorio_atual = getcwd();
	criarDiretorios($diretorio_atual);
	
	//Testando variáveis
	if(!isset($nome)) $nome = "";
	if(!isset($processo)) $processo = "";
	
	//Chamando a consulta aos tribunais
	$consultaTribunal = new ConsultaTribunal();
	$consultaTribunal->setUp($uf, $cnpj, $numero, $nome, $processo);
?>