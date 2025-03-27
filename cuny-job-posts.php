<?php
/**
 * Plugin Name: CUNY Job Posts
 * Plugin URI: https://github.com/millaw/cuny-job-posts-v2
 * Description: Job postings for CUNY colleges. Use the shortcode [cunyc_job_posts] to display current job openings.
 * Version: 1.2.0
 * Author: Milla Wynn
 * Author URI: https://github.com/millaw
 * License: GPLv2
 * Text Domain: cunyc-job-posts
 */

defined('ABSPATH') || exit;

// Define plugin constants
define('CUNY_JP_VERSION', '1.2.0');
define('CUNY_JP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CUNY_JP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'CUNY_Job_Posts_';
    $base_dir = CUNY_JP_PLUGIN_DIR . 'includes/';
    
    if (strpos($class, $prefix) !== 0) {
        return;
    }
    
    $relative_class = substr($class, strlen($prefix));
    $file = $base_dir . 'class-' . strtolower(str_replace('_', '-', $relative_class)) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
require_once CUNY_JP_PLUGIN_DIR . 'includes/class-cuny-job-posts.php';
new CUNY_Job_Posts();