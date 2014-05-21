
function valida()
{
	document.getElementById("blnChecaJS").value = 1;
	
	strErro = "Os Seguintes erros foram encontrados:<br/>";
	intErro = 1;
	
	var nome   	   = document.getElementById("nome").value;
	var email	   = document.getElementById("email").value;
	var qtdPessoas = document.getElementById("qtdPessoas").value;
	
	//Se o nome estiver em branco
	if(nome == "")
	{
		if(intErro == 1)
		{
			document.getElementById("nome").focus();
		}
		strErro += intErro + ". O campo 'Nome' n&atilde;o pode estar em branco.<br/>";
		intErro++;
	}
	//Se o e-mail estiver em branco
	if(email == "")
	{
		if(intErro == 1)
		{
			document.getElementById("email").focus();
		}
		intErro++;
		strErro += intErro + ". O campo 'E-mail' n&atilde;o pode estar em branco.<br/>";
	}
	else
	{
		if (!(/^([a-zA-Z])([a-zA-Z0-9_\.-]+)(@)([a-zA-Z0-9_\-]+)(\.[a-zA-Z0-9.]+)$/.test(email)))
		{
			if(intErro == 1)
			{
				document.getElementById("email").focus();
			}
			intErro++;
			strErro += intErro + ". O campo 'E-mail' possui formato inv&aacute;lido.<br/>";
		}
	}	
	//Se o curso estiver em branco
	if(qtdPessoas == "")
	{
		if(intErro == 1)
		{
			document.getElementById("qtdPessoas").focus();
		}
		strErro += intErro + ". O campo 'Quantidade de Pessoas' n&atilde;o pode estar em branco.<br/>";
		intErro++;
	}
	
	//Caso haja pelo menos um erro ele retorna
	if(intErro > 1)		
	{
		document.getElementById("msg").innerHTML = "<div class='erro'>" + strErro + "</div>";
		return false;
	}
	else
	{
		return true;
	}	
}