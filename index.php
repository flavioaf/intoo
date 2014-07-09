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
	
	<div id="site" class="index">
		<div id="topo">
			<div id="logo">
				<img src="./estilo/images/logo-intoo.png" width="150" />
			</div>
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