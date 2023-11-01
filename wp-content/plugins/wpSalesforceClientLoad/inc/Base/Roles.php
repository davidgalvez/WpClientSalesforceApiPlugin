<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Base;


if(! defined('ABSPATH')) exit();

class Roles extends PluginController
{
    public $roles;

    /**
     * Adds Roles to Plugin
     */
    public function register()    
    {
        $this->setRoles();
        add_action('init', array($this, 'registerRoleList'));
    }

    /**
     * Sets the roles values to the class argument
     */
    public function setRoles()
    {
        $this->roles=[
            [
                "name"=>"sf_case_loader",
                "display_name"=>"Sf Case loader",
                "capabilities"=>
                [
                    "read"=>true,
                    "edit_leadsAL"=>true,
                    "publish_leadsAL"=>true,
                    "edit_published_leadsAL"=>true
                ]
            ]
        ];
    }

    /**
     * Get the list of Roles registered in the roles argument of the class
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Iterate the array of Roles to register each one
     */
    public function registerRoleList()
    {
        $roles=$this->getRoles();
        foreach($roles as $role)
        {
            $this->registerRole($role["name"],$role["display_name"],$role["capabilities"]);
        }
    }

    /**
     * Register one Role with its arguments
     * @param $name Name of the Role to register
     * @param $display_name The name of the Role to display in front-end
     * @param $capabilities Array of capabilities asociated to the role
     */
    public function registerRole($name,$display_name,$capabilities)
    {
        if(!$this->isRegisteredRole($name))
        {
           add_role($name,$display_name,$capabilities);
                  
        }        
    } 

    /**
     * Validates if role exists
     * @param $name Name of the role to validate if exists
     */
    public function isRegisteredRole($name)
    {
        return (get_role($name)!==NULL);
    }

    public static function addAdminCapabilities()
    {
        $adminRole=get_role("administrator");
            $adminRole->add_cap( 'read' );
			$adminRole->add_cap( 'edit_leadsAL' );
			$adminRole->add_cap( 'publish_leadsAL' );
			$adminRole->add_cap( 'edit_published_leadsAL' );
			$adminRole->add_cap( 'edit_others_leadsAL' );
            $adminRole->add_cap( 'read_private_leadsAL' );
			$adminRole->add_cap( 'edit_others_leadsAL' );
			$adminRole->add_cap( 'edit_private_leadsAL' );
			$adminRole->add_cap( 'delete_leadsAL' );
			$adminRole->add_cap( 'delete_published_leadsAL' );
			$adminRole->add_cap( 'delete_private_leadsAL' );
			$adminRole->add_cap( 'delete_others_leadsAL' );

    }

    public static function removeCapabilities()
    {
        $server = rest_get_server();
        $routes = $server->get_routes();
        if(isset($routes["/leadsAL/v1"])) return;
        if(get_role('sf_case_loader')===NULL) return;
        $adminRole=get_role("administrator");           
			$adminRole->remove_cap( 'edit_leadsAL' );
			$adminRole->remove_cap( 'publish_leadsAL' );
			$adminRole->remove_cap( 'edit_published_leadsAL' );
			$adminRole->remove_cap( 'edit_others_leadsAL' );
            $adminRole->remove_cap( 'read_private_leadsAL' );
			$adminRole->remove_cap( 'edit_others_leadsAL' );
			$adminRole->remove_cap( 'edit_private_leadsAL' );
			$adminRole->remove_cap( 'delete_leadsAL' );
			$adminRole->remove_cap( 'delete_published_leadsAL' );
			$adminRole->remove_cap( 'delete_private_leadsAL' );
			$adminRole->remove_cap( 'delete_others_leadsAL' );
        $loaderRole=get_role('sf_case_loader');
            $loaderRole->remove_cap( 'read' );
            $loaderRole->remove_cap( 'edit_leadsAL' );
            $loaderRole->remove_cap( 'publish_leadsAL' );
            $loaderRole->remove_cap( 'edit_published_leadsAL' );  
    }

    public static function removeRol()
    {     
        $server = rest_get_server();
        $routes = $server->get_routes();
        if(isset($routes["/leadsAL/v1"])) return;        
        if(get_role('sf_case_loader')===NULL) return;   
        remove_role("sf_case_loader","Sf Case loader");
	}


}