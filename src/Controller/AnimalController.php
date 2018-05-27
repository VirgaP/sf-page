<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Comment;
use App\Entity\Reservation;
use App\Form\AnimalType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        return $this->render('animal/index.html.twig', [
            'animals' => $animals,
            'animalFilter' => $animalFilter,
            'availableFilter' => $availableFilter,
        ]);
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
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
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
    public function show(Animal $animal, Request $request, CommentRepository $repository, $id): Response
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

        $user = $this->getUser();
         if ($user) {
             $user_id = $user->getId();
         } else {
             $user_id = 0;
         }


        return $this->render('animal/show.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
            'comments' => $repository->findAllApprovedByAnimal($animal->getId()),
            'id' => $id,
            'user' => $user,
            'user_id' => $user_id
        ]);
    }
    /**
     * @Route("/{id}/heart", name="animal_toggle_heart", methods={"POST"})
     */
    public function toggleAnimalHeart(Animal $animal, Request $request, EntityManagerInterface $em)
    {
//        $user_id = $_POST['user_id']; //retrieving userid form ajax call
//        $animal_id = $_POST['animal_id']; //retrieving animalid from ajax call

        $animal->setHeartCount($animal->getHeartCount() + 1);
        $em->flush();


        return new JsonResponse(['hearts' => $animal->getHeartCount()]);
    }

    /**
     * @Route("/{id}/rezervacija", name="reservation_show", methods="GET|POST")
     */
    public function showReservation(Animal $animal, ReservationRepository $repository, Request $request)
    {
        $date = $request->query->get('data');

        // Working hours
        $hours = ['8', '9', '10', '11', '12', '13', '14', '15', '16'];
        // Empty array to push hours to it
        $availableHours = [];
        // If date is entered, get all reservation hours of that day from database
        if ($date) {
            $reservations = $repository->findAllByAnimal($animal->getId(), $date);

            // Pushes reserved hours to array, and returns difference between working hours and reserved hours.
            foreach ($reservations as $key => $value) {
                foreach ($value as $res) {
                    array_push($availableHours, $res);
                }
            }
            $availableHours = array_diff($hours, $availableHours);
        }

        return $this->render('animal/show_reservation.html.twig', [
            'animal' => $animal,
            'hours' => $availableHours,
            'date' => $date,
        ]);
    }

    /**
     * @Route("/{id}/rezervacija/{hour}", name="reservation_add", methods="GET")
     */
    public function addReservation(Animal $animal, $hour, Request $request)
    {
        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setAnimal($animal);
        $reservation->setDate($request->query->get('data'));
        $reservation->setHour($hour);

        $em = $this->getDoctrine()->getManager();
        $em->persist($reservation);
        $em->flush();

        $this->addFlash('success', "Rezervacija nusiųsta patvirtinimui. Patvirtinus, informuosime jus žinute.");
        return $this->redirectToRoute('reservation_show', ['id' => $animal->getId()]);
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
                if ($currentPicture != 'placeholder.png') {
                    $filesystem = new Filesystem();
                    $filesystem->remove($this->getParameter('upload_directory') . $currentPicture);
                }

                // Adds new file
                $newFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
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
        if ($this->isCsrfTokenValid('delete' . $animal->getId(), $request->request->get('_token'))) {

            // Deletes file from server
            $filename = $animal->getPicture();
            $filesystem = new Filesystem();
            $filesystem->remove($this->getParameter('upload_directory') . $filename);

            $em = $this->getDoctrine()->getManager();
            $em->remove($animal);
            $em->flush();
        }

        $this->addFlash('success', "Gyvūnas ištrintas");
        return $this->redirectToRoute('animal_index');
    }
}
