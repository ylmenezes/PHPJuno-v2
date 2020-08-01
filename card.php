<?php
require 'config.php';

$juno->description    = 'COMPRA CARTÃO '.time();
$juno->document       = '26624093048';
$juno->name           = '4DEVS';

// Em caso de cartão de crédito e pagamento parcelado, setar o valor no campo a baixo
$juno->installments   = $_REQUEST['parcelas'];
$juno->creditCardHash = $_REQUEST['hash'];

$juno->amount         = '150.5';
$juno->email          = 'dev.ciaweb@gmail.com';
$juno->street         = 'Uma rua em um lugar';
$juno->number         = '01';
$juno->city           = 'São Paulo';
$juno->state          = 'SP';
$juno->postCode       = "06711000";
$resultado = $juno->creditCard();

print_r($resultado);