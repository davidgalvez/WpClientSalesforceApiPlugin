<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Business;
use wpSfApiCli\Base\PluginController;
use WP_REST_Request;
use wpSfApiCli\Business\ClientMapper;
use wpSfApiCli\Business\ApiValidator;
use wpSfApiCli\Business\ApiLogGenerator;
use wpSfApiCli\Salesforce\SalesforceConector;
if(! defined('ABSPATH')) exit();

class CaseClientSender extends PluginController
{
    private $apiValidator;
    private $clientMapper;
    private $myPostTypeName;   
    private $userId;
    private $clients;
    private $clientSfId;
    private $caseNumber;     
    private $caseDetails;   
    private $totalSent;
    private $totalError;
    private $codeResponse;    
    private $loadDate;
    private $loadStatus;
    private $sfApiToken;


    /**
     * Main Function to get New SF case Info to sent to clients
     */
    public function getNewSalesfoceCaseToSend(WP_REST_Request $request)
    {
        $this->setLoaderAtts();

        if($this->apiValidator->validateNewCaseInput($request)===false)
        {
                $this->codeResponse=400;
                $response=
                [
                    "success"=>"false",
                    "message"=>"Bad json structure, check field names and fields count"
                ];
                wp_send_json($response,$this->codeResponse);
                exit();
        }

        $this->caseNumber=$request["case_number"];
        $this->caseDetails=$this->getNewSalesfoceCaseDetails();
        $totalRecs=$this->caseDetails->totalSize;

        if($totalRecs==0)
        {
            $this->codeResponse=400;
            $response=
                [
                    "success"=>"false",
                    "message"=>"Case (".$this->caseNumber.") not found", 
                    "caseDetails"=>$this->caseDetails                           
                ];
            wp_send_json($response,$this->codeResponse);
            exit();
            
        }

        $this->clientSfId=$this->caseDetails->records[0]->AccountId__c;
        $this->clients=$this->clientMapper->getClientsInfo($this->caseNumber,$this->caseDetails, $this->clientSfId);

        if(count($this->clients)==0)
        {
            $this->codeResponse=400;           
            $response=
                [
                    "success"=>"false",
                    "message"=>"Client Account Id (".$this->clientSfId.") not found",                            
                ];
            wp_send_json($response,$this->codeResponse);
            exit();
        }

        
        $this->sendCaseToAllClients();

        $success="true";
        $message="Case Sent Succesfully";

        
        

        //$logGen= new ApiLogGenerator($this->plugin);
        //$logGen->generateLog($this->caseNumber,$this->userId,$sfResultados->compositeResponse);

        $response=
        [
            "success"=>$success,
            "message"=>$message,
            "data"=>[
                "CaseNumber"=>$this->caseNumber,
                "caseSent"=>$this->caseDetails,
                "userID"=>$this->userId, 
                "totalRecs"=>$totalRecs,
                "clientId"=>$this->clientSfId,
                "clients"=>$this->clients,      
                "clientUrlApi"=>$this->clients[0]["urlAPI"] ,  
                //"loadDate"=>$this->loadDate 
                //"fileName"=>$csvGen->fileName                     
                "sfToken"=>$this->sfApiToken,
                //"sfUrl"=>$sfConecto->urlCasos,                
                //"composite"=>$composite,    
                //"resultCasos"=>$sfConecto->responseCaso
               
            ]        
        ];
        
        wp_send_json($response,$this->codeResponse);  


    }

    private function getNewSalesfoceCaseDetails()
    {
        $sfConecto=new SalesforceConector();
        $sfConecto->getApiToken("consulta");
        $sfConecto->getCaseInfo($this->caseNumber);
        $sfResultados=$sfConecto->responseCaso;      
        $this->sfApiToken=$sfConecto->apiToken;
        return $sfResultados;
    }

    /**
     * Sets Initial values for loader class attributes
     */
    private function setLoaderAtts()
    {
       // $this->myPostTypeName=$this->getPostTypeName();
        $this->userId=get_current_user_id();
        $this->totalSent=0;
        $this->totalError=0;
        $this->codeResponse=201;
        $this->apiValidator=new ApiValidator();
        $this->clientMapper=new ClientMapper($this->plugin);
    }

    private function sendCaseToAllClients()
    {
        $i=0;
        for($i=0;$i<count($this->clients);$i++){            
            $this->clients[$i]["response"]=$this->sendCaseToClient($this->clients[$i]);
        }
    }

    private function sendCaseToClient($client)
    {
        $payload=$client["payload"];
        $url=$client["urlAPI"];
        $args = array(
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Content-Type'=>'application/json',
                'charset'=>'utf-8'
            ),
            'body'        => json_encode($payload),
            'cookies'     => array()
        );
        $response = wp_remote_post( $url, $args );
        if ( is_wp_error( $response ) ) 
        {
            $error_message = $response->get_error_message();
            $respuestaClientSend= array(
                "success"=>"false",
                "message"=>"Something went wrong sending case to client URL ($url): $error_message"
            );            
           
        }
        else {           
            
            $respuestaClientSend= json_decode(wp_remote_retrieve_body($response));                      
           
        }
        return $respuestaClientSend;
    }

}