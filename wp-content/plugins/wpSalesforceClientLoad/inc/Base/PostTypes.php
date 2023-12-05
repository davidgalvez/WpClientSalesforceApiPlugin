<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Base;

if(! defined('ABSPATH')) exit();

class PostTypes extends PluginController
{
    
    public $postTypes;    
   
    /**
     * Adds the PostType to Plugin
     */
    public function register()    
    {
        $this->setPosttypes();
        add_action('init', array($this, 'registerPosttypeList'));
        add_filter('enter_title_here',array($this,'custom_enter_title'));
        add_filter('manage_posts_columns', array($this,'custom_columns'));
    }

    public function setPosttypes()
    {
        
        $this->postTypes = 
        [
            [
                "name" =>"sfClient",
                "arguments"=>
                [
                    "label" => __( "sfClient", "sfClient" ),
                    "labels" =>
                    [
                        "name" => __( "Clientes API Sf", "sfClient" ),
                        "singular_name" => __( "Cliente API Sf", "sfClient" ),
                        "menu_name" => __( "Clientes API Sf", "sfClient" ),
                        "all_items" => __( "Todos los Clientes API Sf", "sfClient" ),
                        "add_new" => __( "Añadir Nuevo Cliente API Sf", "sfClient" ),
                        "add_new_item" => __( "Añadir Nuevo Cliente API Sf", "sfClient" ),
                        "edit_item" => __( "Editar Cliente API Sf", "sfClient" ),
                        "new_item" => __( "Nuevo Cliente API Sf", "sfClient" ),
                        "view_item" => __( "Ver Cliente API Sf", "sfClient" ),
                        "view_items" => __( "Ver Clientes API Sf", "sfClient" ),
                        "search_items" => __( "Buscar Clientes API Sf", "sfClient" ),
                        "not_found" => __( "Cliente API SF no encontrado", "sfClient" ),
                        "not_found_in_trash" => __( "No encontrado en papelera", "sfClient" ),
                        "filter_items_list" => __( "Filtrar Lista Cliente API Sf", "sfClient" ),
                        "items_list_navigation" => __( "Lista de navegación de Clientes API sF", "sfClient" ),
                        "items_list" => __( "Lista de Clientes API Sf", "sfClient" ),
                        "name_admin_bar" => __( "Cliente API Sf", "sfClient" ),
                        "item_published" => __( "Cliente API Sf guardado", "sfClient" ),
                        "item_published_privately" => __( "Cliente API Sf guardado", "sfClient" ),
                        "item_reverted_to_draft" => __( "Cliente API Sf enviado a papelera", "sfClient" ),
                        "item_scheduled" => __( "Item programado", "sfClient" ),
                        "item_updated" => __( "Item actualizado", "sfClient" ),
                    ],
                    "description" => "Lista de Clientes API para integrar casos de Salesforce",
                    "public" => true,
                    "publicly_queryable" => false,
                    "show_ui" => true,
                    "show_in_rest" => true,
                    "rest_base" => "",
                    "rest_controller_class" => "WP_REST_Posts_Controller",
                    "rest_namespace" => "wp/v2",
                    "has_archive" => false,
                    "show_in_menu" => true,
                    "show_in_nav_menus" => true,
                    "delete_with_user" => false,
                    "exclude_from_search" => true,
                    "taxonomies" => array(''),
                    "capability_type" => array("post","posts"),
                    "map_meta_cap" => true,
                    "hierarchical" => false,
                    "can_export" => true,
                    "rewrite" => [ "slug" => "SfClient", "with_front" => true ],
                    "query_var" => "SfClient",
                    "menu_position" => 20,
                    "menu_icon" => "dashicons-database-import",
                    "supports" => [ "title", "author","custom-fields" ],
                    "show_in_graphql" => false,
                ]
            ]
        ];
        
    }

    /**
     * Get the list of PostTypes registered in the postTypes argument of the class
     */
    public function getPostTypes()
    {
        return $this->postTypes;
    }

    
    /**
     * Iterate the array of Posttypes to register each one
     */
    public function registerPosttypeList()
    {
        $postTypes=$this->getPostTypes();
        foreach($postTypes as $posType)
        {
            $this->registerPostype($posType["name"],$posType["arguments"]);
        }
    }

    /**
     * Register one PostType with its arguments
     * @param $name Name of Posttype to register
     * @param $arguments Asociative array with arguments and labes of the postType
     */
    public function registerPostype($name,$arguments)
    {
        if(!$this->isRegisteredPostType($name))
        {
           register_post_type( $name, $arguments );
                  
        }        
    }    

    /**
     * Validates is already exists another postType with the same name we want to register
     * @param $name Name of the postType we want to register
     */
    public function isRegisteredPostType($name)
    {
        return post_type_exists( $name );
    }

    

    function custom_enter_title( $input ) {
        
        $screen = get_current_screen();
        if  ( $screen->post_type == 'sfclient' ) {
            return 'Nombre del Cliente';
        }

        return $input;
    }

    function custom_columns($columns) {

        $screen = get_current_screen();
        if  ( $screen->post_type == 'sfclient' ) {
            $columns['title'] ='Cliente';
        }
        
        return $columns;
    }

}