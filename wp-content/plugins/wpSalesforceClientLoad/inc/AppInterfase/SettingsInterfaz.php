<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\AppInterfase;

class SettingsInterfaz
{
	public $admin_pages = array();
	public $admin_subpages = array();
	public $settings = array();
	public $sections = array();
	public $fields = array();

	public function registrar()
	{
		if ( ! empty($this->admin_pages) ) {
			add_action( 'admin_menu', array( $this, 'addAdminMenu' ) );
		}

		
		if ( !empty($this->settings) ) {
			
			add_action( 'admin_init', array( $this, 'registerCustomFields' ) );
		}
	}

	/**
	 * Agrega las páginas que se deseen registrar al menú del admin
	 * @param array $pages 
	 * Arreglo que contiene a su vez arreglos con los valores de configuración de cada opción que queremos agregar al menú del admin
	 * Cada uno de los arrays de configuración de cada opción del menú debe tener la siguiente estructura:
	 * [
	 * "page_title",
	 * "menu_title",
	 * "capability",
	 * "menu_slug",
	 * "callback",
	 * "icon_url",
	 * "position"
	 * ]
	 */
	public function addPages( array $pages )
	{
		$this->admin_pages = $pages;		

		return $this;
	}

	/**
	 * Para indicar que el menú tendrá sub páginas
	 * @param string $title título del submenu
	 * @return object $this Retorna el mismo objeto para mantener el encadenamiento de funciones
	 */
	public function withSubPage( string $title = null ) 
	{
		if ( empty($this->admin_pages) ) {
			return $this;
		}

		$admin_page = $this->admin_pages[0];

		$subpage = array(
			array(
				'parent_slug' => $admin_page['menu_slug'], 
				'page_title' => $admin_page['page_title'], 
				'menu_title' => ($title) ? $title : $admin_page['menu_title'], 
				'capability' => $admin_page['capability'], 
				'menu_slug' => $admin_page['menu_slug'], 
				'callback' => $admin_page['callback']
			)
		);

		$this->admin_subpages = $subpage;

		return $this;
	}

	/**
	 * Metodo para agregar sub páginas al menú
	 * @param array $pages Array de sub páginas que se agregarán al menú
	 */
	public function addSubPages( array $pages )
	{
		$this->admin_subpages = array_merge( $this->admin_subpages, $pages );

		return $this;
	}

	/**
	 * Agrega una nueva opción al menú del admin
	 */
	public function addAdminMenu()
	{
		foreach ( $this->admin_pages as $page ) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}

		foreach ( $this->admin_subpages as $page ) {
			add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'] );
		}
	}

	/**
	 * Asigna los settings para la creaciòn de custom fields
	 * @param array $settings Arreglo con valores de configuración para los settings del custom fields
	 * cada arreglo debería tener los siguientes índices: 
	 * [
	 * "option_group",
	 * "option_name",
	 * "callback"	 
	 * ]
	 */
	public function setSettings( array $settings )
	{
		$this->settings = $settings;

		return $this;
	}

	/**
	 * Asigna los sections para la creación de custom fields
	 * @param array $sections Arreglo con valores de configuración para los sections del custom field
	 * cada arreglo debería tener los siguientes índices: 
	 * [
	 * "id",
	 * "title",
	 * "page",
	 * "section",
	 * "args"	 
	 * ]
	 */
	public function setSections( array $sections )
	{
		$this->sections = $sections;

		return $this;
	}

	/**
	 * Asigna los fields para la creación de custom fields
	 * @param array $fields Arreglo con valores de configuración para los parametros field del custom field
	 * cada arreglo debería tener los siguientes índices: 
	 * [
	 * "id",
	 * "title",
	 * "page"	 
	 * ]
	 */
	public function setFields( array $fields )
	{
		$this->fields = $fields;

		return $this;
	}

	/**
	 * Registra de forma dinámica los custom fields registrados en settings, sections y fields
	 */
	public function registerCustomFields()
	{
		// register setting
		foreach ( $this->settings as $setting ) {
			register_setting( $setting["option_group"], $setting["option_name"], ( isset( $setting["callback"] ) ? $setting["callback"] : '' ) );
		}

		// add settings section
		foreach ( $this->sections as $section ) {
			add_settings_section( $section["id"], $section["title"], ( isset( $section["callback"] ) ? $section["callback"] : '' ), $section["page"] );
		}

		// add settings field
		foreach ( $this->fields as $field ) {
			add_settings_field( $field["id"], $field["title"], ( isset( $field["callback"] ) ? $field["callback"] : '' ), $field["page"], $field["section"], ( isset( $field["args"] ) ? $field["args"] : '' ) );
		}
	}
	
}