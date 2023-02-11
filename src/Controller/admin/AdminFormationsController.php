<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Repository\PlaylistRepository;
use App\Repository\CategorieRepository;

use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class AdminFormationsController extends AbstractController
{

    /**
     * 
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;


    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;  

    #region admin-page-principale
    
     

    public function __construct(FormationRepository $formationRepository, 
    CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }
    


    /**
     * @Route("/admin/formations", name="admin.formations.pagegestionformations")
     * @param type $id
     * @return Response
     */
    public function pageGestionFormations(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/admin/gestionFormations.html.twig", [
            'formations' => $formations,
            'categories' => $categories        
        ]);       
    }

    /**
     * @Route("/admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sortGestion")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sortGestion($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/admin/gestionFormations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    } 

    /**
     * @Route("/admin/formations/recherche/{champ}", name="admin.formations.findallcontaingestion")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainGestion($champ, Request $request): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/admin/gestionFormations.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => ""
        ]);
    }

    /**
     * @Route("/admin/formations/recherche/{champ}/{table}", name="admin.formations.findallcontainintablegestion")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainInTableGestion($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValueInTable($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/admin/gestionFormations.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #endregion



    /**
     * @Route("/admin/formations/modifFormation/{id}", name="admin.formations.pagemodifformation")
     * @param type $id
     * @return Response
     */
    public function pageModifFormation($id): Response{


        $formation = $this->formationRepository->find($id);
        $dateFormation = $formation->getPublishedAt();
        $dateFormationString = $dateFormation->format("Y-m-d")."T".$dateFormation->format("H:i");

        $categories = $this->categorieRepository->findAll();
        $listePlaylists = $this->playlistRepository->findAllOrderByName("ASC");
        $dateNow = date("Y-m-d")."T"."23:59:59";


        return $this->render("pages/admin/modifFormation.html.twig", [
            "formation" => $formation,
            "categories" => $categories,
            "listeplaylists" => $listePlaylists,
            "dateformation" => $dateFormationString,
            "dateNow" => $dateNow
        ]);        
    }


    /**
     * @Route("/admin/formations/creerFormation", name="admin.formations.pagecreerformation")
     * @param type $id
     * @return Response
     */
    public function pageCreerFormation(): Response{

        $formation = [];

        $categories = $this->categorieRepository->findAll();
        $listePlaylists = $this->playlistRepository->findAllOrderByName("ASC");
        $dateNow = date("Y-m-d")."T"."00:00:00";


        return $this->render("pages/admin/modifFormation.html.twig", [
            "formation" => $formation,
            "categories" => $categories,
            "listeplaylists" => $listePlaylists,
            "dateformation" => "",
            "dateNow" => $dateNow
        ]);        
    }

    /**
     * @Route("/admin/formations/suppression/{id}", name="admin.formations.supprimerformation")
     * @param $id
     * 
     * Route de suppression d'une formation.
     */
    public function supprimerFormation(Formation $formation): Response{

        $this->formationRepository->remove($formation,true);

        return $this->pageGestionFormations();
    }

    /**
     * @Route("/admin/formations/updateFormation/{id}", name="admin.formations.updateFormation")
     * @param $id
     * 
     * Route de mise à jour d'une formation.
     */
    public function updateFormation(Formation $formation, Request $request, $id): Response{

        $categories = [];

        $titre = $request->request->get("titre");
        $description = $request->request->get("description");
        $url = $request->request->get("url");
        $categories = $request->request->get("categories");
        $datePublished = str_replace('T',' ',$request->request->get("dateCreation")).":00";
        $playlist = $this->playlistRepository->find($request->request->get("playlist"));

        $datePublished = \DateTime::createFromFormat("Y-m-d H:i:s",$datePublished);

        $formation->setTitle($titre);
        $formation->setPublishedAt($datePublished);
        $formation->setDescription($description);
        $formation->setVideoId($url);   
        $formation->setPlaylist($playlist);

        foreach($formation->getCategories() as $categorie)
        {
            $formation->removeCategory($categorie);
        }

        foreach($categories as $categorie)
        {
            $formation->addCategory($this->categorieRepository->find($categorie));            
        }

        $this->formationRepository->add($formation,true);
        

        return $this->render("pages/admin/affichagefinal.html.twig", [
            'formation' => $formation,
            'titre'=> $titre,
            'description'=>$description,
            'categories'=>$categories,
            'playlist'=>$playlist,
            'datePublished'=>$datePublished
        ]); 
    }


    /**
     * @Route("/admin/formations/gestion/addformation", name="admin.formations.addformation")
     * @param $id
     * 
     * Route de mise à jour d'une formation.
     */
    public function addFormation(Request $request): Response{

        $categories = [];

        $formation = new Formation();

        $titre = $request->request->get("titre");
        $description = $request->request->get("description");
        $url = $request->request->get("url");
        $categories = $request->request->get("categories");
        $playlist = $this->playlistRepository->find($request->request->get("playlist"));
        $datePublished = str_replace('T',' ',$request->request->get("dateCreation")).":00";

        $datePublished = \DateTime::createFromFormat("Y-m-d H:i:s",$datePublished);


        $formation->setTitle($titre);
        $formation->setPublishedAt($datePublished);
        $formation->setDescription($description);
        $formation->setVideoId($url);   
        $formation->setPlaylist($playlist);

        foreach($categories as $categorie)
        {
            $formation->addCategory($this->categorieRepository->find($categorie));            
        }

        $this->formationRepository->add($formation,true);
        

        return $this->render("pages/admin/affichagefinal.html.twig", [
            'formation' => $formation,
            'titre'=> $titre,
            'description'=>$description,
            'categories'=>$categories,
            'playlist'=>$playlist,
            'datePublished'=>$datePublished
        ]);  
    }
}
