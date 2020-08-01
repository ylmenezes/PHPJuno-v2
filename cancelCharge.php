<?php
require 'config.php';

$resultado = $juno->cancelCharge($_GET['charge']);
print_r($resultado);