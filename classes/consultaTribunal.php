<?php
	require_once 'Testing/Selenium.php';
	require_once 'PHPUnit/Framework/Test.php';
	require_once 'PHPUnit/Framework/Assert.php';
	require_once 'PHPUnit/Framework/SelfDescribing.php';
	require_once 'PHPUnit/Framework/TestCase.php';	
	require_once("./engine/funcoes.php");

	class ConsultaTribunal extends PHPUnit_Framework_TestCase
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
		if($numero >= 22 && $numero <= 27) //5ª Região
		{
			$this->consulta5Regiao($uf, $cnpj);
		}
		if($numero >= 28) //Tribunais de Justiça
		{
			$this->consultaTribunaisJustica($uf, $cnpj);
		}
	  }
	  
	  protected function consulta1Regiao($uf, $cnpj)
	  {
		$selenium1 = new Testing_Selenium("*chrome", "http://processual.trf1.jus.br/consultaProcessual/cpfCnpjParte.php?secao=".$uf);
		$selenium1->start();
		$selenium1->open("/consultaProcessual/cpfCnpjParte.php?secao=".$uf);
		$srcCaptcha = $selenium1->getAttribute("image_captcha@src");
		$selenium1->type("id=cpf_cnpj", $cnpj);
		$selenium1->click("name=mostrarBaixados");
		$selenium1->windowMaximize();
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(202, 52);
		
		imagecopy($captcha, $printscreen, 0, 0, 396, 313, 195, 48);
		imagepng($captcha, "C:\\xampp\\htdocs\\intoo\\trunk\\captchas\\captcha.png");
		
		$urlImagem = "http://processual.trf1.jus.br".$srcCaptcha;			
				
		$selenium2 = new Testing_Selenium("*chrome", "http://beatcaptchas.com/captcha.php");		
		$selenium2->start();
		$selenium2->setTimeout(60000);
		$selenium2->open("http://beatcaptchas.com/captcha.php");
		$selenium2->windowMaximize();
		$selenium2->type("id=key","6ncqawd80jsv5ikz8muwug6wk4zv4bmyomgm8hiy");
		$selenium2->focus('name=file');
		$selenium2->type("name=file","C:\\xampp\\htdocs\\intoo\\trunk\\captchas\\captcha.png");		
		$selenium2->click("name=submit");
		$selenium2->waitForPageToLoad("40000");
		$textoCaptcha = $selenium2->getText("css=td");			

		$selenium1->type("trf1_captcha", $textoCaptcha);
		$selenium1->click("id=enviar");
		$selenium1->waitForPageToLoad("10000");
		
		$texto = $selenium1->getText("css=div.flash.error");
		
		if($texto == "OR: Element css=div.flash.error not found")
		{
			$numeroProcessos = $selenium1->getText("css=td.span-2");
			$nomeParte = $selenium1->getText("css=a.listar-processo");
			
			$texto = $numeroProcessos . " processos , " . $nomeParte;
		}
		else
		{
			$frases = explode(".", $texto);
			$texto = $frases[0];
		}
		
		$selenium1->stop();
		$selenium2->stop();
		$selenium1->close();
		$selenium2->close();
		
		echo utf8_decode($texto);		  
	  }
	  
	  protected function consulta2Regiao($uf, $cnpj)
	  {
		switch($uf)
		{
			case "RJ":
				$url = "http://procweb.jfrj.jus.br/portal/consulta/cons_procs.asp";
			break;
			case "ES":
				$url = "http://www2.jfes.jus.br/jfes/portal/consulta/cons_procs.asp";
			break;
		}
		
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->select("id=TipDocPess", "value=2");
		$selenium->type("id=NumDocPess", $cnpj);
		$captcha  = $selenium->getText("//form[@id='ConsProc']/table/tbody/tr[3]/td/table/tbody/tr[11]/td/font/span/b[2]");
		$pergunta = $selenium->getText("//form[@id='ConsProc']/table/tbody/tr[3]/td/table/tbody/tr[11]/td");		
		
		$resposta = quebrarCaptcha2Regiao($captcha, $pergunta);
		
		if($resposta != 0 || $resposta != "")
		{
			$selenium->type("id=captchacode", $resposta);
		}
		else
		{		
			$selenium->click("name=nenhum");
		}
		
		$selenium->click("id=Pesquisar");
		$selenium->waitForPageToLoad("10000");
		$resultado = $selenium->getText("css=font.alerta");
		
		if($resultado == "OR: Element css=font.alerta not found")
		{			
			$selenium->selectFrame("name=Pessoas");
			$resultado = "Processos encontrados!<br/>" . $selenium->getText("css=table");		
		}
		
		echo $resultado;
		
		$selenium->stop();
		$selenium->close();
	  }
	  
	  protected function consulta3Regiao($uf, $cnpj)	  
	  {			 
		$selenium = new Testing_Selenium("*chrome", "http://www.jfsp.jus.br/foruns-federais/");
		$selenium->start();
		$selenium->open("http://www.jfsp.jus.br/foruns-federais/");
		$selenium->windowMaximize();
		
		if($uf == "SP")
		{
			$selenium->click("name=seleRegiao");
			$selenium->select("id=seleSubsecao", "value=1");
		}
		if($uf == "MS")
		{
			$selenium->click("document.formconsulta.seleRegiao[1]");
			$selenium->select("id=seleSubsecao", "value=49");
		}			
		
		$selenium->select("id=seleDocumento", "value=2");
		$selenium->type("id=num_documento", $cnpj);		
		$selenium->select("id=selePolo", "value=3");
		
		$selenium->click("xpath=(//input[@value='Pesquisar'])[2]");		
		
		$selenium->waitForFrameToLoad("name=consulta",10000);	
		$selenium->selectFrame("name=consulta");			
		$resultado = $selenium->getText("css=font");
		
		echo $resultado;
		
		$selenium->stop();
		$selenium->close();		
	  }
	  
	  protected function consulta4Regiao($uf, $cnpj)
	  {
		switch($uf)
		{
			case "PR":
				$url = "http://www.jfpr.jus.br/";
				$referenciaImg = 'css=div.imagemSeguranca > img';
			break;
			case "RS":
				$url = "http://www2.jfrs.jus.br/";
				$referenciaImg = 'css=div.imagemSeguranca > img';
			break;
			case "SC":
				$url = "http://www2.trf4.jus.br/trf4/controlador.php?txtOrigemPesquisa=1&selForma=CP&txtValor=".$cnpj."&selOrigem=SC&chkMostrarBaixados=S&acao=consulta_processual_valida_pesquisa";
				$referenciaImg = 'css=form[name="valida_pesquisa"] > div > img';
			break;			
		}
		
		$selenium1 = new Testing_Selenium("*chrome", $url);
		$selenium1->start();
		$selenium1->open($url);
		$selenium1->windowMaximize();
		
		if($uf != "SC")
		{
			$selenium1->select("id=selForma", "value=CP");
			$selenium1->type("id=txtValor", $cnpj);
			$selenium1->click("id=chkMostrarBaixados");		
			$selenium1->click("id=botaoEnviar");		
			
			$selenium1->waitForPageToLoad("10000");		
		
			$srcCaptcha = $selenium1->getAttribute($referenciaImg."@src");		
			$arrCaptcha = explode("=", $srcCaptcha);
			$captcha = $arrCaptcha[1];
			
			$selenium1->type("name=txtPalavraGerada", $captcha);
			$selenium1->click("css=input.botao");	
			
			$selenium1->waitForPageToLoad("10000");			
			$resultado = $selenium1->getText("id=areaResultadoAcompanhamento");
			
			$selenium1->stop();
			$selenium1->close();	
			
			echo $resultado;
		}
		else
		{
			$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
			
			$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
			$captcha = imagecreate(120, 40);
			
			imagecopy($captcha, $printscreen, 0, 0, 220, 280, 120, 40);
			imagepng($captcha, "C:\\xampp\\htdocs\\intoo\\trunk\\captchas\\captcha.png");							
					
			$selenium2 = new Testing_Selenium("*chrome", "http://beatcaptchas.com/captcha.php");		
			$selenium2->start();
			$selenium2->setTimeout(60000);
			$selenium2->open("http://beatcaptchas.com/captcha.php");
			$selenium2->windowMaximize();
			$selenium2->type("id=key","6ncqawd80jsv5ikz8muwug6wk4zv4bmyomgm8hiy");
			$selenium2->focus('name=file');
			$selenium2->type("name=file","C:\\xampp\\htdocs\\intoo\\trunk\\captchas\\captcha.png");		
			$selenium2->click("name=submit");
			$selenium2->waitForPageToLoad("40000");
			$textoCaptcha = $selenium2->getText("css=td");
			
			$selenium1->type("name=txtPalavraGerada",$textoCaptcha);
			$selenium1->waitForPageToLoad("10000");			
			$resultado = $selenium1->getText("id=divConteudo");
			
			$selenium1->stop();
			$selenium1->close();	
			$selenium2->stop();
			$selenium2->close();	
			
			echo $resultado;						
		}		
	  }
	  
	  protected function consulta5Regiao($uf, $cnpj)
	  {
		switch($uf)
		{
			case "AL":
				$url = "http://www.trf5.jus.br/";			
			break;
			case "CE":
				$url = "http://www.jfce.jus.br/";			
			break;
			case "PB":
				$url = "http://www.jfpb.jus.br/";			
			break;			
			case "PE":
				$url = "http://www.jfpe.jus.br/";			
			break;	
			case "RN":
				$url = "http://www.jfrn.jus.br/";			
			break;	
			case "SE":
				$url = "http://www.jfse.jus.br/";			
			break;				
		}

		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();	

		$selenium->click("id=aui-3-2-0-11385");
		$selenium->select("name=CodTipDocPess", "value=2");
		$selenium->type("name=NumDocPess",$cnpj);
		$selenium->click("id=Pesquisar");
		$selenium->waitForPageToLoad("40000");		
		
		$resultado = $selenium->getText("//form[@id='ConsProc']/table/tbody/tr/td/table[3]/tbody/tr/td/p/font/span/b");
		
		$selenium->stop();
		$selenium->close();	
		
		echo $resultado;
	  }
	  
	  protected function consultaTribunaisJustica($uf, $cnpj)
	  {
		echo "Nenhum processo encontrado";
	  }
	}
?>