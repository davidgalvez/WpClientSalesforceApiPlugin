<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\Pages;

use \wpSfApiCli\Base\PluginController;
use \wpSfApiCli\AppInterfase\SettingsInterfaz;
use \wpSfApiCli\AppInterfase\Callbacks\AdminCallbacks;


class Admin extends PluginController{

    public $settings;
    public $callbacks;
	public $pages = array();
    public $subpages = array();

    /**
     * Registra el menu para el adminsitrador del plugin
     */
    public function register() 
    {
        $this->settings= new SettingsInterfaz();
        $this->callbacks = new AdminCallbacks($this->plugin);
        $this->setPages();
        $this->setSubPages();

        $this->setSettings();
		$this->setSections();
		$this->setFields();

       $this->settings->addPages($this->pages)->withSubPage( 'Dashboard' )->registrar();
    }  

    

    /**
     * Funcion para definir las opciones de menú del plugin que se agregarán al admin
     */
    public function setPages()
    {
        $this->pages = array(
            array(
                'page_title' => 'Salesforce-Clients Ajustes', 
                'menu_title' => 'Salesforce Clients Ajustes', 
                'capability' => 'administrator', 
                'menu_slug' => 'SfClientApi-options', 
                'callback' => array($this->callbacks,'adminSettingsPage'), 
                'icon_url' => 'dashicons-rest-api', 
                'position' => 150

            )
        );
    }

    /**
     * Funcion para definir la lista de sub páginas del menú del plugin que se agregarán al admin
     */
    public function setSubPages()
    {
        $this->subpages = array(
           /*array(
                'parent_slug' => 'apisf-options', 
                'page_title' => 'Store API Admin', 
                'menu_title' => 'API admin', 
                'capability' => 'manage_options', 
                'menu_slug' => 'apisf-api', 
                'callback' => array($this->callbacks, "adminApiPage")
            )*/
        );
    }

    /**
     * Define los valores que se enviarán como arrays para crear los custom fields
     */
    public function setSettings()
	{
		$args = array(
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_url',
				'callback' => array( $this->callbacks, 'adminOptionsGroup' )
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_clientId'
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_clientSecret'
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_username'
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_password'
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_urlGetCaseInfo'
			),
            array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_urlRes',				
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_clientIdRes'
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_clientSecretRes'
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_usernameRes'
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_passwordRes'
			),
			array(
				'option_group' => 'SfClientApi_options_group',
				'option_name' => 'SfClientApi_urlSetCliResponse'
			)
		);        

		$this->settings->setSettings( $args );
	}

	/**
     * Define el array de las secciones que contendrán a los custom fields
     */
    public function setSections()
	{
		$args = array(
			array(
				'id' => 'SfClientApi_admin_index',
				'title' => 'Configuración general del Api Salesforce',
				'callback' => array( $this->callbacks, 'adminSection' ),
				'page' => 'SfClientApi-options'
			)
		);

		$this->settings->setSections( $args );
	}

	/**
     * Define el array de los campos o custom fields que se usarán en el panel de administración del plugin
     */
    public function setFields()
	{
		$args = array(
			array(
				'id' => 'SfClientApi_url',
				'title' => 'Url Autenticacion Api Salesforce',
				'callback' => array( $this->callbacks, 'adminSfClientApi_url' ),
				'page' => 'SfClientApi-options',
				'section' => 'SfClientApi_admin_index',
				'args' => array(
					'label_for' => 'SfClientApi_url',
					'class' => 'url-class'
				)
            ),
            array(
                'id' => 'SfClientApi_clientId',
                'title' => 'Client ID',
                'callback' => array( $this->callbacks, 'adminSfClientApi_clientId' ),
                'page' => 'SfClientApi-options',
                'section' => 'SfClientApi_admin_index',
                'args' => array(
                    'label_for' => 'SfClientApi_clientId',
                    'class' => 'client-id-class'
                )
            ),
            array(
                'id' => 'SfClientApi_clientSecret',
                'title' => 'Client Secret',
                'callback' => array( $this->callbacks, 'adminSfClientApi_clientSecret' ),
                'page' => 'SfClientApi-options',
                'section' => 'SfClientApi_admin_index',
                'args' => array(
                    'label_for' => 'SfClientApi_clientSecret',
                    'class' => 'client-secret-class'
                )
            ),
            array(
                'id' => 'SfClientApi_username',
                'title' => 'Username',
                'callback' => array( $this->callbacks, 'adminSfClientApi_username' ),
                'page' => 'SfClientApi-options',
                'section' => 'SfClientApi_admin_index',
                'args' => array(
                    'label_for' => 'SfClientApi_username',
                    'class' => 'user-name-class'
                )
            ),
            array(
                'id' => 'SfClientApi_password',
                'title' => 'Password',
                'callback' => array( $this->callbacks, 'adminSfClientApi_password' ),
                'page' => 'SfClientApi-options',
                'section' => 'SfClientApi_admin_index',
                'args' => array(
                    'label_for' => 'SfClientApi_password',
                    'class' => 'password-class'
                )
            ),
            array(
				'id' => 'SfClientApi_urlGetCaseInfo',
				'title' => 'Endpoint Salesforce de Detalle de Caso ',
				'callback' => array( $this->callbacks, 'adminSfClientApi_urlGetCaseInfo' ),
				'page' => 'SfClientApi-options',
				'section' => 'SfClientApi_admin_index',
				'args' => array(
					'label_for' => 'SfClientApi_urlGetCaseInfo',
					'class' => 'url-get-case-info-class'
				)
            ),
            array(
				'id' => 'SfClientApi_urlRes',
				'title' => 'Url Autneticacion Api Response Salesforce',
				'callback' => array( $this->callbacks, 'adminSfClientApi_urlRes' ),
				'page' => 'SfClientApi-options',
				'section' => 'SfClientApi_admin_index',
				'args' => array(
					'label_for' => 'SfClientApi_urlRes',
					'class' => 'url-class-res'
				)
            ),
            array(
                'id' => 'SfClientApi_clientIdRes',
                'title' => 'Client ID Api Response',
                'callback' => array( $this->callbacks, 'adminSfClientApi_clientIdRes' ),
                'page' => 'SfClientApi-options',
                'section' => 'SfClientApi_admin_index',
                'args' => array(
                    'label_for' => 'SfClientApi_clientIdRes',
                    'class' => 'client-id-class-res'
                )
            ),
            array(
                'id' => 'SfClientApi_clientSecretRes',
                'title' => 'Client Secret Api response',
                'callback' => array( $this->callbacks, 'adminSfClientApi_clientSecretRes' ),
                'page' => 'SfClientApi-options',
                'section' => 'SfClientApi_admin_index',
                'args' => array(
                    'label_for' => 'SfClientApi_clientSecretRes',
                    'class' => 'client-secret-class-res'
                )
            ),
            array(
                'id' => 'SfClientApi_usernameRes',
                'title' => 'Username Api Response',
                'callback' => array( $this->callbacks, 'adminSfClientApi_usernameRes' ),
                'page' => 'SfClientApi-options',
                'section' => 'SfClientApi_admin_index',
                'args' => array(
                    'label_for' => 'SfClientApi_usernameRes',
                    'class' => 'user-name-class'
                )
            ),
            array(
                'id' => 'SfClientApi_passwordRes',
                'title' => 'Password Api Response',
                'callback' => array( $this->callbacks, 'adminSfClientApi_passwordRes' ),
                'page' => 'SfClientApi-options',
                'section' => 'SfClientApi_admin_index',
                'args' => array(
                    'label_for' => 'SfClientApi_passwordRes',
                    'class' => 'password-class'
                )
            ),
            array(
				'id' => 'SfClientApi_urlSetCliResponse',
				'title' => 'Endpoint Salesforce de respuesta de clientes',
				'callback' => array( $this->callbacks, 'adminSfClientApi_urlSetCliResponse' ),
				'page' => 'SfClientApi-options',
				'section' => 'SfClientApi_admin_index',
				'args' => array(
					'label_for' => 'SfClientApi_urlSetCliResponse',
					'class' => 'url-set-cli-response-class'
				)
            )
		);

		$this->settings->setFields( $args );
	}
}
