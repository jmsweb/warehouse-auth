<?php

namespace App\Controller\Customer;

use App\Domain\Customer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCustomerController {

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
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     * @throws NotSupported
     */
    function __invoke(Request $request, Response $response, array $args): Response {
        if (isset($args['uuid']) && strlen($args['uuid']) === 36) {
            $customer = $this->em->find(Customer::class, $args['uuid']);
            $response->getBody()->write(json_encode($customer));
            return $response->withStatus(201);
        }
        $customers = $this->em->getRepository(Customer::class)->findAll();
        $response->getBody()->write(json_encode($customers));
        return $response->withStatus(201);
    }
}