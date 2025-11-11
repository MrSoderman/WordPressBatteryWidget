<?php

/*
Plugin Name: Unicode Battery Widget
Description: Battery level widget that shows level and charging status. Has configurable Unicode blocks, and font size.
Version: 1.1
Author: Mr Soderman
Author URI: https://github.com/MrSoderman
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit;

// ------------------------
// Global Settings Page
// ------------------------
add_action('admin_menu', function() {
    add_options_page(
        'Battery Widget Settings',
        'Battery Widget',
        'manage_options',
        'battery-widget-settings',
        'bw_settings_page'
    );
});

function bw_settings_page() {
    if (!current_user_can('manage_options')) return;
    if (isset($_POST['bw_save'])) {
        check_admin_referer('bw_settings_nonce');
        update_option('bw_blocks', intval($_POST['bw_blocks']));
        update_option('bw_fontsize', sanitize_text_field($_POST['bw_fontsize']));
        update_option('bw_emoji', sanitize_text_field($_POST['bw_emoji']));
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    $blocks = get_option('bw_blocks', 8);
    $fontsize = get_option('bw_fontsize', '24px');
    $emoji = get_option('bw_emoji', '⚡');

    ?>
    <div class="wrap">
        <h1>Battery Widget Settings</h1>
        <form method="post">
            <?php wp_nonce_field('bw_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th>Battery Steps (blocks)</th>
                    <td><input type="number" name="bw_blocks" value="<?php echo esc_attr($blocks); ?>" min="1" max="20"></td>
                </tr>
                <tr>
                    <th>Font size</th>
                    <td><input type="text" name="bw_fontsize" value="<?php echo esc_attr($fontsize); ?>"></td>
                </tr>
                <tr>
                    <th>Charging Emoji</th>
                    <td><input type="text" name="bw_emoji" value="<?php echo esc_attr($emoji); ?>"></td>
                </tr>
            </table>
            <p><input type="submit" name="bw_save" class="button button-primary" value="Save Settings"></p>
        </form>
    </div>
    <?php
}

// ------------------------
// Widget Class
// ------------------------
class Unicode_Battery_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'Unicode_battery_widget',
            __('Unicode Battery', 'text_domain'),
            array('description' => __('Battery level in Unicode blocks with emoji when charging', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $blocks = !empty($instance['blocks']) ? intval($instance['blocks']) : get_option('bw_blocks', 8);
        $fontsize = !empty($instance['fontsize']) ? $instance['fontsize'] : get_option('bw_fontsize', '24px');
        $emoji = !empty($instance['emoji']) ? $instance['emoji'] : get_option('bw_emoji', '⚡');

        echo $args['before_widget'];
        ?>
        <div style="text-align:center; font-family:monospace; font-size:<?php echo esc_attr($fontsize); ?>;">
            <span id="bw-battery">[▱▱▱▱▱▱▱▱] 0%</span>
        </div>

        <script>
        const span = document.getElementById('bw-battery');
        
        function updateBatteryDisplay(batt){
            const p = Math.floor(batt.level*100);
            const totalBlocks = <?php echo $blocks; ?>;
            
            // const filled = Math.round(totalBlocks*p/100);
            const filled = p === 0 ? 0 : Math.max(1, Math.round(totalBlocks * p / 100));
            // Above line is an edit to ensure 0% shows empty battery (no filled blocks), 
            // and guarantees at least one filled block for any non-zero charge.
            // Examples of useful Unicode characters: ▲▼◆◇★☆↑↓→←☀☁☂🔋🟥🟩🟨 

            const empty = totalBlocks - filled;
            const battery = '' + '▰'.repeat(filled) + '▱'.repeat(empty) + ''; // []
            const chargingSymbol = batt.charging ? '<?php echo esc_js($emoji); ?>' : '';
            span.textContent = battery + ' ' + p + '%' + chargingSymbol;
        }
        if('getBattery' in navigator){
            navigator.getBattery().then(batt=>{
                updateBatteryDisplay(batt);
                batt.addEventListener('levelchange',()=>updateBatteryDisplay(batt));
                batt.addEventListener('chargingchange',()=>updateBatteryDisplay(batt));
            });
        } else { span.textContent='[?] N/A'; }
        </script>

        <?php
        echo $args['after_widget'];
    }

    public function form($instance) {
        $blocks = !empty($instance['blocks']) ? intval($instance['blocks']) : get_option('bw_blocks', 8);
        $fontsize = !empty($instance['fontsize']) ? $instance['fontsize'] : get_option('bw_fontsize', '24px');
        $emoji = !empty($instance['emoji']) ? $instance['emoji'] : get_option('bw_emoji', '⚡');
        ?>
        <p>
            <label>Battery Blocks:
                <input class="tiny-text" type="number" name="<?php echo $this->get_field_name('blocks'); ?>" value="<?php echo esc_attr($blocks); ?>" min="1" max="20">
            </label>
        </p>
        <p>
            <label>Font Size:
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('fontsize'); ?>" value="<?php echo esc_attr($fontsize); ?>">
            </label>
        </p>
        <p>
            <label>Charging Emoji:
                <input type="text" name="<?php echo $this->get_field_name('emoji'); ?>" value="<?php echo esc_attr($emoji); ?>">
            </label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['blocks'] = (!empty($new_instance['blocks'])) ? intval($new_instance['blocks']) : 8;
        $instance['fontsize'] = (!empty($new_instance['fontsize'])) ? sanitize_text_field($new_instance['fontsize']) : '24px';
        $instance['emoji'] = (!empty($new_instance['emoji'])) ? sanitize_text_field($new_instance['emoji']) : '⚡';
        return $instance;
    }
}

// ------------------------
// Register Widget
// ------------------------
add_action('widgets_init', function() {
    register_widget('Unicode_Battery_Widget');
});