<?php

namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\Playlist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PlaylistFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $formationRepository = $manager->getRepository(Formation::class);


        $playlist = new Playlist();

        $playlist->setName("Playlist test 1");
        $playlist->setDescription("Playlist de test 1");
        $playlist->addFormation($formationRepository->find("1"));

        $manager->persist($playlist);

        $playlist2 = new Playlist();

        $playlist2->setName("Playlist test 2");
        $playlist2->setDescription("Playlist de test 2");
        $playlist2->addFormation($formationRepository->find("2"));

        $manager->persist($playlist2);

        $manager->flush();
    }
}
