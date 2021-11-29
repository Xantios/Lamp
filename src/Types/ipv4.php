<?php

namespace Xantios\Lamp\Types;

class ipv4 {

    public string $value;

    public function __construct(string $ip) {

        if(!filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)) {
            throw new \Exception(sprintf("Invalid IP %s",$ip));
        }

        $this->value = $ip;
    }

    public function __ToString() {
        return (string)$this->value;
    }
}