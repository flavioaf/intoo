<?php
	require("./template/layoutUp.php");
	
	$cnpj = $_POST['cnpj'];
	$cnpj = tiraPontuacao($cnpj);
?>
	<div id="site" class="resultados">
		<div id="topo">
			<div id="logo">
				<img src="./estilo/images/logo-intoo.png" width="150" />
			</div>
		</div>
		<div id="meio">
			<div id="info" class="info">Consultando Tribunais Federais...</div>			
			<div id="imagem"><img id="carregando" src="./estilo/images/ajax-loader-2.gif" width="75" /></div>
			<input type="hidden" id="cnpj" name="cnpj" value="<?php echo $cnpj; ?>" />			
			<div id="resultado">
				<h2>Tribunais Federais</h2>
				<table id="tabelaFederais">
					<tr>
						<th>Tribunal</th>
						<th>Resultado</th>
					</tr>
				</table>
				<br/>
				<h2>Tribunais de Justi&ccedil;a</h2>
				<table id="tabelaJustica">
					<tr>
						<th>Tribunal</th>
						<th>Resultado</th>
					</tr>
				</table>
				<h2>Tribunais Regionais do Trabalho</h2>
				<table id="tabelaTrabalho">
					<tr>
						<th>Tribunal</th>
						<th>Resultado</th>
					</tr>
				</table>					
			</div>
			
			<script type="text/javascript">
			<!--
				var numero = 28;			
				pegaDados();
			//-->
			</script>
		</div>
	</div>
<?php	
	require("./template/layoutDown.php");
?>