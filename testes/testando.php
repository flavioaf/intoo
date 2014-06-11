<?php

require_once 'Testing/Selenium.php';

class Example extends PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $this = new Testing_Selenium("*chrome", "https://www.google.com.br/")
    $this->type("id=cpf_cnpj", "15987391000157");
    $this->click("name=mostrarBaixados");
    $this->click("id=ouvir_codigo");
    $this->click("id=atualizar_captcha");
    // $this->();
  }
}
?>