<?php

namespace Xantios\Lamp\Hue;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Xantios\Lamp\Types\ipv4;

class ApiClient
{

    private Client $client;

    public function __construct(ipv4 $address)
    {
        $this->client = new Client([
            'base_uri' => 'http://' . $address . '/api/',
            'timeout' => 5,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function register() :ApiResponse
    {
        $apiResponse = new ApiResponse();

        try {
            $resp = $this->client->post("", [
                'body' => json_encode(['devicetype' => 'Commandline Light Controller'], JSON_THROW_ON_ERROR)
            ]);

            $data = json_decode((string)$resp->getBody(), false, 512, JSON_THROW_ON_ERROR);

            if(isset($data[0]->error)) {
                $apiResponse->setError($data[0]->error->description);
            }

            $apiResponse->setData($data[0]);
            return $apiResponse;

        } catch (GuzzleException $e) {
            $apiResponse->setError("Cant connect " . $e->getMessage());
            return $apiResponse;
        } catch (\JsonException $e) {
            $apiResponse->setError("Cant parse " . $e->getMessage());
            return $apiResponse;
        }
    }
}