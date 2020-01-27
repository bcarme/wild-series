<?php

namespace App\DataFixtures;

use App\Service\Slugify;
use Faker;
use App\Entity\Actor;
use App\Entity\Program;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('us_US');

        for ($i = 1; $i <= 100; $i++) {
            $category = new Category();
            $category->setName($faker->word);
            $manager->persist($category);
            $this->addReference('category_'.$i, $category);

            $program = new Program();
            $slugify = new Slugify();
            $program->setTitle($faker->sentence(4, true));
            $program->setSummary($faker->text(100));
            $program->setCategory($this->getReference('category_'.$i));
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $this->addReference('program_'.$i, $program);
            $manager->persist($program);

            for($j = 1; $j <= 5; $j ++) {
                $actor = new Actor();
                $actor->setName($faker->firstName);
                $actor->addProgram($this->getReference('program_'.$i));
                $actor->setSlug($slugify->generate($actor->getName()));
                $manager->persist($actor);
            }

        }

        $manager->flush();

    }
}
