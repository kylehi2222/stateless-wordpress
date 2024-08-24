<?php

if(!class_exists('PeepSo3_Site_Health')) {
    require_once(dirname(__FILE__) . '/site_health.php');
    //new PeepSoError('Autoload issue: PeepSo3_Site_health not found ' . __FILE__);
}

class PeepSo3_Site_Health_PeepSo_Online extends PeepSo3_Site_Health {

    public function test() {

        // Uncomment to force the error
        // $override = 1;

        $test = PeepSoCom_Connect::is_offline() ? 1 : 0;
        if(isset($override)) {
            $test = $override;
        }

        $this->label = __('A connection to PeepSo.com servers is required', 'peepso-core');

        if($test > 0) {
            $this->status = 'critical';
        }

        return $this->result();
    }
}

PeepSo3_Site_Health_PeepSo_Online::get_instance();