<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ServiceFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $customer = new Service();
        $customer->setNamespace("customer");
        $customer->setUrl("http://customer-symfony-nginx/api");
        $customer->setVersion("1.0");
        $manager->persist($customer);

        $order = new Service();
        $order->setNamespace("order");
        $order->setUrl("http://order-symfony-nginx/api");
        $order->setVersion("1.0");
        $manager->persist($order);

        $kitchen = new Service();
        $kitchen->setNamespace("kitchen");
        $kitchen->setUrl("http://kitchen-symfony-nginx/api");
        $kitchen->setVersion("1.0");
        $manager->persist($kitchen);

        $delivery = new Service();
        $delivery->setNamespace("delivery");
        $delivery->setUrl("http://delivery-symfony-nginx/api");
        $delivery->setVersion("1.0");
        $manager->persist($delivery);

        $manager->flush();
    }
}
