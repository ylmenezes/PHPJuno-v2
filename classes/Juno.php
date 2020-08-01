<?php 
/**
 * Integração com o getway de pagamento da juno
 * API Versão 2 da Juno <https://dev.juno.com.br/api/v2> 
 * 
 * =========================================================================
 * 
 *  Para iniciar a classe você deve informar o ClienteID e ClienteSecret
 * 
 *  $juno = new Juno(token, clientID, clientSecret);
 * 
 * Obs.: Por padrão a classe vem habilitado para produção
 * Para usar em modo de teste, passar como parâmetro TRUE para $this->sandbox
 * 
 * ==========================================================================
 *
 * Criado em 2020-07-30 
 * @author Yan Menezes  <https://yanmenezes.com.br>
 * @copyright Copyright (c) 2020
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @since	Version 1.0.0
 * 
 */

class Juno{

    private $clientID        = null;
    private $clientSecret    = null;
    private $sandbox         = false;

    public $interest        = 0;        // Juros ao mês [ 0.00 até 20.00 ]
    public $fine            = 0;        // Multa de pagamento após o vencimento [ 0.00 até 20.00 ]
    public $notify          = false;    // Permitir Juno notificar o cliente [ TRUE = PERMITIR / FALSE = BLOQUEIA ]
    public $installments    = 0;        // Números de parcelas para o pagamento
    public $maxOverdueDays  = 30;       // Número de dias que permitir aceitar a cobrança [ Juros só funciona com essa função habilitada ]
    public $paymentAdvance  = false;    // Permiti o adiantamento da cobrança [ TRUE = Antecipa / False = Não antecipa ]
    
    /** Daddos charge */
    public $description;
    public $amount;
    public $dueDate;

    /** Dados de billing */
    public $name;
    public $document;
    public $email          = null;
    public $street         = null;
    public $number         = null;
    public $city           = null;
    public $state          = null;
    public $postCode       = null;
    public $creditCardHash = null;

    public $token;
    public $token_access;
    public $token_base64;

    const SANDBOX        = 'https://sandbox.boletobancario.com/api-integration/';
    const PROD           = 'https://api.juno.com.br/';

    const SANDBOX_AUTH   = 'https://sandbox.boletobancario.com/';
    const PROD_AUTH      = 'https://api.juno.com.br/';

    function __construct($token, $clientID, $clientSecret, $sandbox = false)
    {
        $this->token        = $token;
        $this->clientID     = $clientID;
        $this->clientSecret = $clientSecret;
        $this->sandbox      = $sandbox;
        $this->token_base64 = base64_encode($this->clientID.':'.$this->clientSecret);
        $this->token_access = $this->auth();
    }

    /**
     * Criar uma cobrança na forma de BOLETO.
     * @return Object 
     */
    public function createCharge()
    {
        try{

            $data = array(
                'charge' => [
                    'description'   =>  $this->description,
                    'amount'        =>  $this->amount,
                    'dueDate'       =>  $this->dueDate,
                    'paymentTypes'  => array('BOLETO'),
                ],
                'billing' => [
                    'name'     =>  $this->name,
                    'document' =>  $this->document,
                ],
            );

            $aCharge = $this->request( 'charges', 'POST', $data);
            if( $aCharge->status ){
                throw new Exception( $aCharge->details[0]->message );
            }else{
                return $aCharge;
            }
        }catch(Exception $e){
          $this->exception($e);
        }
    }

     /**
     * Cancelar uma cobrança na forma de BOLETO.
     * @return Object 
     */
    public function cancelCharge($id = null)
    {
        try{

            if($id == null)
                throw new Exception('Informe o ID da cobrança!');

            $aCancel = $this->request( 'charges/'.$id.'/cancelation', 'PUT',null,false);

            if( $aCancel->status ){
                throw new Exception( $aCancel->details[0]->message );
            }else{
                return $aCancel;
            }
        }catch(Exception $e){
          $this->exception($e);
        }
    }

    /**
     * Detalhes da cobrança
     * @return Object
     */
    public function getChargeDetails($id)
    {
        try{

            if($id == null)
                throw new Exception('Informe o ID da cobrança!');

            return $this->request( 'charges/'.$id, 'GET');
        }catch(Exception $e){
            $this->exception($e);
        }
    }


    /** 
     * Cria pagamento para cobrança
     * @return Object
     */
    public function payments($id)
    {
        $chargeCreditCard = array(
            'chargeId'  => $id,
            'billing'   => [
                'address'  => [
                    'street'    => $this->street,
                    'number'    => $this->number,
                    'city'      => $this->city,
                    'state'     => $this->state,
                    'postCode'  => $this->postCode,
                ]
            ],
            'creditCardDetails' => [
                'creditCardHash' => $this->creditCardHash
            ]

        );

        return $this->request('payments', 'POST', $chargeCreditCard);
    }

    /**
     * Criar uma cobrança na forma de Cartão de Crédito.
     * @return Object 
     */
    public function creditCard()
    {
        try{
            $data = array(
                'charge' => [
                    'description'  => $this->description,
                    'amount'       => $this->amount,
                    'paymentTypes' => array('CREDIT_CARD'),
                ],
                'billing' => [
                    'name'     => $this->name,
                    'document' => $this->document,
                    'email'    => $this->email,
                ],
            );

            if($this->installments > 1){
                unset($data['charge']['amount']);
                $data['charge']['totalAmount']  = $this->amount;
                $data['charge']['installments'] = $this->installments;
            }

            $oCharge = $this->request('charges', 'POST', $data);
            
            if( $oCharge->status ){
                throw new Exception($oCharge->details[0]->message);
            }else{
                $aPayments = $this->payments($oCharge->_embedded->charges[0]->id);
                if( $aPayments->code ){
                    throw new Exception($oCharge->details[0]->message);
                }else{
                    return $aPayments;
                }
            }
        }catch(Exception $e){
           $this->exception($e);
        }
    }

    /**
     * Estorna cobrança de Cartão de crédito.
     * @return Object 
     */
    public function refunds($id = null)
    {
        try{

            if($id == null)
                throw new Exception('Informe o ID da cobrança!');

            $aRefunds = $this->request( 'payments/'.$id.'/refunds', 'POST');

            if( $aRefunds->status ){
                throw new Exception( $aRefunds->status.'-'.$aRefunds->details[0]->message );
            }else{
                return $aRefunds;
            }
        }catch(Exception $e){
          $this->exception($e);
        }
    }

    /**
     * Balnaço da conta
     * @return Object
     */
    public function balance()
    {
        try{
            return $this->request( 'balance', 'GET' );
        }catch(Exception $e){
           $this->exception($e);
        }
    }


    /** 
     * Cria planos de assinatura
     * @return Object
     */
    public function createPlan($name = null, $price = null)
    {
        try{
            if($name == null)
                throw new Exception('Defina um nome ao plano');
            
            if($price == null)
                throw new Exception('Defina um valor ao plano');

            $data = array(
                'name'   => $name,
                'amount' => $price,
            );

            $aPlans = $this->request('plans', 'POST', $data);
            if( $aPlans->status ){
                throw new Exception( $aPlans->status.'-'.$aPlans->details[0]->message );
            }else{
                return $aPlans;
            }
        }catch(Exception $e){
            $this->exception($e);
        }
    }

    /** 
     * Pegar planos de assinatura
     * @return Object
     */
    public function getPlans()
    {
        try{
           
            $aPlans = $this->request('plans', 'GET');
            if( $aPlans->status ){
                throw new Exception( $aPlans->status.'-'.$aPlans->details[0]->message );
            }else{
                return $aPlans;
            }
        }catch(Exception $e){
            $this->exception($e);
        }
    }


    /**
     * Gera um token de autorização para acessar os recursos da API de Integração da Juno. 
     * @return String access_token
     */
    private function auth()
    {
        try{
            $exec = curl_init();
            curl_setopt_array($exec,array(
                CURLOPT_URL             => ( $this->sandbox ? Juno::SANDBOX_AUTH : Juno::PROD_AUTH ).'authorization-server/oauth/token',
                CURLOPT_ENCODING        => "UTF-8",
                CURLOPT_MAXREDIRS       => 2,
                CURLOPT_POST            => TRUE,
                CURLOPT_FOLLOWLOCATION  => TRUE,
                CURLOPT_RETURNTRANSFER  => TRUE,
                CURLOPT_CUSTOMREQUEST   => "POST",
                CURLOPT_POSTFIELDS      => 'grant_type=client_credentials',
                CURLOPT_HTTPHEADER      => array(
                    'Content-Type:application/x-www-form-urlencoded', 
                    'Authorization: Basic '.$this->token_base64
                )
            )); 

            $response = curl_exec($exec);
            $erro = curl_error($exec);
            curl_close($exec);

            if($response == false)
                throw new Exception('cURL Erro: '.$erro);
            
            $aAuth = json_decode( $response );
            return $aAuth->access_token;
                
        }catch(Exception $e){
           $this->exception($e);
        }
    }

    /**
     * Metod de execução da classe
     * @return Object
     */
    private function request($url, $method, $data = null, $return = TRUE)
    {
        try{
            $exec = curl_init();
            if($data)
                curl_setopt($exec, CURLOPT_POSTFIELDS, json_encode($data) );

            curl_setopt_array($exec,array(
                CURLOPT_URL             => ( $this->sandbox ? Juno::SANDBOX : Juno::PROD ).$url,
                CURLOPT_ENCODING        => "UTF-8",
                CURLOPT_MAXREDIRS       => 2,
                CURLOPT_POST            => TRUE,
                CURLOPT_FOLLOWLOCATION  => TRUE,
                CURLOPT_RETURNTRANSFER  => $return,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST   => $method,
                CURLOPT_HTTPHEADER      => array(
                    'Authorization: Bearer '.$this->token_access,
                    'X-Api-Version: 2' ,
                    'X-Resource-Token: '.$this->token,
                    'Content-Type: application/json'
                )
            )); 

            $response = curl_exec($exec);
            $erro = curl_error($exec);
            curl_close($exec);

            if($response == false)
                throw new Exception('cURL Erro: '.$erro);
            
            return json_decode( $response );
                
        }catch(Exception $e){
           $this->exception($e);
        }
    }

    /** 
     * Metodo var_dump and die
     */
    private function dd($data)
    {
        var_dump($data);
        die;
    }

    private function exception($e)
    {
        $aException = array(
            'error'   => true,
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'message' => $e->getMessage(),
        );
        $this->dd($aException);
    }

}