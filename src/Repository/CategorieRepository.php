<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function add(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
   
    /**
     * findAllForOnePlaylist
     * 
     * Retourne la liste des catégories des formations d'une playlist
     *
     * @param  mixed $idPlaylist
     * @return array
     */
    public function findAllForOnePlaylist($idPlaylist): array{
        return $this->createQueryBuilder('c')
                ->join('c.formations', 'f')
                ->join('f.playlist', 'p')
                ->where('p.id=:id')
                ->setParameter('id', $idPlaylist)
                ->orderBy('c.name', 'ASC')
                ->getQuery()
                ->getResult();        
    }  
    
        
    /**
     * createCategory
     *
     * @param  mixed $categorie
     * @param  mixed $properties
     * @return void
     */
    public function createCategory(Categorie $categorie, array $properties)
    {
        $newCategorie = new Categorie();

        if($this->findBy(["name" => $properties["name"]]) === [])
        {
            $newCategorie->setName($properties["name"]);

            $this->add($newCategorie, true);
        }
    }
    
    /**
     * removeCategorie
     *
     * @param  mixed $categorie
     * @return void
     */
    public function removeCategorie(Categorie $categorie)
    {
        if(count($categorie->getFormations()) === 0)
        {
            $this->remove($categorie, true);   
        }

        else
        {
            throw new \Exception("Impossible to remove a non-empty category.");
        }
    }
}
