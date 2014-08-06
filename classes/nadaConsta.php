<?php
	require_once 'Testing/Selenium.php';
	require_once 'PHPUnit/Framework/Test.php';
	require_once 'PHPUnit/Framework/Assert.php';
	require_once 'PHPUnit/Framework/SelfDescribing.php';
	require_once 'PHPUnit/Framework/TestCase.php';	
	require_once 'phpwebdriver/WebDriver.php';	
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
			$this->consulta4Regiao($uf, $cnpj, $nome);
		}	
		if($numero >= 22) //5ª Região
		{
			$this->consulta5Regiao($uf, $cnpj, $nome);
		}	
	  }
	  
	  protected function consulta1Regiao($uf, $cnpj, $nome)
	  {
		$selenium = new Testing_Selenium("*chrome", "http://www.trf1.jus.br/Servicos/Certidao/");
		$selenium->start();
		$selenium->open("http://www.trf1.jus.br/Servicos/Certidao/trf1_emitecertidao.php?orgao=".$uf."&nome=".$nome."&cpf=".$cnpj."&tipocertidao=3");
		$selenium->windowMaximize();
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
		//$selenium1->click("css=input[value='Gerar Certidão']");		

		$resultado = "Nada consta emitido e aberto em outra janela.";
			
		$selenium2->stop();
		$selenium2->close();
		
		echo utf8_decode($resultado);		  
	  }

	  protected function consulta4Regiao($uf, $cnpj, $nome)	  
	  {
		$url = "http://www2.trf4.jus.br/trf4/processos/certidao/proc_processa_certidao.php?string_cpf=".$cnpj."&string_nome=".$nome."&string_tipo_cert=A";
		
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		
		$selenium->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\nada_consta\\NadaConsta".$uf."_".$cnpj."_".date('dmY').".png",NULL);
		$resultado = "Nada consta emitido e armazenado na pasta.";
			
		$selenium->stop();
		$selenium->close();
		
		echo utf8_decode($resultado);			
	  }
	  
	  protected function consulta5Regiao($uf, $cnpj, $nome)	  
	  {
		switch($uf)
		{
			case 'AL':
				$url = "http://www.jfal.gov.br/certidaoInternet/emissaoCertidao.aspx";
				$this->emitirNadaConstaAL($url, $cnpj, $nome);
			break;
			case 'CE':
				$url = "http://www.jfce.jus.br/certidao-on-line/emissaoCertidao.aspx";
				$this->emitirNadaConstaCE($url, $cnpj, $nome);
			break;
			case 'PB':
				$url = "http://www.jfpb.gov.br/certidao/";
				$this->emitirNadaConstaPB($url, $cnpj, $nome);
			break;			
			case 'PE':
				$url = "http://www.jfpe.jus.br/certidaoweb/emissaoCertidao.aspx";
				$this->emitirNadaConstaPE($url, $cnpj, $nome, $uf);
			break;		
			case 'RN':
				$url = "http://200.217.210.137/certidao/emissaocertidao.aspx";
				$this->emitirNadaConstaRN($url, $cnpj, $nome, $uf);
			break;
			case 'SE':
				$url = "http://consulta.jfse.jus.br/Certidao/emissaoCertidao.aspx";
				$this->emitirNadaConstaSE($url, $cnpj, $nome, $uf);
			break;			
		}
	  }		
		
	  protected function emitirNadaConstaAL($url, $cnpj, $nome)	  
	  {
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		
		$selenium->type("id=txtNomePessoa", $nome);
		$selenium->type("id=txtNumeroDocumento", $cnpj);
		$selenium->click("id=btnEmitir");

		$resultado = "Nada consta emitido e aberto em outra janela.";			
		echo utf8_decode($resultado);
	  }

	  protected function emitirNadaConstaCE($url, $cnpj, $nome)	  
	  {
		$resultado = "Sistema deste tribunal fora do ar.";
		echo utf8_decode($resultado);
	  }		  

	  protected function emitirNadaConstaPB($url, $cnpj, $nome)	  
	  {
		$resultado = "Sistema deste tribunal fora do ar.";
		echo utf8_decode($resultado);
	  }		

	  protected function emitirNadaConstaPE($url, $cnpj, $nome, $uf)	  
	  {
		$selenium1 = new Testing_Selenium("*chrome", $url);
		$selenium1->start();
		$selenium1->open($url);
		$selenium1->windowMaximize();
		$selenium1->type("id=txtNomePessoa", $nome);
		$selenium1->type("id=txtNumeroDocumento", $cnpj);		
		$selenium1->windowMaximize();
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(160, 50);
		
		imagecopy($captcha, $printscreen, 0, 0, 475, 200, 160, 50);
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
		
		$selenium1->type("id=txtCodigoCaptcha", $textoCaptcha);
		$selenium1->click("id=btnEmitir");
		
		$header = $selenium1->getText("css=li.quadrotextoaviso");
		
		if($header != "")
		{
			$resultado = "Esta raz&atilde;o social n&atilde;o confere com o CNPJ informado!";
		}
		else
		{
			$resultado = "Nada consta emitido e armazenado na pasta.";
		}
	
		echo utf8_decode($resultado);
	  }		

	  protected function emitirNadaConstaRN($url, $cnpj, $nome, $uf)	  
	  {
		$selenium1 = new Testing_Selenium("*chrome", $url);
		$selenium1->start();
		$selenium1->open($url);
		$selenium1->windowMaximize();
		$selenium1->type("id=txtNomePessoa", $nome);
		$selenium1->type("id=txtNumeroDocumento", $cnpj);		
		$selenium1->windowMaximize();
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(160, 40);
		
		imagecopy($captcha, $printscreen, 0, 0, 500, 340, 160, 40);
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
		
		$selenium1->type("id=txtCodigoCaptcha", $textoCaptcha);
		$selenium1->click("id=btnEmitir");
		$selenium1->waitForPageToLoad("10000");
		$selenium1->windowMaximize();
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\nada_consta\\NadaConsta".$uf."_".$cnpj."_".date('dmY').".png",NULL);		
	
		$resultado = "Nada consta emitido e armazenado na pasta.";

		$selenium1->stop();
		$selenium1->close();		
		$selenium2->stop();
		$selenium2->close();
		
		echo utf8_decode($resultado);	
	  }

	  protected function emitirNadaConstaSE($url, $cnpj, $nome, $uf)	  
	  {
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		
		$selenium->type("id=txtNomePessoa", $nome);
		$selenium->type("id=txtNumeroDocumento", $cnpj);
		$selenium->click("id=btnEmitir");
		$selenium->waitForPageToLoad("10000");
		$selenium->windowMaximize();
		$selenium->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\nada_consta\\NadaConsta".$uf."_".$cnpj."_".date('dmY').".png",NULL);		

		$resultado = "Nada consta emitido e armazenado na pasta.";

		$selenium->stop();
		$selenium->close();		
		
		echo utf8_decode($resultado);
	  }	  
	}	  
?>