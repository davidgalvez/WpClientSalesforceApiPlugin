<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Business;

use wpSfApiCli\Base\PluginController;


if(! defined('ABSPATH')) exit();

class ApiLogGenerator extends PluginController
{
    private $userName;
    private $loadNumber;    
    public $fileName;

    /**
     * Main function that sets attributes and generates .log file report
     */
    public function generateLog($loadNumber,$userId,$responseApi)
    {
        $this->setAttributes($loadNumber,$userId);

        // Open or create the file
        $fp = fopen($this->fileName, 'a');
        //fwrite($this->fileName,"Regsistrando log..." . PHP_EOL);

        //Write each line with API log information
        foreach($responseApi as $responseCaso)
        {
            $logCaso=$this->logResponseApi($responseCaso);
            $logLine="wpLoadNumber: ".$this->loadNumber." | wpLoadUser: ".$this->userName." | ".$logCaso;
            fwrite($fp,$logLine . PHP_EOL);
        } 
        
        // close file
        fclose($fp);
        
    }

    public function logResponseApi($infoResponse)
    {
        $logCaso="timestamp: ".date("Y-m-d H:i:s")." | referenceID: ".$infoResponse->referenceId." | statusCode: ".$infoResponse->httpStatusCode;
        if($infoResponse->httpStatusCode==201)
        {
            $logCaso.=" | idSalesforceCase: ".$infoResponse->body->id;                
        }else{
            $logCaso.=" | errorCode: ".$infoResponse->body[0]->errorCode." | errorMessage: ".$infoResponse->body[0]->message;                
        }
        return $logCaso;
    }


    private function setAttributes($loadNumber,$userId)
    {
        $this->setUser($userId);
        $this->setLoadNumber($loadNumber);        
        $this->setFileName();
    }

    private function setUser($userId)
    {
        $user=get_userdata( $userId );
        $this->userName=$user->user_login;
    }

    private function setLoadNumber($loadNumber)
    {
        $this->loadNumber=$loadNumber;
    }

    private function setFileName()
    {
        $fecha = date("Y_m_d");
        $this->fileName=$this->pluginPath."/Logs/$fecha.log";
    }
}