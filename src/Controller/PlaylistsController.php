<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Playlist;
use App\Entity\Formation;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController {
    
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
    
    private const PAGE_PLAYLISTS = "pages/playlists.html.twig";
    
    public function __construct(PlaylistRepository $playlistRepository, 
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    public function index(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories            
        ]);
    }

    /**
     * @Route("/playlists/tri/{champ}/{ordre}", name="playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        switch($champ){
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
            break;
            case "nbformations":
                $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
            break;
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/playlists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }         
    
    /**
     * @Route("/playlists/recherche/{champ}", name="playlists.findallcontain")
     */
    public function findAllContain($champ, Request $request): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeur' => $valeur,
            'table' => ""
        ]);
    }  

    /**
     * @Route("/playlists/recherche/{champ}/{table}", name="playlists.findallcontainintable")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainInTable($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValueInTable($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * @Route("/playlists/playlist/{id}", name="playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);        
    }

    /**
     * @Route("/playlists/gestion", name="playlists.pagegestionplaylists")
     * @return Response
     */
    public function pageGestionPlaylists(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/gestionPlaylists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories            
        ]);
    }

    /**
     * @Route("/playlists/gestion/tri/{champ}/{ordre}", name="playlists.sortgestion")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sortGestion($champ, $ordre): Response{
        switch($champ){
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
            break;
            case "nbformations":
                $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
            break;
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/gestionPlaylists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/playlists/gestion/recherche/{champ}", name="playlists.findallcontaingestion")
     */
    public function findAllContainGestion($champ, Request $request): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/gestionPlaylists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeur' => $valeur,
            'table' => ""
        ]);
    } 
    
    /**
     * @Route("/playlists/gestion/modifPlaylist/{id}", name="playlists.pagemodifplaylist")
     * @param type $id
     * @return Response
     */
    public function pageModifPlaylist($id): Response{

        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        $listeFormations = $this->formationRepository->findAll();

        return $this->render("pages/modifPlaylist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations,
            'listeFormations'=> $listeFormations
        ]);        
    }

    /**
     * @Route("/playlists/gestion/creerPlaylist", name="playlists.pagecreationplaylist")
     * @param type $id
     * @return Response
     */
    public function pageCreationPlaylist(): Response{

        return $this->render("pages/playlists/ajouterPlaylists.html.twig", [
        ]);        
    }

    /**
     * @Route("/playlists/gestion/removeFormationFromPlaylist/{idformation}/{idplaylist}", name="playlists.removefromplaylist")
     * @param type $id
     * @return Response
     */
    public function removeFormationFromPlaylist($idformation,$idplaylist): Response{

        $playlist = $this->playlistRepository->find($idplaylist);

        $formation = $this->formationRepository->find($idformation);

        $playlist->removeFormation($formation);

    
        $this->playlistRepository->add($playlist,true);

        $redirectId = $playlist->getId();

        return $this->redirectToRoute("playlists.pagemodifplaylist",array('id' => $redirectId));

           
    }

    /**
     * @Route("/playlists/gestion/addFormationToPlaylist/{idplaylist}", name="playlists.addformationtoplaylist")
     * @param type $id
     * @return Response
     */
    public function addFormationToPlaylist(Request $request, $idplaylist): Response{

        $playlist = $this->playlistRepository->find($idplaylist);

        $formation = $this->formationRepository->find($request->get("addFormation"));

        $playlist->addFormation($formation);

    
        $this->playlistRepository->add($playlist,true);

        $redirectId = $playlist->getId();

        return $this->redirectToRoute("playlists.pagemodifplaylist",array('id' => $redirectId));

       
    }


    /**
     * @Route("/playlists/gestion/deletePlaylist/{idplaylist}", name="playlists.deleteplaylist")
     * @param type $id
     * @return Response
     */
    public function deletePlaylist(Request $request, $idplaylist): Response{

        $playlist = $this->playlistRepository->find($idplaylist);

        if(count($playlist->getFormations()) === 0)
        {
            $this->playlistRepository->remove($playlist, true);
        }

        return $this->redirectToRoute("playlists.pagegestionplaylists");

    }
    

    /**
     * @Route("/playlists/gestion/createPlaylist/", name="playlists.createplaylist")
     * @param type $id
     * @return Response
     */
    public function createPlaylist(Request $request): Response{

        $newPlaylist = new Playlist();

        $newPlaylist->setName($request->get("nomFormation"));
        $newPlaylist->setDescription($request->get("descriptionFormation"));

        $this->playlistRepository->add($newPlaylist, true);

        return $this->redirectToRoute("playlists.pagegestionplaylists");
        
    }


    /**
     * @Route("/playlists/gestion/modifInfosPlaylist/{idplaylist}", name="playlists.modifinfosplaylist")
     * @param type $id
     * @return Response
     */
    public function modifInfosPlaylist(Request $request, $idplaylist): Response{

        $playlist = $this->playlistRepository->find($idplaylist);

        $titre = $request->get("nomFormation");

        $description = $request->get("descriptionFormation");

        $playlist->setName($titre);

        $playlist->setDescription($description);

    
        $this->playlistRepository->add($playlist,true);

        $redirectId = $playlist->getId();

        return $this->redirectToRoute("playlists.pagemodifplaylist",array('id' => $redirectId));

    }
}
