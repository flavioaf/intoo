<?php
	require("./template/layoutUp.php");
?>
	<script type="text/javascript">
	<!--
		$(document).ready(function(){
			$("#cnpj").mask("99.999.999/9999-99");
			$("#processo").mask("9999999-99.9999.9.99.9999");
		});
		
		function mudaAction()
		{
			var consultaProcessual = document.getElementById("consultaProcessual").checked;
			var emitirNadaConsta = document.getElementById("emitirNadaConsta").checked;
			
			if(consultaProcessual)
			{
				document.getElementById("frmConsulta").action = "consultaTribunais.php";
				$("#num_processo").css("display", "block");
			}
			if(emitirNadaConsta)
			{
				document.getElementById("frmConsulta").action = "emiteNadaConsta.php";
				$("#num_processo").css("display", "none");
			}			
		}
		
		function valida()
		{
			document.getElementById("blnChecaJS").value = 1;
			
			strErro = "Os Seguintes erros foram encontrados:<br/>";
			intErro = 1;
			
			var consultaProcessual = document.getElementById("consultaProcessual").checked;
			var emitirNadaConsta = document.getElementById("emitirNadaConsta").checked;
			var cnpj = document.getElementById("cnpj").value;
			
			//Se o cnpj estiver em branco
			if(cnpj == "")
			{
				if(intErro == 1)
				{
					document.getElementById("cnpj").focus();
				}
				strErro += intErro + ". O campo 'CNPJ' n&atilde;o pode estar em branco.<br/>";
				intErro++;
			}
			else
			{
				if (!(/^([0-9]{2}[\.]?[0-9]{3}[\.]?[0-9]{3}[\/]?[0-9]{4}[-]?[0-9]{2})|([0-9]{3}[\.]?[0-9]{3}[\.]?[0-9]{3}[-]?[0-9]{2})$/.test(cnpj)))
				{
					if(intErro == 1)
					{
						document.getElementById("cnpj").focus();
					}
					strErro += intErro + ". O campo 'CNPJ' possui formato inv&aacute;lido. Voc&ecirc; deve digitar no formato XX.XXX.XXX/XXX-XX<br/>";
					intErro++;
				}
			}	

			if(emitirNadaConsta)
			{
				var nome = document.getElementById("nome").value;
			
				if(nome == "")
				{
					if(intErro == 1)
					{
						document.getElementById("nome").focus();
					}
					strErro += intErro + ". O campo 'Nome' n&atilde;o pode estar em branco.<br/>";
					intErro++;
				}
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
	//-->
	</script>
	
	<div id="site" class="index">
		<div id="topo">
			<div id="logo">
				<img src="./estilo/images/logo-intoo.png" width="150" />
			</div>
		</div>
		<div id="meio">
			<div id="msg"></div>
			<fieldset>
				<legend>Consulta</legend>
				<form id="frmConsulta" action="consultaTribunais.php" method="post">
					<label for="consultaProcessual">Consulta Processual: </label><input type="radio" id="consultaProcessual" name="consulta" checked="checked" onchange="javascript:mudaAction();" /> <label for="emitirNadaConsta">Emitir Nada Consta: </label><input type="radio" id="emitirNadaConsta" name="consulta" onchange="javascript:mudaAction();" /><br/>
					<div id="outrosCampos">
					</div>
					<label>CNPJ: </label><input type="text" id="cnpj" name="cnpj" /> <span class="asterisco">*</span><br/>
					<label>Nome: </label><input type="text" id="nome" name="nome" /><br/>
					<div id="num_processo"><label>N&uacute;mero do Processo: </label><input type="text" id="processo" name="processo" /><br/></div>
					<input type="hidden" id="blnChecaJS" name="blnChecaJS" value="0" />
					<input type="submit" id="consultar" name="consultar" value="Consultar" onclick="return valida();" />
				</form>
			</fieldset>
		</div>
	</div>

<?php
	require("./template/layoutDown.php");
?>