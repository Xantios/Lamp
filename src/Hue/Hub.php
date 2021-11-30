<?php

namespace Xantios\Lamp\Hue;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Xantios\Lamp\Types\ipv4;

class Hub {

    private Client $client;
    private HubArray $hubs;

    public function __construct() {
        $this->client = new Client();
    }

    public function get(ipv4 $ip) :?HubConfig {
        foreach($this->hubs as $hub) {
            if((string)$hub->ip === (string)$ip) {
                return $hub;
            }
        }

        return null;
    }

    public function first() {
        return $this->hubs[0];
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     * @throws \Exception
     */
    public function search() :HubArray {

        $out = new HubArray();

        // Discovery API returns an array of bridges in your network
        $request = $this->client->get('https://discovery.meethue.com');
        $data = json_decode((string)$request->getBody(), false, 512, JSON_THROW_ON_ERROR);
        
        foreach($data as $item) {
            $out[] = new HubConfig(new ipv4($item->internalipaddress));
        }

        return $out;
    }
}

