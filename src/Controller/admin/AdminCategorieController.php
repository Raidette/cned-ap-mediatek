<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategorieController extends AbstractController
{

    /**
     * @var CategorieRepository  
     */
    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * pageGestioncategories
     * 
     * @Route("/admin/categories/", name="admin.categories.pagegestioncategories")
     *
     * @return Response
     */
    public function pageGestioncategories(): Response
    {

        $listeCategories = $this->categorieRepository->findAll();

        return $this->render('pages/admin/gestionCategories.html.twig', [
            "listeCategories" => $listeCategories
        ]);
    }

    /**
     * ajoutCategorie
     * 
     * @Route("/admin/categories/ajout", name="admin.categories.ajoutCategorie")
     *
     * @param Request $request
     * @return Response
     */
    public function ajoutCategorie(Request $request): Response
    {

        $newCategorie = new Categorie();

        $name = $request->get("name");


        $this->categorieRepository->createCategory($newCategorie, [

            "name" => $name

    ]);

        return $this->redirectToRoute("admin.categories.pagegestioncategories");

    }
 
    /**
     * supprCategorie
     *
     * @Route("/admin/categories/suppr/{id}", name="admin.categories.supprCategorie")
     * 
     * @param Categorie $categorie
     * @param Request $request
     * @return Response
     */
    public function supprCategorie(Categorie $categorie, Request $request): Response
    {
        $this->categorieRepository->removeCategorie($categorie);

        return $this->redirectToRoute('admin.categories.pagegestioncategories');
    }
}
