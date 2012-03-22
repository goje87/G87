<?php
$setup = new Setup();
$setup->preInit();
$stepScript = "$setup->setupDir/$setup->step.step.php";
if(file_exists($stepScript)) include($stepScript);
$setup->init();
?>

<html>
  <head>
    <title><?= $setup->app ?> Setup</title>
    <link rel="stylesheet" href="/G87/js/basic.css" />
  </head>
  <body>
    <form method="post">
      <div>
        <? include("$setup->setupDir/$setup->step.step.tpl"); ?>
      </div>
      <? if(!$setup->hideNextButton && $setup->step != "finish") : ?>
        <input type="submit" name="submit" value="Next &raquo;" />
      <? endif; ?>
    </form>
  </body>
</html>