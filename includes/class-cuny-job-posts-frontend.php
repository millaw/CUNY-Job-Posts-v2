<?php
class CUNY_Job_Posts_Frontend {
    private $options;

    public function __construct() {
        $this->options = get_option('cunyc_job_posts_settings');
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('cunyc_job_posts', array($this, 'shortcode'));
    }

    public function enqueue_assets() {
        wp_register_script(
            'cunyc_job_posts_script',
            CUNY_JP_PLUGIN_URL . 'assets/js/cuny-jp-script.js',
            array('jquery'),
            CUNY_JP_VERSION,
            true
        );
        
        wp_register_style(
            'cunyc_job_posts_style',
            CUNY_JP_PLUGIN_URL . 'assets/css/cuny-jp-style.css',
            false,
            CUNY_JP_VERSION,
            'all'
        );

        wp_enqueue_script('cunyc_job_posts_script');
        wp_enqueue_style('cunyc_job_posts_style');
    }

    public function shortcode($atts) {
        if (!isset($this->options['college_url']) || empty($this->options['college_url'])) {
            return '<p>Please configure the college URL in the CUNY Job Posts settings page.</p>';
        }

        $college_url = esc_url_raw($this->options['college_url']);
        return $this->get_feed($college_url);
    }

    private function get_feed($url) {
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return '<p>Error fetching job postings. Please try again later.</p>';
        }

        $json = wp_remote_retrieve_body($response);
        $data = json_decode($json, true);

        if (empty($data)) {
            return '<p>No job postings at this moment. Please come back and check later.</p>';
        }

        $output = '';
        foreach ($data as $job) {
            $output .= $this->generate_job_card($job);
        }

        return $output;
    }

    private function generate_job_card($job) {
        $job_title = esc_html($job['title']);
        $job_url = esc_url($job['url']);
        $job_date = esc_html(date('F d, Y', strtotime($job['date_new'])));
        $job_id = esc_html($job['reqid']);
        $job_description = esc_html($job['description']);

        ob_start();
        ?>
        <div class="job-card">
            <h3><a href="<?php echo $job_url; ?>" target="_blank"><?php echo $job_title; ?></a></h3>
            <p><strong>Date Posted:</strong> <?php echo $job_date; ?></p>
            <p><strong>Job ID:</strong> <?php echo $job_id; ?></p>
            <div class="card-title"><strong>Apply Now:</strong> 
                <a href="https://home.cunyfirst.cuny.edu/psp/cnyihprd/EMPLOYEE/HRMS/c/HRS_HRAM_EMP_FL.HRS_CG_SEARCH_FL.GBL?Page=HRS_APP_JBPST_FL&Action=U&FOCUS=Employee&SiteId=1&PostingSeq=1&JobOpeningId=<?php echo $job_id; ?>" target="_blank">Current CUNY Employees</a> | 
                <a href="https://hrsa.cunyfirst.cuny.edu/psp/erecruit/EMPLOYEE/HRMSCG/c/HRS_HRAM_FL.HRS_CG_SEARCH_FL.GBL?Page=HRS_APP_JBPST_FL&Action=U&FOCUS=Applicant&SiteId=1&PostingSeq=1&JobOpeningId=<?php echo $job_id; ?>" target="_blank">External Applicants</a>
            </div>
            <a class="cuny-jp-accordion" onclick="toggleContent('tab-<?php echo $job_id; ?>')">More info...</a>
            <div class="cuny-jp-panel" id="tab-<?php echo $job_id; ?>">
                <div class="more-content">
                    <p><strong>POSITION DETAILS:</strong></p>
                    <p><?php echo $job_description; ?></p>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}