<?php

namespace App\Service;

class Token {

    private array $headers;
    private string $secret;

    public function __construct() {
        $this->headers = [
            'alg' => 'HS256',
            'typ' => 'JWT',
            'iss' => 'warehouse-auth',
            'aud' => 'dorado-pc.attlocal.net'
        ];
        $this->secret = 'SomeSuperSecret';
    }

    public function generate(array $payload): string {
        $headers = $this->encode(json_encode($this->headers));
        $payload['exp'] = time() + 600;
        $payload = $this->encode(json_encode($payload));
        $signature = hash_hmac('SHA256', $headers.$payload, $this->secret, true);
        $signature = $this->encode($signature);

        //JWT = header.payload.signature !!MUST CONCAT PERIOD (.) IN JWT!!
        return "$headers.$payload.$signature";
    }

    private function encode(string $str): string {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    public function is_valid(string $jwt): bool {
        $token = explode('.', $jwt);

        if (!isset($token[1]) && !isset($token[2])) {
            return false;
        }

        $header = base64_decode($token[0]);
        $payload = base64_decode($token[1]);
        $signature = $token[2];

        if (!json_decode($payload)) {
            return false;
        }

        if ((json_decode($payload)->exp - time()) < 0) {
            return false;
        }

        if (isset(json_decode($header)->iss) && isset(json_decode($payload)->iss)) {
            if (json_decode($header)->iss != json_decode($payload)->iss) {
                return false;
            }
        } else {
            return false;
        }

        if (isset(json_decode($header)->aud) && isset(json_decode($payload)->aud)) {
            if (json_decode($header)->aud != json_decode($payload)->aud) {
                return false;
            }
        } else {
            return false;
        }

        $base64_header = $this->encode($header);
        $base64_payload = $this->encode($payload);
        $base64_signature = $this->encode(
            hash_hmac(
                'SHA256',
                $base64_header.$base64_payload,
                $this->secret,
                true
            )
        );

        return ($base64_signature === $signature);
    }
}