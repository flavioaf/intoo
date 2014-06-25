<?php

require_once 'Testing/Selenium.php';

class Example extends PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $this = new Testing_Selenium("*chrome", "http://www.tjac.jus.br/")
    // $this->getTex("//table[3]/tbody/tr/td[2]/table/tbody/tr/td/span");
    // $this->getTex("css=span > span");
    // $this->getTex("//table[3]/tbody/tr[3]/td[2]/table/tbody/tr/td");
    // $this->getTex("xpath=(//span[@id=''])[3]");
    // $this->getTex("xpath=(//span[@id=''])[4]");
  }
}
?>