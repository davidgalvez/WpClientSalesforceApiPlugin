<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Business;
if(! defined('ABSPATH')) exit();

use wpSfApiCli\Base\PluginController;
use wpSfApiCli\Base\PostTypes;
use WP_Query;

class ClientMapper extends PluginController
{

    private $postTypeName;
    private $clients; 
    private $mappedFields;   
    private $caseNumber;
    private $caseInfo;

    public function getClientsInfo($caseNumber,$caseInfo,$clientSfId)
    {
        $this->caseNumber=$caseNumber;
        $this->caseInfo=$caseInfo;
        $this->caseInfo->records[0]->AccountId__c;
        $this->postTypeName=$this->getPostTypeName();
        $this->clients=[];      
        $i=0;  
        $loop = new WP_Query( array( 'post_type' => $this->postTypeName) );
        while ( $loop->have_posts() ) : 
            $loop->the_post();
            if($clientSfId==get_field("sfclient_account_id"))
            {
                $this->clients[$i]["name"]=get_the_title();
                $this->clients[$i]["urlAPI"]=get_field("sfclient_url_endpoint");
                $this->clients[$i]["payLoad"]=$this->getMappedFields(get_the_ID());
                $i++;
            }
        endwhile;
        return $this->clients;
    }

    private function getMappedFields($id)
    {
        $mapeoCliente=[];        
        $j=0;
        if(get_field('mapeo_de_campos_api', $id))
        {
            while( the_repeater_field('mapeo_de_campos_api', $id) )
            {
                $mapeoCliente[get_sub_field('campo_api_cliente')]=$this->getFieldValue(get_sub_field('campo_api_salesforce'),get_sub_field('otro'));
            }            
        }
        return $mapeoCliente;

    }

    private function getFieldValue($campoSalesforce,$campoOtro)
    {
        $valorCampo="";
        if(!isset($this->caseInfo->totalSize)) return "";
        if($this->caseInfo->totalSize==0) return "";
        if($campoSalesforce=="CaseNumber") return $this->caseNumber;
        if($campoSalesforce=="--Otro--") return $campoOtro;
        if(!isset($this->caseInfo->records[0])) return "";
        $campos=(array)$this->caseInfo->records[0];
        if(!isset($campos[$campoSalesforce])) return "";
        //return $campoSalesforce;
        return $campos[$campoSalesforce];       
        
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

}