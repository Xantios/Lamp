<?php

namespace Xantios\Lamp\Hue;

class ApiResponse {

    public bool $error = false;
    public string $message = "";
    public mixed $data;

    public function setError(string $msg) :void {
        $this->error = true;
        $this->message = $msg;
    }

    public function setMessage(string $msg) :void {
        $this->error = false;
        $this->message = $msg;
    }

    public function setData(mixed $data) :void {
        $this->data = $data;
    }
}