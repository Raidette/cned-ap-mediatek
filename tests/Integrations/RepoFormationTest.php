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


class RepoFormationTest extends KernelTestCase
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
    

    public function getFormation(): Formation
    {
        return (new Formation())
            ->setTitle("Formation test");
    }


    public function testPersistFormation(){


        $formationRepository = $this->entityManager->getRepository(Formation::class);
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);

        $formationTest = new Formation();
        $dateTime = new \DateTime();
        $titre = "Formation test";
        $description = "Tests via phpunit";
        $videoId = "B3d_RjqrVrg";
        $playlist = $playlistRepository->find("1");
        $categories = [];

        $formationRepository->persistFormation($formationTest,[
            "titre" => $titre,
            "description" => $description,
            "datePublished" => $dateTime,
            "url" => $videoId,
            "playlist" => "1",
            "categories" => []
        ]);

        $formationInDatabase = $formationRepository->findOneBy(["title"=>$titre]);

        $this->assertSame($titre,$formationInDatabase->getTitle());
        $this->assertSame($description,$formationInDatabase->getDescription());
        $this->assertSame($dateTime,$formationInDatabase->getPublishedAt());
        $this->assertSame($videoId,$formationInDatabase->getVideoId());
        $this->assertSame($playlist,$formationInDatabase->getPlaylist());
      
    }

    public function testModifFormation()
    {
        $formationRepository = $this->entityManager->getRepository(Formation::class);
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $categorieRepository = $this->entityManager->getRepository(Categorie::class);

        $categories = [$categorieRepository->find("2"),$categorieRepository->find("3")];

        $titre = "Test de formation";

        $formationTest = $formationRepository->findOneBy(["title"=>$titre]);

        $formationRepository->modifFormation($formationTest,[

            "titre" => $formationTest->getTitle(),
            "description" => $formationTest->getDescription(),
            "url" => $formationTest->getVideoId(),
            "categories" => [2,3],
            "playlist" => $formationTest->getPlaylist(),
            "datePublished" => $formationTest->getPublishedAt()

        ]);

        $formationInDatabase = $formationRepository->findOneBy(["title"=>$titre]);

        $this->assertEquals($formationInDatabase->getCategories()[1],$categories[0]);   
        $this->assertEquals($formationInDatabase->getCategories()[2],$categories[1]);  
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}