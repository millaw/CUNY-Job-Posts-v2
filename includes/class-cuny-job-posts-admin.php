<?php
class CUNY_Job_Posts_Admin {
    private $options;

    public function __construct() {
        $this->options = get_option('cunyc_job_posts_settings');
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'settings_init'));
    }

    public function add_admin_menu() {
        add_options_page(
            'CUNY Job Posts Settings',
            'CUNY Job Posts',
            'manage_options',
            'cunyc-job-posts',
            array($this, 'settings_page')
        );
    }

    public function settings_init() {
        register_setting(
            'cunyc_job_posts_settings_group',
            'cunyc_job_posts_settings',
            array($this, 'sanitize_settings')
        );

        add_settings_section(
            'cunyc_job_posts_settings_section',
            'College Settings',
            array($this, 'settings_section_callback'),
            'cunyc-job-posts'
        );

        add_settings_field(
            'college_url',
            'College JSON Feed URL',
            array($this, 'college_url_callback'),
            'cunyc-job-posts',
            'cunyc_job_posts_settings_section'
        );
    }

    public function sanitize_settings($input) {
        $new_input = array();
        
        if (isset($input['college_url'])) {
            $new_input['college_url'] = esc_url_raw($input['college_url']);
        }

        return $new_input;
    }

    public function settings_section_callback() {
        echo '<p>Enter the JSON feed URL for your CUNY college job postings. Example: <code>https://cuny.jobs/<college>/new-jobs/feed/json</code></p>';
    }

    public function college_url_callback() {
        printf(
            '<input type="url" id="college_url" name="cunyc_job_posts_settings[college_url]" value="%s" class="regular-text" required />',
            isset($this->options['college_url']) ? esc_attr($this->options['college_url']) : ''
        );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>CUNY Job Posts Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('cunyc_job_posts_settings_group');
                do_settings_sections('cunyc-job-posts');
                submit_button();
                ?>
            </form>
            
            <h2>Shortcode Usage</h2>
            <p>Use the following shortcode to display job postings:</p>
            <p><code>[cunyc_job_posts]</code></p>
        </div>
        <?php
    }
}