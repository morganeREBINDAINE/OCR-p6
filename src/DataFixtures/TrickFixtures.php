<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class TrickFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i=0; $i<30; $i++) {
            $trick = new Trick();
            $trick
                ->setName($faker->sentence(5, false))
                ->setDescription($faker->paragraph(10));
             $manager->persist($trick);
        }

        $manager->flush();
    }
}
