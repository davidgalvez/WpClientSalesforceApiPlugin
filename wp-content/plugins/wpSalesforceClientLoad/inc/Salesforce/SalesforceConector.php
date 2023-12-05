<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Salesforce;

if(! defined('ABSPATH')) exit();

class SalesforceConector
{
    private $url;
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;

    private $urlResponse;
    private $clientIdResponse;
    private $clientSecretResponse;
    private $usernameResponse;
    private $passwordResponse;

    public $responseConector;
    public $responseCaso;
    public $apiToken;
    public $urlCasos;
    public $urlGetCaseDetail;
    public $urlSendCaseResponse;
    public $compositePayload;

    public function getApiToken($tipoApi)
    {
        $this->setConnectionInfo();

        $body["consulta"]=array( 
            'grant_type' => 'password', 
            'client_id' => $this->clientId,
            'client_secret'=>$this->clientSecret,
            'username'=>$this->username,
            'password'=>$this->password
        );

        $body["respuesta"]=array( 
            'grant_type' => 'password', 
            'client_id' => $this->clientIdResponse,
            'client_secret'=>$this->clientSecretResponse,
            'username'=>$this->usernameResponse,
            'password'=>$this->passwordResponse
        );

        $bodyApi=($tipoApi=="respuesta")?$body["respuesta"]:$body["consulta"];
        
        
        $args = array(
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Content-Type'=>'application/x-www-form-urlencoded'
            ),
            'body'        => $bodyApi,
            'cookies'     => array()
        );
        $response = wp_remote_post( $this->url, $args );

        // error check
        if ( is_wp_error( $response ) ) 
        {
            $error_message = $response->get_error_message();
            $this->responseConector= "Something went wrong On Api connection: $error_message";
            $this->apiToken="";
            $this->urlCasos="";
        }
        else {
            //echo 'Response: <pre>';
            
            $this->responseConector= json_decode(wp_remote_retrieve_body($response));
            $this->apiToken=$this->responseConector;
            $this->apiToken=$this->responseConector->access_token;
            $this->urlCasos=$this->responseConector->instance_url.'/services/data/v53.0/composite/';
            //$this->urlGetCaseDetail=$this->responseConector->instance_url."/services/data/v53.0/query/?q=SELECT+FIELDS(custom)+FROM+Case+WHERE+CaseNumber+=+'::caseNo::'+LIMIT+1";
            $this->urlGetCaseDetail=$this->responseConector->instance_url.$this->urlGetCaseDetail;
            $this->urlSendCaseResponse=$this->responseConector->instance_url.$this->urlSendCaseResponse;
            //echo '</pre>';
        }
    }
    
    private function setConnectionInfo()
    {
        
        $this->clientId=esc_attr( get_option( 'SfClientApi_clientId' ) );
        $this->clientSecret=esc_attr( get_option( 'SfClientApi_clientSecret' ) );
        $this->username=esc_attr( get_option( 'SfClientApi_username' ) );
        $this->password=esc_attr( get_option( 'SfClientApi_password' ) );
        $this->url=esc_attr( get_option( 'SfClientApi_url' ) );
        $this->urlGetCaseDetail=esc_attr(get_option('SfClientApi_urlGetCaseInfo'));

        $this->clientIdResponse=esc_attr( get_option( 'SfClientApi_clientIdRes' ) );
        $this->clientSecretResponse=esc_attr( get_option( 'SfClientApi_clientSecretRes' ) );
        $this->usernameResponse=esc_attr( get_option( 'SfClientApi_usernameRes' ) );
        $this->passwordResponse=esc_attr( get_option( 'SfClientApi_passwordRes' ) );
        $this->urlResponse=esc_attr( get_option( 'SfClientApi_urlRes' ) );
        $this->urlSendCaseResponse=esc_attr( get_option( 'SfClientApi_urlSetCliResponse' ) );
        
    }

    public function getCaseInfo($caseId)
    {
        $urlGetDetail=str_replace('::caseNo::',"'$caseId'",$this->urlGetCaseDetail);
        
        $args = array(
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Content-Type'=>'application/json',
                'Authorization'=>'Bearer '.$this->apiToken
            ),
            //'body'        => json_encode($composite),
            //'data_format' => 'body',
            'cookies'     => array()
        );
        $response = wp_remote_get( $urlGetDetail, $args );

        // error check
        if ( is_wp_error( $response ) ) 
        {
            $error_message = $response->get_error_message();
            $this->responseCaso= "Something went wrong getting case Info ($urlGetDetail): $error_message";
           
        }
        else {           
            
            $this->responseCaso= json_decode(wp_remote_retrieve_body($response));                      
           
        }
    }

    public function sendCaseResponse($caseResponse)
    {
        
        $args = array(
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Content-Type'=>'application/json',
                'Authorization'=>'Bearer '.$this->apiToken
            ),
            'body'        => json_encode($caseResponse),
            'data_format' => 'body',
            'cookies'     => array()
        );
        $response = wp_remote_post( $this->urlSendCaseResponse, $args );
        $instanceURL=$this->responseConector->instance_url;

        // error check
        if ( is_wp_error( $response ) ) 
        {
            $error_message = $response->get_error_message();
            $this->responseCaso= "Something went wrong: $error_message";
           
        }
        else {           
            
            $this->responseCaso= json_decode(wp_remote_retrieve_body($response));            
           
        }
    }

    public function createCases($composite)
    {
        
        $args = array(
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Content-Type'=>'application/json',
                'Authorization'=>'Bearer '.$this->apiToken
            ),
            'body'        => json_encode($composite),
            'data_format' => 'body',
            'cookies'     => array()
        );
        $response = wp_remote_post( $this->urlCasos, $args );

        // error check
        if ( is_wp_error( $response ) ) 
        {
            $error_message = $response->get_error_message();
            $this->responseCaso= "Something went wrong: $error_message";
           
        }
        else {           
            
            $this->responseCaso= json_decode(wp_remote_retrieve_body($response));            
           
        }
    }

    public function singleCasePayload($lead_data)
    {
        $bodyPayload= array(
            "status"=> "ready",
            "Origin"=> "Digital",
            "Priority"=> "High",
            "Trusted_Form__c"=> $lead_data["Trusted_Form_Alt"],
            "Jornaya__c"=> $lead_data["Jornaya"],            
            "Date_Subscribed__c"=> $this->cleanDateInput($lead_data["date_subscribed"]),
            "Phone_Numbercontact__c"=> $lead_data["phone"],
            "Email__c"=> $lead_data["email"],
            "FirstName__c"=> $lead_data["fname"],            
            "Last_Name__c"=> $lead_data["lname"],
            "Date_of_Birth__c"=> $this->cleanDateInput($lead_data["date_of_birth"]),
            "Address_Street__c"=> $lead_data["address"],
            "City__c"=> $lead_data["city"],
            "StateUS__c"=> $lead_data["state"],
            "Area_Code__c"=> $lead_data["zip"],
            "Country__c"=> $lead_data["country"],
            "Offer_URL__c"=> $lead_data["offer_url"]           
        );
        $payload=array(
            "method"=> "POST",
            "url"=> "/services/data/v53.0/sobjects/Case",
            "referenceId"=> "Case".$lead_data["caseID"],
            "body"=>$bodyPayload
        );
        return $payload;
    }

    public function getCompositePayload($cases)
    {
        
        $composite=[];
        foreach($cases as $lead)
        {
           $singlePayload= $this->singleCasePayload($lead);
           array_push($composite,$singlePayload);
        } 

        $requestBody=array(
            "allOrNone"=> true,
            "collateSubrequests"=> true,
            "compositeRequest"=>$composite
        );
        return $requestBody;
    }

    public function cleanDateInput($dateInput)
    {
        $time=strtotime($dateInput);

        $dateClean=($time!==false)?date('Y-m-d',$time):"";
        return $dateClean;
    }
    
}