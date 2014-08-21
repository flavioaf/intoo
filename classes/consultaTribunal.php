<?php
	require_once 'Testing/Selenium.php';
	require_once 'PHPUnit/Framework/Test.php';
	require_once 'PHPUnit/Framework/Assert.php';
	require_once 'PHPUnit/Framework/SelfDescribing.php';
	require_once 'PHPUnit/Framework/TestCase.php';	
	require_once("./engine/funcoes.php");

	class ConsultaTribunal extends PHPUnit_Framework_TestCase
	{
	  public function setUp($uf, $cnpj, $numero, $nome, $processo)
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
		if($numero >= 28 && $numero < 52) //Tribunais de Justiça
		{
			$this->consultaTribunaisJustica($uf, $cnpj, $nome, $processo);
		}
		if($numero >= 52) //Tribunais Regionais do Trabalho
		{
			$this->consultaTribunaisRegionaisTrabalho($cnpj, $numero);
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

		if($textoCaptcha == "OR: Element css=td not found")
		{
			$texto = "Falha ao resolver o captcha!";
		}
		else
		{
			$selenium1->type("trf1_captcha", $textoCaptcha);
			$selenium1->click("id=enviar");
			$selenium1->waitForPageToLoad("10000");
			
			$texto = $selenium1->getText("css=div.flash.error");
			
			if($texto == "OR: Element css=div.flash.error not found")
			{
				$numeroProcessos = (int)$selenium1->getText("css=td.span-2");
				$nomeParte = $selenium1->getText("css=a.listar-processo");	

				if($nomeParte == "OR: Element css=a.listar-processo not found")
				{
					$texto = "Nenhum registro encontrado para o CPF/CNPJ informado:[cnpj: ".$cnpj.", mostrar processos baixados: Sim]";
				}
				else
				{
					if($numeroProcessos > 1)
					{
						$plural = "s";
					}
					else
					{
						$plural = "";
					}
				
					$texto = $numeroProcessos . " processo".$plural." , " . $nomeParte . "<br/>";
					
					$selenium1->click("css=a.listar-processo");
					$selenium1->waitForFrameToLoad("css=div.lista-processo", 2000);
					
					$texto .= "<table class='mini-tabela'><tr><th>N&uacute;mero novo</th><th>N&uacute;mero antigo</th><th>Classe</th><th>Descri&ccedil;&atilde;o da Classe</th></tr>";
					
					for($i=0; $i < $numeroProcessos; $i++)
					{
						$row = $i + 1;
						$numeroNovo   = $selenium1->getTable("css=div.lista-processo > table.".$row.".0");
						$numeroAntigo = $selenium1->getTable("css=div.lista-processo > table.".$row.".1");
						$classe		  = $selenium1->getTable("css=div.lista-processo > table.".$row.".2");
						$descricao	  = $selenium1->getTable("css=div.lista-processo > table.".$row.".3");
					
						$texto .= "<tr><td>".$numeroNovo."</td><td>".$numeroAntigo."</td><td>".$classe."</td><td>".$descricao."</td></tr>";
					}
					
					$texto .= "</table>";
				}
			}
			else
			{
				$frases = explode(".", $texto);
				$texto = $frases[0];
			}
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
			$resultado = "Processos encontrados!<br/>";		
			
			for($i=0; $i < 3; $i++)
			{
				$j = $i + 3;
				$resultado .= $selenium->getText("//form[@id='ResConsPessDados']/table/tbody/tr/td/table/tbody/tr/td/table/tbody/tr[".$j."]/td/p") . "<br/>";
			}
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
		
		if($resultado != "O Sistema não Encontrou processos que atendam aos critérios informados.")
		{
			$palavras = explode(" ", $resultado);
			$numProcessos = (int)$palavras[3];
			
			if($numProcessos > 0)
			{			
				$resultado .= "<table class='mini-tabela'><tr><th>Processo</th><th>Classe</th><th>Secretaria</th><th>Situa&ccedil;&atilde;o</th><th>Numera&ccedil;&atilde;o antiga</th><th>Localiza&ccedil;&atilde;o</th></tr>";
				
				for($i=0; $i < $numProcessos; $i++)
				{
					$row = $i + 1;
					$processo    = $selenium->getTable("css=table.".$row.".0");
					$classe	     = $selenium->getTable("css=table.".$row.".1");
					$secretaria  = $selenium->getTable("css=table.".$row.".2");
					$situacao	 = $selenium->getTable("css=table.".$row.".3");
					$numAntiga	 = $selenium->getTable("css=table.".$row.".4");
					$localizacao = $selenium->getTable("css=table.".$row.".5");
								
					$arrLinha = explode(" ", $processo);
					
					if($arrLinha[0] == "OR:")
					{
						break;
					}
				
					$resultado .= "<tr><td>".$processo."</td><td>".$classe."</td><td>".$secretaria."</td><td>".$situacao."</td><td>".$numAntiga."</td><td>".$localizacao."</td></tr>";
				}
				
				$resultado .= "</table>";			
			}
		}
		
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
		$selenium1->waitForPageToLoad("20000");
		
		if($uf != "SC")
		{
			$selenium1->select("id=selForma", "value=CP");
			$selenium1->type("id=txtValor", $cnpj);
			$selenium1->click("id=chkMostrarBaixados");		
			$selenium1->click("id=botaoEnviar");		
			
			$selenium1->waitForPageToLoad("20000");		
		
			$srcCaptcha = $selenium1->getAttribute($referenciaImg."@src");		
			$arrCaptcha = explode("=", $srcCaptcha);
			$captcha = $arrCaptcha[1];
			
			$selenium1->type("name=txtPalavraGerada", $captcha);
			$selenium1->click("css=input.botao");	
			
			$selenium1->waitForPageToLoad("20000");			
			$resultado = $selenium1->getText("id=areaResultadoAcompanhamento");
			$arrResultado = explode(" ", $resultado);
			
			if($arrResultado[0] == "OR:")
			{
				$resultado = "Nenhum processo encontrado para o CNPJ ". $cnpj;
			}
			
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
			$arrResultado = explode(" ", $resultado);
			
			if($arrResultado[0] == "OR:")
			{
				$resultado = "Nenhum processo encontrado para o CNPJ ". $cnpj;
			}			
			
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
		$arrResultado = explode(" ", $resultado);
		
		if($arrResultado[0] == "OR:")
		{
			$resultado = "Nenhum processo encontrado para o CNPJ ". $cnpj;
		}			
		
		$selenium->stop();
		$selenium->close();	
		
		echo $resultado;
	  }	 
	  
	  protected function consultaTribunaisJustica($uf, $cnpj, $nome, $processo)
	  {
		$resultado = "";
		$consulta = false;
		
		switch($uf)
		{
			case "AC":	
				$url = "http://esaj.tjac.jus.br/cpo/pg/search.do?paginaConsulta=1&localPesquisa.cdLocal=1&cbPesquisa=DOCPARTE&tipoNuProcesso=SAJ&dePesquisa=".$cnpj."&pbEnviar=Pesquisar";
				$resultado = $this->consultaTribunalJusticaAC($url, $cnpj);
				$consulta = true;
			break;			
			case "AL":
				$url = "http://www2.tjal.jus.br/cpopg/search.do;jsessionid=FF1AEA3366DA36C6397FB6CE055C1AE8?dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&dadosConsulta.tipoNuProcesso=UNIFICADO&dadosConsulta.valorConsulta=".$cnpj;
				$resultado = $this->consultaTribunalJusticaAL($url, $cnpj);
				$consulta = true;			
			break;
			case "AP":
				$url = "http://app.tjap.jus.br/tucujuris/publico/processo/";
				$resultado = $this->consultaTribunalJusticaAP($url, $cnpj, $nome, $processo);
				$consulta = true;
			break;
			case "BA":
				$url = "http://esaj.tjba.jus.br/cpopg/search.do;jsessionid=C5B4903AD4877336FB91CFA8FDC68CFF.cpopg2?dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&dadosConsulta.tipoNuProcesso=UNIFICADO&dadosConsulta.valorConsulta=".$cnpj;
				$resultado = $this->consultaTribunalJusticaBA($url, $cnpj);
				$consulta = true;
			break;		
			case "CE":
				$url = "http://esaj.tjce.jus.br/cpopg/search.do;jsessionid=C6F6DE39A53B03910D29330FC252B412.cpos1?conversationId=&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&dadosConsulta.tipoNuProcesso=UNIFICADO&dadosConsulta.valorConsulta=".$cnpj;
				$resultado = $this->consultaTribunalJusticaCE($url, $cnpj);
				$consulta = true;
			break;
			case "DF":
				$url = "http://tjdf19.tjdft.jus.br/cgi-bin/tjcgi1?NXTPGM=tjhtml101&submit=ok&SELECAO=10&CHAVE=".$cnpj."&CIRC=ZZ&CHAVE1=&ORIGEM=INTER";
				$resultado = $this->consultaTribunalJusticaDF($url, $cnpj);
				$consulta = true;
			break;		
			case "ES":
				$url = "http://aplicativos.tjes.jus.br/consultaunificada/faces/pages/pesquisaSimplificada.xhtml";
				$resultado = $this->consultaTribunalJusticaES($url, $cnpj, $nome);
				$consulta = true;
			break;	
			case "MA":
				$url = "http://pje.tjma.jus.br/pje/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTribunalJusticaMA($url, $cnpj);
				$consulta = true;
			break;	
			case "MS":
				$url = "http://www.tjms.jus.br/cpopg5/search.do?cdForo=0&cbPesquisa=DOCPARTE&dePesquisa=".$cnpj."&tipoNuProcesso=SAJ";
				$resultado = $this->consultaTribunalJusticaMS($url, $cnpj);
				$consulta = true;
			break;	
			case "MT":
				$url = "http://servicos.tjmt.jus.br/processos/comarcas/consulta.aspx";
				$resultado = $this->consultaTribunalJusticaMT($url, $cnpj, $nome);
				$consulta = true;
			break;		
			case "PA":
				$url = "http://wsconsultas.tjpa.jus.br/consultaprocessoportal/consulta/consulta/principal";
				$resultado = $this->consultaTribunalJusticaPA($url, $cnpj, $nome, $processo);
				$consulta = true;
			break;		
			case "PB":
				$url = "http://app.tjpb.jus.br/consultaprocessual2/views/consultarPorParte.jsf";
				$resultado = $this->consultaTribunalJusticaPB($url, $cnpj, $nome, $processo);
				$consulta = true;
			break;	
			case "PE":
				$url = "http://www.tjpe.jus.br/processos/consulta2grau/ole_busca_processos2.asp";
				$resultado = $this->consultaTribunalJusticaPE($url, $cnpj);
				$consulta = true;
			break;		
			case "PI":
				$url = "http://www.tjpi.jus.br/themisconsulta/";
				$resultado = $this->consultaTribunalJusticaPI($url, $cnpj, $nome, $processo);
				$consulta = true;
			break;	
			case "PR":
				$url = "http://portal.tjpr.jus.br/civel/publico/consulta/processo.do?actionType=iniciar";
				$resultado = $this->consultaTribunalJusticaPR($url, $cnpj, $nome, $processo);
				$consulta = true;
			break;
			case "RJ":
				$url = "http://www4.tjrj.jus.br/ConsultaUnificada/consulta.do#tabs-cpf-indice4";
				$resultado = $this->consultaTribunalJusticaRJ($url, $cnpj);
				$consulta = true;
			break;	
			case "RN":
				$url = "http://esaj.tjrn.jus.br/cpo/pg/search.do;jsessionid=CA600C1A47B9BCC34FB046B144588F8D.appsWeb1?paginaConsulta=1&localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&tipoNuProcesso=UNIFICADO&dePesquisa=".$cnpj;
				$resultado = $this->consultaTribunalJusticaRN($url, $cnpj);
				$consulta = true;
			break;
			case "RO":
				$url = "http://www.tjro.jus.br/pginicial/form_appg_apsg.shtml";
				$resultado = $this->consultaTribunalJusticaRO($url, $cnpj);
				$consulta = true;
			break;	
			case "RR":
				$url = "http://www.tjrr.jus.br/tjrr-siscom-webapp/pages/index_nome.jsp";
				$resultado = $this->consultaTribunalJusticaRR($url, $nome, $processo);
				$consulta = true;
			break;
			case "RS":
				$url = "http://www3.tjrs.jus.br/site_php/consulta/index.php";
				$resultado = $this->consultaTribunalJusticaRS($url, $nome, $processo);
				$consulta = true;
			break;				
		}

		if(!$consulta)
		{
			$resultado .= "Nenhum processo encontrado";
		}
		
		echo $resultado;			
	  }	 

	  protected function consultaTribunalJusticaAC($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
			
		$resultado .= $selenium->getText("//div[@id='spwTabelaMensagem']/table/tbody/tr[2]/td[2]/li") . "<br/>";
		$arrResultado = explode(" ", $resultado);
		
		if($arrResultado[0] == "OR:") //Existe pelo menos um processo
		{		
			$paginacao = $selenium->getText("css=#paginacaoSuperior > tbody > tr > td");
			$arrPaginacao = explode(" ", $paginacao);		
			$qtdProcessos = (int)$arrPaginacao[5];
		
			if($arrPaginacao[0] == "OR:") //Somente um processo
			{		
				$resultado = $this->buscarProcessoESAJ($selenium);
			}
			else //Vários processos			
			{
				$resultado = "";
				$resultado .= "<b>Foram encontrados " . $qtdProcessos . " processos</b><br/>";
				
				$selenium->click("class=linkProcesso");
				$selenium->waitForPageToLoad("1000");
				
				$resultado .= $this->buscarProcessoESAJ($selenium) . "<br/>";				
				$resultado .= "<b>Para ver os demais " . ($qtdProcessos - 1) . " processos, clique no link a seguir: <a target='_blank' href='http://esaj.tjac.jus.br/cpo/pg/search.do?paginaConsulta=1&localPesquisa.cdLocal=1&cbPesquisa=DOCPARTE&tipoNuProcesso=SAJ&dePesquisa=".$cnpj."&pbEnviar=Pesquisar'>ver processos</a></b>";
			}
		}
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }	  	

	  protected function consultaTribunalJusticaAL($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$selenium->click("css=a.linkProcesso");		
		$selenium->waitForPageToLoad("10000");
		
		$resultado .= $selenium->getText("//div[@id='spwTabelaMensagem']/table/tbody/tr[2]/td[2]/li") . "<br/>";
		$arrResultado = explode(" ", $resultado);
		
		if($arrResultado[0] == "OR:") //Existe pelo menos um processo
		{		
			$paginacao = $selenium->getText("css=#paginacaoSuperior > tbody > tr > td");
			$arrPaginacao = explode(" ", $paginacao);		
			$qtdProcessos = (int)$arrPaginacao[5];
		
			if($arrPaginacao[0] == "OR:") //Somente um processo
			{		
				$resultado = $this->buscarProcessoESAJ($selenium);
			}
			else //Vários processos			
			{
				$resultado = "";
				$resultado .= "<b>Foram encontrados " . $qtdProcessos . " processos</b><br/>";
				
				$selenium->click("class=linkProcesso");
				$selenium->waitForPageToLoad("1000");
				
				$resultado .= $this->buscarProcessoESAJ($selenium) . "<br/>";				
				$resultado .= "<b>Para ver os demais " . ($qtdProcessos - 1) . " processos, clique no link a seguir: <a target='_blank' href='http://www2.tjal.jus.br/cpopg/search.do;jsessionid=FF1AEA3366DA36C6397FB6CE055C1AE8?dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&dadosConsulta.tipoNuProcesso=UNIFICADO&dadosConsulta.valorConsulta=".$cnpj."'>ver processos</a></b>";
			}
		}
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }

	  protected function consultaTribunalJusticaAP($url, $cnpj, $nome, $processo)
	  {
		$resultado = "";
	  
		if(isset($nome) && $nome != "")
		{
			$selenium = new Testing_Selenium("*chrome", $url);
			$selenium->start();
			$selenium->open($url);	
			$selenium->waitForPageToLoad("10000");				
			
			$selenium->windowMaximize();
			$selenium->type("id=formConsultaPublica:inputParteNome", $nome);
			$selenium->click("id=formConsultaPublica:botaoConsultar");
			$selenium->waitForPageToLoad("10000");	
			
			$resultados = $selenium->getText("css=p.help-block");
			$resultado .= "<b>". $resultados . "</b><br/><br/>";
			$arrResultados = explode(" ", $resultados);
			$qtdProcessos = (int)$arrResultados[0];
			
			$processo = $selenium->getText("css=h3.pull-left");		
		
			if(existeElemento($processo))
			{			
				$resultado .= "<b>" . $processo . "</b><br/>";
			}
			
			$vara = $selenium->getText("css=td > span");	
		
			if(existeElemento($vara))
			{
				$resultado .= $vara . "<br/>";	
			}
			
			$tipo_acao = $selenium->getText("//div[@id='div-resultado']/table/tbody/tr/td/span[2]");	
		
			if(existeElemento($tipo_acao))
			{
				$resultado .= $tipo_acao . "<br/>";	
			}					

			$grau = $selenium->getText("//div[@id='div-resultado']/table/tbody/tr/td/span[3]");
		
			if(existeElemento($grau))
			{			
				$resultado .= $grau . "<br/>";		
			}

			$parte_autora = $selenium->getText("css=div.parte-nome");
			$arrParteAutora = explode(" ", $parte_autora);
		
			if(existeElemento($parte_autora))	
			{
				$resultado .= "Parte autora: " . $parte_autora . "<br/>";	
			}

			$parte_re = $selenium->getText("//div[@id='div-resultado']/table/tbody/tr/td/span[4]/div/div[2]/div[2]");		
		
			if(existeElemento($parte_re))
			{
				$resultado .= "Parte r&eacute;: " . $parte_re . "<br/>";
			}

			$resultado .= "<br/><b>Para ver os demais " . ($qtdProcessos - 1) . " processos, clique no link a seguir: <a target='_blank' href='http://app.tjap.jus.br/tucujuris/publico/processo/index.xhtml'>ver processos</a></b>";				
			
			$selenium->stop();
			$selenium->close();	
		}
		else
		{
			$resultado .= "N&atilde;o foi poss&iacute;vel realizar sua pesquisa. Por favor preencha o nome da parte."; 
		}	
		
		return $resultado;
	  }
	  
	  protected function consultaTribunalJusticaBA($url, $cnpj)
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
		
		$resultado .= $selenium1->getText("//div[@id='spwTabelaMensagem']/table/tbody/tr[2]/td[2]/li") . "<br/>";
		$arrResultado = explode(" ", $resultado);
		
		if($arrResultado[0] == "OR:") //Existe pelo menos um processo
		{		
			$paginacao = $selenium1->getText("css=#paginacaoSuperior > tbody > tr > td");
			$arrPaginacao = explode(" ", $paginacao);		
			$qtdProcessos = (int)$arrPaginacao[5];
		
			if($arrPaginacao[0] == "OR:") //Somente um processo
			{		
				$resultado = $this->buscarProcessoESAJ($selenium1);
			}
			else //Vários processos			
			{
				$resultado = "";
				$resultado .= "<b>Foram encontrados " . $qtdProcessos . " processos</b><br/>";
				
				$selenium1->click("class=linkProcesso");
				$selenium1->waitForPageToLoad("1000");
				
				$resultado .= $this->buscarProcessoESAJ($selenium1) . "<br/>";				
				$resultado .= "<b>Para ver os demais " . ($qtdProcessos - 1) . " processos, clique no link a seguir: <a target='_blank' href='http://esaj.tjba.jus.br/cpopg/search.do;jsessionid=C5B4903AD4877336FB91CFA8FDC68CFF.cpopg2?dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&dadosConsulta.tipoNuProcesso=UNIFICADO&dadosConsulta.valorConsulta=".$cnpj."'>ver processos</a></b>";
			}
		}
		
		$selenium1->stop();
		$selenium1->close();	
		$selenium2->stop();
		$selenium2->close();			

		return $resultado;
	  }

	  protected function consultaTribunalJusticaCE($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$resultado .= $selenium->getText("//div[@id='spwTabelaMensagem']/table/tbody/tr[2]/td[2]/li") . "<br/>";
		$arrResultado = explode(" ", $resultado);
		
		if($arrResultado[0] == "OR:") //Existe pelo menos um processo
		{		
			$paginacao = $selenium->getText("css=#paginacaoSuperior > tbody > tr > td");
			$arrPaginacao = explode(" ", $paginacao);		
			$qtdProcessos = (int)$arrPaginacao[5];
		
			if($arrPaginacao[0] == "OR:") //Somente um processo
			{		
				$resultado = $this->buscarProcessoESAJ($selenium);
			}
			else //Vários processos			
			{
				$resultado = "";
				$resultado .= "<b>Foram encontrados " . $qtdProcessos . " processos</b><br/>";
				
				$selenium->click("class=linkProcesso");
				$selenium->waitForPageToLoad("1000");
				
				$resultado .= $this->buscarProcessoESAJ($selenium) . "<br/>";				
				$resultado .= "<b>Para ver os demais " . ($qtdProcessos - 1) . " processos, clique no link a seguir: <a target='_blank' href='http://esaj.tjce.jus.br/cpopg/search.do;jsessionid=C6F6DE39A53B03910D29330FC252B412.cpos1?conversationId=&dadosConsulta.localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&dadosConsulta.tipoNuProcesso=UNIFICADO&dadosConsulta.valorConsulta=".$cnpj."'>ver processos</a></b>";
			}
		}
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }		
	  
	  protected function consultaTribunalJusticaDF($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$resultados = $selenium->getText("css=font");
		if(existeElemento($resultados)) 
		{
			$resultado .= $resultados;	
			$arrResultado = explode(" ", $resultados);
			$qtdProcessos = (int)$arrResultado[0];
		}
		
		$resultado = "";
		$resultado .= "<b>Foram encontrados " . $qtdProcessos . " processos</b><br/><br/>";
		
		$selenium->click("id=processo_1_1_1");
		$selenium->waitForPageToLoad("1000");
		
		$circunscricao = $selenium->getText("id=i_nomeCircunscricao");
		if(existeElemento($circunscricao)) $resultado .= "<b>Circunscri&ccedil;&atilde;o:</b> " . $circunscricao . "<br/>";	
	
		$processo = $selenium->getText("id=i_numeroProcesso14");
		if(existeElemento($processo)) $resultado .= "<b>Processo:</b> " . $processo . "<br/>";		
		
		$data_distribuicao = $selenium->getText("id=i_dataDistribuicao");
		if(existeElemento($data_distribuicao)) $resultado .= "<b>Data Distribui&ccedil:</b> " . $data_distribuicao . "<br/>";	

		$num_processo = $selenium->getText("id=i_numeroProcesso20");
		if(existeElemento($num_processo)) $resultado .= "<b>Numeração Única do Processo (CNJ):</b> " . $selenium->getText("id=i_numeroProcesso20") . "<br/>";		
		
		$vara = $selenium->getText("id=i_descricaoVara");
		if(existeElemento($vara)) $resultado .= "<b>Vara:</b> " . $vara . "<br/><br/>";		
		
		$resultado .= "<b>Para ver os demais " . ($qtdProcessos - 1) . " processos, clique no link a seguir: <a target='_blank' href='http://tjdf19.tjdft.jus.br/cgi-bin/tjcgi1?NXTPGM=tjhtml101&submit=ok&SELECAO=10&CHAVE=".$cnpj."&CIRC=ZZ&CHAVE1=&ORIGEM=INTER'>ver processos</a></b>";		
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }	  
	  
	  protected function consultaTribunalJusticaES($url, $cnpj, $nome)
	  {		
		$resultado = "";
	  
		if(isset($nome) && $nome != "")
		{
			$selenium = new Testing_Selenium("*chrome", $url);
			$selenium->start();
			$selenium->open($url);
			$selenium->windowMaximize();
			$selenium->waitForPageToLoad("10000");
			
			$selenium->type("id=txtPesquisaSimplificada", $nome);
			$selenium->click("id=btnRealizarPesquisaSimplificada");
			$selenium->waitForPageToLoad("10000");
			
			$resultados = $selenium->getText("css=#layoutResultados > div.ui-layout-unit-content.ui-widget-content > div > span");	
			
			if(existeElemento($resultados)) 
			{
				$resultado .= "<b>" . $resultados . "</b><br/><br/>";
				
				$processo = $selenium->getText("css=span.proPesq");
				if(existeElemento($processo)) $resultado .= "<b>" . $processo . "</b><br/>";
				
				$ultimo_andamento = $selenium->getText("//tbody[@id='tabelaResultados_data']/tr/td/div/div/span[2]");
				if(existeElemento($ultimo_andamento)) $resultado .= "&Uacute;ltimo andamento em " . $ultimo_andamento . "<br/>";
				
				$tipo_acao = $selenium->getText("//tbody[@id='tabelaResultados_data']/tr/td/div/div[2]/span[2]");
				if(existeElemento($tipo_acao)) $resultado .= "<b>A&ccedil;&atilde;o:</b> " . $tipo_acao . "<br/>";
				
				$vara = $selenium->getText("//tbody[@id='tabelaResultados_data']/tr/td/div/div[3]/span[2]");
				if(existeElemento($vara)) $resultado .= "<b>Vara:</b> " . $vara . "<br/>";				
				
				$situacao = $selenium->getText("//tbody[@id='tabelaResultados_data']/tr/td/div/div[4]/span[2]");
				if(existeElemento($situacao)) $resultado .= "<b>Situa&ccedil;&atilde;o: </b> " . $situacao . "<br/>";	

				$peticao_inicial = $selenium->getText("//tbody[@id='tabelaResultados_data']/tr/td/div/div[5]/span[2]");
				if(existeElemento($peticao_inicial)) $resultado .= "<b>Peti&ccedil;&atilde;o inicial: </b> " . $peticao_inicial . "<br/>";				
				
				$requerente = $selenium->getText("css=span.dadosPesq");
				if(existeElemento($requerente)) $resultado .= "<b>Requerente: </b> " . $requerente . "<br/>";	

				$requerido = $selenium->getText("//tbody[@id='tabelaResultados_data']/tr/td/div/div[7]/span");
				if(existeElemento($requerido)) $resultado .= "<b>Requerido: </b> " . $requerido . "<br/>";		

				$resultado .= "<br/><b>Para ver os demais processos, clique no link a seguir: <a target='_blank' href='http://aplicativos.tjes.jus.br/consultaunificada/faces/pages/pesquisaSimplificada.xhtml;jsessionid=l6FxTrqHGnyxWF2yJNC3XGJ1p2jg4623pR5b7RjpTPKQ7sns7ZXM!-811950300'>ver processos</a></b>";				
			}
			else
			{
				$resultado .= "N&atilde;o foram encontrados resultados para os termos pesquisados.";
			}
		
			$selenium->stop();
			$selenium->close();	
		}
		else
		{
			$resultado .= "N&atilde;o foi poss&iacute;vel realizar sua pesquisa. Por favor preencha o nome da parte."; 
		}
		
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

		$selenium1->type("id=fPP:j_id113:verifyCaptcha", $textoCaptcha);
		$selenium1->click("id=fPP:searchProcessos");
		$selenium1->waitForPageToLoad("10000");
		
		$resultado .= $selenium1->getText("css=span.rich-messages-label");	
		$arrResultado = explode(" ", $resultado);		
		
		if($arrResultado[0] == "OR:") //Encontrou pelo menos um processo
		{	
			$resultado = "";
			$qtdProcessos = (int)$selenium1->getText("//div[@id='fPP:processosTable:j_id138']/div/span");

			if($qtdProcessos > 0)
			{
				$resultado .= "<b>Total de registros: ".$qtdProcessos."</b><br/>";
			
				$processo = $selenium1->getText("id=fPP:processosTable:350:j_id144");
				$arrProcesso = explode(" ", $processo);

				if($arrProcesso[0] != "OR:")
				{			
					$resultado .= "<b>Processo:</b> " . $processo . "<br/>";
				}
				
				$classe = $selenium1->getText("id=fPP:processosTable:350:j_id147");
				$arrClasse = explode(" ", $classe);

				if($arrClasse[0] != "OR:")
				{			
					$resultado .= "<b>Classe judicial:</b> " . $classe . "<br/>";
				}		

				$assunto = $selenium1->getText("id=fPP:processosTable:350:j_id150");
				$arrAssunto = explode(" ", $assunto);

				if($arrAssunto[0] != "OR:")
				{			
					$resultado .= "<b>Assunto principal:</b> " . $assunto . "<br/>";
				}		

				$ultimaMovimentacao = $selenium1->getText("id=fPP:processosTable:350:j_id153");
				$arrUltimaMovimentacao = explode(" ", $ultimaMovimentacao);

				if($arrUltimaMovimentacao[0] != "OR:")
				{			
					$resultado .= "<b>&Uacute;ltima movimenta&ccedil;&atilde;o:</b> " . $ultimaMovimentacao . "<br/>";
				}

				$partes = $selenium1->getText("id=fPP:processosTable:350:j_id156");
				$arrPartes = explode(" ", $partes);

				if($arrPartes[0] != "OR:")
				{			
					$resultado .= "<b>Partes:</b> " . $partes . "<br/>";
				}					
			}
		}		
		
		$selenium1->stop();
		$selenium1->close();	
		$selenium2->stop();
		$selenium2->close();		

		return $resultado;
	  }	  
	  
	  protected function consultaTribunalJusticaMS($url, $cnpj)
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
		
		$resultado .= $selenium1->getText("//div[@id='spwTabelaMensagem']/table/tbody/tr[2]/td[2]/li") . "<br/>";
		$arrResultado = explode(" ", $resultado);
		
		if($arrResultado[0] == "OR:") //Existe pelo menos um processo
		{		
			$paginacao = $selenium1->getText("css=#paginacaoSuperior > tbody > tr > td");
			$arrPaginacao = explode(" ", $paginacao);		
			$qtdProcessos = (int)$arrPaginacao[5];
		
			if($arrPaginacao[0] == "OR:") //Somente um processo
			{		
				$resultado = $this->buscarProcessoESAJ($selenium1);
			}
			else //Vários processos			
			{
				$resultado = "";
				$resultado .= "<b>Foram encontrados " . $qtdProcessos . " processos</b><br/>";
				
				$selenium1->click("class=linkProcesso");
				$selenium1->waitForPageToLoad("1000");
				
				$resultado .= $this->buscarProcessoESAJ($selenium1) . "<br/>";				
				$resultado .= "<b>Para ver os demais " . ($qtdProcessos - 1) . " processos, clique no link a seguir: <a target='_blank' href='http://www.tjms.jus.br/cpopg5/search.do?cdForo=0&cbPesquisa=DOCPARTE&dePesquisa=".$cnpj."&tipoNuProcesso=SAJ'>ver processos</a></b>";
			}
		}
		
		$selenium1->stop();
		$selenium1->close();	
		$selenium2->stop();
		$selenium2->close();			

		return $resultado;
	  }
	  
	  protected function consultaTribunalJusticaMT($url, $cnpj, $nome)
	  {
		$resultado = "";
		
		if(isset($nome) && $nome != "")
		{
			$selenium = new Testing_Selenium("*chrome", $url);
			$selenium->start();
			$selenium->open($url);
			$selenium->windowMaximize();
			$selenium->waitForPageToLoad("10000");
			
			$selenium->select("id=ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_ddlComarcas", "value=");
			$selenium->type("id=ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_txtNomeParte", $nome);
			$selenium->click("id=ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_btSubmit");
			$selenium->waitForPageToLoad("10000");
			
			$selenium->click("id=ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_listPartes_ctrl0_LinkButton1");
			$selenium->waitForPageToLoad("10000");
			
			$processo = $selenium->getText("//div[@id='listaProcesso']/div/div/span");
			if(existeElemento($processo)) $resultado .= "<b>".$processo."</b><br/>";
			
			$nome_parte = $selenium->getText("css=span.tamanho13.family");
			if(existeElemento($nome_parte)) $resultado .= "<b>Nome da Parte:</b> ".$nome_parte."<br/>";		

			$comarca = $selenium->getText("//span[@id='ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_lblParametroPesquisa']/table/tbody/tr/td[2]/span");
			if(existeElemento($comarca)) $resultado .= "<b>Comarca:</b> ".$comarca."<br/>";			

			$assunto = $selenium->getText("//span[@id='ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_lblParametroPesquisa']/table[2]/tbody/tr/td[2]/span");
			if(existeElemento($assunto)) $resultado .= "<b>Assunto:</b> ".$assunto."<br/>";		

			$tipo_acao = $selenium->getText("//span[@id='ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_lblParametroPesquisa']/table[2]/tbody/tr[2]/td[2]/span");
			if(existeElemento($tipo_acao)) $resultado .= "<b>Tipo de A&ccedil;&atilde;o:</b> ".$tipo_acao."<br/>";				
			
			$lotacao = $selenium->getText("//span[@id='ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_lblParametroPesquisa']/table[2]/tbody/tr[3]/td[2]/span");
			if(existeElemento($lotacao)) $resultado .= "<b>Lota&ccedil;&atilde;o:</b> ".$lotacao."<br/>";			
			
			$livro = $selenium->getText("//span[@id='ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_lblParametroPesquisa']/table[2]/tbody/tr[4]/td[2]/span");
			if(existeElemento($livro)) $resultado .= "<b>Livro:</b> ".$livro."<br/>";		

			$tipo = $selenium->getText("//span[@id='ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_lblParametroPesquisa']/table[2]/tbody/tr[5]/td[2]/span");
			if(existeElemento($tipo)) $resultado .= "<b>Tipo:</b> ".$tipo."<br/>";		

			$juiz_atual = $selenium->getText("//span[@id='ctl00_ctl00_ctl00_ctl00_phMiolo_ContentPrincipal_cphConsultaProcessoPrincipal_Principal_lblParametroPesquisa']/table[2]/tbody/tr[6]/td[2]/span");
			if(existeElemento($juiz_atual)) $resultado .= "<b>Juiz Atual:</b> ".$juiz_atual."<br/>";

			$resultado .= "<br/><b>Para ver os demais processos, clique no link a seguir: <a target='_blank' href='http://servicos.tjmt.jus.br/processos/comarcas/listaParte.aspx'>ver processos</a></b>";			
			
			$selenium->stop();
			$selenium->close();				
		}
		else
		{
			$resultado .= "N&atilde;o foi poss&iacute;vel realizar sua pesquisa. Por favor preencha o nome da parte."; 		
		}
		
		return $resultado;
	  }
	  
	  protected function consultaTribunalJusticaPA($url, $cnpj, $nome, $processo)
	  {
		$resultado = "";
		
		if(isset($nome) && $nome != "")
		{
			$selenium1 = new Testing_Selenium("*chrome", $url);
			$selenium1->start();
			$selenium1->open($url);
			$selenium1->windowMaximize();
			$selenium1->waitForPageToLoad("10000");

			$selenium1->click("css=#menuConsultaDetalhada > a");
			
			$selenium1->waitForCondition("document.getElementById('radioPorNomeParte')", 1000);
			$selenium1->click("id=radioPorNomeParte");
			$selenium1->click("id=checkNomeCompleto");
			
			$selenium1->waitForCondition("document.getElementById('inputTextPorNomeParte')", 1000);
			$selenium1->type("id=inputTextPorNomeParte", $nome);	
			
			$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
			$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
			$captcha = imagecreate(150, 50);
			
			imagecopy($captcha, $printscreen, 0, 0, 200, 425, 150, 50);
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
						
			$selenium1->type("id=textCaptcha", $textoCaptcha);
			$selenium1->click("id=buttonPesquisar");
			
			$resultado .= "<table class='mini-tabela'><tr><th>N&uacute;mero Processo</th><th>Classe</th><th>Assunto</th><th>Vara</th><th>Nome da Parte</th><th>Participa&ccedil;&atilde;o</th></tr>";
			
			for($i=0; $i < 10; $i++)
			{
				$row = $i + 1;
				$numProcesso  = $selenium1->getTable("css=table.tablesorter.tablesorter-blue.".$row.".0");
				$classe		  = $selenium1->getTable("css=table.tablesorter.tablesorter-blue.".$row.".1");
				$assunto	  = $selenium1->getTable("css=table.tablesorter.tablesorter-blue.".$row.".2");
				$vara	 	  = $selenium1->getTable("css=table.tablesorter.tablesorter-blue.".$row.".3");
				$nomeParte	  = $selenium1->getTable("css=table.tablesorter.tablesorter-blue.".$row.".4");
				$participacao = $selenium1->getTable("css=table.tablesorter.tablesorter-blue.".$row.".5");
			
				$resultado .= "<tr>";
				if(existeElemento($numProcesso)){ $resultado .= "<td>".$numProcesso."</td>"; }else{ $resultado .= "<td></td>"; };
				if(existeElemento($classe)){ $resultado .= "<td>".$classe."</td>"; }else{ $resultado .= "<td></td>"; }
				if(existeElemento($assunto)){ $resultado .= "<td>".$assunto."</td>"; }else{ $resultado .= "<td></td>"; }
				if(existeElemento($vara)){ $resultado .= "<td>".$vara."</td>"; }else{ $resultado .= "<td></td>"; }
				if(existeElemento($nomeParte)){ $resultado .= "<td>".$nomeParte."</td>"; }else{ $resultado .= "<td></td>"; }
				if(existeElemento($participacao)){ $resultado .= "<td>".$participacao."</td>"; }else{ $resultado .= "<td></td>"; }
				$resultado .= "</tr>";		
			}
			
			$resultado .= "</table>";

			$selenium1->stop();
			$selenium1->close();	
			$selenium2->stop();
			$selenium2->close();				
		}
		else
		{
			if(isset($processo) && $processo != "")
			{						
				$selenium1 = new Testing_Selenium("*chrome", $url);
				$selenium1->start();
				$selenium1->open($url);
				$selenium1->windowMaximize();
				$selenium1->waitForPageToLoad("10000");

				$selenium1->waitForCondition("document.getElementById('textProcesso')", 1000);
				$selenium1->type("id=textProcesso", $processo);
				
				$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
				$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
				$captcha = imagecreate(150, 50);
				
				imagecopy($captcha, $printscreen, 0, 0, 175, 330, 150, 50);
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
							
				$selenium1->type("id=textCaptcha", $textoCaptcha);
				$selenium1->click("id=buttonPesquisar");
				
				$selenium1->waitForCondition("document.getElementById('divDadosProcesso')", 1000);
				
				$processo = $selenium1->getText("//div[@id='divDadosProcesso']/table/tbody/tr/td[2]");				
				if(existeElemento($processo)) $resultado .= "<b>".$processo."<br/>";				

				$instancia = $selenium1->getText("//div[@id='divDadosProcesso']/table/tbody/tr[3]/td[2]");
				if(existeElemento($instancia)) $resultado .= "<b>Inst&acirc;ncia:</b> ".$instancia."<br/>";		

				$comarca = $selenium1->getText("//div[@id='divDadosProcesso']/table/tbody/tr[4]/td[2]");
				if(existeElemento($comarca)) $resultado .= "<b>Comarca:</b> ".$comarca."<br/>";			

				$situacao = $selenium1->getText("//div[@id='divDadosProcesso']/table/tbody/tr[5]/td[2]");
				if(existeElemento($situacao)) $resultado .= "<b>Situa&ccedil&atilde;o:</b> ".$situacao."<br/>";			

				$area = $selenium1->getText("//div[@id='divDadosProcesso']/table/tbody/tr[6]/td[2]");
				if(existeElemento($area)) $resultado .= "<b>&Aacute;rea:</b> ".$area."<br/>";	

				$selenium1->stop();
				$selenium1->close();	
				$selenium2->stop();
				$selenium2->close();					
			}
			else
			{
				$resultado .= "N&atilde;o foi poss&iacute;vel realizar sua pesquisa. Por favor preencha o nome da parte ou o n&uacute;mero do processo."; 	
			}
		}
		
		return $resultado;
	  }	  
	  
	  protected function consultaTribunalJusticaPB($url, $cnpj, $nome, $processo)
	  {
		$resultado = "";
		
		if(isset($nome) && $nome != "")
		{
			$selenium = new Testing_Selenium("*chrome", $url);
			$selenium->start();
			$selenium->open($url);
			$selenium->windowMaximize();
			$selenium->waitForPageToLoad("10000");
			
			$selenium->select("id=formConsultaProcessual:blocoConsultaPadrao:cpSelectTipoPesquisa", "value=3");
			$selenium->type("id=formConsultaProcessual:blocoConsultaPadrao:textoPesquisa", $nome);
			$selenium->click("id=formConsultaProcessual:blocoConsultaPadrao:botaoConsultar");
				
			$selenium->waitForPageToLoad("10000");
			$resultados = $selenium->getText("css=h4");
			
			if(existeElemento($resultados))
			{
				$resultado .= "<b>" . $resultados . "</b><br/>";
				$arrResultado = explode(" ", $resultados);
				$qtdProcessos = (int)$arrResultado[6];
				
				if($qtdProcessos > 50)
				{
					$range = 50;
				}
				else
				{
					$range = $qtdProcessos;
				}
				
				$resultado .= "<table class='mini-tabela'><tr><th>Nome da Parte</th><th>N&ordm; do Processo</th><th>Comarca</th><th>Jurisdi&ccedil;&atilde;o</th></tr>";
				
				for($i=0; $i < $range; $i++)
				{
					$row = $i + 1;
					$nomeParte   = $selenium->getTable("id=formConsultaProcessualParte:partesSelect.".$row.".1");
					$numProcesso = $selenium->getTable("id=formConsultaProcessualParte:partesSelect.".$row.".2");
					$comarca	 = $selenium->getTable("id=formConsultaProcessualParte:partesSelect.".$row.".4");
					$jurisdicao	 = $selenium->getTable("id=formConsultaProcessualParte:partesSelect.".$row.".5");		
				
					$resultado .= "<tr>";
					if(existeElemento($nomeParte)){ $resultado .= "<td>".$nomeParte."</td>"; }else{ $resultado .= "<td></td>"; };
					if(existeElemento($numProcesso)){ $resultado .= "<td>".$numProcesso."</td>"; }else{ $resultado .= "<td></td>"; }
					if(existeElemento($comarca)){ $resultado .= "<td>".$comarca."</td>"; }else{ $resultado .= "<td></td>"; }
					if(existeElemento($jurisdicao)){ $resultado .= "<td>".$jurisdicao."</td>"; }else{ $resultado .= "<td></td>"; }
					$resultado .= "</tr>";		
				}
				
				$resultado .= "</table>";				
			}
			else
			{
				$resultado = "Nenhum processo encontrado.";
			}

			$selenium->stop();
			$selenium->close();			
		}
		
		return $resultado;
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
		$selenium1->type("id=cpfcnpj", $cnpj);
		
		$selenium1->windowMaximize();		
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(120, 30);
		
		imagecopy($captcha, $printscreen, 0, 0, 210, 290, 120, 30);
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
		$selenium2->waitForPageToLoad("50000");
		$textoCaptcha = $selenium2->getText("css=td");			
		
		$selenium1->type("id=CaptchaBox6",$textoCaptcha);
		$selenium1->click("css=tr.texto01 > td > div > input.input02");
		
		$selenium1->waitForPageToLoad("10000");				
		$resultados = $selenium1->getText("css=span.menu_01");		
		
		if(existeElemento($resultados))
		{
			$resultado .= "<b>".$resultados."</b><br/>";
			$arrResultados = explode(" ", $resultados);
			$qtdProcessos = (int)$arrResultados[1];
			
			$resultado .= "<table class='mini-tabela'><tr><th>Nome da Parte</th><th>CNPJ</th></tr>";
			
			for($i=0; $i < 20; $i++)
			{
				$row = $i + 1;
				$nomeParte   = $selenium1->getTable("//table[2].".$row.".0");
				$CNPJ = $selenium1->getTable("//table[2].".$row.".1");	
			
				$resultado .= "<tr>";
				if(existeElemento($nomeParte)){ $resultado .= "<td><a target='_blank' href='http://www.tjpe.jus.br/processos/consulta2grau/ole_busca_processos_nome2.asp?pessoa=746213&nome=".$nomeParte."'>".$nomeParte."</a></td>"; }else{ $resultado .= "<td></td>"; };
				if(existeElemento($CNPJ)){ $resultado .= "<td>".$CNPJ."</td>"; }else{ $resultado .= "<td></td>"; }
				$resultado .= "</tr>";		
			}
			
			$resultado .= "</table>";				
		}
		else
		{
			$resultado .= "Nenhum resultado encontrado.";
		}
		
		$selenium1->stop();
		$selenium1->close();	
		$selenium2->stop();
		$selenium2->close();	

		return $resultado;
	  }	  
	  
	  protected function consultaTribunalJusticaPI($url, $cnpj, $nome, $processo)
	  {
		$resultado = "";
		
		if(isset($nome) && $nome != "")
		{
			$selenium = new Testing_Selenium("*chrome", $url);
			$selenium->start();
			$selenium->open($url);
			$selenium->windowMaximize();
			$selenium->waitForPageToLoad("10000");

			$selenium->click("link=Por parte");
			$selenium->waitForCondition("document.getElementById('select-comarca-numero')", 1000);
			
			$selenium->click("id=checkbox-parte-juridica");
			$selenium->type("id=input-parte-nome", $nome);
			$selenium->click("name=consulta.mostrarBaixados");
			$selenium->click("xpath=(//button[@type='submit'])[2]");			
			$selenium->waitForPageToLoad("10000");
			
			$resultados = $selenium->getText("css=strong");	
			
			if(existeElemento($resultados)) 
			{
				$resultado .= "<b>" . $resultados . "</b><br/><br/>";				
			
				$processo = $selenium->getText("css=div.numero-processo > a");
				if(existeElemento($processo)) $resultado .= "<b>" . $processo . "</b><br/>";
				
				$data_abertura = $selenium->getText("//div[@id='processos']/div/table/tbody/tr[2]/td");
				if(existeElemento($data_abertura)) $resultado .= "<b>Data de abertura:</b> " . $data_abertura . "<br/>";

				$natureza = $selenium->getText("//div[@id='processos']/div/table/tbody/tr[3]/td");
				if(existeElemento($natureza)) $resultado .= "<b>Natureza:</b> " . $natureza . "<br/>";	

				$classe = $selenium->getText("//div[@id='processos']/div/table/tbody/tr[4]/td");
				if(existeElemento($classe)) $resultado .= "<b>Classe:</b> " . $classe . "<br/>";			
				
				$vara = $selenium->getText("//div[@id='processos']/div/table/tbody/tr[5]/td");
				if(existeElemento($vara)) $resultado .= "<b>Vara:</b> " . $vara . "<br/>";

				$resultado .= "<br/><b>Para ver os outros  processos, clique no link a seguir: <a target='_blank' href='http://www.tjpi.jus.br/themisconsulta/consulta/parte'>ver processos</a></b>";						
			}
			else
			{
				$resultado .= "Nenhum resultado encontrado.";
			}
		}		
		else
		{
			$resultado .= "N&atilde;o foi poss&iacute;vel realizar sua pesquisa. Por favor preencha o nome da parte ou o n&uacute;mero do processo.";
		}
		
		return $resultado;
	  }	  	 

	  protected function consultaTribunalJusticaPR($url, $cnpj, $nome, $processo)
	  {
		$resultado = "";
	  
		if(isset($processo) && $processo != "")
		{
			$selenium1 = new Testing_Selenium("*chrome", $url);
			$selenium1->start();
			$selenium1->open($url);	
			$selenium1->waitForPageToLoad("10000");		
			
			$selenium1->type("id=numeroUnico", $processo);				
			$selenium1->windowMaximize();		
			$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
			$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
			$captcha = imagecreate(200, 75);
			
			imagecopy($captcha, $printscreen, 0, 0, 50, 440, 200, 75);
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
			$selenium2->waitForPageToLoad("50000");
			$textoCaptcha = $selenium2->getText("css=td");
			
			$selenium1->type("id=recaptcha_response_field", $textoCaptcha);
			$selenium1->click("id=pesquisar");
			
			$resultado .= "<table class='mini-tabela'><tr><th>N&uacute;mero</th><th>Partes</th><th>Classe Processual</th><th>Vara</th></tr>";
			
			$numero = $selenium1->getTable("css=table.resultTable.0.0");
			$partes = $selenium1->getTable("css=table.resultTable.0.1");	
			$classe = $selenium1->getTable("css=table.resultTable.0.2");	
			$vara	= $selenium1->getTable("css=table.resultTable.0.3");	
		
			$resultado .= "<tr>";
			if(existeElemento($numero)){ $resultado .= "<td>".$numero."</td>"; }else{ $resultado .= "<td></td>"; };
			if(existeElemento($partes)){ $resultado .= "<td>".$partes."</td>"; }else{ $resultado .= "<td></td>"; }
			if(existeElemento($classe)){ $resultado .= "<td>".$classe."</td>"; }else{ $resultado .= "<td></td>"; }
			if(existeElemento($vara)){ $resultado .= "<td>".$vara."</td>"; }else{ $resultado .= "<td></td>"; }
			$resultado .= "</tr>";		

			$resultado .= "</table>";			

			$selenium1->stop();
			$selenium1->close();	
			$selenium2->stop();
			$selenium2->close();				
		}
		
		return $resultado;
	  }
	  
	  protected function consultaTribunalJusticaRJ($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);	
		$selenium->waitForPageToLoad("10000");		
		
		$selenium->select("document.consultaCPFForm.origem","label=1ªInstância");
		$selenium->select("document.consultaCPFForm.comarca","label=Capital");
		$selenium->select("document.consultaCPFForm.competencia","label=Cível");
		$selenium->type("id=numeroCpfCnpj", $cnpj);
		$selenium->click("id=pesquisa");
		$selenium->waitForPageToLoad("10000");

		$total = $selenium->getText("//div[@id='content']/table/tbody/tr/td[2]/h3");
		
		if(existeElemento($total))
		{
			$resultado .= "<b>".$total."</b>";
			$arrTotal = explode(" ", $total);
			$qtdTotal = (int)$arrTotal[4];
			
			if($qtdTotal > 0)
			{				
				$processo = $selenium->getText("//div[@id='content']/form/table[3]/tbody/tr/td");		
				if(existeElemento($processo)) $resultado .= "<br/><br/><b>" . $processo . "</b><br/>";
				
				$autor = $selenium->getText("//div[@id='content']/form/table[3]/tbody/tr[2]/td");	
				if(existeElemento($autor)) $resultado .= "<b>Autor:</b> " . $autor . "<br/>";
				
				$reu = $selenium->getText("//div[@id='content']/form/table[3]/tbody/tr[3]/td");
				if(existeElemento($reu)) $resultado .= "<b>R&eacute;u:</b> " . $reu . "<br/>";
				
				$fase = $selenium->getText("//div[@id='content']/form/table[3]/tbody/tr[4]/td");	
				if(existeElemento($fase)) $resultado .= "<b>Fase:</b> " . $fase . "<br/>";
				
				$comarca = $selenium->getText("//div[@id='content']/form/table[3]/tbody/tr[5]/td");
				if(existeElemento($comarca)) $resultado .= "<b>Comarca:</b> " . $comarca . "<br/>";
				
				$serventia = $selenium->getText("//div[@id='content']/form/table[3]/tbody/tr[6]/td");	
				if(existeElemento($serventia)) $resultado .= "<b>Serventia:</b> " . $serventia . "<br/>";	

				$resultado .= "<br/><b>Para ver os demais " . ($qtdTotal - 1) . " processos, clique no link a seguir: <a target='_blank' href='http://www4.tjrj.jus.br/consultaProcessoNome/consultaCPF.do'>ver processos</a></b>";										
			}		
		}
		
		$selenium->stop();
		$selenium->close();	
		
		return $resultado;
	  }

	  protected function consultaTribunalJusticaRN($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
			
		$resultado .= $selenium->getText("//div[@id='spwTabelaMensagem']/table/tbody/tr[2]/td[2]/li") . "<br/>";
		$arrResultado = explode(" ", $resultado);
		
		if($arrResultado[0] == "OR:") //Existe pelo menos um processo
		{		
			$paginacao = $selenium->getText("css=#paginacaoSuperior > tbody > tr > td");
			$arrPaginacao = explode(" ", $paginacao);		
			$qtdProcessos = (int)$arrPaginacao[5];
		
			if($arrPaginacao[0] == "OR:") //Somente um processo
			{		
				$resultado = $this->buscarProcessoESAJ($selenium);
			}
			else //Vários processos			
			{
				$resultado = "";
				$resultado .= "<b>Foram encontrados " . $qtdProcessos . " processos</b><br/>";
				
				$selenium->click("class=linkProcesso");
				$selenium->waitForPageToLoad("10000");
				
				$resultado .= $this->buscarProcessoESAJ($selenium) . "<br/>";				
				$resultado .= "<b>Para ver os demais " . ($qtdProcessos - 1) . " processos, clique no link a seguir: <a target='_blank' href='http://esaj.tjrn.jus.br/cpo/pg/search.do;jsessionid=CA600C1A47B9BCC34FB046B144588F8D.appsWeb1?paginaConsulta=1&localPesquisa.cdLocal=-1&cbPesquisa=DOCPARTE&tipoNuProcesso=UNIFICADO&dePesquisa=".$cnpj."'>ver processos</a></b>";
			}
		}
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }

	  protected function consultaTribunalJusticaRO($url, $cnpj)
	  {		
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$selenium->click("id=grau");
		$selenium->select("id=tipo","label=Documento das Partes");
		$selenium->select("id=TpDoc","label=CNPJ");
		$selenium->type("id=argumentos",$cnpj);
		$selenium->click("name=Submit3");
		$selenium->waitForPageToLoad("10000");
		
		$registrosEncontrados = $selenium->getText("css=#corpo > div");
		
		if(existeElemento($registrosEncontrados))
		{
			$arrRegistrosEncontrados = explode("-", $registrosEncontrados);
			$segundaFrase = trim($arrRegistrosEncontrados[1]);
			$resultado .= "<b>" . $segundaFrase . "</b><br/>";
			
			$arr2RegistrosEncontrados = explode(" ", $segundaFrase);
			$qtdRegistros = (int)$arr2RegistrosEncontrados[0];
			
			if($qtdRegistros > 0)
			{
				$resultado .= "<table class='mini-tabela'><tr><th>Nome</th><th>C&oacute;digo de Cadastro</th><th></th></tr>";
			
				for($i = 0; $i < $qtdRegistros; $i++)
				{										
					$nome = $selenium->getTable("css=#_idJsp6 > table.".$i.".0");
					$codigo = $selenium->getTable("css=#_idJsp6 > table.".$i.".1");	
					$mf = $selenium->getTable("css=#_idJsp6 > table.".$i.".2");	
				
					$resultado .= "<tr>";
					if(existeElemento($nome)){ $resultado .= "<td>".$nome."</td>"; }else{ $resultado .= "<td></td>"; };
					if(existeElemento($codigo)){ $resultado .= "<td>".$codigo."</td>"; }else{ $resultado .= "<td></td>"; }
					if(existeElemento($mf)){ $resultado .= "<td>".$mf."</td>"; }else{ $resultado .= "<td></td>"; }
					$resultado .= "</tr>";					
				}
				
				$resultado .= "</table>";					
			}
		}
		else
		{
			$resultado .= "Nenhum registro encontrando para este CNPJ.";
		}
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }

	  protected function consultaTribunalJusticaRR($url, $nome, $processo)
	  {
		$resultado = "";
	  
		if(isset($nome) && $nome != "")
		{
			$url = "http://www.tjrr.jus.br/tjrr-siscom-webapp/pages/proc_nome.jsp?comrCodigo=0010&numero=1";
		
			$selenium = new Testing_Selenium("*chrome", $url);
			$selenium->start();
			$selenium->open($url);
			$selenium->windowMaximize();
			$selenium->waitForPageToLoad("10000");			

			$selenium->type("name=nomePessoa", $nome);
			$selenium->click("name=btn_pesquisar");
			$selenium->waitForPageToLoad("120000");	
			
			$pessoasEncontradas = $selenium->getText("//p[2]/b");
			
			if(existeElemento($pessoasEncontradas))
			{
				$resultado .= "<b>".$pessoasEncontradas."</b><br/>";
				$arrPessoasEncontradas = explode(" ", $pessoasEncontradas);
				$qtdPessoas = (int)$arrPessoasEncontradas[2];
				
				if($qtdPessoas > 0)
				{
					$resultado .= "<table class='mini-tabela'><tr><th>Nome</th><th>N&uacute;mero</th></tr>";
				
					for($i = 0; $i < $qtdPessoas; $i++)
					{										
						$nome = $selenium->getText("//table[".($i+2)."]/thead/tr/th/b");
						$numero = $selenium->getText("//table[".($i+2)."]/thead/tr/th[2]/b");									
						$infoProcesso = $selenium->getText("//table[".($i+2)."]/tbody/tr[3]/td/div");
						
						$arrProcesso  = explode("[", $infoProcesso);
						$arrProcesso2 = explode("-", $arrProcesso[1]);
						$linkProcesso = $arrProcesso2[0];
					
						$resultado .= "<tr>";
						if(existeElemento($nome)){ $resultado .= "<td><a target='_blank' href='http://www.tjrr.jus.br/tjrr-siscom-webapp/pages/proc_resultado.jsp?listaProcessos=".$linkProcesso."&comrCodigo=10&numero=1'>".$nome."</a></td>"; }else{ $resultado .= "<td></td>"; };
						if(existeElemento($numero)){ $resultado .= "<td>".$numero."</td>"; }else{ $resultado .= "<td></td>"; }					
						$resultado .= "</tr>";					
					}
					
					$resultado .= "</table>";
				}					
				else
				{
					$resultado .= "Nenhuma pessoa foi encontrada com o nome fornecido.";
				}
			}
		}		
		else
		{
			if(isset($processo) && $processo != "")
			{
				$url = "http://www.tjrr.jus.br/tjrr-siscom-webapp/pages/proc_massiva.jsp?comrCodigo=0010&numero=1";
				
				$selenium = new Testing_Selenium("*chrome", $url);
				$selenium->start();
				$selenium->open($url);
				$selenium->windowMaximize();
				$selenium->waitForPageToLoad("10000");	
			}
			else
			{
				$resultado .= "N&atilde;o foi poss&iacute;vel realizar sua pesquisa. Por favor preencha o nome."; 
			}
		}

		return $resultado;
	  }	  	 

	  protected function consultaTribunalJusticaRS($url, $nome, $processo)
	  {
		$resultado = "";
	  
		if(isset($nome) && $nome != "")
		{				
			$selenium1 = new Testing_Selenium("*chrome", $url);
			$selenium1->start();
			$selenium1->open($url);
			$selenium1->windowMaximize();
			$selenium1->waitForPageToLoad("10000");

			$selenium1->click("link=Por Nome da Parte");
			$selenium1->waitForCondition("document.getElementById('nome_parte')", 1000);
			$selenium1->type("id=nome_parte", $nome);
			
			$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
			$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
			$captcha = imagecreate(150, 50);
			
			imagecopy($captcha, $printscreen, 0, 0, 725, 475, 150, 50);
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
			$selenium2->waitForPageToLoad("50000");
			$textoCaptcha = $selenium2->getText("css=td");
			
			$selenium1->type("id=code", $textoCaptcha);
			$selenium1->click("id=btnPesquisar");
			$selenium1->waitForPageToLoad("10000");
			
			$processosEncontrados = $selenium1->getText("//div[@id='conteudo']/table[3]/tbody/tr/td");
			
			if(existeElemento($processosEncontrados))
			{
				$resultado .= "Não foram encontrados processos para o nome conforme os critérios acima. Tente refazer a pesquisa com o critério Situação selecionado na opção Baixados.";
			}

			$selenium1->stop();
			$selenium1->close();
			$selenium2->stop();
			$selenium2->close();			
		}		
		else
		{
			$resultado .= "N&atilde;o foi poss&iacute;vel realizar sua pesquisa. Por favor preencha o nome da parte."; 
		}

		return $resultado;
	  }

	  protected function consultaTribunaisRegionaisTrabalho($cnpj, $numero)
	  {
		$resultado = "";
		$consulta = false;		
		
		switch($numero)
		{
			case 52:	
				$url = "http://www.trt1.jus.br/consulta-processual";
				$resultado = $this->consultaTRT1Regiao($url);
				$consulta = true;
			break;
			case 53:	
				$url = "https://pje.trt2.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT2Regiao($url, $cnpj);
				$consulta = true;
			break;			
			case 54:	
				$url = "https://pje.trt3.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT3Regiao($url);
				$consulta = true;
			break;
			case 55:	
				$url = "http://www.trt4.jus.br/portal/portal/trt4/consultas/consulta_lista";
				$resultado = $this->consultaTRT4Regiao($url);
				$consulta = true;
			break;			
			case 56:	
				$url = "https://pje.trt5.jus.br/consultaprocessual/pages/consultas/ConsultaProcessual.seam";
				$resultado = $this->consultaTRT5Regiao($url);
				$consulta = true;
			break;			
			case 57:	
				$url = "http://www.trt6.jus.br/portal/";
				$resultado = $this->consultaTRT6Regiao($url);
				$consulta = true;
			break;			
			case 58:	
				$url = "https://pje.trt7.jus.br/consultaprocessual/pages/consultas/ConsultaProcessual.seam";
				$resultado = $this->consultaTRT7Regiao($url);
				$consulta = true;
			break;
			case 59:	
				$url = "https://pje.trt8.jus.br/consultaprocessual/pages/consultas/ConsultaProcessual.seam";
				$resultado = $this->consultaTRT8Regiao($url);
				$consulta = true;
			break;			
			case 60:	
				$url = "https://pje.trt9.jus.br/consultaprocessual/pages/consultas/ConsultaProcessual.seam";
				$resultado = $this->consultaTRT9Regiao($url);
				$consulta = true;
			break;
			case 61:	
				$url = "http://pje.trt10.jus.br/primeirograu/login.seam";
				$resultado = $this->consultaTRT10Regiao($url);
				$consulta = true;
			break;		
			case 62:	
				$url = "https://pje.trt11.jus.br/consultaprocessual/pages/consultas/ConsultaProcessual.seam";
				$resultado = $this->consultaTRT11Regiao($url);
				$consulta = true;
			break;
			case 63:	
				$url = "https://pje.trt12.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT12Regiao($url);
				$consulta = true;
			break;
			case 64:	
				$url = "https://pje.trt13.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT13Regiao($url);
				$consulta = true;
			break;
			case 65:	
				$url = "http://pje.trt14.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT14Regiao($url);
				$consulta = true;
			break;	
			case 66:	
				$url = "https://pje.trt15.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT15Regiao($url);
				$consulta = true;
			break;
			case 67:	
				$url = "http://pje.trt16.jus.br/primeirograu/login.seam";
				$resultado = $this->consultaTRT16Regiao($url);
				$consulta = true;
			break;	
			case 68:	
				$url = "http://pje.trtes.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT17Regiao($url);
				$consulta = true;
			break;			
			case 69:	
				$url = "https://pje.trt18.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT18Regiao($url);
				$consulta = true;
			break;
			case 70:	
				$url = "http://www.trt19.jus.br/siteTRT19/irPara?id=7";
				$resultado = $this->consultaTRT19Regiao($url);
				$consulta = true;
			break;	
			case 71:	
				$url = "https://pje.trt20.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT20Regiao($url);
				$consulta = true;
			break;	
			case 72:	
				$url = "https://pje.trt21.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT21Regiao($url);
				$consulta = true;
			break;	
			case 73:	
				$url = "http://pje.trt22.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT22Regiao($url);
				$consulta = true;
			break;	
			case 74:	
				$url = "https://pje.trt23.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT23Regiao($url);
				$consulta = true;
			break;	
			case 75:	
				$url = "https://pje.trt24.jus.br/primeirograu/ConsultaPublica/listView.seam";
				$resultado = $this->consultaTRT24Regiao($url);
				$consulta = true;
			break;				
		}

		if(!$consulta)
		{
			$resultado .= "Nenhum processo encontrado";
		}
		
		echo $resultado;			
	  }

	  protected function consultaTRT1Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por nome
	  }

	  protected function consultaTRT2Regiao($url, $cnpj)
	  {
		$resultado = "";
	  
		$selenium = new Testing_Selenium("*chrome", $url);
		$selenium->start();
		$selenium->open($url);
		$selenium->windowMaximize();
		$selenium->waitForPageToLoad("10000");
		
		$selenium->type("id=cnpj", $cnpj);
		$selenium->click("id=submit");
		$selenium->waitForPageToLoad("10000");
		
		$resultado .= $selenium->getHtmlSource();
		
		$selenium->stop();
		$selenium->close();	

		return $resultado;
	  }

	  protected function consultaTRT3Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo ou OAB
	  }

	  protected function consultaTRT4Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo ou OAB
	  }	 	  

	  protected function consultaTRT5Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo ou OAB
	  }	  

	  protected function consultaTRT6Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT7Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT8Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }	  
	  
	  protected function consultaTRT9Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }		 

	  protected function consultaTRT10Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }		  
	  
	  protected function consultaTRT11Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT12Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }		  
	  
	  protected function consultaTRT13Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT14Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT15Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT16Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT17Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }	  

	  protected function consultaTRT18Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }	 
	  
	  protected function consultaTRT19Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT20Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }	

	  protected function consultaTRT21Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT22Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT23Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function consultaTRT24Regiao($url)
	  {
		return "N&atilde;o foram encontrados processos para o CNPJ conforme os crit&eacute;rios acima.";
		//Consulta por número do processo
	  }

	  protected function buscarProcessoESAJ($selenium)
	  {
		$resultado = "";
		$resultado .= "<h4>Dados do Processo</h4>";
		
		$processo = $selenium->getText("//table[3]/tbody/tr/td[2]/table/tbody/tr/td/span");	
		$arrProcesso = explode(" ", $processo);
		
		if($arrProcesso[0] != "OR:")
		{
			$resultado .= "<b>Processo:</b> " . $processo . "<br/>";
		}

		$classe = $selenium->getText("css=span > span");			
		$arrClasse = explode(" ", $classe);
		
		if($arrClasse[0] != "OR:")
		{
			$resultado .= "<b>Classe:</b> " . $classe . "<br/>";
		}

		$area = $selenium->getText("//table[3]/tbody/tr[3]/td[2]/table/tbody/tr/td");
		$arrArea = explode(" ", $area);
		
		if($arrArea[0] != "OR:")
		{
			$resultado .= $area . "<br/>";
		}
		
		$assunto = $selenium->getText("xpath=(//span[@id=''])[3]");
		$arrAssunto = explode(" ", $assunto);
		
		if($arrAssunto[0] != "OR:")
		{
			$resultado .= "<b>Assunto:</b> " . $assunto . "<br/>";
		}

		$localFisico = $selenium->getText("xpath=(//span[@id=''])[4]");
		$arrLocalFisico = explode(" ", $localFisico);
		
		if($arrLocalFisico[0] != "OR:")
		{
			$resultado .= "<b>Local f&iacute;sico:</b> " . $localFisico . "<br/>";
		}
		
		$outrosAssuntos = $selenium->getText("xpath=(//span[@id=''])[5]");
		$arrOutrosAssuntos = explode(" ", $outrosAssuntos);
		
		if($arrOutrosAssuntos[0] != "OR:")
		{
			$resultado .= "<b>Outros assuntos:</b> " . $outrosAssuntos . "<br/>";
		}

		$distribuicao = $selenium->getText("xpath=(//span[@id=''])[6]");
		$arrDistribuicao = explode(" ", $distribuicao);
		
		if($arrDistribuicao[0] != "OR:")
		{
			$resultado .= "<b>Distribui&ccedil;&atilde;o:</b> " . $distribuicao . "<br/>";
		}
		
		$vara = $selenium->getText("xpath=(//span[@id=''])[7]");	
		$arrVara = explode(" ", $vara);
		
		if($arrVara[0] != "OR:")
		{
			$resultado .= $vara . "<br/>";
		}
		
		$valor_acao = $selenium->getText("xpath=(//span[@id=''])[8]");
		$arrValorAcao = explode(" ", $valor_acao);
		
		if($arrValorAcao[0] != "OR:")
		{
			$resultado .= "<b>Valor da a&ccedil;&atilde;o:</b> " . $valor_acao . "<br/>";
		}					
		
		$resultado .= "<h4>Partes do Processo</h4>";
		
		$requerente = $selenium->getText("//table[@id='tablePartesPrincipais']/tbody/tr/td[2]");
		$arrRequerente = explode(" ", $requerente);
		
		if($arrRequerente[0] != "OR:")
		{
			$resultado .= "<b>Requerente:</b> " . $requerente . "<br/>";
		}			
		
		$requerido = $selenium->getText("//table[@id='tablePartesPrincipais']/tbody/tr[2]/td[2]");
		$arrRequerido = explode(" ", $requerido);
		
		if($arrRequerido[0] != "OR:")
		{
			$resultado .= "<b>Requerido:</b> " . $requerido . "<br/>";
		}	

		return $resultado;
	  }	 
	}		  	
?>