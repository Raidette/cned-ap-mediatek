<?php

namespace App\Repository;

use App\Entity\Formation;
use App\Repository\PlaylistRepository;
use App\Repository\CategorieRepository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{

    /**
     * @var Repository
     */
    private $categorieRepository;
    /**
     * @var Repository
     */
    private $playlistRepository;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);

        $this->categorieRepository = new CategorieRepository($registry);
        $this->playlistRepository = new PlaylistRepository($registry);

    }

    public function add(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
 
    /**
     * findAllOrderBy
     *
     * Retourne toutes les formations triées sur un champ
     * 
     * @param  mixed $champ
     * @param  mixed $ordre
     * @param  mixed $table
     * @return array
     */
    public function findAllOrderBy($champ, $ordre, $table=""): array{
        if($table==""){
            return $this->createQueryBuilder('f')
                    ->orderBy('f.'.$champ, $ordre)
                    ->getQuery()
                    ->getResult();
        }else{
            return $this->createQueryBuilder('f')
                    ->join('f.'.$table, 't')
                    ->orderBy('t.'.$champ, $ordre)
                    ->getQuery()
                    ->getResult();            
        }
    }

    
    /**
     * findByContainValue
     * 
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     *
     * @param  mixed $champ
     * @param  mixed $valeur
     * @return array
     */
    public function findByContainValue($champ, $valeur): array{
        if($valeur==""){
            return $this->findAll();
        }
        
        return $this->createQueryBuilder('f')
                    ->where('f.'.$champ.' LIKE :valeur')
                    ->orderBy('f.publishedAt', 'DESC')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * findByContainValueInTable
     * 
     * Enregistrements dont un champ dans une table spécifique (ex :) contient une valeur
     * ou tous les enregistrements si la valeur est vide
     *
     * @param  mixed $champ
     * @param  mixed $valeur
     * @param  mixed $table
     * @return array
     * 
     * @example Rechercher tous les cours de POO dans la table des cours en java
     */
    public function findByContainValueInTable($champ, $valeur, $table=""): array{
        if($valeur==""){
            return $this->findAll();
        }
        
        return $this->createQueryBuilder('f')
                    ->join('f.'.$table, 't')                    
                    ->where('t.'.$champ.' LIKE :valeur')
                    ->orderBy('f.publishedAt', 'DESC')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->getQuery()
                    ->getResult();   
    }
    
    
    /**
     * findAllLasted
     * 
     * Retourne les n formations les plus récentes
     *
     * @param  mixed $nb
     * @return array
     */
    public function findAllLasted($nb) : array {
        return $this->createQueryBuilder('f')
                ->orderBy('f.publishedAt', 'DESC')
                ->setMaxResults($nb)     
                ->getQuery()
                ->getResult();
    }    
    
    /**
     * findAllForOnePlaylist
     * Retourne la liste des formations d'une playlist
     *
     * @param  mixed $idPlaylist
     * @return array
     */
    public function findAllForOnePlaylist($idPlaylist): array{
        return $this->createQueryBuilder('f')
                ->join('f.playlist', 'p')
                ->where('p.id=:id')
                ->setParameter('id', $idPlaylist)
                ->orderBy('f.publishedAt', 'ASC')   
                ->getQuery()
                ->getResult();        
    }
    
    /**
     * modifFormation
     *
     * @param Formation $formation
     * @param array $properties
     * @return void
     */
    public function modifFormation(Formation $formation, array $properties)
    {

        $oldCategories = $formation->getCategories();

        if($oldCategories != $properties["categories"] && $oldCategories != null);
        {
            foreach($oldCategories as $categorie)
            {
                $formation->removeCategory($categorie);
            }
        }

        $this->persistFormation($formation, $properties);
    }


        
    /**
     * persistFormation
     *
     * @param  mixed $formation
     * @param  mixed $properties
     * @return void
     */
    public function persistFormation(Formation $formation, array $properties)
    {

        $limitDate = new \DateTime();    
        $limitDate = $limitDate->setTime(0,0,0)->add(new \DateInterval("P1D"));


        if($properties["datePublished"] < $limitDate)
        {

            $formation->setTitle($properties["titre"]);
            $formation->setPublishedAt($properties["datePublished"]);
            $formation->setDescription($properties["description"]);
            $formation->setVideoId($properties["url"]);   
            $formation->setPlaylist($this->playlistRepository->find($properties["playlist"]));
    

            
            foreach($properties["categories"] as $categorie)
            {
                $formation->addCategory($this->categorieRepository->find($categorie));            
            }

            $this->getEntityManager()->persist($formation);
            $this->getEntityManager()->flush();
        }

        else
        {
            throw new \Exception("Wrong date for insertion : date must be today or before");
        }
    } 
}
