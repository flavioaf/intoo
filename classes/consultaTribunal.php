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
		$selenium2->waitForPageToLoad("10000");
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
		$selenium->waitForPageToLoad("10000");
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
		$selenium->waitForPageToLoad("10000");
		
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
		$selenium1->waitForPageToLoad("10000");
		
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
			$selenium2->waitForPageToLoad("10000");
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
		$selenium->waitForPageToLoad("10000");		

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
		$resultado = "";
		$consulta = false;
		
		switch($uf)
		{
			case "AC":	
				$url = "http://esaj.tjac.jus.br/cpo/pg/search.do?paginaConsulta=1&localPesquisa.cdLocal=1&cbPesquisa=DOCPARTE&tipoNuProcesso=SAJ&dePesquisa=".$cnpj."&pbEnviar=Pesquisar";
				$resultado = $this->consultaTribunalJusticaAC($url);
				$consulta = true;
			break;			
			case "AL":
				$url = "http://www2.tjal.jus.br/cpopg/search.do;jsessionid=FF1AEA3366DA36C6397FB6CE055C1AE8?dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&dadosConsulta.tipoNuProcesso=UNIFICADO&dadosConsulta.valorConsulta=".$cnpj;
				$resultado = $this->consultaTribunalJusticaAL($url);
				$consulta = true;			
			break;
			case "AP":
				$resultado = "Voc&ecirc; precisa informar pelo menos um sobrenome da parte para realizar a consulta.";
				$consulta = true;
			break;
			case "BA":
				$url = "http://esaj.tjba.jus.br/cpopg/search.do;jsessionid=C5B4903AD4877336FB91CFA8FDC68CFF.cpopg2?dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&dadosConsulta.tipoNuProcesso=UNIFICADO&dadosConsulta.valorConsulta=".$cnpj;
				$resultado = $this->consultaTribunalJusticaBA($url);
				$consulta = true;
			break;		
			case "CE":
				$url = "http://esaj.tjce.jus.br/cpopg/search.do;jsessionid=C6F6DE39A53B03910D29330FC252B412.cpos1?conversationId=&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&dadosConsulta.tipoNuProcesso=UNIFICADO&dadosConsulta.valorConsulta=".$cnpj;
				$resultado = $this->consultaTribunalJusticaCE($url);
				$consulta = true;
			break;
			case "DF":
				$url = "http://tjdf19.tjdft.jus.br/cgi-bin/tjcgi1?NXTPGM=tjhtml101&submit=ok&SELECAO=10&CHAVE=".$cnpj."&CIRC=ZZ&CHAVE1=&ORIGEM=INTER";
				$resultado = $this->consultaTribunalJusticaDF($url);
				$consulta = true;
			break;		
			case "ES":
				$url = "http://aplicativos.tjes.jus.br/consultaunificada/faces/pages/pesquisaSimplificada.xhtml";
				$resultado = $this->consultaTribunalJusticaES($url, $cnpj);
				$consulta = true;
			break;	
			case "MA":
				$url = "http://pje.tjma.jus.br/pje/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTribunalJusticaMA($url, $cnpj);
				$consulta = true;
			break;	
			case "MS":
				$url = "http://www.tjms.jus.br/cpopg5/search.do?cdForo=0&cbPesquisa=DOCPARTE&dePesquisa=".$cnpj."&tipoNuProcesso=SAJ";
				$resultado = $this->consultaTribunalJusticaMS($url);
				$consulta = true;
			break;	
			case "MT":
				$url = "http://www.tjmt.jus.br/paginas/servicos/ConsultaProcessual/Default.aspx";
				$resultado = $this->consultaTribunalJusticaMT($url, $cnpj);
				$consulta = true;
			break;		
			case "PA":
				$url = "http://wsconsultas.tjpa.jus.br/consultaprocessoportal/consulta/consulta/principal";
				$resultado = $this->consultaTribunalJusticaPA($url, $cnpj);
				$consulta = true;
			break;		
			case "PB":
				$url = "http://www.tjpb.jus.br/";
				$resultado = $this->consultaTribunalJusticaPB($url, $cnpj);
				$consulta = true;
			break;	
			case "PE":
				$url = "http://www.tjpe.jus.br/processos/consulta2grau/ole_busca_processos2.asp";
				$resultado = $this->consultaTribunalJusticaPE($url, $cnpj);
				$consulta = true;
			break;				
		}

		if(!$consulta)
		{
			$resultado .= "Nenhum processo encontrado";
		}
		
		echo $resultado;			
	  }	 

	  protected function consultaTribunalJusticaAC($url)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$resultado .= "Processo: " . $selenium->getText("//table[3]/tbody/tr/td[2]/table/tbody/tr/td/span") . "<br/>";
		$resultado .= "Classe: " . $selenium->getText("css=span > span") . "<br/>";
		$resultado .= $selenium->getText("//table[3]/tbody/tr[3]/td[2]/table/tbody/tr/td") . "<br/>";
		$resultado .= "Assunto: " . $selenium->getText("xpath=(//span[@id=''])[3]") . "<br/>";
		$resultado .= "Local f&iacute;sico: " . $selenium->getText("xpath=(//span[@id=''])[4]") . "<br/>";		
		//Buscar mais campos posteriormente
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }	  	

	  protected function consultaTribunalJusticaAL($url)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$resultado .= "Processo: " . $selenium->getText("css=a.linkProcesso") . "<br/>";		
		$resultado .= $selenium->getText("css=div.espacamentoLinhas") . "<br/>";
		$resultado .= $selenium->getText("//div[@id='divProcesso1M0001B730000']/div/div[3]") . "<br/>";	
		//Buscar mais campos posteriormente
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }		  
	  
	  protected function consultaTribunalJusticaBA($url)
	  {		
		$resultado = "";
	  
		$selenium1 = new Testing_Selenium("*chrome", $url);
		$selenium1->start();
		$selenium1->open($url);	
		$selenium1->waitForPageToLoad("10000");		
		
		$selenium1->windowMaximize();		
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(200, 50);
		
		imagecopy($captcha, $printscreen, 0, 0, 175, 400, 200, 50);
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
		
		$selenium1->type("id=defaultCaptchaCampo", $textoCaptcha);
		$selenium1->click("id=pbEnviar");
		$selenium1->waitForPageToLoad("10000");		
		
		$resultado .= "Processo: " . $selenium1->getText("css=a.linkProcesso") . "<br/>";		
		$resultado .= $selenium1->getText("css=div.espacamentoLinhas") . "<br/>";
		$resultado .= $selenium1->getText("//div[@id='divProcesso0100010S60000']/div/div[3]") . "<br/>";	
		// Buscar mais campos posteriormente
		
		$selenium1->stop();
		$selenium1->close();	
		$selenium2->stop();
		$selenium2->close();			

		return $resultado;
	  }

	  protected function consultaTribunalJusticaCE($url)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$resultado .= "Processo: " . $selenium->getText("css=a.linkProcesso") . "<br/>";		
		$resultado .= $selenium->getText("css=div.espacamentoLinhas") . "<br/>";
		$resultado .= $selenium->getText("//div[@id='divProcesso010007GNR0000']/div/div[3]") . "<br/>";	
		//Buscar mais campos posteriormente
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }		
	  
	  protected function consultaTribunalJusticaDF($url)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$resultado .= $selenium->getText("css=font");	
		//Buscar mais dados posteriormente
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }	  
	  
	  protected function consultaTribunalJusticaES($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$selenium->type("id=txtPesquisaSimplificada", $cnpj);
		$selenium->click("id=btnRealizarPesquisaSimplificada");
		$selenium->waitForPageToLoad("10000");
		
		$resultado .= $selenium->getText("css=#layoutResultados > div.ui-layout-unit-content.ui-widget-content > div") . "<br/>";	
		$resultado .= $selenium->getText("css=td > div.ui-dt-c");	
		//Buscar mais dados posteriormente
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }		  
	  
	  protected function consultaTribunalJusticaMA($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium1 = new Testing_Selenium("*chrome", $url);
		$selenium1->start();
		$selenium1->open($url);
		$selenium1->windowMaximize();
		$selenium1->waitForPageToLoad("20000");
			
		$selenium1->click("document.fPP.tipoMascaraDocumento[1]");
		$selenium1->type("id=fPP:dpDec:documentoParte", $cnpj);
		
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(120, 40);
		
		imagecopy($captcha, $printscreen, 0, 0, 100, 550, 120, 40);
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
		
		echo "Captcha: " . $textoCaptcha . "<br/>";
		
		$selenium1->type("id=fPP:j_id113:verifyCaptcha", $textoCaptcha);
		$selenium1->click("id=fPP:searchProcessos");
		$selenium1->waitForPageToLoad("10000");
		
		$resultado .= $selenium1->getText("css=span.rich-messages-label");	
		
		$selenium1->stop();
		$selenium1->close();	
		$selenium2->stop();
		$selenium2->close();		

		return $resultado;
	  }	  
	  
	  protected function consultaTribunalJusticaMS($url)
	  {		
		$resultado = "";
	  
		$selenium1 = new Testing_Selenium("*chrome", $url);
		$selenium1->start();
		$selenium1->open($url);	
		$selenium1->waitForPageToLoad("10000");		
		
		$selenium1->windowMaximize();		
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(200, 50);
		
		imagecopy($captcha, $printscreen, 0, 0, 175, 400, 200, 50);
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
		
		$selenium1->type("id=defaultCaptchaCampo", $textoCaptcha);
		$selenium1->click("id=pbEnviar");
		$selenium1->waitForPageToLoad("10000");		
		
		$resultado .= "Processo: " . $selenium1->getText("css=a.linkProcesso") . "<br/>";		
		$resultado .= $selenium1->getText("css=div.espacamentoLinhas") . "<br/>";
		$resultado .= $selenium1->getText("//div[@id='divProcesso2W0000FZY0000']/div/div[3]") . "<br/>";	
		// Buscar mais campos posteriormente
		
		$selenium1->stop();
		$selenium1->close();	
		$selenium2->stop();
		$selenium2->close();			

		return $resultado;
	  }
	  
	  protected function consultaTribunalJusticaMT($url, $cnpj)
	  {
		return "Nenhum processo encontrado para o CNPJ " . $cnpj;
	  }
	  
	  protected function consultaTribunalJusticaPA($url, $cnpj)
	  {
		return "Nenhum processo encontrado para o CNPJ " . $cnpj;
	  }	  
	  
	  protected function consultaTribunalJusticaPB($url, $cnpj)
	  {
		return "Não existem resultados para o Processo informado no grau de jurisdição selecionado.";
	  }

	  protected function consultaTribunalJusticaPE($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium1 = new Testing_Selenium("*chrome", $url);
		$selenium1->start();
		$selenium1->open($url);	
		$selenium1->waitForPageToLoad("10000");		
		
		$selenium1->click("id=rbBusca6");
		$selenium1->select("id=tipo","label=CNPJ");
		
		$selenium1->windowMaximize();		
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(120, 30);
		
		imagecopy($captcha, $printscreen, 0, 0, 210, 300, 120, 30);
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

		echo "Captcha: " . $textoCaptcha . "<br/>";
		
		$selenium1->type("id=CaptchaBox6", $textoCaptcha);
		$selenium1->click("css=input.input_02");
		$selenium1->waitForPageToLoad("10000");		
		
		$resultado .= $selenium1->getText("css=span.menu_01");		
		// Buscar mais campos posteriormente
		
		$selenium1->stop();
		$selenium1->close();	
		$selenium2->stop();
		$selenium2->close();			

		return $resultado;
	  }	  
	}		  	
?>