<?php

namespace PhpFinance\Lib;

class JsonResponse
{
    private $data;
    private $code;

    public function __construct($data, $code) {
        $this->data = $data;
        $this->code = $code;
    }

    public function process()
    {
        http_response_code($this->code);
        $json = json_encode([
            'data' => $this->data
        ]);
        
        return $json;
    }
}
