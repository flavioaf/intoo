<?php
	require_once 'Testing/Selenium.php';
	require_once 'PHPUnit/Framework/Test.php';
	require_once 'PHPUnit/Framework/Assert.php';
	require_once 'PHPUnit/Framework/SelfDescribing.php';
	require_once 'PHPUnit/Framework/TestCase.php';	
	require_once("./engine/funcoes.php");

	class NadaConsta extends PHPUnit_Framework_TestCase
	{
	  public function setUp($uf, $cnpj, $numero, $nome)
	  {
		if($numero < 15) //1ª Região
		{
			$this->consulta1Regiao($uf, $cnpj, $nome);
		}
		if($numero == 15 || $numero == 16) //2ª Região
		{
			$this->consulta2Regiao($uf, $cnpj, $nome);
		}
		if($numero == 17 || $numero == 18) //3ª Região
		{
			$this->consulta3Regiao($uf, $cnpj, $nome);
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
	  
	  protected function consulta1Regiao($uf, $cnpj, $nome)
	  {
		$selenium = new Testing_Selenium("*chrome", "http://www.trf1.jus.br/Servicos/Certidao/");
		$selenium->start();
		$selenium->open("http://www.trf1.jus.br/Servicos/Certidao/trf1_emitecertidao.php?orgao=".$uf."&nome=".$nome."&cpf=".$cnpj."&tipocertidao=3");
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("1000");
		$selenium->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\nada_consta\\NadaConsta".$uf."_".$cnpj."_".date('dmY').".png",NULL);
		$resultado = "Nada consta emitido e armazenado na pasta.";
			
		$selenium->stop();
		$selenium->close();
		
		echo utf8_decode($resultado);		  
	  } 

	  protected function consulta2Regiao($uf, $cnpj, $nome)
	  {	  
		$url = "http://www8.trf2.jus.br/cncweb/RelCertidaoOriginais.aspx?Param1=".$nome."&Param2=".$cnpj."&Param3=201400288691&Param4=".date('d/m/Y')."&Param5=%".date('h:m:s');
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("1000");
		
		for($i = 0; $i < 5; $i++)
		{
			$selenium->click("id=zoomOut");
		}
		
		$selenium->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\nada_consta\\NadaConsta".$uf."_".$cnpj."_".date('dmY').".png",NULL);
		$resultado = "Nada consta emitido e armazenado na pasta.";
			
		$selenium->stop();
		$selenium->close();
		
		echo utf8_decode($resultado);		  
	  }	 

	  protected function consulta3Regiao($uf, $cnpj, $nome)
	  {	  
		switch($uf)
		{
			case 'MS':
				$url = "http://www.jfms.jus.br/csp/jfmsint/reqcertidao.csp";
			break;
			case 'SP':
				$url = "http://www.jfsp.jus.br/csp/jfspint/reqcertidao.csp";
			break;
		}
	  
		$selenium1 = new Testing_Selenium("*chrome", $url);
		$selenium1->start();
		$selenium1->open($url);
		$selenium1->windowMaximize();
		$selenium1->type("id=nom_parte", $nome);
		$selenium1->select("id=seleDocumento", "label=2- Jurídica");
		$selenium1->type("id=cpf_cnpj", $cnpj);		
		$selenium1->windowMaximize();
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(60, 40);
		
		imagecopy($captcha, $printscreen, 0, 0, 490, 230, 60, 40);
		imagepng($captcha, "C:\\xampp\\htdocs\\intoo\\trunk\\captchas\\captcha.png");		
				
		$selenium2 = new Testing_Selenium("*chrome", "http://beatcaptchas.com/captcha.php");		
		$selenium2->start();
		$selenium2->setTimeout(60000);
		$selenium2->open("http://beatcaptchas.com/captcha.php");
		$selenium2->windowMaximize();
		$selenium2->waitForPageToLoad("10000");
		$selenium2->type("id=key","6ncqawd80jsv5ikz8muwug6wk4zv4bmyomgm8hiy");
		$selenium2->focus('name=file');
		$selenium2->type("name=file","C:\\xampp\\htdocs\\intoo\\trunk\\captchas\\captcha.png");		
		$selenium2->click("name=submit");
		$selenium2->waitForPageToLoad("40000");
		$textoCaptcha = $selenium2->getText("css=td");
		
		$selenium1->type("id=num_seguranca", $textoCaptcha);
		
		$selenium1->click('css=input[type="button"]');
		$selenium1->click("name=botao");
		
		$resultado = "Nada consta emitido e aberto em outra janela.";
			
		$selenium1->stop();
		$selenium1->close();
		$selenium2->stop();
		$selenium2->close();		
		
		echo utf8_decode($resultado);		  
	  }	  
	}		  	
?>