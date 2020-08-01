<?php
require 'config.php';

$resultado = $juno->getChargeDetails($_GET['charge']);
print_r($resultado);