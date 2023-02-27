<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
            
        $categorie = new Categorie();

        $categorie->setName("Catégorie test 1");

        $manager->persist($categorie);

        $categorie2 = new Categorie();

        $categorie2->setName("Catégorie test 2");

        $manager->persist($categorie2);

        $categorie3 = new Categorie();

        $categorie3->setName("Catégorie test 3");

        $manager->persist($categorie3);

        $manager->flush();
    }
}
