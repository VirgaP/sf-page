<?php

namespace App\Controller;

use App\Repository\AnimalRepository;
use http\Env\Request;
use http\Env\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/home", name="home")
     */
    public function index(AnimalRepository $animalRepository, \Symfony\Component\HttpFoundation\Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('App:Animal')->createQueryBuilder('a');

        $query = $queryBuilder->getQuery();

        $paginator  = $this->get('knp_paginator');

        $animals = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 12)/*limit per page*/
        );
        return $this->render('home/home.html.twig', [
            'animals' => $animals,
        ]);


    }
}
