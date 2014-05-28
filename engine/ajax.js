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
			estado = "Amapá";
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
			estado = "Goiás";
		break;		
		case 7:
			uf = "MA";
			estado = "Maranhão";
		break;				
	}
	
	criaRequisicao();	//Instancia o objeto que vai estabelecer a requisição assíncrona com o servidor.
	
	cnpj = document.getElementById("cnpj").value;
	document.getElementById("info").innerHTML = "Consultando Tribunal Federal do " + estado;
	document.getElementById("tabelaResultados").innerHTML += "<tr><td>Tribunal Federal do " + estado + "</td>";
	
	var url = "executarConsulta.php?cnpj="+cnpj+"&uf="+uf+"&estado="+estado; //Escreva aqui o script que vai rodar no servidor.
	
	request.open("GET", url, true); //Esse método abre a requisição com o servidor. Ou seja, faz o seu script php começar a rodar no servidor sem que o usuário veja uma página em branco!
	request.onreadystatechange = atualizaPagina; //Uma das linhas mais importantes! Chama a função atualizaPagina somente quando a requisição termina de ser processada.
	request.send(null); //Envia nulo para o servidor para indicar que a requisição terminou.		
}

function atualizaPagina() 
{ 
	if (request.readyState == 4) 
	{ 
		numero++;
		var texto = request.responseText;			
		
		if(texto != "")
		{
			document.getElementById("tabelaResultados").innerHTML += "<td class='tdTabela'>" + texto + "</td></tr>";			
			
			if(numero <= 7)
			{
				pegaDados();
			}
			else
			{
				document.getElementById("info").innerHTML = "Consulta concluída!";
				document.getElementById("imagem").innerHTML = "<img id='carregando' src='./estilo/images/success_512.png' width='300' height='300' />";		
			}
		}				
	}
}