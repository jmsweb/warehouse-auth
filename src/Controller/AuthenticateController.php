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
class AuthenticateController {

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
     * @throws NotSupported
     */
    function __invoke(Request $request, Response $response, array $args): Response {
        $serverParams = $request->getServerParams();
        if (
            array_key_exists('PHP_AUTH_USER', $serverParams) &&
            array_key_exists('PHP_AUTH_PW', $serverParams)
        ) {
            // dorado@attlocal.net, 123456
            /* @var Customer $customer */
            $customer = $this->em->getRepository(Customer::class)
                ->findOneBy([
                    'email' => $serverParams['PHP_AUTH_USER']
                ]);

            if ($customer && password_verify($serverParams['PHP_AUTH_PW'], $customer->getPassword())) {
                // Generate JWT
                $jwt = $this->container->get('token')->generate([
                    'iss' => 'warehouse-auth',
                    'aud' => $_ENV['COOKIE_DOMAIN'],
                    'name' => "{$customer->getFirstName()} {$customer->getLastName()}",
                    'email' => $customer->getEmail(),
                    'customer_id' => $customer->getId()
                ]);

                $response->getBody()->write(json_encode([
                    'success' => true,
                    'jwt' => $jwt
                ]));

                return $response->withStatus(201);
            }
        }

        $response->getBody()->write(json_encode( [ 'success' => false ] ));
        return $response->withStatus(201);
    }
}