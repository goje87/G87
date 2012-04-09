<?php 
class Step extends SetupStep {
  public function execute() {
    $contents = file_get_contents(G87_DOCUMENT_ROOT."/G87/setup/__user.ini.file");
    $contents = preg_replace("/&{g87_document_root}/", G87_DOCUMENT_ROOT, $contents);
    $file = fopen(G87_DOCUMENT_ROOT."/.user.ini", "w");
    fwrite($file, $contents); 
    fclose($file);
    Setup::nextStep();
  }
}
?>