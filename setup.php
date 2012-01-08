<?php
// TODO: There should be a button asking user for proceeding with the setup.
require_once("InstallationStep.php");

$step1 = new InstallationStep();
$step1->message = "Creating <code>.user.ini</code>...";
$step1->execute = function() {
  $script_filename = $_SERVER['SCRIPT_FILENAME'];
  $g87_document_root = preg_replace('/\/G87\/setup\.php/', '', $script_filename);
  $contents = file_get_contents("__user.ini.file");
  $contents = preg_replace("/&{g87_document_root}/", $g87_document_root, $contents);
  $file = fopen("$g87_document_root/.user.ini", "w");
  fwrite($file, $contents); 
  fclose($file);
  //copy("__user.ini.file", "../.user.ini");
};

$step2 = new InstallationStep();
$step2->message = "Creating <code>.htaccess</code>...";
$step2->execute = function() {
  $script_filename = $_SERVER['SCRIPT_FILENAME'];
  $g87_document_root = preg_replace('/\/G87\/setup\.php/', '', $script_filename);
  $contents = file_get_contents("__htaccess.file");
  $contents = preg_replace("/&{g87_document_root}/", $g87_document_root, $contents);
  $file = fopen("$g87_document_root/.htaccess", "w");
  fwrite($file, $contents); 
  fclose($file);
};
// TODO: Have additional step to create serverConfig.json to hold automatically
//       generated G87 server variables. As of now these are in siteConfig.json

$steps = InstallationStep::$steps;
error_log(print_r($steps));
?>


<html>
  <head>
    <title>Configuring G87 server</title>
    <link rel="stylesheet" href="/G87/js/basic.css" />
  </head>
  <body>
    <ol>
      <? foreach($steps as $step): ?>
        <li> <?= $step->message ?> </li>
        <? call_user_func($step->execute); ?>
      <? endforeach ?>
    </ol>
    <h3>SETUP COMPLETED!!</h3>
  </body>
</html>
