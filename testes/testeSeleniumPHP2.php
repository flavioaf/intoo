<?php
	session_start();

	require_once 'Testing/Selenium.php';
	require_once 'PHPUnit/Framework/Test.php';
	require_once 'PHPUnit/Framework/Assert.php';
	require_once 'PHPUnit/Framework/SelfDescribing.php';
	require_once 'PHPUnit/Framework/TestCase.php';
	require_once 'phpwebdriver/WebDriver.php';
	
	function get_file($file, $local_path, $newfilename) 
	{ 
		$err_msg = ''; 
		echo "<br>Attempting message download for $file<br>"; 
		$out = fopen($local_path.$newfilename,"wb");
		
		if($out == FALSE)
		{ 
		  print "File not opened<br>"; 
		  exit; 
		} 

		$ch = curl_init(); 

		curl_setopt($ch, CURLOPT_FILE, $out); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_URL, $file); 

		curl_exec($ch); 
		echo "<br>Error is : ".curl_error ( $ch); 

		curl_close($ch); 
		//fclose($handle); 

	}//end function 

	class ConsultaTribunal extends PHPUnit_Framework_TestCase
	{
	  public function setUp()
	  {		
		$selenium1 = new Testing_Selenium("*chrome", "http://processual.trf1.jus.br/consultaProcessual/cpfCnpjParte.php?secao=AC");
		$selenium1->start();
		$selenium1->open("/consultaProcessual/cpfCnpjParte.php?secao=AC");
		$selenium1->type("id=cpf_cnpj", "15987391000157");
		$selenium1->click("name=mostrarBaixados");
			
		$url ="http://processual.trf1.jus.br/consultaProcessual/cpfCnpjParte.php?secao=AC";
		$html = file_get_contents($url);

		$doc = new DOMDocument();
		@$doc->loadHTML($html);

		$img = $doc->getElementById('image_captcha');
		$srcCaptcha = $img->getAttribute('src');
		echo "src : ". $srcCaptcha;
			
		get_file("http://processual.trf1.jus.br".$srcCaptcha,"C:\\xampp\\htdocs\\intoo\\trunk\\","captcha.jpg");
				
		$selenium2 = new Testing_Selenium("*chrome", "http://beatcaptchas.com/captcha.php");		
		$selenium2->start();
		$selenium2->open("http://beatcaptchas.com/captcha.php");
		$selenium2->type("id=key","6ncqawd80jsv5ikz8muwug6wk4zv4bmyomgm8hiy");
		$selenium2->attachFile("id=file","http://processual.trf1.jus.br/consultaProcessual/captcha/image.php?id=020afb4ca10fbd006ccb78d33082b598");
		$selenium2->click("name=submit");
		//$selenium->waitForPageToLoad("30000");
		$texto = $selenium1->getText("css=div.flash.error");
		
		echo $texto;
	  }
	}
?>