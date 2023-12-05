<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Base;
use wpSfApiCli\Business\CaseClientSender;
use wpSfApiCli\Business\CaseResponseSender;
use wpSfApiCli\Business\ApiValidator;
if(! defined('ABSPATH')) exit();

class ApiEndpoints extends PluginController
{
    private $apiRoutes;
    private $apiValidator;
    private $leadsLoader;
    private $caseSender;
    private $caseResponseSender;
    public function register()
    {
        $this->setApiLeadsLoader();
        $this->setApiValidator();
        $this->setApiRoutes();
        add_action( 'rest_api_init', array($this, 'registerApiRoutes'));
    }

    public function setApiLeadsLoader()
    {
        //$this->leadsLoader = new LeadsLoader($this->plugin);
        $this->caseSender = new CaseClientSender($this->plugin);
        $this->caseResponseSender = new CaseResponseSender($this->plugin);
    }
    public function setApiValidator()
    {
        $this->apiValidator = new ApiValidator();
    }

    public function setApiRoutes()
    {
        $this->apiRoutes =
        [
            [
                "namespace"=>"wpSfApiCli/v1",
                "route"=>"send-case",
                "args"=>
                [
                    "methods"=>'POST',
                    "callback"=>array($this->caseSender,'getNewSalesfoceCaseToSend'),
                    "permission_callback" =>array($this->apiValidator,'validatePermitions')
                ]
            ],
            [
                "namespace"=>"wpSfApiCli/v1",
                "route"=>"send-response",
                "args"=>
                [
                    "methods"=>'POST',
                    "callback"=>array($this->caseResponseSender,'sendCaseResponseToSalesforce'),
                    "permission_callback" =>array($this->apiValidator,'validatePermitions')
                ]
            ]
        ];
    }

    
    public function registerApiRoutes() 
    {
        foreach($this->apiRoutes as $route)
        {
            register_rest_route($route["namespace"],$route["route"],$route["args"]);
        }
    }
}