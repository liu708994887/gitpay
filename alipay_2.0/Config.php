<?php
class Config {
    
    private $config;
    
    public function __construct() {
        global $config;
        $this->config = $config; 
    }

    public function get($key) {
        return isset($this->config[$key]) ? $this->config[$key] : null; 
    }
}


