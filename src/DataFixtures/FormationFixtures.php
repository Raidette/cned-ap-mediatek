<?php

namespace App\DataFixtures;

use App\Entity\Playlist;
use App\Entity\Categorie;
use App\Entity\Formation;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class FormationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $playlistRepository = $manager->getRepository(Playlist::class);
        $categorieRepository = $manager->getRepository(Categorie::class);

        $playlist1 = $playlistRepository->findAll();
            
            
        $formation = new Formation();

        $formation->setDescription("Description test");
        $formation->setTitle("Test de formation");
        $formation->setPublishedAt(\DateTime::createFromFormat("Y-m-d","2023-02-15"));
        $formation->setPlaylist($playlistRepository->find("1"));
        $formation->addCategory($categorieRepository->find(1));

        $manager->persist($formation);

        $formation2 = new Formation();

        $formation2->setDescription("Description test 2");
        $formation2->setTitle("Test de formation 2");
        $formation2->setPublishedAt(\DateTime::createFromFormat("Y-m-d","2023-02-16"));
        
        $manager->persist($formation2);

        $manager->flush();

        $formation3 = new Formation();

        $formation3->setDescription("Description test 3");
        $formation3->setTitle("Test de formation 3");
        $formation3->setPublishedAt(\DateTime::createFromFormat("Y-m-d","2023-02-16"));
        
        $manager->persist($formation3);

        $manager->flush();
    }
}
