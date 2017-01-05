<?php

/**
 * Builds and Handles the Settings pages for this plugin.
 * 
 * @since 1.2.0
 */
class Funny_Quotes_Settings_Page
{
    /**
     * Holds the values to be used in the fields callbacks
     *
     * @since 1.2.0
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            __('Funny Quotes', 'funny-quotes'), 
            __('Funny Quotes', 'funny-quotes'), 
            'manage_options', 
            'funny-quotes-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     * 
     * @since 1.2.0
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'funny_quotes' );
        ?>
        <div class="wrap">
            <h1><?php _e('Funny Quotes Settings', 'funny-quotes'); ?></h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'funny_quotes_option_group' );
                do_settings_sections( 'funny-quotes-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     *
     * @since 1.2.0
     */
    public function page_init()
    {        
        register_setting(
            'funny_quotes_option_group', // Option group
            'funny_quotes', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'funny_quotes_setting_section', // ID
            __('Dashboard', 'funny-quotes'), // Title
            array( $this, 'print_section_info' ), // Callback
            'funny-quotes-setting-admin' // Page
        );  

        add_settings_field(
            'whentoshow', // ID
            __('When to show', 'funny-quotes'), // Title 
            array( $this, 'field_whentoshow' ), // Callback
            'funny-quotes-setting-admin', // Page
            'funny_quotes_setting_section' // Section           
        );      
  
    }

    /**
     * Sanitize each setting field as needed
     *
     * @since 1.2.0
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        // var_dump( $input );
        // exit;
        if( isset( $input['whentoshow'] ) )
            $new_input['whentoshow'] = sanitize_text_field( $input['whentoshow'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     * 
     * @since 1.2.0
     */
    public function print_section_info()
    {
        print __('Enter your settings below:', 'funny-quotes');
    }

    /** 
     * Get the settings option array and print one of its values
     *
     * @since 1.2.0
     */
    public function field_whentoshow()
    {
        ?>
        <p>
            
            <select name="funny_quotes[whentoshow]">
                    <option value="every-page-load" <?php selected( $this->options['whentoshow'], 'every-page-load' ); ?>>
                        <?php _e('Every page load', 'funny-quotes') ?>
                    </option>
                    <option value="one-per-hour" <?php selected( $this->options['whentoshow'], 'one-per-hour' ); ?>>
                        <?php _e('One per hour', 'funny-quotes') ?>
                    </option>
                    <option value="one-per-day" <?php selected( $this->options['whentoshow'], 'one-per-day' ); ?>>
                        <?php _e('One per day', 'funny-quotes') ?>
                    </option>
            </select>
        </p>
        <?php
    }

}
