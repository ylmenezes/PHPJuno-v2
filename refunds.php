<?php
require 'config.php';

$resultado = $juno->refunds($_GET['id']);
print_r($resultado);