<?php
/**
 * @package wpSfApiCli
 */
namespace wpSfApiCli\AppInterfase\Callbacks;

use \wpSfApiCli\Base\PluginController;

class AdminCallbacks extends PluginController
{
    /**
     * Invoca a la plantilla html que mostrará la página de configuración General del plugin
     */
    public function adminSettingsPage()
    {
        require_once $this->pluginPath.'/templates/admin/settings_tpl.php';

    }

    

    /**
     * Invoca el metodo para crear el option group para los custom fields
     */
    public function adminOptionsGroup( $input )
	{
		return $input;
	}

	/**
     * Invoca el metodo para mostrar al crear la sección del custom field
     */
    public function adminSection()
	{
		echo 'Sección de campos personalizados';
	}

	/**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_url
     */
    public function adminSfClientApi_url()
	{
		$value = esc_attr( get_option( 'SfClientApi_url' ) );
		echo '<input type="text" class="regular-text" name="SfClientApi_url" value="' . $value . '" placeholder="Ingresa la URL de conexion del API">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_clientId
     */
    public function adminSfClientApi_clientId()
	{
		$value = esc_attr( get_option( 'SfClientApi_clientId' ) );
		echo '<input type="text" class="regular-text" name="SfClientApi_clientId" value="' . $value . '" placeholder="Ingresa el ClientId">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_clientSecret
     */
    public function adminSfClientApi_clientSecret()
	{
		$value = esc_attr( get_option( 'SfClientApi_clientSecret' ) );
		echo '<input type="text" class="regular-text" name="SfClientApi_clientSecret" value="' . $value . '" placeholder="Ingresa el ClientSecret">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_username
     */
    public function adminSfClientApi_username()
	{
		$value = esc_attr( get_option( 'SfClientApi_username' ) );
		echo '<input type="text" class="regular-text" name="SfClientApi_username" value="' . $value . '" placeholder="Ingresa el Username">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_username
     */
    public function adminSfClientApi_password()
	{
		$value = esc_attr( get_option( 'SfClientApi_password' ) );
		echo '<input type="password" class="regular-text" name="SfClientApi_password" value="' . $value . '" placeholder="Ingresa el Password">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_urlGetCaseInfo
     */
    public function adminSfClientApi_urlGetCaseInfo()
	{
        $server = rest_get_server();
        $routes = $server->get_routes();
        print_r(isset($routes["/wpSfApiCli/v1"]));
		$value = esc_attr( get_option( 'SfClientApi_urlGetCaseInfo' ) );       
		echo '<input type="text" class="regular-text" name="SfClientApi_urlGetCaseInfo" value="' . $value . '" placeholder="URL de para obtener detalles de casos del API">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_url
     */
    public function adminSfClientApi_urlRes()
	{
		$value = esc_attr( get_option( 'SfClientApi_urlRes' ) );
		echo '<input type="text" class="regular-text" name="SfClientApi_urlRes" value="' . $value . '" placeholder="Ingresa la URL de autenticacion del API">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_clientId
     */
    public function adminSfClientApi_clientIdRes()
	{
		$value = esc_attr( get_option( 'SfClientApi_clientIdRes' ) );
		echo '<input type="text" class="regular-text" name="SfClientApi_clientIdRes" value="' . $value . '" placeholder="Ingresa el ClientId">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_clientSecret
     */
    public function adminSfClientApi_clientSecretRes()
	{
		$value = esc_attr( get_option( 'SfClientApi_clientSecretRes' ) );
		echo '<input type="text" class="regular-text" name="SfClientApi_clientSecretRes" value="' . $value . '" placeholder="Ingresa el ClientSecret">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_username
     */
    public function adminSfClientApi_usernameRes()
	{
		$value = esc_attr( get_option( 'SfClientApi_usernameRes' ) );
		echo '<input type="text" class="regular-text" name="SfClientApi_usernameRes" value="' . $value . '" placeholder="Ingresa el Username">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_username
     */
    public function adminSfClientApi_passwordRes()
	{
		$value = esc_attr( get_option( 'SfClientApi_passwordRes' ) );
		echo '<input type="password" class="regular-text" name="SfClientApi_passwordRes" value="' . $value . '" placeholder="Ingresa el Password">';
	}

    /**
     * Muestra el input con el que se actualizará el valor del custom field de SfClientApi_urlCasos
     */
    public function adminSfClientApi_urlSetCliResponse()
	{
        $server = rest_get_server();
        $routes = $server->get_routes();
        print_r(isset($routes["/wpSfApiCli/v1"]));
		$value = esc_attr( get_option( 'SfClientApi_urlSetCliResponse' ) );       
		echo '<input type="text" class="regular-text" name="SfClientApi_urlSetCliResponse" value="' . $value . '" placeholder="URL de para enviar la respuesta del API del cliente por caso">';
	}

	
}