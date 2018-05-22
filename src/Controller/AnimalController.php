<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Comment;
use App\Form\AnimalType;
use App\Form\CommentType;
use App\Pagination\PaginatedCollection;
use App\Repository\AnimalRepository;
use App\Repository\CommentRepository;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/animal")
 */
class AnimalController extends Controller
{
    /**
     * @Route("/", name="animal_index", methods="GET")
     */
    public function index(Request $request): Response
    {
        $animals = $this->getDoctrine()
            ->getRepository(Animal::class)
            ->findAll();

        return $this->render('animal/index.html.twig', ['animals' => $animals]);
    }

    /* *
     * @param $page
     * @param $key
     * @param $type
     * @return Response
     * @Route ("/list/{type}/{page}/{key}", defaults={"page"=1, "key"="all", "type"="title"})
     */
//    public function listAction($page, $key, $type)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $rpp = $this->container->getParameter('animals_per_page');
//
//        $repo = $em->getRepository('App:Animal');
//
//        list($res, $totalcount) = $repo->getResultAndCount($page, $rpp, $key, $type);
//
//        $paginator = new Util\Paginator($page, $totalcount, $rpp);
//        $pagelist = $paginator->getPagesList();
//
//        return $this->render('animal/list.html.twig', array('res' => $res, 'paginator' => $pagelist, 'cur' => $page, 'total' => $paginator->getTotalPages(), 'key'=>$key, 'type'=>$type));
//    }


    /**
     * @Route("/list", name="animal_list", methods="GET")
     */
    public function listAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('App:Animal')->createQueryBuilder('a');

        $query = $queryBuilder->getQuery();

        $paginator  = $this->get('knp_paginator');

        $animals = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 2)/*page number*/,
            $request->query->getInt('limit', 10)/*limit per page*/
        );

        return $this->render('animal/list.html.twig', [
            'animals' => $animals,
        ]);
    }
    /**
     * @Route("/filter", name="animal_index_filter", methods="GET")
     */
    public function indexFilter(Request $request): Response
    {
        $animalsRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Animal::class);

        $animalFilter = $request->query->get('animal');
        $availableFilter = $request->query->get('available');

        if ($animalFilter && (!isset($availableFilter) || $availableFilter == "")) {
            $animals = $animalsRepository->filterAnimal($animalFilter);
        } else if (!$animalFilter && isset($availableFilter) && ($availableFilter != "")) {
            $animals = $animalsRepository->filterAvailable($availableFilter);
        } else if ($animalFilter && isset($availableFilter) && ($availableFilter != "")) {
            $animals = $animalsRepository->filterBoth($animalFilter, $availableFilter);
        } else {
            $animals = $animalsRepository->findAll();
        }

        return $this->render('animal/index-filter.html.twig', [
            'animals' => $animals,
            'animalFilter' => $animalFilter,
            'availableFilter' => $availableFilter,
        ]);
    }

    /**
     * @Route("/new", name="animal_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('picture')->getData();
            if ($imageFile != null) {
                /** @var UploadedFile $file */
                $animal = $form->getData();
                $file = $animal->getPicture();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $this->getParameter('upload_directory'),
                    $fileName
                );
                $animal->setPicture($fileName);
            } else {
                $animal->setPicture('placeholder.png');
            }

            $animal->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($animal);
            $em->flush();

            $this->addFlash('success', "Naujas gyvūnas sukurtas");
            return $this->redirectToRoute('animal_index');
        }

        return $this->render('animal/new.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="animal_show", methods="GET|POST")
     */
    public function show(Animal $animal, Request $request, CommentRepository $repository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setAnimal($animal);
            $comment->setUser($this->getUser());
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', "Komentaras nusiųstas patvirtinimui. Komentaras bus rodomas tik jį patvirtinus.");
            return $this->redirectToRoute('animal_show', ['id' => $animal->getId()]);
        }

        return $this->render('animal/show.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
            'comments' => $repository->findAllApprovedByAnimal($animal->getId()),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="animal_edit", methods="GET|POST")
     */
    public function edit(Request $request, Animal $animal): Response
    {
        $currentPicture = $animal->getPicture();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Checks if user changes picture
            $imageFile = $form->get('picture')->getData();
            if ($imageFile != null) {
                // Deletes old file if not placeholder
                if ($currentPicture != 'placeholder.png'){
                    $filesystem = new Filesystem();
                    $filesystem->remove($this->getParameter('upload_directory').$currentPicture);
                }

                // Adds new file
                $newFileName = md5(uniqid()).'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('upload_directory'),
                    $newFileName
                );
                $animal->setPicture($newFileName);
                $this->getDoctrine()->getManager()->flush();
            } else { // If picture remains same
                $animal->setPicture($currentPicture);
                $this->getDoctrine()->getManager()->flush();
            }
            $this->addFlash('success', "Duomenys atnaujinti");
            return $this->redirectToRoute('animal_edit', ['id' => $animal->getId()]);
        }

        return $this->render('animal/edit.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="animal_delete", methods="DELETE")
     */
    public function delete(Request $request, Animal $animal): Response
    {
        if ($this->isCsrfTokenValid('delete'.$animal->getId(), $request->request->get('_token'))) {

            // Deletes file from server
            $filename = $animal->getPicture();
            $filesystem = new Filesystem();
            $filesystem->remove($this->getParameter('upload_directory').$filename);

            $em = $this->getDoctrine()->getManager();
            $em->remove($animal);
            $em->flush();
        }

        $this->addFlash('success', "Gyvūnas ištrintas");
        return $this->redirectToRoute('animal_index');
    }
}
