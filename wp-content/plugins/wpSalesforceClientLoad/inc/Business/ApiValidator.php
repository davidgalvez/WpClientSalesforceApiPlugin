<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Business;

if(! defined('ABSPATH')) exit();

class ApiValidator
{
    private $ncols;
    private $fields;
    private $fieldsNewCase;
    private $fieldsCaseResponse;

    /**
     * Sets the fields and number of cols to validate in request
     */
    public function setValidators()
    {       
        $this->fieldsNewCase=[
            "case_number",
        ];
        $this->fieldsCaseResponse=[
            "case_number",
            "result",
            "message",
        ];
        $this->fields=[
            "email",
            "fname",
            "lname",
            "date_of_birth",
            "phone",
            "country",
            "ip", //
            "address",
            "city",
            "state",
            "zip",
            "offer_url",
            "date_subscribed",
            "comments",//
            "case_type",
            "Trusted_Form_Alt",
            "Jornaya",
            "Aux_1",//
            "Aux_2",//
            "Aux_3",//
            "Aux_4",//
            "campaign",//
            "env"//
        ];
        $this->ncols=count($this->fields);
    }

    public function validatePermitions()
    {
        return current_user_can("edit_sfClient");  
    }

    /**
     * Validates the structure of the Json Request
     * @param $request json request sent to the API
     */
    public function validateInput($request)
    {
        $this->setValidators();
        foreach($request->get_params() as $lead)
        {
            
            if($this->validateNcols($lead)===false) return false;
            if($this->validateValidCols($lead)==false) return false;
        }
        return $this->ncols;
    }

    /**
     * Validates the structure of the Json Request
     * @param $request json request sent to the API
     */
    public function validateNewCaseInput($request)
    {
        $this->setValidators();
            
        if($this->validateNewCaseCols($request)==false) return false;
        
        return $this->ncols;
    }

    /**
     * Validates the structure of the Json Request
     * @param $request json request sent to the API
     */
    public function validateResponseCaseInput($request)
    {
        $this->setValidators();
            
        if($this->validateResponseCaseCols($request)==false) return false;
        
        return $this->ncols;
    }

    private function validateNcols($lead)
    {   
        return (count($lead)==$this->ncols);
        
    }

    private function validateNewCaseCols($request){
        foreach($request as $key=>$value)
        {
            if(!in_array($key,$this->fieldsNewCase)) return false;
        }
        return true;
    }

    private function validateResponseCaseCols($request){
        foreach($request as $key=>$value)
        {
            if(!in_array($key,$this->fieldsCaseResponse)) return false;
        }
        if($request["result"]!="Aceptar" and $request["result"]!="Rechazar"){
            return false;
        }
        return true;
    }

    private function validateValidCols($lead)
    {
        foreach($lead as $key=>$value)
        {
            if(!in_array($key,$this->fields)) return false;
        }
        return true;
    }

    public function getApiFields()
    {
        return $this->fields;
    }
}