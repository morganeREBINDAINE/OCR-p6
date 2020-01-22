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
        // 10 tricks
        $tricks = [
            [
                'name' => 'One-Two',
                'description' => 'Un trick dans lequel la main du snowboarder attrape le coin de la planche situé derrière son pied arrière.',
                'trickGroup' => 'grab',
            ],
            [
                'name' => 'Back-side Air',
                'description' => 'Le grab star du snowboard qui peut être fait d\'autant de façon différentes qu\'il y a de styles de riders. Il consiste à attraper la carre arrière entre les pieds, ou légèrement devant, et à pousser avec sa jambe arrière pour ramener la planche devant. C\'est une figure phare en pipe ou sur un hip en backside. C\'est généralement avec ce trick que les riders vont le plus haut.',
                'trickGroup' => 'grab',
            ],
            [
                'name' => 'Switch',
                'description' => ' Lorsque l\'on ride de son mauvais côté, tous les noms de figures sont précédées de la dénomination switch. Un regular fera donc ses tricks en switch, comme un goofie, et inversement.',
                'trickGroup' => 'rotation',
            ],
            [
                'name' => 'Mc Twist',
                'description' => 'Un grand classique des rotations tête en bas qui se fait en backside, sur un mur backside de pipe. Le Mc Twist est généralement fait en japan, un grab très tweaké (action d\'accentuer un grab en se contorsionnant).',
                'trickGroup' => 'rotation',
            ],
            [
                'name' => 'Cork',
                'description' => ' Le diminutif de corkscrew qui signifie littéralement tire-bouchon et désignait les premières simples rotations têtes en bas en frontside. Désormais, on utilise le mot cork à toute les sauces pour qualifier les figures où le rider passe la tête en bas, peu importe le sens de rotation. Et dorénavant en compétition, on parle souvent de double cork, triple cork et certains riders vont jusqu\'au quadruple cork !',
                'trickGroup' => 'flip',
            ],
            [
                'name' => 'Handplant',
                'description' => 'Un trick inspiré du skate qui consiste à tenir en équilibre sur une ou deux mains au sommet d\'une courbe. Existe avec de nombreuses variantes dans les grabs et les rotations.',
                'trickGroup' => 'slide',
            ],
            [
                'name' => 'Crippler',
                'description' => 'Une autre rotation tête en bas classique qui s\'apparente à un backflip sur un mur frontside de pipe ou un quarter.',
                'trickGroup' => 'one_foot',
            ],
            [
                'name' => 'Rotation frontside et backside',
                'description' => ' Un snowboarder peut faire des rotations déclenchées du côté de ses pointes de pied, en frontside ou de ses talons, en backside. On parle aussi de frontside et backside pour les murs de halfpipe et les hips. Les rotations vont du demi-tour en 180 degrés jusqu\'à des 1800 degrés, soit cinq tours !',
                'trickGroup' => 'rotation',
            ],
            [
                'name' => '270',
                'description' => 'Désigne le degré de rotation, soit 3/4 de tour, fait en entrée ou en sortie sur un jib. Certains riders font également des rotations en 450 degrés avant ou après les jibs.',
                'trickGroup' => 'rotation',
            ],
            [
                'name' => 'Air to fakie',
                'description' => 'En pipe, sur un quarter ou un hip, ce terme désigne un saut sans rotation où le rider retombe dans le sens inverse.',
                'trickGroup' => 'old_school',
            ],
        ];

        foreach ($tricks as $trickAttributes) {
            $trick = new Trick();
            foreach ($trickAttributes as $trickAttribute => $trickAttributeValue) {
                $setter = 'set' . ucfirst($trickAttribute);
                $trick->$setter($trickAttributeValue);
            }
            $manager->persist($trick);
        }

        $manager->flush();
    }
}
