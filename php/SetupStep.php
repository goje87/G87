<?php
abstract class SetupStep {
  abstract public function execute();
  
  public function SetupStep($setup) {
    $this->setup = $setup;
  }
}
?>