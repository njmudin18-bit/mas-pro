<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Include Composer's autoloader
require_once FCPATH . 'vendor/autoload.php';

use Pusher\Pusher;

class PusherLibrary {

    private $pusher;

    public function __construct() {
        // Load the Pusher configuration file
        //$this->load->config('pusher');
        

        // Initialize the Pusher options
        $options = array(
            'cluster' => 'ap1', //$this->config->item('cluster'),
            'encrypted' => true
        );

        // Initialize the Pusher object
        $this->pusher = new Pusher(
            '5edb73018471f125ad33', //$this->config->item('app_key'),
            '6910adcf0b0006388b07', //$this->config->item('app_secret'),
            '1375913', //$this->config->item('app_id'),
            $options
        );
    }

    public function trigger($channel, $event, $data) {
        try {
            $this->pusher->trigger($channel, $event, $data);
            // Log the success (optional)
            log_message('info', 'Event triggered successfully: ' . $event . ' on channel: ' . $channel);
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Error triggering event: ' . $e->getMessage() . ' for event: ' . $event . ' on channel: ' . $channel);
        }
    }
}