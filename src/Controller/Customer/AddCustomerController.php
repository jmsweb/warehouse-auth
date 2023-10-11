<?php

namespace App\Controller\Customer;

use App\Domain\Customer;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;

class AddCustomerController {
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
     * @throws NotSupported
     * @throws ORMException
     */
    function __invoke(Request $request, Response $response, array $args): Response {
        $serverParams = $request->getServerParams();
        if (
            !array_key_exists('PHP_AUTH_USER', $serverParams) ||
            !array_key_exists('PHP_AUTH_PW', $serverParams)
        ) {
            $response->getBody()->write( json_encode([
                'success' => false
            ]));
            return $response->withStatus(201);
        }

        $data = json_decode($request->getBody());
        $customer = $this->em->getRepository(Customer::class)->findOneBy([
            'email' => $serverParams['PHP_AUTH_USER']
        ]);

        if ($customer) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => "Integrity constraint violation: duplicate email ({$serverParams['PHP_AUTH_USER']}) found."
            ]));
            return $response->withStatus(201);
        }

        $customer = new Customer();
        $customer->setId(Uuid::uuid4());
        $customer->setEmail($serverParams['PHP_AUTH_USER']); // dorado@attlocal.net
        $customer->setPassword(password_hash($serverParams['PHP_AUTH_PW'], PASSWORD_BCRYPT)); // 123456
        $customer->setFirstName($data->first_name);
        $customer->setLastName($data->last_name);
        $customer->setIsAdmin(filter_var($data->isAdmin, FILTER_VALIDATE_BOOLEAN));
        $customer->setCreateDate(new DateTime());
        $this->em->persist($customer);
        $this->em->flush();
        $response->getBody()->write( json_encode([
            'success' => true,
            'message' => "Added {$serverParams['PHP_AUTH_USER']} - $customer->firstName $customer->lastName (".($customer->isAdmin ? 'Admin' : 'Customer').")"
        ]));
        // <b>Added</b> {customer.email} - {customer.firstName} {customer.lastName} ({customer.isAdmin ? 'Admin': 'Customer'})
        return $response->withStatus(201);
    }
}