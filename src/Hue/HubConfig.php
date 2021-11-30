<?php

namespace Xantios\Lamp\Hue;

use Xantios\Lamp\Types\ipv4;

class HubConfig
{
    public string $name = "";
    public ipv4 $ip;
    public bool $available = false;
    private \GuzzleHttp\Client $apiClient;

    public function __construct(ipv4 $bridgeIp)
    {
        $this->ip = $bridgeIp;
        $this->client = new \GuzzleHttp\Client();
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp($newIp): bool
    {
        $this->ip = $newIp;
        return true;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    private function pingTest(): bool
    {
        //
        return false;
    }
}