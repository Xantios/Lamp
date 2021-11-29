<?php

namespace Xantios\Lamp\Hue;

use \ArrayAccess;
use Xantios\Lamp\Types\ipv4;
use Xantios\Lamp\Types\TypedArray;


class HubConfig {

    public string $name = "";
    public ipv4 $ip;
    public bool $available = false;
    private \GuzzleHttp\Client $apiClient;

    public function __construct(ipv4 $bridgeIp) {
        $this->ip = $bridgeIp;
        $this->client = new \GuzzleHttp\Client();
    }

    public function getIp() :string {
        return $this->ip;
    }

    public function setIp($newIp) :bool {
        $this->ip = $newIp;
        return true;
    }

    public function isAvailable() :bool {
        return $this->available;
    }   

    private function pingTest() :bool {
        // 
    }
}

class Hub {

    private \GuzzleHttp\Client $client;
    private HubArray $hubs;

    public function __construct() {
        $this->client = new \GuzzleHttp\Client();
        $this->hubs = $this->search();
    }

    public function get(ipv4 $ip) :HubConfig {
        foreach($this->hubs as $hub) {
            if((string)$hub->ip == (string)$ip) {
                return $hub;
            }
        }

        return [];
    }

    public function first() {
        return $this->hubs[0];
    }

    public function search() :HubArray {

        $out = new HubArray();

        // Discovery API returns an array of bridges in your network
        $request = $this->client->get('https://discovery.meethue.com');
        $data = json_decode((string)$request->getBody());
        
        foreach($data as $item) {
            $out[] = new HubConfig(new ipv4($item->internalipaddress));
        }

        return $out;
    }
}

class HubArray extends TypedArray {
    public string $type = "Xantios\Lamp\Hue\HubConfig";
}