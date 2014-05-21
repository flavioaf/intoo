<?php
	require("./template/layoutUp.php");
?>
	<script type="text/javascript">
	<!--
		$(document).ready(function(){
			$("#cnpj").mask("99.999.999/9999-99");
		});
	//-->
	</script>
	
	<div id="site">
		<div id="topo">
			<h2>Intoo - Consulta de Processos</h2>
		</div>
		<div id="meio">		
			<fieldset>
				<legend>Consulta Processual</legend>
				<form id="frmConsulta" action="consultaTribunais.php" method="post">
					<label>CNPJ: </label><input type="text" id="cnpj" name="cnpj" />
					<input type="submit" id="consultar" name="consultar" value="Consultar" />
				</form>
			</fieldset>
		</div>
	</div>

<?php
	require("./template/layoutDown.php");
?>