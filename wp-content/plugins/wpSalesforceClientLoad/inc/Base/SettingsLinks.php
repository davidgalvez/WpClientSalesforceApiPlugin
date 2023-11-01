<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Base;

class SettingsLinks extends PluginController{

    /**
     * Registra el método para incluir los javascripts y estilos del plugin
     */
    public function register() 
    {
        
        add_filter('plugin_action_links_'.$this->plugin, array($this,'settings_links'));   
                
    }

    /**
     * Incluye los javascripts y estilos del plugin
     */
    function settings_links($links) 
    {
        $settings_link="<a href='admin.php?page=SfClientApi-options'>Ajustes</a>";
        array_push($links,$settings_link);
        return $links;
    }
}