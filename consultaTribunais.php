<?php
	require("./template/layoutUp.php");
	
	$cnpj = $_POST['cnpj'];
	$cnpj = tiraPontuacao($cnpj);
?>
	<div id="site">
		<div id="topo"></div>
		<div id="meio">
			<div id="info" class="info">Consultando Tribunais Federais...</div>
			<div id="imagem"><img id="carregando" src="./estilo/images/ajax-loader-2.gif" /></div>
			<input type="hidden" id="cnpj" name="cnpj" value="<?php echo $cnpj; ?>" />
			<div id="resultado"><table id="tabelaResultados"><tr><th>Tribunal</th><th>Resultado</th></tr></table></div>
			
			<script type="text/javascript">
			<!--
				var numero = 1;			
				pegaDados();
			//-->
			</script>
		</div>
	</div>
<?php	
	require("./template/layoutDown.php");
?>