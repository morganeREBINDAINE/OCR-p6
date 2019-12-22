<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TrickFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct( UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

//        for ($i=0; $i<30; $i++) {
//            $trick = new Trick();
//            $trick
//                ->setName('test')
//                ->setDescription('description')
//                ->setGroup('lol');
//             $manager->persist($trick);
//        }

        $user = new User();
        $password = $this->encoder->encodePassword($user, 'coucou');
        $user->setUsername('morgane')
            ->setPassword($password)
            ->setEmail('mrebindaine@hotmail.com');
        $manager->persist($user);

        $manager->flush();
    }
}
