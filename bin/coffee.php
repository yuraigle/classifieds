<?php

// composer autoloader
require '../vendor/autoload.php';

try
{
    $opts = new Zend_Console_Getopt(array(
        'help|h' => 'Displays usage information.',
        'input|i=s' => 'Input file',
        'out|o=s' => 'Out file',
    ));
    $opts->parse();
}
catch (Zend_Console_Getopt_Exception $e)
{
    exit($e->getMessage() ."\n\n". $e->getUsageMessage());
}

if(isset($opts->h))
{
    echo $opts->getUsageMessage();
    exit;
}

$in = $opts->i;
$out = $opts->o;

try
{
  $js = CoffeeScript\Compiler::compile(file_get_contents($in));
  file_put_contents($out, $js);
}
catch (Exception $e)
{
  echo $e->getMessage();
}