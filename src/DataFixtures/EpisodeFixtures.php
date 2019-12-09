<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i=0 ; $i<25 ; $i++){
            $episode=new Episode();
            $episode->setTitle($faker->word());
            $episode->setNumber($faker->randomDigit);
            $episode->setSynopsis($faker->paragraph);
            $slugify = new Slugify();
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $episode->setSeason($this->getReference('season_'.random_int(0,10)));
            $manager->persist($episode);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
