<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CategorieRepository;

use App\Entity\Categorie;


class CategorieController extends AbstractController
{

    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository) {
        $this->categorieRepository = $categorieRepository;
    }
    

    #[Route('/categories/gestion', name: 'categories.pagegestioncategories')]
    public function pagegestioncategories(): Response
    {

        $listeCategories = $this->categorieRepository->findAll();

        return $this->render('pages/categories/gestioncategories.html.twig', [
            "listeCategories" => $listeCategories
        ]);
    }

    #[Route('/categories/gestion/ajout', name: 'categories.ajoutCategorie')]
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

    #[Route('/categories/gestion/suppr/{id}', name: 'categories.supprCategorie')]
    public function supprCategorie(Categorie $categorie, Request $request): Response
    {

        if(count($categorie->getFormations()) === 0)
        {
            $this->categorieRepository->remove($categorie, true);   
        }


        return $this->redirectToRoute('categories.pagegestioncategories');
    }
}
