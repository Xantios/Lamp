<?php

namespace Xantios\Lamp;

use JsonException;
use stdClass;

use Xantios\Lamp\Types\ipv4;

class Config
{
    public bool $valid;
    public ipv4 $hub;
    public string $username;

    private string $path;
    private array $paths;

    public function __construct()
    {
        $home = posix_getpwuid(posix_getuid())['dir'];

        $this->paths = [
            $home."/.lamp.json",
            dirname(__DIR__) . "/config.json",
            $home."/lamp.json",
        ];

        try {
            $config = $this->readConfig();

            $this->valid = true;
            $this->hub = new ipv4($config->hub);
            $this->username = $config->username;
        } catch (JsonException $e) {
            $this->valid = false;
            $this->hub = new ipv4("0.0.0.0");
            $this->username = "";
            $this->path = "";
        }
    }

    public function store() :string
    {
        $that = (string)$this;

        if($this->path === "") {
            $this->path = $this->paths[0];
        }

        file_put_contents($this->path,$that);
        return $this->path;
    }

    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        $temp = get_object_vars($this);
        foreach($temp as $key => $value) {
            @$temp[$key] = (string)$value;
        }
        return json_encode($temp, JSON_THROW_ON_ERROR);
    }

    public function valid() :bool
    {
        return $this->valid;
    }

    public function hub(): ipv4
    {
        return $this->hub;
    }

    public function username(): string
    {
        return $this->username;
    }

    /**
     * @throws JsonException
     */
    private function readConfig(): stdClass
    {
        foreach($this->paths as $path) {
            if(file_exists($path)) {
                $configFile = file_get_contents($path);
                $this->path = $path;
                return json_decode($configFile, false, 512, JSON_THROW_ON_ERROR);
            }
        }

        throw new \JsonException("File not found",254,null);
    }
}