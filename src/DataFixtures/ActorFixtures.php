<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Norman Reedus',
        'Melissa McBride',
        'Lauren Cohan',
        'Danai Gurira'
    ];

    protected $faker;

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('us_US');

        // on créé 10 personnes
        for ($i = 0; $i < 10; $i++) {
            $actorFake = new Actor();
            $actorFake->setName($faker->name);
            $manager->persist($actorFake);
            $this->addReference('actor_' . $i, $actorFake);
            $actorFake->addProgram($this->getReference('program_'.random_int(0, count(ProgramFixtures::PROGRAMS)-1)));
        }

        foreach (self::ACTORS as $key => $actorName){
            $actor = new Actor();
            $actor->setName($actorName);
            $manager->persist($actor);
            $actor->addProgram($this->getReference('program_0'));

        }
        $manager->flush();
    }


    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

}
