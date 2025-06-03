<?php
namespace App\Core;

class Response {
    public function setStatusCode(int $code) {
        // http_response_code($code);
    }

    public function redirect(string $url) {
        // header('Location: ' . $url);
        // exit;
    }

    public function json(array $data, int $statusCode = 200) {
        // header('Content-Type: application/json');
        // $this->setStatusCode($statusCode);
        // echo json_encode($data);
        // exit;
    }
}
?>
