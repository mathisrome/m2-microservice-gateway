<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApplicationFixtures extends Fixture
{
    public function __construct(
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $customer = new Application();
        $customer->setUsername("api-customer");
        $customer->setApiKey(uniqid("", true));
        $manager->persist($customer);

        $order = new Application();
        $order->setUsername("api-order");
        $order->setApiKey(uniqid("", true));
        $manager->persist($order);

        $kitchen = new Application();
        $kitchen->setUsername("api-kitchen");
        $kitchen->setApiKey(uniqid("", true));
        $manager->persist($kitchen);

        $delivery = new Application();
        $delivery->setUsername("api-delivery");
        $delivery->setApiKey(uniqid("", true));
        $manager->persist($delivery);

        $manager->flush();
    }
}
