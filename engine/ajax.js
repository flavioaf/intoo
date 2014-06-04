//Fun��es para utiliza��o de AJAX

// Esse script � uma biblioteca que precisa ser adicionada para a utiliza��o das fun��es de altera��o de texto din�mico.
function criaRequisicao() 
{ 
	try {
		request = new XMLHttpRequest(); //Tenta instanciar o objeto XMLHttpRequest (Firefox, Netscape, Opera e similares)
	} catch (trymicrosoft) { //Se n�o existe o objeto XMLHttpRequest, procura ver se tem um objeto Microsoft (IE antes do 6.0)
		try {
			request = new ActiveXObject("Msxml2.XMLHTTP"); 
		} catch (othermicrosoft) { //Se n�o existe o objeto do IE antes do 6.0, procura ver se tem um outro objeto Microsoft (IE a partir do 6.0)
			try {
				request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (failed) {
				request = null;
				}
			}
		}
	 if (request == null) //Se n�o funcionou nenhum desses, cara j� est� na hora de trocar de browser!
		alert("Seu browser n�o tem suporte a AJAX!");
}

function pegaDados() 
{ 
	var uf;
	var estado;						

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
	}
	
	criaRequisicao();	//Instancia o objeto que vai estabelecer a requisi��o ass�ncrona com o servidor.
	
	cnpj = document.getElementById("cnpj").value;
	document.getElementById("info").innerHTML = "Consultando Tribunal Federal do " + estado;
	document.getElementById("tabelaResultados").innerHTML += "<tr><td>Tribunal Federal do " + estado + "</td>";
	
	var url = "chamadaSelenium.php?cnpj="+cnpj+"&uf="+uf; //Escreva aqui o script que vai rodar no servidor.
	
	request.open("GET", url, true); //Esse m�todo abre a requisi��o com o servidor. Ou seja, faz o seu script php come�ar a rodar no servidor sem que o usu�rio veja uma p�gina em branco!
	request.onreadystatechange = atualizaPagina; //Uma das linhas mais importantes! Chama a fun��o atualizaPagina somente quando a requisi��o termina de ser processada.
	request.send(null); //Envia nulo para o servidor para indicar que a requisi��o terminou.		
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
		
			document.getElementById("tabelaResultados").innerHTML += "<td class='tdTabela "+ cor +"'>" + texto + "</td></tr>";			
			
			if(numero <= 14)
			{
				pegaDados();
			}
			else
			{
				document.getElementById("info").innerHTML = "Consulta conclu&iacute;da!";
				document.getElementById("imagem").innerHTML = "<img id='carregando' src='./estilo/images/success_512.png' width='300' height='300' />";		
			}
		}				
	}
}