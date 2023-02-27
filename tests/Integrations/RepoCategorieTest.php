<?php

namespace App\Tests\Integrations;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\Categorie;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Persistence\ManagerRegistry;


class RepoCategorieTest extends KernelTestCase
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

    public function testCreateCategorie()
    {
        $categorieRepository = $this->entityManager->getRepository(Categorie::class);

        $categorieTest = new Categorie();
        $name = "categorie test insertion";
        
        $categorieRepository->createCategory($categorieTest, [

            "name" => $name
        ]);

        $categorieInDatabase = $categorieRepository->findOneBy(["name"=>$name]);

        $this->assertSame($categorieInDatabase->getName(),$name);
    }

    public function testRemoveCategorie()
    {
        $categorieRepository = $this->entityManager->getRepository(Categorie::class);

        $categorieTest = $categorieRepository->find(2);
        
        $categorieRepository->removeCategorie($categorieTest);

        $this->assertSame($categorieRepository->find(2),null);
    }

    public function testRemoveCategorieFail()
    {
        $categorieRepository = $this->entityManager->getRepository(Categorie::class);

        $categorieTest = $categorieRepository->find(1);

        $this->expectException("Exception");
    
        $categorieRepository->removeCategorie($categorieTest);

        $this->assertSame($categorieRepository->find(1),null);
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}