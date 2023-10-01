<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
class HealthCheck {

    function __construct() {
    }

    function __invoke(Request $request, Response $response, array $args): Response {
        $response->getBody()->write(json_encode([
            'microservice' => [
                'name' => 'Warehouse Authentication Microservice',
                'status' => 'OK'
            ]
        ]));

        return $response->withStatus(201);
    }
}