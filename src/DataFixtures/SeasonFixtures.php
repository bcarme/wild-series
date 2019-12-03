<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 11; $i++) {
            $season = new Season();
            $season->setDescription($faker->text);
            $season->setYear($faker->year($max = 'now'));
            $season->setSeasonNumber($i);
            $season->setProgram($this->getReference('program_'.random_int(0, count(ProgramFixtures::PROGRAMS)-1)));
            $this->addReference('season_'.$i, $season);
            $manager->persist($season);
        }
        $manager->flush();
    }



    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

}
