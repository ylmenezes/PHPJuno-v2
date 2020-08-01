<?php

require 'config.php';

$juno->description  = 'PRODUTO '.time();
$juno->dueDate      = '2020-08-02';
$juno->document     = '26624093048';
$juno->name         = '4DEVS';
$juno->amount       = '150.5';
$resultado = $juno->createCharge();

print_r($resultado);
?>