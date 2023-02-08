<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Formation;
use DateTimeInterface;

/**
 * Controleur des formations
 *
 * @author emds
 */
class FormationsController extends AbstractController {

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

    private const PAGE_FORMATIONS = "pages/formations.html.twig";
    
    public function __construct(FormationRepository $formationRepository, 
    CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
        $this->playlistRepository = $playlistRepository;
    }
    
    /**
     * @Route("/formations", name="formations")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/formations/tri/{champ}/{ordre}/{table}", name="formations.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }     
    
    /**
     * @Route("/formations/recherche/{champ}", name="formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => ""
        ]);
    }
    
    /**
     * @Route("/formations/recherche/{champ}/{table}", name="formations.findallcontainintable")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainInTable($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValueInTable($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * @Route("/formations/formation/{id}", name="formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);        
    }


    #region admin-page-principale
    
     /**
     * @Route("/formations/controle", name="formations.pagecontroleformations")
     * @param type $id
     * @return Response
     */
    public function pageControleFormations(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/controleFormations.html.twig", [
            'formations' => $formations,
            'categories' => $categories        
        ]);       
    }

    /**
     * @Route("/formations/controle/tri/{champ}/{ordre}/{table}", name="formations.sortControle")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sortControle($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/controleFormations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    } 

    /**
     * @Route("/formations/controle/recherche/{champ}", name="formations.findallcontaincontrole")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainControle($champ, Request $request): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/controleFormations.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => ""
        ]);
    }

    /**
     * @Route("/formations/controle/recherche/{champ}/{table}", name="formations.findallcontainintablecontrole")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainInTableControle($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValueInTable($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/controleFormations.html.twig", [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #endregion



    /**
     * @Route("/formations/controle/modifFormation/{id}", name="formations.pagemodifformation")
     * @param type $id
     * @return Response
     */
    public function pageModifFormation($id): Response{

        $formation = [];

        if($id != "")
        {
            $formation = $this->formationRepository->find($id);
            $dateFormation = $formation->getPublishedAt();
            $dateFormationString = $dateFormation->format("Y-m-d")."T".$dateFormation->format("H:i");
        }

        $categories = $this->categorieRepository->findAll();
        $listePlaylists = $this->playlistRepository->findAllOrderByName("ASC");
        $dateNow = date("Y-m-d")."T"."23:59:59";


        return $this->render("pages/modifFormation.html.twig", [
            "formation" => $formation,
            "categories" => $categories,
            "listeplaylists" => $listePlaylists,
            "dateformation" => $dateFormationString,
            "dateNow" => $dateNow
        ]);        
    }


    /**
     * @Route("/formations/controle/creerFormation", name="formations.pagecreerformation")
     * @param type $id
     * @return Response
     */
    public function pageCreerFormation(): Response{

        $formation = [];

        $categories = $this->categorieRepository->findAll();
        $listePlaylists = $this->playlistRepository->findAllOrderByName("ASC");
        $dateNow = date("Y-m-d")."T"."00:00:00";


        return $this->render("pages/modifFormation.html.twig", [
            "formation" => $formation,
            "categories" => $categories,
            "listeplaylists" => $listePlaylists,
            "dateformation" => "",
            "dateNow" => $dateNow
        ]);        
    }

    /**
     * @Route("/formations/controle/suppression/{id}", name="formations.supprimerformation")
     * @param $id
     * 
     * Route de suppression d'une formation.
     */
    public function supprimerFormation(Formation $formation): Response{

        $this->formationRepository->remove($formation,true);

        return $this->index();
    }

    /**
     * @Route("/formations/controle/updateFormation/{id}", name="formations.updateFormation")
     * @param $id
     * 
     * Route de mise à jour d'une formation.
     */
    public function updateFormation(Formation $formation, Request $request, $id): Response{

        $categories = [];

        $titre = $request->request->get("titre");
        $description = $request->request->get("description");
        $url = $request->request->get("url");
        $categories = array($request->request->get("categories"));
        $datePublished = str_replace('T',' ',$request->request->get("dateCreation")).":00";
        $playlist = $this->playlistRepository->find($request->request->get("playlist"));

        //$datePublished = \DateTime::createFromFormat("Y-m-d H:i:s",$datePublished);

        $formation->setTitle($titre);
        $formation->setPublishedAt(\DateTime::createFromFormat("Y-m-d H:i:s",$datePublished));
        $formation->setDescription($description);
        $formation->setVideoId($url);   
        $formation->setPlaylist($playlist);

        foreach($formation->getCategories() as $categorie)
        {
            $formation->removeCategory($categorie);
        }

        foreach($categories[0] as $categorie)
        {
            $formation->addCategory($this->categorieRepository->find($categorie));            
        }

        $this->formationRepository->add($formation,true);
        

        return $this->render("pages/affichagefinal.html.twig", [
            'formation' => $formation,
            'titre'=> $titre,
            'description'=>$description,
            'categories'=>$categories,
            'playlist'=>$playlist,
            'datePublished'=>$datePublished
        ]); 
    }


    /**
     * @Route("/formations/controle/addformation", name="formations.addformation")
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
        $datePublished = str_replace('T',' ',$request->request->get("dateCreation"));

        $datePublished = \DateTime::createFromFormat("Y-m-d H:i:s",$datePublished);


        $formation->setTitle($titre);
        //$formation->setPublishedAt(\DateTime::createFromFormat("Y-m-d H:i:s",$datePublished));
        $formation->setDescription($description);
        $formation->setVideoId($url);   
        $formation->setPlaylist($playlist);

        foreach($categories as $categorie)
        {
            $formation->addCategory($this->categorieRepository->find($categorie));            
        }

        //$this->formationRepository->add($formation,true);
        

        return $this->render("pages/affichagefinal.html.twig", [
            'formation' => $formation,
            'titre'=> $titre,
            'description'=>$description,
            'categories'=>$categories,
            'playlist'=>$playlist,
            'datePublished'=>$datePublished
        ]);  
    }
}
