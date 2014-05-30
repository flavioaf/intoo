<?php
	require_once 'Testing/Selenium.php';
	require_once 'PHPUnit/Framework/Test.php';
	require_once 'PHPUnit/Framework/Assert.php';
	require_once 'PHPUnit/Framework/SelfDescribing.php';
	require_once 'PHPUnit/Framework/TestCase.php';
	require_once 'phpwebdriver/WebDriver.php';

	class ConsultaTribunal extends PHPUnit_Framework_TestCase
	{
	  public function setUp()
	  {		
		$selenium1 = new Testing_Selenium("*chrome", "http://processual.trf1.jus.br/consultaProcessual/cpfCnpjParte.php?secao=AC");
		$selenium1->start();
		$selenium1->open("/consultaProcessual/cpfCnpjParte.php?secao=AC");
		$srcCaptcha = $selenium1->getAttribute("image_captcha@src");
		$selenium1->type("id=cpf_cnpj", "15987391000157");
		$selenium1->click("name=mostrarBaixados");
		$selenium1->windowMaximize();
		$selenium1->captureEntirePageScreenshot("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png",NULL);
		
		$printscreen = imagecreatefrompng("C:\\xampp\\htdocs\\intoo\\trunk\\screenshots\\print.png");
		$captcha = imagecreate(202, 52);
		
		imagecopy($captcha, $printscreen, 0, 0, 396, 313, 195, 48);
		imagepng($captcha, "C:\\xampp\\htdocs\\intoo\\trunk\\captchas\\captcha.png");
		
		$urlImagem = "http://processual.trf1.jus.br".$srcCaptcha;			
				
		$selenium2 = new Testing_Selenium("*chrome", "http://beatcaptchas.com/captcha.php");		
		$selenium2->start();
		$selenium2->open("http://beatcaptchas.com/captcha.php");
		$selenium2->type("id=key","6ncqawd80jsv5ikz8muwug6wk4zv4bmyomgm8hiy");
		//$selenium2->attachFile("name=file","C:\Users\Flavio\Desktop\captcha.png");
		//$selenium2->click("name=file");
		//$selenium2->focus('name=file');
		$selenium2->type("name=file","C:\Users\Flavio\Desktop\captcha.png");
		// $selenium2->click("name=submit");
		// $textoCaptcha = $selenium2->getText("css=td");
		
		// echo "Captcha: " . $textoCaptcha;

		// $selenium1->type("trf1_captcha", $textoCaptcha);
		// $selenium1->click("id=enviar");
		
		// $texto = $selenium1->getText("css=div.flash.error");
		
		// echo $texto;
		echo "fim";
	  }
	}
?>