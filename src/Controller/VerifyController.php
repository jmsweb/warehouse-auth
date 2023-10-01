<?php

namespace App\Controller;

use App\Domain\Customer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
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

        $response->getBody()->write(json_encode([
            'success' => $this->container->get('token')->is_valid(json_decode($request->getBody()))
        ]));

        return $response->withStatus(201);
    }
}