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
		//echo "<script type='text/javascript'>document.getElementById('info').innerHTML = 'Consultando Tribunal Federal do '".$estado.";</script>";
	
		$macro = 'CODE:';
		$macro .= 'URL GOTO=http://processual.trf1.jus.br/consultaProcessual/cpfCnpjParte.php?secao='.$uf. "\r\n";
		$macro .= 'TAG POS=1 TYPE=P FORM=NAME:form1 ATTR=TXT:CPF<SP>ou<SP>CNPJ<SP>da<SP>Parte'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:TEXT FORM=NAME:form1 ATTR=NAME:cpf_cnpj CONTENT='.$cnpj. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:CHECKBOX FORM=NAME:form1 ATTR=NAME:mostrarBaixados CONTENT=YES'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:TEXT FORM=NAME:form1 ATTR=NAME:trf1_captcha CONTENT=9sf6'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:SUBMIT FORM=NAME:form1 ATTR=NAME:enviar'. "\r\n";
		$macro .= 'TAG POS=1 TYPE=INPUT:TEXT FORM=NAME:form1 ATTR=NAME:cpf_cnpj CONTENT='.$cnpj. "\r\n";
		$macro .= 'TAG POS=1 TYPE=DIV ATTR=CLASS:flash<SP>error EXTRACT=TXT'. "\r\n";
		
		$s = $iim1->iimPlay($macro);		
		$texto = $iim1->iimGetLastExtract;	

		echo $texto;
	}