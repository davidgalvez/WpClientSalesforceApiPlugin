<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Base;
use wpSfApiCli\Base\Roles;

class Deactivate{

    /**
     * Desactiva el plugin y remueve las configuraciones creadas
     */
    public static function deactivate() 
    {
        Roles::removeCapabilities();
        Roles::removeRol();
        flush_rewrite_rules();
    }
}