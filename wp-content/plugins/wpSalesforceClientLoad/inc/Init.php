<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli;

final class Init{
    
    /**
     * Store classes in array
     * @return array array of stored classes
     */
    public static function get_services()
    {
        return [  
            Pages\Admin::class,  
            Base\SettingsLinks::class, 
            Base\Roles::class,
            Base\PostTypes::class,
            Base\ApiEndpoints::class                     
        ];
    }

    /**
     * Generates an instance of each class and calls the register() method in case it exists
     * @param string $plugin Unique route of plugin will be used as identifier it should be sent the value of plugin_basename(__FILE__) from the root of the plugin
     */
    public static function register_services(string $plugin)
    {
        foreach(self::get_services() as $class)
        {
            $service=self::instance_class($class,$plugin);
            if(method_exists($service,'register'))
            {
                $service->register();
            }
        }
    }

    /**
     * Creates a new instance of the class that receives as parameter
     * @param class $clase  to instance
     * @param string $plugin unique plugin route 
     * @return object Returns an instance of the class
     */
    private static function instance_class($clase,$plugin)
    {
        $service = new $clase($plugin);
        return $service;
    }
}