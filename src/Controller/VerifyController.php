<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// https://dev.to/thedevdrawer/json-web-tokens-without-firebase-jwt-3mop
class VerifyController {

    protected ContainerInterface $container;
    protected EntityManager $em;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $this->container->get('entity');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function __invoke(Request $request, Response $response, array $args): Response {

        $jwt = json_decode($request->getBody());

        if (!$this->container->get('token')->is_valid($jwt)) {
            $response->getBody()->write(json_encode(['success' => false]));
            return $response->withStatus(401);
        }

        //$payload = $this->container->get('token')->get_payload($jwt);
        $payload = $this->container->get('token')->get_customer_payload($jwt);
        $response->getBody()->write(json_encode([
            'success' => true,
            'payload' => $payload
        ]));

        return $response->withStatus(201);
    }
}