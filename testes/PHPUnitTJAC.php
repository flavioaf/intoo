<?php
class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://www.tjac.jus.br/");
  }

  public function testMyTestCase()
  {
  }
}
?>