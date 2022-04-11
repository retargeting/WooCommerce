<?php
class WooCommerceRTG
{
    private $getList = [ 'customers', 'products', 'products-cron','products-cron-now', 'products-static', 'products-cron-next'];
    private $option;
    /**
     * WooCommerceRTG constructor.
     */
    public function __construct()
    {
        $this->option = get_option('woocommerce_rtg_tracker_settings');

        if($this->getOption('rtg_products_feed_cron')){
            /* RTG CRON START */
            /* $CRON = wp_next_scheduled( 'RTG_CRON_FEED' ); */
            add_filter( 'cron_schedules', [$this, 'RTG_CRON_SCHEDULES'] );
 /*
            if ( !$CRON ) {
                 wp_schedule_event( time(), 'RTG_CRON_SCHEDULES', 'RTG_CRON_FEED' );
            }
 */
            add_action( 'RTG_CRON_FEED', [ $this, 'RTG_CRON_FUNC' ] );           
        }
        /* RTG CRON END */

        add_action( 'plugins_loaded', [ $this, 'init' ] );
        add_action( 'template_redirect', [ $this, 'templateRedirect' ] );
 
    }
    
    /** 
     * Initialise
     */
    public function init()
    {
        if (class_exists('WC_Integration') && !$this->isFeed())
        {
            include_once RTG_TRACKER_DIR . '/includes/class-rtg-integration.php';

            add_filter( 'woocommerce_integrations', [ $this, 'addIntegration' ] );
            add_filter( 'plugin_action_links', [ $this, 'addAction' ],10,2);
        }
    }

    /**
     * WooCommerce integrations hook
     *
     * @param $integrations
     * @return array
     */
    public function addIntegration($integrations)
    {
        $integrations[] = 'WooCommerceRTGIntegration';

        return $integrations;
    }

    /**
     * Add Action Button Settings
     */
    public function addAction($actions, $plugin_file='')
    {
        if($plugin_file === 'woocommerce-retargeting/woocommerce-retargeting.php'){
            array_unshift($actions,'<a href="'.admin_url('/admin.php?page=wc-settings&tab=integration&section=rtg_tracker').'" target="_blank" rel="noopener noreferrer">Settings</a>');
        }
        return $actions;
    }

    /* CRON JOBS */
    function RTG_CRON_SCHEDULES( $schedules ) {
        $schedules['RTG_CRON_SCHEDULES'] = array(
                'interval'  => 10800, /*10800 60 * 5 = 300| 60*60*3 = 10800 */
                'display'   => __( 'RTG_CRON_SCHEDULES', 'RTG' )
        );

        return $schedules;
    }

    function RTG_CRON_FUNC() {
        $_GET['rtg-feed'] = 'products-cron';
        $_GET['isCronInternal'] = 'true';
        $this->genFeed();
        // $res = file_get_contents(get_site_url().'?rtg-feed=products-cron', false, $context);
    }
    /* CRON JOBS STOP */
    function rtgDisable(){
        $CRON = wp_next_scheduled( 'RTG_CRON_FEED' );

        if($CRON){
            wp_unschedule_event( $CRON, 'RTG_CRON_FEED' );
        }

        wp_clear_scheduled_hook('RTG_CRON_SCHEDULES');
    }

    function rtgInstall(){
        if($this->getOption('rtg_products_feed_cron') && !wp_next_scheduled( 'RTG_CRON_FEED' )){
            chmod(RTG_TRACKER_DIR, 0755);
            wp_schedule_event( time(), 'RTG_CRON_SCHEDULES', 'RTG_CRON_FEED' );
        }
    }

    /**
     * Template redirect hook
     */
    public function templateRedirect()
    {
        if ($this->isFeed())
        {
            $this->genFeed();
        }
    }
    function sh_get_next_cron_time( $cron_name ){

        foreach( _get_cron_array() as $timestamp => $crons ){
            if( in_array( $cron_name, array_keys( $crons ) ) ){
                return [date('d.F.Y H:i:s', $timestamp), date('d.F.Y H:i:s')];
            }
        }
        return false;
    }
    function genFeed(){
        include_once RTG_TRACKER_DIR . '/includes/class-rtg-feed.php';
        /**
         * Initialise feed
         */
        $RTGFeed = new WooCommerceRTGFeed();

        try
        {
            switch ($_GET['rtg-feed'])
            {
                case 'customers':
                    $this->doOption('rtg_customers_feed',[ $RTGFeed, 'getCustomers', null]);
                    break;
                case 'products':
                    $this->doOption('rtg_products_feed',[
                        $RTGFeed, 'productsCSV','doLive'
                    ]);
                    break;
                case 'products-cron':
                    $this->doOption('rtg_products_feed_cron', [
                        $RTGFeed, 'productsCSV','doCron'
                    ]);
                    break;
                case 'products-cron-now':
                    file_get_contents(get_site_url().'?rtg-feed=products-cron', false, $context);
                    break ;
                case 'products-static':
                    $this->doOption('rtg_products_feed_cron',[
                        $RTGFeed, 'productsCSV','doStatic'
                    ]);
                    break;
                case 'products-cron-next':
                    var_dump($this->sh_get_next_cron_time('RTG_CRON_FEED'));
                    break;
            }
        }
        catch (Exception $ex)
        {
            $errors = error_get_last();
            $myfile = fopen(RTG_TRACKER_DIR . '/RTG.log', "w+") or die("Unable to open file!");
            
            fwrite($myfile, "COPY ERROR: ".$errors['type']);
            fwrite($myfile, "<br />\n".$errors['message']);
            fwrite($myfile, "<br />\n".json_encode($errors));
            fwrite($myfile, "<br />\n".$ex->getMessage());

            fclose($myfile);
        }
        exit(0);
    }

    function doOption($opt, array $action)
    {
        $this->option[$opt] = isset( $this->option[$opt] ) ? $this->option[$opt] : 'yes';
        return $this->option[$opt] == 'yes' ? $action[0]->{$action[1]}($action[2]) : false;
    }

    function getOption($opt, $type = true)
    {
        if ($type) {
            $this->option[$opt] = isset( $this->option[$opt] ) ? $this->option[$opt] : 'yes';
            return $this->option[$opt] == 'yes';
        }

        return isset( $this->option[$opt] ) ? $this->option[$opt] : '';
    }

    /**
     * Check for feed query string
     *
     * @return bool
     */
    private function isFeed()
    {
        return isset( $_GET['rtg-feed'] ) && in_array( $_GET['rtg-feed'], $this->getList );
    }
}