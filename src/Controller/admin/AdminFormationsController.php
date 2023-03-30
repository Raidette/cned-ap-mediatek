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

    /**
     * 
     * @var string
     */
    private const PAGE_GESTION = "pages/admin/gestionFormations.html.twig";

    /**
     * 
     * @var string
     */
    private const DATE_FORMAT = "Y-m-d\\TH:i:s";

    #region admin-page-principale
    
     

    public function __construct(FormationRepository $formationRepository, 
    CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }
    


 
    /**
     * pageGestionFormations
     * 
     * @Route("/admin/formations", name="admin.formations.pagegestionformations")
     * 
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
     * sortGestion
     * 
     * @Route("/admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sortGestion")
     *
     * @param  mixed $champ
     * @param  mixed $ordre
     * @param  mixed $table
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
     * findAllContainGestion
     *
     * @Route("/admin/formations/recherche/{champ}", name="admin.formations.findallcontaingestion")
     *
     * @param  mixed $champ
     * @param Request $request
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
     * findAllContainInTableGestion
     * 
     * @Route("/admin/formations/recherche/{champ}/{table}", name="admin.formations.findallcontainintablegestion")
     * 
     * @param mixed $champ
     * @param Request $request
     * @param mixed $table
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
     * pageModifFormation
     *
     * @Route("/admin/formations/modifFormation/{id}", name="admin.formations.pagemodifformation")
     * 
     * @param  mixed $id
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
     *
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
     * supprimerFormation
     *
     * @Route("/admin/formations/suppression/{id}", name="admin.formations.supprimerformation")
     * 
     * Route de suppression d'une formation.
     * @param Formation $formation
     * @return Response
     */
    public function supprimerFormation(Formation $formation): Response{

        $this->formationRepository->remove($formation,true);

        return $this->pageGestionFormations();
    }

    
    /**
     * updateFormation
     *
     * @Route("/admin/formations/updateFormation/{id}", name="admin.formations.updateFormation")
     * 
     * @param Formation $formation
     * @param Request $request
     * @param mixed $id
     * @return Response
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
     * addFormation
     * 
     * @Route("/admin/formations/gestion/addformation", name="admin.formations.addformation")
     *
     * @param Request $request
     * @return Response
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
