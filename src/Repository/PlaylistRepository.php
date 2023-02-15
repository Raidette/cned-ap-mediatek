<?php

namespace App\Repository;

use App\Entity\Playlist;
use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{

    private const ID_SELECTOR = 'p.id id';
    private const NAME_SELECTOR = 'p.name name';
    private const CATEGORIE_SELECTOR = 'c.name categoriename';


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function add(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * Retourne toutes les playlists triées sur le nom de la playlist
     * @param $champ
     * @param $ordre
     * @return Playlist[]
     */
    public function findAllOrderByName($ordre): array{
        return $this->createQueryBuilder('p')
                    ->leftjoin('p.formations', 'f')
                    ->groupBy('p.id')
                    ->orderBy('p.name', $ordre)
                    ->getQuery()
                    ->getResult();
    }
    /**
    * Retourne toutes les playlists triées sur le nombre de formations
    * @param type $ordre
    * @return Playlist[]
    */
    public function findAllOrderByNbFormations($ordre): array{
        return $this->createQueryBuilder('p')
                    ->leftjoin('p.formations', 'f')
                    ->groupBy('p.id')
                    ->orderBy('count(f.title)', $ordre)
                    ->getQuery()
                    ->getResult();
    }
        /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur): array{
        if($valeur==""){
            return $this->findAllOrderByName('ASC');
        }    
        
        return $this->createQueryBuilder('p')
                    ->leftjoin('p.formations', 'f')
                    ->where('p.'.$champ.' LIKE :valeur')

                    ->setParameter('valeur', '%'.$valeur.'%')

                    ->groupBy('p.id')
                    ->orderBy('p.name', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
    
    
    /**
     * Enregistrements dont un champ dans une table spécifique (ex :) contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table permet de chercher dans une table spécifique
     * @return Playlist[]
     * @example Rechercher tous les cours de POO dans la table des cours en java
     */
    public function findByContainValueInTable($champ, $valeur, $table=""): array{
        if($valeur==""){
            return $this->findAllOrderByName('ASC');
        }    
        
        return $this->createQueryBuilder('p')
                    ->leftjoin('p.formations', 'f')
                    ->leftjoin('f.categories', 'c')
                    ->where('c.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')

                    ->groupBy('p.id')
                    ->orderBy('p.name', 'ASC')
                    ->getQuery()
                    ->getResult();
    }   


    public function removeFormationFromPlaylist(Playlist $playlist, Formation $formation)
    {

        $playlist->removeFormation($formation);

        $this->getEntityManager()->flush();

    }

    public function deletePlaylist($idplaylist)
    {
        $playlist = $this->find($idplaylist);

        if(count($playlist->getFormations()) === 0)
        {
            $this->remove($playlist, true);
        }

        else
        {
            throw new \Exception("Impossible to delete a non-empty playlist");
        }
    }
}
