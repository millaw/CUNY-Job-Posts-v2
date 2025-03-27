<?php
class CUNY_Job_Posts {
    public function __construct() {
        $this->includes();
        $this->init_classes();
    }

    private function includes() {
        require_once CUNY_JP_PLUGIN_DIR . 'includes/class-cuny-job-posts-admin.php';
        require_once CUNY_JP_PLUGIN_DIR . 'includes/class-cuny-job-posts-frontend.php';
    }

    private function init_classes() {
        new CUNY_Job_Posts_Admin();
        new CUNY_Job_Posts_Frontend();
    }
}