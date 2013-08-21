<?php
class firewall extends base {
    
    public $ips = array();
    
    function blockIp($ip = "*.*.*.*") {
        $this->ips["block"][] = $ip;
        return $this;
    }
    function blockIps($ips = array("*.*.*.*")) {
        foreach($ips as $ip)
            $this->blockIp($ip);
        return $this;
    }
    function allowIp($ip = "*.*.*.*") {
        $this->ips["allow"][] = $ip;
        return $this;
    }
    function allowIps($ips = array("*.*.*.*")) {
        foreach($ips as $ip)
            $this->allowIp($ip);
        return $this;
    }
    function reservedArea() {
        foreach($this->ips["allow"] as $allowedip) {
            $star = strpos($allowedip, "*");
            $searchfor = substr($allowedip, 0, $star);
            if(substr($_SERVER["REMOTE_ADDR"], 0, strlen($searchfor)) == $searchfor)
                return true;
        }
        foreach($this->ips["block"] as $blockedip) {
            $star = strpos($blockedip, "*");
            $searchfor = substr($blockedip, 0, $star);
            if(substr($_SERVER["REMOTE_ADDR"], 0, strlen($searchfor)) == $searchfor)
                return false;
        }
        return true;
    }
}
