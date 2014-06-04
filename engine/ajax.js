//Funções para utilização de AJAX

// Esse script é uma biblioteca que precisa ser adicionada para a utilização das funções de alteração de texto dinâmico.
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
	
	criaRequisicao();	//Instancia o objeto que vai estabelecer a requisição assíncrona com o servidor.
	
	cnpj = document.getElementById("cnpj").value;
	document.getElementById("info").innerHTML = "Consultando Tribunal Federal do " + estado;
	document.getElementById("tabelaResultados").innerHTML += "<tr><td>Tribunal Federal do " + estado + "</td>";
	
	var url = "chamadaSelenium.php?cnpj="+cnpj+"&uf="+uf; //Escreva aqui o script que vai rodar no servidor.
	
	request.open("GET", url, true); //Esse método abre a requisição com o servidor. Ou seja, faz o seu script php começar a rodar no servidor sem que o usuário veja uma página em branco!
	request.onreadystatechange = atualizaPagina; //Uma das linhas mais importantes! Chama a função atualizaPagina somente quando a requisição termina de ser processada.
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