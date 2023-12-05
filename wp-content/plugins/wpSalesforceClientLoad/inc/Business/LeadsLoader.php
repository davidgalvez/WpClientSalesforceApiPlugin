<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Business;
use wpSfApiCli\Base\PluginController;
use WP_REST_Request;
use wpSfApiCli\Base\PostTypes;
use wpSfApiCli\Business\ApiValidator;
use wpSfApiCli\Business\CsvGenerator;
use wpSfApiCli\Business\ApiLogGenerator;
use wpSfApiCli\Salesforce\SalesforceConector;

if(! defined('ABSPATH')) exit();

class LeadsLoader extends PluginController
{
    private $myPostTypeName;   
    private $userId;
    private $LastTax;
    private $maxLoad;
    private $newLoad;
    private $totalLoaded;
    private $totalError;
    private $codeResponse;
    private $apiValidator;
    private $loadDate;
    private $loadStatus;

    /**
     * Main function to get the request validate and load leads to database
     */
    public function loadLeads(WP_REST_Request $request)
    {       
        $this->setLoaderAtts();

        if($this->apiValidator->validateInput($request)===false)
        {
                $this->codeResponse=400;
                $response=
                [
                    "status"=>"ERROR",
                    "message"=>"Bad json structure, check field names and fields count"
                ];
                wp_send_json($response,$this->codeResponse);
                exit();
        }

        $leadsLoaded=[];
        foreach($request->get_params() as $lead)
        {
            $nleadId=$this->loadLead($lead);   
            if($nleadId)
            {
                $lead["caseID"]=$nleadId;
                array_push($leadsLoaded,$lead);
                $this->totalLoaded++;
            } 
            else $this->totalError++;
        }

        if($this->totalLoaded==0)
        {
            $this->codeResponse=304;
            $response=
                [
                    "status"=>"EMPTY",
                    "message"=>"No Data was loaded"
                ];
            wp_send_json($response,$this->codeResponse);
            exit();

        }
        
        $sfConecto=new SalesforceConector();
        $sfConecto->getApiToken("consulta");         
        $composite=$sfConecto->getCompositePayload($leadsLoaded);   
        $sfConecto->createCases($composite);
        $sfResultados=$sfConecto->responseCaso;        

        $logGen= new ApiLogGenerator($this->plugin);
        $logGen->generateLog($this->newLoad,$this->userId,$sfResultados->compositeResponse);  

      // $csvGen=new CsvGenerator($this->plugin);
      // $csvGen->generateCsv($this->newLoad,$this->userId,$this->loadDate,$this->loadStatus,$request->get_params());      

        $response=
        [
            "status"=>"SUCCESS",
            "messasge"=>"Data loaded successfully",
            "data"=>[
                "LoadNumber"=>$this->newLoad,
                "totalLoaded"=>$this->totalLoaded,
                "totalError"=>$this->totalError,
                "loadDate"=>$this->loadDate 
                //"fileName"=>$csvGen->fileName                     
                //"sfToken"=>$sfConecto->apiToken,
                //"sfUrl"=>$sfConecto->urlCasos,                
                //"composite"=>$composite,    
                //"resultCasos"=>$sfConecto->responseCaso
               
            ]        
        ];
        
        wp_send_json($response,$this->codeResponse);       
        
    }

    /**
     * Sets Initial values for loader class attributes
     */
    private function setLoaderAtts()
    {
        $this->myPostTypeName=$this->getPostTypeName();
        $this->userId=get_current_user_id();
        $this->LastTax=$this->getLastLoad();
        $this->maxLoad=intval(explode("-",$this->LastTax)[1]);
        $this->newLoad="Load-".str_pad(($this->maxLoad+1), 6, '0', STR_PAD_LEFT);
        $this->totalLoaded=0;
        $this->totalError=0;
        $this->codeResponse=201;
        $this->apiValidator=new ApiValidator();
    }

    private function setLoadDate($loadDate)
    {
        $this->loadDate=$loadDate;
    }

    private function setLoadStatus($loadStatus)
    {
        $this->loadStatus=$loadStatus;
    }
    

    /**
     * Gets the plugin custom post type name
     * @return string name of plugin custom post type
     */
    private function getPostTypeName()
    {
        $myPostType=new PostTypes($this->plugin);
        $myPostType->setPosttypes();        
        return $myPostType->getPostTypes()[0]["name"];

    }

    /**
     * Loads Lead Data into custom post Type
     * @param $lead object with lead data to load
     * @return $post_id Id of the lead loaded to the database
     */
    private function loadLead($lead)
    {

        $title= $lead["email"]." -  ".$lead["fname"]." ".$lead["lname"];        
        $env=($lead["env"]=="prod")?'private':'draft';
        $this->setLoadStatus($env);
        $post_id = wp_insert_post(array (
            'post_type' => $this->myPostTypeName,
            'post_title' => $title,
            'post_author' => $this->userId,
            'post_status' => $env,
            'comment_status' => 'closed',   
            'ping_status' => 'closed',      
            ));
            if($post_id)  wp_set_post_terms( $post_id, $this->newLoad,'ApiLoads');
            if($post_id) $this->addMetaData($post_id,$lead);
            if($post_id) $this->setLoadDate(get_the_date('Y/m/d g:i:s A',$post_id));

        return($post_id);
            
    }

    /**
     * Gets the last custom post loaded
     * @return object custom post type object
     */
    private function getLastLeadLoaded()
    {
        $args = array(
            'post_type' =>$this->myPostTypeName,
            'posts_per_page' => 1
        );

        return wp_get_recent_posts($args,OBJECT);

    }

    /**
     * Get the correlative code of the last load
     * @return string $lastTax  Correlative code of the last loading process
     */
    private function getLastLoad()
    { 
        $LastTax="";
        $recent_post = $this->getLastLeadLoaded();

        if($recent_post)
        {
            $LastTax=get_the_terms($recent_post[0]->ID,'ApiLoads')[0]->name;            
        }

        return $LastTax;
    }

    /**
     * Adds the details of the lead in post meta data
     */
    private function addMetaData($post_id, $lead)
    {
        if ($post_id) {
            // insert post meta
            foreach($lead as $key=>$value)
            {
                add_post_meta($post_id, $key, $value);
            }          
         }
         return $post_id;
    }
    
}