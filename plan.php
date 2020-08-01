<?php
require 'config.php';

$resultado = $juno->createPlan($_GET['name'],$_GET['price']);
print_r($resultado);