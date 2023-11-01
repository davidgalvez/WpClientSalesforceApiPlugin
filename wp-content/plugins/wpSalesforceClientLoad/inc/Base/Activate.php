<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Base;
//use wpSfApiCli\Base\Roles;

class Activate{

    public static function activate() 
    {
        //Roles::addAdminCapabilities();
        flush_rewrite_rules();
    }
}