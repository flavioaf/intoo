<?php
	require_once 'Testing/Selenium.php';
	require_once 'PHPUnit/Framework/Test.php';
	require_once 'PHPUnit/Framework/Assert.php';
	require_once 'PHPUnit/Framework/SelfDescribing.php';
	require_once 'PHPUnit/Framework/TestCase.php';	
	require_once("./engine/funcoes.php");

	class NadaConsta extends PHPUnit_Framework_TestCase
	{
	  public function setUp($uf, $cnpj, $numero)
	  {
		if($numero < 15) //1ª Região
		{
			$this->consulta1Regiao($uf, $cnpj);
		}
		if($numero == 15 || $numero == 16) //2ª Região
		{
			$this->consulta2Regiao($uf, $cnpj);
		}
		if($numero == 17 || $numero == 18) //3ª Região
		{
			$this->consulta3Regiao($uf, $cnpj);
		}
		if($numero >= 19 && $numero <= 21) //4ª Região
		{
			$this->consulta4Regiao($uf, $cnpj);
		}	
		if($numero >= 22) //5ª Região
		{
			$this->consulta5Regiao($uf, $cnpj);
		}	
	  }
	  
	  protected function consulta1Regiao($uf, $cnpj)
	  {
		$selenium = new Testing_Selenium("*chrome", "http://www.trf1.jus.br/Servicos/Certidao/");
		$selenium->start();
		$selenium->open("http://www.trf1.jus.br/Servicos/Certidao/trf1_emitecertidao.php?orgao=".$uf."&nome=EMPRESA&cpf=".$cnpj."&tipocertidao=3");
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("1000");
		$selenium->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\nada_consta\\NadaConsta".$uf."_".$cnpj."_".date('dmY').".png",NULL);
		$resultado = "Nada consta emitido e armazenado na pasta.";
			
		$selenium->stop();
		$selenium->close();
		
		echo utf8_decode($resultado);		  
	  }  
	}		  	
?>