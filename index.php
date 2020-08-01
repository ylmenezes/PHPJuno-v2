<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Classe Juno</title>
    <script type="text/javascript" src="https://sandbox.boletobancario.com/boletofacil/wro/direct-checkout.min.js"></script>

    <!-- URL DE PRODUçÂO -->
    <!-- <script type="text/javascript" src="https://www.boletobancario.com/boletofacil/wro/direct-checkout.min.js"></script> -->

    <script type="text/javascript">
        var checkout = new DirectCheckout('PUBLIC_TOKEN',false); 
        /* Em produção utilizar o construtor new DirectCheckout('PUBLIC_TOKEN'); */
        var cardData = {
            cardNumber: '0000000000000000',
            holderName: 'NAME_TITULAR',
            securityCode: 'CVV',
            expirationMonth: 'MONTH',
            expirationYear: 'YEAR'
        };

        checkout.getCardHash(cardData, function(cardHash) {
            alert(cardHash)
        }, function(error) {
           alert(error)
        });
    </script>
</head>
<body>

</body>
</html>
