<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Business;
use wpSfApiCli\Base\PluginController;
use WP_REST_Request;
use wpSfApiCli\Business\ApiValidator;
use wpSfApiCli\Business\ApiLogGenerator;
use wpSfApiCli\Salesforce\SalesforceConector;
if(! defined('ABSPATH')) exit();

class CaseResponseSender extends PluginController
{
    private $apiValidator;
    private $userId;
    private $resultClient;
    private $caseNumber;     
    private $messageClient; 
    private $codeResponse;    
    private $loadDate;
    private $loadStatus;
    private $sfApiToken;
    private $sfSendResponseResult;


    /**
     * Main Function to send Client Case creation response to Salesforce
     */
    public function sendCaseResponseToSalesforce(WP_REST_Request $request)
    {
        $this->setLoaderAtts();

        if($this->apiValidator->validateResponseCaseInput($request)===false)
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
        $this->resultClient=$request["result"];
        $this->messageClient=$request["message"];
        $payloadResponse=[
            "allOrNone"=>true,
            "collateSubrequests"=>true,
            "compositeRequest"=>[
                    [
                        "method"=>"GET",
                        "url"=>"/services/data/v57.0/query/?q=SELECT+id+FROM+lead_de_oportunidad__c+WHERE+Aceptar_Rechazar__c +=+''+AND+Lead__r.CaseNumber+=+'".$this->caseNumber."'+Limit+1",
                        "referenceId"=>"OpportunityLead"
                    ],
                    [
                        "method"=>"PATCH",
                        "url"=>"/services/data/v57.0/sobjects/Lead_de_oportunidad__c/@{OpportunityLead.records[0].Id}",
                        "referenceId"=>"UpdateLeadStatus",
                        "body"=>[
                            "Aceptar_Rechazar__c"=> "Aceptar",
                            "Comments__c"=> "".$this->messageClient.""
                        ]
                    ]

                ]
            ];
        $clientApiResponse=array(
            "case_number"=>$this->caseNumber,
            "result"=>$this->resultClient,
            "message"=>$this->messageClient,
        );
        //$this->sfSendResponseResult=$this->sendNewSalesfoceCaseResponse($clientApiResponse);
        $this->sfSendResponseResult=$this->sendNewSalesfoceCaseResponse($payloadResponse);

        
        if(is_string($this->sfSendResponseResult))
        {
            $this->codeResponse=400;           
            $response=
                [
                    "success"=>"false",
                    "message"=>"Could not send case response to Salesforce",
                    "salesforceMessage"=>$this->sfSendResponseResult,                            
                ];
            wp_send_json($response,$this->codeResponse);
            exit();
        }

        if($this->sfSendResponseResult->compositeResponse[0]->httpStatusCode==400 or $this->sfSendResponseResult->compositeResponse[1]->httpStatusCode==400)
        {
            $this->codeResponse=400;           
            $response=
                [
                    "success"=>"false",
                    "message"=>"Could not send case response to Salesforce",
                    "salesforceMessage"=>$this->sfSendResponseResult,                            
                ];
            wp_send_json($response,$this->codeResponse);
            exit();
        }

        
        $success="true";
        $message="Case Response Sent Succesfully";

        
        

        //$logGen= new ApiLogGenerator($this->plugin);
        //$logGen->generateLog($this->caseNumber,$this->userId,$sfResultados->compositeResponse);

        $response=
        [
            "success"=>$success,
            "message"=>$message,
            "data"=>[
                "CaseNumber"=>$this->caseNumber,
                "resultClient"=>$this->resultClient,
                //"userID"=>$this->userId, 
                "message"=>$this->messageClient,
                "errorCodeSF"=>$this->sfSendResponseResult->compositeResponse[0]->httpStatusCode,
                //"ApiPayload"=>$payloadResponse, 
                //"fileName"=>$csvGen->fileName                     
                //"sfToken"=>$this->sfApiToken,
                //"sfUrl"=>$sfConecto->urlCasos,                
                //"composite"=>$composite,    
                "resultSendResponse"=>$this->sfSendResponseResult               
            ]        
        ];
        
        wp_send_json($response,$this->codeResponse);  


    }

    private function sendNewSalesfoceCaseResponse($caseClientResponse)
    {
        $sfConecto=new SalesforceConector();
        $sfConecto->getApiToken("respuesta");
        $sfConecto->sendCaseResponse($caseClientResponse);
        $sfResultados=$sfConecto->responseCaso;      
        $this->sfApiToken=$sfConecto->apiToken;
        return $sfResultados;
    }

    /**
     * Sets Initial values for loader class attributes
     */
    private function setLoaderAtts()
    {
        $this->userId=get_current_user_id();
        $this->codeResponse=201;
        $this->apiValidator=new ApiValidator();       
    }

    

    

}