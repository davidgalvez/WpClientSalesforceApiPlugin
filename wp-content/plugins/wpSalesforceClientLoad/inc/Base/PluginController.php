<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Base;

if(! defined('ABSPATH')) exit();
class PluginController
{

    protected $plugin;   
    protected $pluginPath;
    protected $pluginUrl;     


    /**
     * Controls global vars for the plugin
     * @param string $plugin unique name of the plugin path.
     */
    public function __construct(string $plugin)
    {
        
        $this->plugin=$plugin;           
        $this->pluginPath=plugin_dir_path(dirname( __FILE__, 2 ) );
        $this->pluginUrl=plugin_dir_url( dirname( __FILE__, 2 ) );      
        
    }
    
}