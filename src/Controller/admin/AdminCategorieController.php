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

    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository = $categorieRepository;
    }
    
    #[Route('/admin/categories/', name: 'admin.categories.pagegestioncategories')]
    public function pageGestioncategories(): Response
    {

        $listeCategories = $this->categorieRepository->findAll();

        return $this->render('pages/admin/gestionCategories.html.twig', [
            "listeCategories" => $listeCategories
        ]);
    }

    #[Route('/admin/categories/ajout', name: 'admin.categories.ajoutCategorie')]
    public function ajoutCategorie(Request $request): Response
    {

        $newCategorie = new Categorie();

        $name = $request->get("name");


        if($this->categorieRepository->findBy(["name" => $name]) === [])
        {
            $newCategorie->setName($name);

            $this->categorieRepository->add($newCategorie, true);
        }

        return $this->redirectToRoute("categories.pagegestioncategories");

    }

    #[Route('/admin/categories/suppr/{id}', name: 'admin.categories.supprCategorie')]
    public function supprCategorie(Categorie $categorie, Request $request): Response
    {

        if(count($categorie->getFormations()) === 0)
        {
            $this->categorieRepository->remove($categorie, true);   
        }

        return $this->redirectToRoute('categories.pagegestioncategories');
    }
}
