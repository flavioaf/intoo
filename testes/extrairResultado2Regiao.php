<?php

require_once 'Testing/Selenium.php';

class Example extends PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $this = new Testing_Selenium("*chrome", "https://mail.google.com/")
    // $this->("//form[@id='ResConsPess']/table/tbody/tr/td/table/tbody/tr[2]/td/p[2]");
    // $this->();
  }
}
?>