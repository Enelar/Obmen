<?php

include("../vendor/autoload.php");

$less = new lessc;

header("Content-Type: text/css");
try
{
  echo $less->compileFile('a.less');
} catch(Exception $e)
{
  echo $e->getMessage();
}