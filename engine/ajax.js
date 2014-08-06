//Funções para utilização de AJAX
// Esse script é uma biblioteca que precisa ser adicionada para a utilização das funções de alteração de texto dinâmico.

var tabela = "";

function criaRequisicao() 
{ 
	try {
		request = new XMLHttpRequest(); //Tenta instanciar o objeto XMLHttpRequest (Firefox, Netscape, Opera e similares)
	} catch (trymicrosoft) { //Se não existe o objeto XMLHttpRequest, procura ver se tem um objeto Microsoft (IE antes do 6.0)
		try {
			request = new ActiveXObject("Msxml2.XMLHTTP"); 
		} catch (othermicrosoft) { //Se não existe o objeto do IE antes do 6.0, procura ver se tem um outro objeto Microsoft (IE a partir do 6.0)
			try {
				request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (failed) {
				request = null;
				}
			}
		}
	 if (request == null) //Se não funcionou nenhum desses, cara já está na hora de trocar de browser!
		alert("Seu browser não tem suporte a AJAX!");
}

function pegaDados() 
{ 
	var uf = "";
	var estado = "";
	var regiao = 0;
	tabela = "";
	
	switch(numero)
	{
		case 1:
			uf = "AC";
			estado = "Acre";
		break;
		case 2:
			uf = "AP";
			estado = "Amap&aacute;";
		break;
		case 3:
			uf = "AM";
			estado = "Amazonas";
		break;
		case 4:
			uf = "BA";
			estado = "Bahia";
		break;
		case 5:
			uf = "DF";
			estado = "Distrito Federal";
		break;		
		case 6:
			uf = "GO";
			estado = "Goi&aacute;s";
		break;		
		case 7:
			uf = "MA";
			estado = "Maranh&atilde;o";
		break;			
		case 8:
			uf = "MT";
			estado = "Mato Grosso";
		break;	
		case 9:
			uf = "MG";
			estado = "Minas Gerais";
		break;	
		case 10:
			uf = "PA";
			estado = "Par&aacute;";
		break;	
		case 11:
			uf = "PI";
			estado = "Piau&iacute;";
		break;			
		case 12:
			uf = "RO";
			estado = "Rond&ocirc;nia";
		break;	
		case 13:
			uf = "RR";
			estado = "Roraima";
		break;	
		case 14:
			uf = "TO";
			estado = "Tocantins";
		break;		
		case 15:
			uf = "ES";
			estado = "Esp&iacute;rito Santo";
		break;		
		case 16:
			uf = "RJ";
			estado = "Rio de Janeiro";
		break;
		case 17:
			uf = "MS";
			estado = "Mato Grosso do Sul";
		break;
		case 18:
			uf = "SP";
			estado = "S&atilde;o Paulo";
		break;				
		case 19:
			uf = "PR";
			estado = "Paran&aacute;";
		break;	
		case 20:
			uf = "RS";
			estado = "Rio Grande do Sul";
		break;	
		case 21:
			uf = "SC";
			estado = "Santa Catarina";
		break;			
		case 22:
			uf = "AL";
			estado = "Alagoas";
		break;
		case 23:
			uf = "CE";
			estado = "Cear&aacute;";
		break;
		case 24:
			uf = "PB";
			estado = "Para&iacute;ba";
		break;
		case 25:
			uf = "PE";
			estado = "Pernambuco";
		break;
		case 26	:
			uf = "RN";
			estado = "Rio Grande do Norte";
		break;
		case 27:
			uf = "SE";
			estado = "Sergipe";
		break;		
		case 28:
			uf = "AC";
			estado = "Acre";
		break;	
		case 29:
			uf = "AL";
			estado = "Alagoas";
		break;	
		case 30:
			uf = "AP";
			estado = "Amap&aacute;";
		break;			
		case 31:
			uf = "BA";
			estado = "Bahia";
		break;	
		case 32:
			uf = "CE";
			estado = "Cear&aacute;";
		break;	
		case 33:
			uf = "DF";
			estado = "Distrito Federal";
		break;	
		case 34:
			uf = "ES";
			estado = "Esp&iacute;rito Santo";
		break;		
		case 35:
			uf = "MA";
			estado = "Maranh&atilde;o";
		break;	
		case 36:
			uf = "MS";
			estado = "Mato Grosso do Sul";
		break;		
		case 37:
			uf = "MT";
			estado = "Mato Grosso";
		break;		
		case 38:
			uf = "PA";
			estado = "Par&aacute;";
		break;	
		case 39:
			uf = "PB";
			estado = "Para&iacute;ba";
		break;	
		case 40:
			uf = "PE";
			estado = "Pernambuco";
		break;		
		case 41:
			uf = "PI";
			estado = "Piau&iacute;";
		break;	
		case 42:
			uf = "PR";
			estado = "Paran&aacute;";
		break;	
		case 43:
			uf = "RJ";
			estado = "Rio de Janeiro";
		break;	
		case 44:
			uf = "RN";
			estado = "Rio Grande do Norte";
		break;	
		case 45:
			uf = "RO";
			estado = "Rond&ocirc;nia";
		break;	
		case 46:
			uf = "RR";
			estado = "Roraima";
		break;	
		case 47:
			uf = "RS";
			estado = "Rio Grande do Sul";
		break;	
		case 48:
			uf = "SC";
			estado = "Santa Catarina";
		break;
		case 49:
			uf = "SE";
			estado = "Sergipe";
		break;
		case 50:
			uf = "TO";
			estado = "Tocantins";
		break;		
		case 51:
			uf = "SP";
			estado = "S&atilde;o Paulo";
		break;
		case 52:
			regiao = 1;
		break;
		case 53:
			regiao = 2;
		break;
		case 54:
			regiao = 3;
		break;		
		case 55:
			regiao = 4;
		break;		
		case 56:
			regiao = 5;
		break;		
		case 57:
			regiao = 6;
		break;		
		case 58:
			regiao = 7;
		break;		
		case 59:
			regiao = 8;
		break;
		case 60:
			regiao = 9;
		break;	
		case 61:
			regiao = 10;
		break;	
		case 62:
			regiao = 11;
		break;	
		case 63:
			regiao = 12;
		break;	
		case 64:
			regiao = 13;
		break;	
		case 65:
			regiao = 14;
		break;	
		case 66:
			regiao = 15;
		break;
		case 67:
			regiao = 16;
		break;
		case 68:
			regiao = 17;
		break;
		case 69:
			regiao = 18;
		break;
		case 70:
			regiao = 19;
		break;
		case 71:
			regiao = 20;
		break;
		case 72:
			regiao = 21;
		break;
		case 73:
			regiao = 22;
		break;
		case 74:
			regiao = 23;
		break;
		case 75:
			regiao = 24;
		break;		
	}
	
	criaRequisicao();	//Instancia o objeto que vai estabelecer a requisição assíncrona com o servidor.
	
	cnpj = document.getElementById("cnpj").value;
	
	if(numero <= 27)
	{
		document.getElementById("info").innerHTML = "Consultando Tribunal Federal do " + estado;	
		tabela += "<tr><td class='tdEstado'>Tribunal Federal do " + estado + "</td>";
	}
	if(numero > 27 && numero < 52)
	{
		document.getElementById("info").innerHTML = "Consultando Tribunal de Justi&ccedil;a do " + estado;	
		tabela += "<tr><td class='tdEstado'>Tribunal de Justi&ccedil;a do " + estado + "</td>";
	}
	if(numero >= 52)
	{
		document.getElementById("info").innerHTML = "Consultando "+ regiao +"&ordm; Tribunal Regional do Trabalho";	
		tabela += "<tr><td class='tdEstado'>"+ regiao +"&ordm; Tribunal Regional do Trabalho</td>";
	}	
		
	var url = "chamadaSelenium.php?cnpj="+cnpj+"&uf="+uf+"&numero="+numero+"&regiao="+regiao; //Escreva aqui o script que vai rodar no servidor.
	
	request.open("GET", url, true); //Esse método abre a requisição com o servidor. Ou seja, faz o seu script php começar a rodar no servidor sem que o usuário veja uma página em branco!
	request.onreadystatechange = atualizaPagina; //Uma das linhas mais importantes! Chama a função atualizaPagina somente quando a requisição termina de ser processada.
	request.send(null); //Envia nulo para o servidor para indicar que a requisição terminou.		
}

function pegaDadosNadaConsta() 
{ 
	var uf = "";
	var estado = "";
	tabela = "";	
	
	switch(numero)
	{
		case 1:
			uf = "AC";
			estado = "Acre";
		break;
		case 2:
			uf = "AP";
			estado = "Amap&aacute;";
		break;
		case 3:
			uf = "AM";
			estado = "Amazonas";
		break;
		case 4:
			uf = "BA";
			estado = "Bahia";
		break;
		case 5:
			uf = "DF";
			estado = "Distrito Federal";
		break;		
		case 6:
			uf = "GO";
			estado = "Goi&aacute;s";
		break;		
		case 7:
			uf = "MA";
			estado = "Maranh&atilde;o";
		break;			
		case 8:
			uf = "MT";
			estado = "Mato Grosso";
		break;	
		case 9:
			uf = "MG";
			estado = "Minas Gerais";
		break;
		case 10:
			uf = "PA";
			estado = "Par&aacute;";
		break;		
		case 11:
			uf = "PI";
			estado = "Piau&iacute;";
		break;	
		case 12:
			uf = "RO";
			estado = "Rond&ocirc;nia";
		break;	
		case 13:
			uf = "RR";
			estado = "Roraima";
		break;	
		case 14:
			uf = "TO";
			estado = "Tocantins";
		break;	
		case 15:
			uf = "ES";
			estado = "Esp&iacute;rito Santo";
		break;	
		case 16:
			uf = "RJ";
			estado = "Rio de Janeiro";
		break;		
		case 17:
			uf = "MS";
			estado = "Mato Grosso do Sul";
		break;
		case 18:
			uf = "SP";
			estado = "S&atilde;o Paulo";
		break;		
		case 19:
			uf = "PR";
			estado = "Paran&aacute;";
		break;
		case 20:
			uf = "RS";
			estado = "Rio Grande do Sul";
		break;
		case 21:
			uf = "SC";
			estado = "Santa Catarina";
		break;
		case 22:
			uf = "AL";
			estado = "Alagoas";
		break;
		case 23:
			uf = "CE";
			estado = "Cear&aacute;";
		break;
		case 24:
			uf = "PB";
			estado = "Para&iacute;ba";
		break;
		case 25:
			uf = "PE";
			estado = "Pernambuco";
		break;		
		case 26:
			uf = "RN";
			estado = "Rio Grande do Norte";
		break;	
		case 27:
			uf = "SE";
			estado = "Sergipe";
		break;			
	}

	criaRequisicao();	//Instancia o objeto que vai estabelecer a requisição assíncrona com o servidor.	
	nome = document.getElementById("nome").value;
	cnpj = document.getElementById("cnpj").value;
	
	document.getElementById("info").innerHTML = "Consultando Tribunal Federal do " + estado;	
	tabela += "<tr><td class='tdEstado'>Tribunal Federal do " + estado + "</td>";
	
	var url = "chamadaNadaConsta.php?nome="+nome+"&cnpj="+cnpj+"&uf="+uf+"&numero="+numero; //Escreva aqui o script que vai rodar no servidor.
	
	request.open("GET", url, true); //Esse método abre a requisição com o servidor. Ou seja, faz o seu script php começar a rodar no servidor sem que o usuário veja uma página em branco!
	request.onreadystatechange = atualizaPaginaNadaConsta; //Uma das linhas mais importantes! Chama a função atualizaPagina somente quando a requisição termina de ser processada.
	request.send(null); //Envia nulo para o servidor para indicar que a requisição terminou.		
}

function atualizaPagina() 
{ 
	var cor;	

	if (request.readyState == 4) 
	{ 
		numero++;
		var texto = request.responseText;			
		
		if(texto != "")
		{
			if(numero%2 == 0)
			{
				cor = "par";
			}
			else
			{
				cor = "impar";
			}
		
			tabela += "<td class='tdTabela "+ cor +"'>" + texto + "</td></tr>";
			
			if(numero <= 28)
			{
				document.getElementById("tabelaFederais").innerHTML += tabela;		
			}
			if(numero > 28 && numero < 53)
			{
				document.getElementById("tabelaJustica").innerHTML += tabela;
			}
			if(numero >= 53)
			{
				document.getElementById("tabelaTrabalho").innerHTML += tabela;
			}
			
			if(numero <= 75)
			{
				pegaDados();
			}
			else
			{
				document.getElementById("info").innerHTML = "Consulta conclu&iacute;da!";
				document.getElementById("imagem").innerHTML = "<img id='carregando' src='./estilo/images/success_512.png' width='75' height='75' />";		
			}
		}				
	}
}

function atualizaPaginaNadaConsta() 
{ 
	var cor;	

	if (request.readyState == 4) 
	{ 
		numero++;
		var texto = request.responseText;			
		
		if(texto != "")
		{
			if(numero%2 == 0)
			{
				cor = "par";
			}
			else
			{
				cor = "impar";
			}
		
			tabela += "<td class='tdTabela "+ cor +"'>" + texto + "</td></tr>";
			document.getElementById("tabelaFederais").innerHTML += tabela;		
			
			if(numero <= 27)
			{
				pegaDadosNadaConsta();
			}
			else
			{
				document.getElementById("info").innerHTML = "Consulta conclu&iacute;da!";
				document.getElementById("imagem").innerHTML = "<img id='carregando' src='./estilo/images/success_512.png' width='75' height='75' />";		
			}
		}				
	}
}