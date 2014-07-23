<?php
	require("./template/layoutUp.php");
?>
	<script type="text/javascript">
	<!--
		$(document).ready(function(){
			$("#cnpj").mask("99.999.999/9999-99");
		});
		
		function mudaAction()
		{
			var consultaProcessual = document.getElementById("consultaProcessual").checked;
			var emitirNadaConsta = document.getElementById("emitirNadaConsta").checked;
			
			if(consultaProcessual)
			{
				document.getElementById("frmConsulta").action = "consultaTribunais.php";
			}
			if(emitirNadaConsta)
			{
				document.getElementById("frmConsulta").action = "emiteNadaConsta.php";
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
			<fieldset>
				<legend>Consulta</legend>
				<form id="frmConsulta" action="consultaTribunais.php" method="post">
					<label for="consultaProcessual">Consulta Processual: </label><input type="radio" id="consultaProcessual" name="consulta" checked="checked" onchange="javascript:mudaAction();" /> <label for="emitirNadaConsta">Emitir Nada Consta: </label><input type="radio" id="emitirNadaConsta" name="consulta" onchange="javascript:mudaAction();" /><br/>
					<label>CNPJ: </label><input type="text" id="cnpj" name="cnpj" />
					<input type="submit" id="consultar" name="consultar" value="Consultar" />
				</form>
			</fieldset>
		</div>
	</div>

<?php
	require("./template/layoutDown.php");
?>