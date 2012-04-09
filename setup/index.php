<?php
include_once("../init.php");
Setup::preInit();
$stepScript = Setup::$setupDir."/".Setup::$step.".step.php";
if(file_exists($stepScript)) include($stepScript);
Setup::init();
?>

<html>
  <head>
    <title><?= Setup::$app ?> Setup</title>
    <link rel="stylesheet" href="/G87/js/basic.css" />
  </head>
  <body>
    <form method="post">
      <div>
        <? include(Setup::$setupDir."/".Setup::$step.".step.tpl"); ?>
      </div>
      <? if(!Setup::$hideNextButton && Setup::$step != "finish") : ?>
        <input type="submit" name="submit" value="Next &raquo;" />
      <? endif; ?>
    </form>
  </body>
</html>