<?php

include("../vendor/autoload.php");

$less = new lessc;

header("Content-Type: text/css");
echo $less->compileFile('a.less');