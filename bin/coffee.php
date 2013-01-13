<?php

// composer autoloader
require '../vendor/autoload.php';

$file = '../assets/coffee/core.coffee';
$out = '../public/js/core.js';

try
{
  $js = CoffeeScript\Compiler::compile(file_get_contents($file));
  file_put_contents($out, $js);
}
catch (Exception $e)
{
  echo $e->getMessage();
}
