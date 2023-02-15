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

    private const PAGE_GESTION = "pages/admin/gestionFormations.html.twig";

    private const DATE_FORMAT = "Y-m-d\\TH:i:s";

    #region admin-page-principale
    
     

    public function __construct(FormationRepository $formationRepository, 
    CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }
    


    /**
     * @Route("/admin/formations", name="admin.formations.pagegestionformations")
     * @param $id
     * @return Response
     */
    public function pageGestionFormations(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render($this::PAGE_GESTION, [
            'formations' => $formations,
            'categories' => $categories        
        ]);       
    }

    /**
     * @Route("/admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sortGestion")
     * @param $champ
     * @param $ordre
     * @param $table
     * @return Response
     */
    public function sortGestion($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render($this::PAGE_GESTION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    } 

    /**
     * @Route("/admin/formations/recherche/{champ}", name="admin.formations.findallcontaingestion")
     * @param $champ
     * @param $request
     * @param $table
     * @return Response
     */
    public function findAllContainGestion($champ, Request $request): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render($this::PAGE_GESTION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => ""
        ]);
    }

    /**
     * @Route("/admin/formations/recherche/{champ}/{table}", name="admin.formations.findallcontainintablegestion")
     * @param $champ
     * @param $request
     * @param $table
     * @return Response
     */
    public function findAllContainInTableGestion($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValueInTable($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render($this::PAGE_GESTION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #endregion



    /**
     * @Route("/admin/formations/modifFormation/{id}", name="admin.formations.pagemodifformation")
     * @param $id
     * @return Response
     */
    public function pageModifFormation($id): Response{


        $formation = $this->formationRepository->find($id);
        $dateFormation = $formation->getPublishedAt();
        $dateFormationString = $dateFormation->format($this::DATE_FORMAT);

        $categories = $this->categorieRepository->findAll();
        $listePlaylists = $this->playlistRepository->findAllOrderByName("ASC");
        $dateToday = new \DateTime();
        $dateNow = $dateToday->setTime(23,59,59)->format($this::DATE_FORMAT);


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
     * @param $id
     * @return Response
     */
    public function pageCreerFormation(): Response{

        $formation = [];

        $categories = $this->categorieRepository->findAll();
        $listePlaylists = $this->playlistRepository->findAllOrderByName("ASC");
        $dateToday = new \DateTime();
        $dateNow = $dateToday->setTime(23,59,59)->format($this::DATE_FORMAT);

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

        $titre = $request->request->get("titre");
        $description = $request->request->get("description");
        $url = $request->request->get("url");
        $categories = $request->request->get("categories");
        $playlist = $request->get("playlist");
        $datePublished = $request->get("dateCreation");

        $datePublished = \DateTime::createFromFormat("Y-m-d\\TH:i",$datePublished);

        $this->formationRepository->modifFormation($formation,
        [
            "titre" => $titre,
            "description" => $description,
            "url" => $url,
            "categories" => $categories,
            "playlist" => $playlist,
            "datePublished" => $datePublished
        ]
        );
        

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


        $formation = new Formation();

        $titre = $request->request->get("titre");
        $description = $request->request->get("description");
        $url = $request->request->get("url");
        $categories = $request->request->get("categories");
        $playlist = $request->get("playlist");
        $datePublished = $request->get("dateCreation");

        $datePublished = \DateTime::createFromFormat("Y-m-d\\TH:i",$datePublished);


        $this->formationRepository->persistFormation($formation,
        [
            "titre" => $titre,
            "description" => $description,
            "url" => $url,
            "categories" => $categories,
            "playlist" => $playlist,
            "datePublished" => $datePublished
        ]
        );
        
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
