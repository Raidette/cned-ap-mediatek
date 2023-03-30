<?php

namespace App\Controller\admin;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Playlist;
use App\Entity\Formation;


class AdminPlaylistsController extends AbstractController
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
        
    public function __construct(PlaylistRepository $playlistRepository, 
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }

    /**
     * pageGestionPlaylists
     * @Route("/admin/playlists", name="admin.playlists.pagegestionplaylists")
     * @return Response
     */
    public function pageGestionPlaylists(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/admin/gestionPlaylists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * sortGestion
     *
     * @Route("/admin/playlists/tri/{champ}/{ordre}", name="admin.playlists.sortgestion")
     * @param  mixed $champ
     * @param  mixed $ordre
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
        return $this->render("pages/admin/gestionPlaylists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    
    /**
     * findAllContainGestion
     * 
     * @Route("/admin/playlists/recherche/{champ}", name="admin.playlists.findallcontaingestion")
     *
     * @param  mixed $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContainGestion($champ, Request $request): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/admin/gestionPlaylists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeur' => $valeur,
            'table' => ""
        ]);
    } 
  
    /**
     * findAllContainInTableGestion
     * 
     * @Route("/admin/playlists/recherche/{champ}/{table}", name="admin.playlists.findallcontainintablegestion")
     * 
     * @param  mixed $champ
     * @param Request $request
     * @param  mixed $table
     * @return Response
     */
    public function findAllContainInTableGestion($champ, Request $request, $table): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValueInTable($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render("pages/admin/gestionPlaylists.html.twig", [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeur' => $valeur,
            'table' => ""
        ]);
    } 
    
    /**
     */    
    /**
     * pageModifPlaylist
     * 
     * @Route("/admin/playlists/modif/{id}", name="admin.playlists.pagemodifplaylist")
     * @param mixed $id
     * @return Response
     */
    public function pageModifPlaylist($id): Response{

        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        $listeFormations = $this->formationRepository->findAll();

        return $this->render("pages/admin/modifPlaylist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations,
            'listeFormations'=> $listeFormations
        ]);        
    }

    /**
     * @Route("/admin/playlists/creerPlaylist", name="admin.playlists.pagecreationplaylist")
     * @return Response
     */
    public function pageCreationPlaylist(): Response{

        return $this->render("pages/admin/ajouterPlaylists.html.twig", [
        ]);        
    }

    /**
     * removeFormationFromPlaylist
     * 
     * @Route("/admin/playlists/removeFormationFromPlaylist/{idformation}/{idplaylist}", name="admin.playlists.removefromplaylist")
     * 
     * @param  mixed $idformation
     * @param  mixed $idplaylist
     * @return Response
     */
    public function removeFormationFromPlaylist($idformation,$idplaylist): Response{

        $playlist = $this->playlistRepository->find($idplaylist);

        $formation = $this->formationRepository->find($idformation);

        $this->playlistRepository->removeFormationFromPlaylist($playlist,$formation);

        $redirectId = $playlist->getId();

        return $this->redirectToRoute("admin.playlists.pagemodifplaylist",array('id' => $redirectId));

    }

 
    /**
     * addFormationToPlaylist
     * 
     * @Route("/admin/playlists/addFormationToPlaylist/{idplaylist}", name="admin.playlists.addformationtoplaylist")
     * @param Request $request
     * @param  mixed $idplaylist
     * @return Response
     */
    public function addFormationToPlaylist(Request $request, $idplaylist): Response{

        $playlist = $this->playlistRepository->find($idplaylist);

        $formation = $this->formationRepository->find($request->get("addFormation"));

        $this->playlistRepository->addFormationToPlaylist($playlist,$formation);

        $redirectId = $playlist->getId();

        return $this->redirectToRoute("admin.playlists.pagemodifplaylist",array('id' => $redirectId));
       
    }



    /**
     * deletePlaylist
     * 
     * @Route("/admin/playlists/deletePlaylist/{idplaylist}", name="admin.playlists.deleteplaylist")
     * 
     * @param Request $request
     * @param  mixed $idplaylist
     * @return Response
     */
    public function deletePlaylist(Request $request, $idplaylist): Response{

        $this->playlistRepository->deletePlaylist($idplaylist);

        return $this->redirectToRoute("admin.playlists.pagegestionplaylists");

    }
    

   
    /**
     * createPlaylist
     * 
     * @Route("/admin/playlists/createPlaylist", name="admin.playlists.createplaylist")
     * 
     * @param Request $request
     * @return Response
     */
    public function createPlaylist(Request $request): Response{

        $newPlaylist = new Playlist();

        $newPlaylist->setName($request->get("nomFormation"));
        $newPlaylist->setDescription($request->get("descriptionFormation"));

        $this->playlistRepository->add($newPlaylist, true);

        return $this->redirectToRoute("admin.playlists.pagegestionplaylists");
        
    }


    /**
     * modifInfosPlaylist
     *
     * @Route("/admin/playlists/modifInfosPlaylist/{idplaylist}", name="admin.playlists.modifinfosplaylist")
     * 
     * @param Request $request
     * @param mixed $idplaylist
     * @return Response
     */
    public function modifInfosPlaylist(Request $request, $idplaylist): Response{

        $playlist = $this->playlistRepository->find($idplaylist);

        $titre = $request->get("nomFormation");

        $description = $request->get("descriptionFormation");

        $this->playlistRepository->persistPlaylist($playlist,[
            "titre" => $titre,
            "description" => $description
        ]);


        $redirectId = $playlist->getId();

        return $this->redirectToRoute("admin.playlists.pagemodifplaylist",array('id' => $redirectId));

    }
}
