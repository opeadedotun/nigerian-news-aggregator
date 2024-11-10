<?php
/*
Plugin Name: Nigerian News Headlines Aggregator
Description: Aggregates headlines from major Nigerian gossip and news blogs
Version: 1.0
Author: Opeyemi Adedotun
*/

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Register activation hook
register_activation_hook(__FILE__, 'nga_activate_plugin');

function nga_activate_plugin() {
    // Create custom table for caching feeds
    global $wpdb;
    $table_name = $wpdb->prefix . 'nga_cached_feeds';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        source_url varchar(255) NOT NULL,
        title text NOT NULL,
        excerpt text NOT NULL,
        link varchar(255) NOT NULL,
        pub_date datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Add menu item to WordPress admin
add_action('admin_menu', 'nga_add_admin_menu');

function nga_add_admin_menu() {
    add_menu_page(
        'Nigerian News Aggregator Settings',
        'News Aggregator',
        'manage_options',
        'nigerian-news-aggregator',
        'nga_settings_page',
        'dashicons-rss',
        100
    );
}

// Create the settings page
function nga_settings_page() {
    ?>
    <div class="wrap">
        <h2>Nigerian News Aggregator Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('nga_options');
            do_settings_sections('nga_options');
            ?>
            <table class="form-table">
                <tr>
                    <th>Update Interval (minutes)</th>
                    <td>
                        <input type="number" name="nga_update_interval" 
                               value="<?php echo get_option('nga_update_interval', 30); ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings
add_action('admin_init', 'nga_register_settings');

function nga_register_settings() {
    register_setting('nga_options', 'nga_update_interval');
}

// Function to fetch and parse RSS feeds
function nga_fetch_feeds() {
    $feeds = array(
        'https://www.naijanews.com/feed/',
        'https://www.legit.ng/rss/',
        'https://www.yabaleftonline.ng/feed/'
    );
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'nga_cached_feeds';
    
    // Clear old entries
    $wpdb->query("TRUNCATE TABLE $table_name");
    
    foreach ($feeds as $feed_url) {
        $rss = fetch_feed($feed_url);
        
        if (!is_wp_error($rss)) {
            $max_items = $rss->get_item_quantity(10);
            $rss_items = $rss->get_items(0, $max_items);
            
            foreach ($rss_items as $item) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'source_url' => $feed_url,
                        'title' => $item->get_title(),
                        'excerpt' => wp_trim_words($item->get_description(), 20),
                        'link' => $item->get_permalink(),
                        'pub_date' => $item->get_date('Y-m-d H:i:s')
                    )
                );
            }
        }
    }
}

// Schedule feed updates
add_action('init', 'nga_schedule_updates');

function nga_schedule_updates() {
    if (!wp_next_scheduled('nga_feed_update')) {
        wp_schedule_event(time(), 'nga_custom_interval', 'nga_feed_update');
    }
}

// Add custom interval
add_filter('cron_schedules', 'nga_add_cron_interval');

function nga_add_cron_interval($schedules) {
    $interval = get_option('nga_update_interval', 30);
    
    $schedules['nga_custom_interval'] = array(
        'interval' => $interval * 60,
        'display' => sprintf(__('Every %d minutes'), $interval)
    );
    
    return $schedules;
}

// Hook for scheduled updates
add_action('nga_feed_update', 'nga_fetch_feeds');

// Shortcode to display headlines
add_shortcode('nigerian_news', 'nga_display_headlines');

function nga_display_headlines($atts) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'nga_cached_feeds';
    
    $headlines = $wpdb->get_results(
        "SELECT * FROM $table_name ORDER BY pub_date DESC LIMIT 30"
    );
    
    $output = '<div class="nga-headlines">';
    
    foreach ($headlines as $headline) {
        $output .= sprintf(
            '<div class="nga-headline">
                <h3><a href="%s" target="_blank">%s</a></h3>
                <p class="nga-excerpt">%s</p>
                <p class="nga-meta">Source: %s | Published: %s</p>
            </div>',
            esc_url($headline->link),
            esc_html($headline->title),
            esc_html($headline->excerpt),
            esc_html(parse_url($headline->source_url, PHP_URL_HOST)),
            date('F j, Y g:i a', strtotime($headline->pub_date))
        );
    }
    
    $output .= '</div>';
    
    // Add CSS styles
    $output .= '
    <style>
        .nga-headlines {
            max-width: 800px;
            margin: 0 auto;
        }
        .nga-headline {
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .nga-headline h3 {
            margin: 0 0 10px 0;
        }
        .nga-headline a {
            color: #333;
            text-decoration: none;
        }
        .nga-headline a:hover {
            color: #0073aa;
        }
        .nga-excerpt {
            color: #666;
            margin: 10px 0;
        }
        .nga-meta {
            font-size: 0.8em;
            color: #999;
        }
    </style>';
    
    return $output;
}