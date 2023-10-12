<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Service\Token;

// https://dev.to/thedevdrawer/json-web-tokens-without-firebase-jwt-3mop
class ExtendController {

    protected ContainerInterface $container;
    protected EntityManager $em;
    protected Token $token;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $this->container->get('entity');
        $this->token = $this->container->get('token');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws NotSupported
     */
    function __invoke(Request $request, Response $response, array $args): Response {
        $jwt = json_decode($request->getBody());

        if ($this->token->is_valid($jwt)) {
            $payload = $this->token->get_customer_payload($jwt);
            $jwt = $this->token->generate([
                'iss' => 'warehouse-auth',
                'aud' => $_ENV['COOKIE_DOMAIN'],
                'name' => $payload['name'],
                'admin' => $payload['admin'],
                'email' => $payload['email'],
                'id' => $payload['id'],
            ]);

            $response->getBody()->write(json_encode([
                'success' => true,
                'jwt' => $jwt,
                'payload' => $payload
            ]));

            return $response->withStatus(201);
        }

        $response->getBody()->write(json_encode( [ 'success' => false ] ));
        return $response->withStatus(201);
    }
}