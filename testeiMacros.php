<?php
	$iim1 = new COM("imacros");
	$s = $iim1->iimInit("-runner");	
	
	$str = 'CODE:';
	$str .= 'VERSION BUILD=9002379' . "\r\n";
	$str .= 'TAB CLOSEALLOTHERS' . "\r\n";
	$str .= 'URL GOTO=http://www.urbame.com.br/' . "\r\n";
	$str .= 'TAG POS=1 TYPE=H3 ATTR=* EXTRACT=TXT' . "\r\n";
	
	//$s = $iim1->iimSet("-var_keyword", $_GET["keyword"]);	
	$s = $iim1->iimPlay($str);
	
	echo "iimplay=";
	echo $s;
	echo "extract=";  
	echo $iim1->iimGetLastExtract;
	
	$s = $iim1->iimExit();
?>