<?php

namespace App\Tests\Integrations;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\Categorie;

use App\Repository\PlaylistRepository;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Doctrine\Persistence\ManagerRegistry;


class RepoPlaylistTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    

    public function getPlaylist(): Playlist
    {
        return (new Playlist())
            ->setName("Playlist test");
    }


    public function testPersistPlaylist(){

        $formationRepository = $this->entityManager->getRepository(Formation::class);
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);

        $playlistTest = new Playlist();

        $name = "Playlist test persist";
        $description = "Playlist de test persist 1";

        

        $playlistRepository->persistPlaylist($playlistTest,[
            "name"=>$name,
            "description"=>$description
        ]);

        $playlistInDatabase = $playlistRepository->findOneBy(["name"=>$name]);

        $this->assertSame($name,$playlistInDatabase->getName());
        $this->assertSame($description,$playlistInDatabase->getDescription());
    }

    public function testRemoveFormationFromPlaylist(){

        $formationRepository = $this->entityManager->getRepository(Formation::class);
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);

        $playlistTest = $playlistRepository->find(1);
        $formationTest = $formationRepository->find(1);

        $playlistRepository->removeFormationFromPlaylist($playlistTest,$formationTest);

        $this->assertSame($formationTest->getPlaylist(),null);
    }

    public function testAddFormationToPlaylist(){

        $formationRepository = $this->entityManager->getRepository(Formation::class);
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);

        $playlistTest = $playlistRepository->find(1);
        $formationTest = $formationRepository->find(2);

        $playlistRepository->addFormationToPlaylist($playlistTest,$formationTest);

        $this->assertSame($formationTest->getPlaylist(),$playlistTest);

    }

    public function testFailDeletePlaylist(){

        $formationRepository = $this->entityManager->getRepository(Formation::class);
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);

        $playlistTest = $playlistRepository->find(1);
        $formationTest = $formationRepository->find(2);


        $this->expectException("Exception");

        $playlistRepository->deletePlaylist($playlistTest);

        $listeFormations = $playlistTest->getFormations();

        foreach($listeFormations as $formation)
        {
            $playlistRepository->removeFormationFromPlaylist($playlistTest,$formation);
        }

        $this->assertSame(null,"2",dump($playlistTest));

    }

    public function testDeletePlaylist(){

        $formationRepository = $this->entityManager->getRepository(Formation::class);
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);

        $playlistTest = $playlistRepository->find(1);
        $formationTest = $formationRepository->find(2);

        $listeFormations = $playlistTest->getFormations();

        foreach($listeFormations as $formation)
        {
            $playlistRepository->removeFormationFromPlaylist($playlistTest,$formation);
        }

        $playlistRepository->deletePlaylist(1);

        $this->assertSame($playlistRepository->find(1),null);

    }


    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}