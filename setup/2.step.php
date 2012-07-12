<?php 
class Step extends SetupStep {
  public function execute() {
    $contents = file_get_contents(G87_DOCUMENT_ROOT."/G87/setup/__htaccess.file");
    $contents = preg_replace("/&{g87_document_root}/", G87_DOCUMENT_ROOT, $contents);
    $file = fopen(G87_DOCUMENT_ROOT."/.htaccess", "w");
    fwrite($file, $contents); 
    fclose($file);
    $this->setup->finish();
  }
}
?>
