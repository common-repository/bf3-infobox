<?php
/**
 * Calls bf3stats.com API
 * @author Calaelen <info@calaelen.com>
 */
class bf3stats_api
{
    protected $errormsg = '';

    /**
     * Returns Player Array or false
     * @param string $playername Origin ID
     * @param string $platform pc, ps3, 360
     * @return bool|array
     */
    public function getPlayerStats($playername, $platform)
    {
        if(!$playername = $this->sanitizePlayername($playername)) return false;

        //get Player from transient WP Cache
        if ($data = $this->loadPlayerCache($playername)) return $data;

        //no cache? get it from api
        $postdata=array();
        $postdata['player'] = $playername;
        $postdata['opt']= 'all';
        $url = 'http://api.bf3stats.com/'.$platform.'/player/';
        $args = array('body' => $postdata);

        // Send POST Request to bf3stats API - more infos: http://bf3stats.com/api
        $response = wp_remote_post( $url, $args );

        if( is_wp_error( $response ) ) {
            $this->errormsg = "BF3 Stats API error status: ".$response->get_error_message();
            return false;
        } else {
            $data=json_decode($response['body'],true);
            if($data['status'] != 'data') {
                ($data['error']) ? $errorinfos = " - ".$data['error'] : $errorinfos = '';
                $this->errormsg = "BF3stats.com API Error: " . $data['status'] . $errorinfos;
                return false;
            }
            $this->errormsg = '';
            $this->savePlayerCache($playername, $data);
            return $data;
        }

    }


    /** @return string */
    public function getErrorMsg() {
        return $this->errormsg;
    }

    /**
     * WordPress Transient Cache Loader
     * @param string $playername
     * @return array|bool
     */
    public function loadPlayerCache($playername) {
        $data = false;
        if($playername = $this->sanitizePlayername($playername)) {
            $data = get_transient( 'bf3_stats_playerdata_'.$playername );
        }
        return $data;
    }

    /**
     * WordPress Transient Cache Save
     * @param string $playername
     * @param array $data
     */
    public function savePlayerCache($playername, $data) {
        if($playername = $this->sanitizePlayername($playername) AND is_array($data)) {
            set_transient( 'bf3_stats_playerdata_'.$playername, $data, 1 * HOUR_IN_SECONDS ); //cache one hour
        }
    }

    /**
     * WordPress Transient Cache Delete
     * @param string $playername
     */
    public function clearPlayerCache($playername) {
        if($playername = $this->sanitizePlayername($playername)) {
            delete_transient( 'bf3_stats_playerdata_'.$playername );
        }
    }

    /**
     * @param string $playername
     * @return bool|string
     */
    protected function sanitizePlayername($playername) {
        $playername = trim(strtolower(esc_attr($playername)));
        if(!$playername) {
            $this->errormsg = "No valid playername";
            return false;
        } else {
            return $playername;
        }
    }

}