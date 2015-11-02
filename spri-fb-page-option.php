<?php

class spri_fb_page_option_menu {

    function __construct() {

// Add menu
        add_action( 'admin_menu', array( $this, 'add_menu_and_page' ) );


// set settings
        add_action( 'admin_init', array( $this, 'register_setting' ) );
    }

    function add_menu_and_page() {
        $menu = add_menu_page(
                'SPRI Facebook page dashboard',
                'SPRI Facebook page feed Settings',
                'administrator',
                'spri-fb-page-setting',
                array( $this, 'display_main_settings_page' )
        );

        add_submenu_page(
                'spri-fb-page-setting',
                'SPRI Facebook Post Dashboard',
                'Post Dashboard',
                'manage_options',
                'spri-fb-page-dashboard',
                array( $this, 'display_fb_post_dashboard' )
        );
    }

    function display_main_settings_page() {

        ?>
        <div>
            <h2>SPRI Facebook Page Feed options</h2>

            <form action="options.php" method="post">

                <?php settings_fields( 'spri_fb_page_option_group' ); ?>
                <?php do_settings_sections( 'spri-fb-page-setting' ); ?>

                <input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>"/>
            </form>
        </div>
        <?php

    }

    function display_fb_post_dashboard() {

        echo <<<TEXT
Place Holder for dashboard

Display page list and its posts
TEXT;
        ?>

        <pre>
<?php
//var_dump( $t['feed']['data'] );

?>
</pre>
        <?php
    }

    function register_setting() {
        register_setting( 'spri_fb_page_option_group', 'spri_fb_page_option_name' );

        add_settings_section( 'spri_fb_page_option_section',
                'Attributes',
                array( $this, 'display_section_information' ),
                'spri-fb-page-setting' );

        add_settings_field(
                'spri_fb_app_id',
                'Facebook App ID',
                array( $this, 'display_app_id_form' ),
                'spri-fb-page-setting',
                'spri_fb_page_option_section'
        );

        add_settings_field(
                'spri_fb_app_secret',
                'Facebook App Secret',
                array( $this, 'display_app_secret_form' ),
                'spri-fb-page-setting',
                'spri_fb_page_option_section'
        );

        add_settings_field(
                'spri_fb_app_client_token',
                'Facebook Client Token',
                array( $this, 'display_client_token_form' ),
                'spri-fb-page-setting',
                'spri_fb_page_option_section'
        );
    }

    function display_section_information() {
        echo <<<DESC
Please set you facebook app ID, app secret and client token.
DESC;

    }

    function display_app_id_form() {
        $options = get_option( 'spri_fb_page_option_name' );
        echo "<input name='spri_fb_page_option_name[app_id]' size='100' type='text' value='{$options['app_id']}' />";
    }

    function display_app_secret_form() {
        $options = get_option( 'spri_fb_page_option_name' );
        echo "<input name='spri_fb_page_option_name[app_secret]' size='100' type='text' value='{$options['app_secret']}' />";
    }

    function display_client_token_form() {
        $options = get_option( 'spri_fb_page_option_name' );
        echo "<input name='spri_fb_page_option_name[client_token]' size='100' type='text' value='{$options['client_token']}' />";
    }
}