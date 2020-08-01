<?php
require 'config.php';

$resultado = $juno->getPlans();
print_r($resultado);