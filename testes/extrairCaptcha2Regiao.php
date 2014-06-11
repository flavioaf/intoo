<?php

require_once 'Testing/Selenium.php';

class Example extends PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $this = new Testing_Selenium("*chrome", "https://mail.google.com/")
    // $this->getTex("//form[@id='ConsProc']/table/tbody/tr[3]/td/table/tbody/tr[11]/td/font/span/b[2]");
  }
}
?>