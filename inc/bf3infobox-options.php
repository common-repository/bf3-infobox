<?php

class bf3infobox_options
{
    public $options;

    public function __construct()
    {
        $this->options = get_option('bf3infobox_settings');
        $this->register_settings_and_fields();
    }

    public static function add_options_menu()
    {
        add_options_page(
            __('BF3 Infobox Options', 'bf3infobox'),
            'Battlefield 3 Info',
            'administrator',
            'bf3infobox-options',
            array('bf3infobox_options','display_options_page')
        );
    }

    public static function add_default_options_on_activation()
    {
        /*
        $tmp = get_option('bf3infobox_settings');
        if(!is_array($tmp)) {

        }
        */
        //forced update during beta, production needs some version info check to add new flags
        $arr = array(
            "plugin_version"        => BF3_INFOBOX_VERSION,
            "display_playername"    => "on",
            "display_platform"      => "on",
            "display_tillnextrank"  => "on",
            "display_generalstats"  => "on",
        );
        update_option('bf3infobox_settings', $arr);
    }

    public function display_options_page()
    {
    ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php echo __('BF3 Infobox Options', 'bf3infobox'); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields('bf3infobox_settings') ?>
                <?php do_settings_sections('bf3infobox-options'); ?>
                <p class="submit">
                    <input id="submit" class="button-primary" type="submit" value="Save Changes" name="submit">
                </p>
            </form>
        </div>
    <?php
    }

    public function register_settings_and_fields()
    {
        register_setting('bf3infobox_settings','bf3infobox_settings');

        add_settings_section(
            'bf3infobox_display_section',
            __('Widget Display Options - Show:', 'bf3infobox'),
            array($this, 'bf3infobox_display_options_cb'),
            'bf3infobox-options'
        );

        add_settings_field(
            'display_playername',
            __('Playername (Origin ID) and Link to bf3stats.com:', 'bf3infobox'),
            array($this, 'bf3infobox_checkbox_setting'),
            'bf3infobox-options',
            'bf3infobox_display_section',
            array('id' => 'display_playername')
        );

        add_settings_field(
            'display_platform',
            __('Platform (PC/PS3/360):', 'bf3infobox'),
            array($this, 'bf3infobox_checkbox_setting'),
            'bf3infobox-options',
            'bf3infobox_display_section',
            array('id' => 'display_platform')
        );

        add_settings_field(
            'display_tillnextrank',
            __('Points till next Rank:', 'bf3infobox'),
            array($this, 'bf3infobox_checkbox_setting'),
            'bf3infobox-options',
            'bf3infobox_display_section',
            array('id' => 'display_tillnextrank')
        );

        add_settings_field(
            'display_generalstats',
            __('General Stats Table:', 'bf3infobox'),
            array($this, 'bf3infobox_checkbox_setting'),
            'bf3infobox-options',
            'bf3infobox_display_section',
            array('id' => 'display_generalstats')
        );
    }


    public function bf3infobox_display_options_cb() {
        //Info between header and options
    }

    //------------------------ Settings / Inputs -------------------//

    //display checkbox
    public function bf3infobox_checkbox_setting($args) {
        $id = $args['id'];
        $checked = (isset($this->options[$id]) AND $this->options[$id] === 'on') ? 'checked' : '' ;
        echo "<input type='checkbox' name='bf3infobox_settings[$id]' $checked \>";
    }
}

