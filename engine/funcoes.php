<?php		
	/*
	----------------------------------------------------------------------------------------------------------------------------------------
	Função que verifica a session e se tiver imprime
	----------------------------------------------------------------------------------------------------------------------------------------	
	*/
	
	function imprimeSESSION()
	{			
		//testa se tem alguma mensagem, se tiver, printa
		if(isset($_SESSION['msg']))
		{
			echo utf8_encode($_SESSION['msg']);
			unset($_SESSION['msg']);
		}
		
		//testa se tem algum erro, se tiver, printa
		if(isset($_SESSION['strErro']))
		{
			echo utf8_encode($_SESSION['strErro']);
			unset($_SESSION['strErro']);
		}	
	}	
	/*
	----------------------------------------------------------------------------------------------------------------------------------------
	Função que consulta os Tribunais Federais
	----------------------------------------------------------------------------------------------------------------------------------------	
	*/
	
	function consultaTribunalFederal($cnpj, $iim1, $uf, $estado)
	{		
		$macro = 'CODE:';
		$macro .= 'URL GOTO=http://processual.trf1.jus.br/consultaProcessual/cpfCnpjParte.php?secao='.$uf. "\r\n";
		$macro .= 'TAG POS=1 TYPE=P FORM=NAME:form1 ATTR=TXT:CPF<SP>ou<SP>CNPJ<SP>da<SP>Parte'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:TEXT FORM=NAME:form1 ATTR=NAME:cpf_cnpj CONTENT='.$cnpj. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:CHECKBOX FORM=NAME:form1 ATTR=NAME:mostrarBaixados CONTENT=YES'. "\r\n";		
		$macro .= 'ONDOWNLOAD FOLDER=c:\Users\Flavio\Desktop FILE=pic.jpg'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=IMG ATTR=ID:image_captcha CONTENT=EVENT:SAVEITEM'. "\r\n";
		$macro .= 'TAB OPEN'. "\r\n";
		$macro .= 'TAB T=2'. "\r\n";
		$macro .= 'URL GOTO=http://beatcaptchas.com/captcha.php'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:TEXT FORM=ACTION:upload.php ATTR=ID:key CONTENT=6ncqawd80jsv5ikz8muwug6wk4zv4bmyomgm8hiy'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:FILE FORM=ACTION:upload.php ATTR=NAME:file CONTENT=c:\Users\Flavio\Desktop\pic.jpg'. "\r\n";
		//$macro .= 'WAIT SECONDS=1'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:SUBMIT FORM=ACTION:upload.php ATTR=NAME:submit'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=BODY ATTR=TXT:* EXTRACT=TXT'. "\r\n";
		$macro .= 'SET !VAR1 {{!EXTRACT}}'. "\r\n";
		$macro .= 'TAB T=1'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:TEXT FORM=NAME:form1 ATTR=NAME:trf1_captcha CONTENT={{!var1}}'. "\r\n";		
		$macro .= 'TAG POS=1 TYPE=INPUT:SUBMIT FORM=NAME:form1 ATTR=NAME:enviar'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:TEXT FORM=NAME:form1 ATTR=NAME:cpf_cnpj CONTENT='.$cnpj. "\r\n";		
		$macro .= 'TAG POS=1 TYPE=DIV ATTR=CLASS:flash<SP>error EXTRACT=TXT'. "\r\n";
		// $macro .= 'TAG POS=1 TYPE=DIV ATTR=CLASS:span-2 EXTRACT=TXT'. "\r\n";
		// $macro .= 'TAG POS=1 TYPE=DIV ATTR=CLASS:listar-processo EXTRACT=TXT'. "\r\n";
		//$macro .= 'SAVEAS TYPE=EXTRACT FOLDER=c:\Users\Flavio\Desktop FILE=Extract_{{!NOW:ddmmyy_hhnnss}}.csv'. "\r\n";
		
		$s = $iim1->iimPlay($macro);		
		$texto = $iim1->iimGetLastExtract();			

		echo $texto;
	}
	
	/*
	----------------------------------------------------------------------------------------------------------------------------------------
	Função que tira pontuação do CNPJ
	----------------------------------------------------------------------------------------------------------------------------------------	
	*/	
	function tiraPontuacaoCNPJ($cnpj)
	{
		$partesCNPJ = explode("/", $cnpj);
		$metade1 = $partesCNPJ[0];
		$metade2 = $partesCNPJ[1];
		
		$partes1 = explode(".", $metade1);
		$partes2 = explode("-", $metade2);
		$cnpj = $partes1[0] . $partes1[1] . $partes1[2] . $partes2[0] . $partes2[1];
		
		return $cnpj;
	}
	
	/*
	----------------------------------------------------------------------------------------------------------------------------------------
	Função que tira pontuação do processo
	----------------------------------------------------------------------------------------------------------------------------------------	
	*/	
	function tiraPontuacaoProcesso($processo)
	{
		$partesProcesso = explode("-", $processo);
		$metade1 = $partesProcesso[0];
		$metade2 = $partesProcesso[1];
		
		$partesMetade2 = explode(".", $metade2);
		$processo = $metade1 . $partesMetade2[0] . $partesMetade2[1] . $partesMetade2[2] . $partesMetade2[3] . $partesMetade2[4];
		
		return $processo;
	}	
	
	/*
	----------------------------------------------------------------------------------------------------------------------------------------
	Funções para quebrar o captcha da 2ª Região
	----------------------------------------------------------------------------------------------------------------------------------------	
	*/		
	function quebrarCaptcha2Regiao($captcha, $pergunta)
	{
		$frases = explode($captcha, $pergunta);
		$sentenca = explode("?", $frases[1]);		
		$pergunta = trim($sentenca[0]);
		
		$palavras = explode(" ", $pergunta);
		$pronome = trim(reset($palavras)); //primeira palavra da pergunta
		$alvo = utf8_decode(trim(end($palavras))); //ultima palavra da pergunta
		
		if($alvo == "números")
		{
			$alvo = "num";
		}
		
		$resposta = respondePergunta($captcha, $pronome, $alvo);
		
		return $resposta;
	}
	
	function respondePergunta($captcha, $pronome, $alvo)
	{
		$arrVogais = array("A", "E", "I", "O", "U");
		$vogais = array();
		$consoantes = array();
		$numeros = array();
		$caracteres = explode(" ", $captcha);
		$resposta = "";
		
		foreach($caracteres as $letra)
		{
			if(is_numeric($letra)) //Se é um número
			{
				array_push($numeros, $letra);			
			}
			else
			{
				if(in_array($letra, $arrVogais)) //Se é vogal
				{
					array_push($vogais, $letra);
				}
				else //Se é consoante
				{
					array_push($consoantes, $letra); 
				}
			}
		}
		
		if($pronome == "Quais")
		{
			switch($alvo)
			{	
				case "vogais":
					foreach($vogais as $digito)
					{
						$resposta .= $digito;
					}
				break;
				case "consoantes":
					foreach($consoantes as $digito)
					{
						$resposta .= $digito;
					}
				break;		
				case "num":
					foreach($numeros as $digito)
					{
						$resposta .= $digito;
					}
				break;						
			}			
		}
		if($pronome == "Quantos")
		{
			switch($alvo)
			{	
				case "vogais":
					$resposta = count($vogais);
				break;
				case "consoantes":
					$resposta = count($consoantes);
				break;		
				case "num":						
					$resposta = count($numeros);
				break;						
			}
		}
		
		return $resposta;
	}
	
	/*
	----------------------------------------------------------------------------------------------------------------------------------------
	Função para testar se existe um elemento capturado pelo Selenium
	----------------------------------------------------------------------------------------------------------------------------------------	
	*/		
	
	function existeElemento($elemento)
	{
		$arrElemento = explode(" ", $elemento);
		
		if($arrElemento[0] != "OR:")
		{
			return true;
		}
		else
		{
			return false;
		}
	}