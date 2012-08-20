<?php
$path = Router::getPath("/error");
if(!$path) $path = G87_DOCUMENT_ROOT."/G87/php/error.view";

$status = $_SERVER['REDIRECT_STATUS'];
$statusConst = Utils::getClassConst("G87", "STATUS_$status");

G87::render($path, $statusConst);
?>

