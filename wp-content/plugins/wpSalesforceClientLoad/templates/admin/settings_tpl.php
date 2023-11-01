<div class="wrap">
    <h1>SALESFORCE API Integracion -  Ajustes Generales</h1>
    <form method="post" action="options.php" method="post">
        <?php settings_fields( 'SfClientApi_options_group' ); ?>
        <?php do_settings_sections( 'SfClientApi-options' ); ?>
        <?php submit_button(); ?>
    </form>
</div>