<?php

namespace App\Controller;

use App\Entity\About;
use App\Entity\Animal;
use App\Entity\Message;
use App\Entity\UserMessage;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\UserMessageRepository;
use App\Form\ProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/susisiekti", name="message_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $this->addFlash('success', "Žinutė išsiųsta");

            return $this->redirectToRoute('home');
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/apie", name="about_us")
     */
    public function about()
    {
        $about = $this->getDoctrine()
            ->getRepository(About::class)
            ->findAll();

        return $this->render('about.html.twig', ['about' => $about]);
    }

    /**
     * @Route("/zinutes", name="user_messages_index")
     * @Security("is_granted('ROLE_USER')")
     */
    public function userMessages(UserMessageRepository $repository)
    {
        return $this->render('default/messages.html.twig', [
            'messages' => $repository->findAllByUser($this->getUser())
        ]);
    }

    /**
     * @Route("/zinutes/{id}", name="user_message_show", methods="GET")
     */
    public function show(UserMessage $userMessage): Response
    {
        $this->denyAccessUnlessGranted('see', $userMessage->getUser());

        $userMessage->setIsSeen(true);
        $id = $userMessage->getUser();

        $em = $this->getDoctrine()->getManager();
        $em->persist($userMessage);
        $em->flush();
        return $this->render('default/message_show.html.twig', [
            'userMessage' => $userMessage,
            'id' => $id,
        ]);
    }

    public function countMessages(UserMessageRepository $repository)
    {
        return $this->render('default/user_message_count.html.twig', [
            'userMessageCount' => $repository->countAllUnseenMessages($this->getUser()->getId())
        ]);
    }

    /**
     * @Route("/zinutes/{id}", name="user_message_delete", methods="DELETE")
     */
    public function delete(Request $request, UserMessage $userMessage): Response
    {
        $this->denyAccessUnlessGranted('see', $userMessage->getUser());

        if ($this->isCsrfTokenValid('delete'.$userMessage->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userMessage);
            $em->flush();
        }

        return $this->redirectToRoute('user_messages_index');
    }

    /**
     * @Route("/{id}/anketa/atnaujinti", name="edit_profile_data")
     */
    public function editProfile(Request $request, User $user)
    {
        // Access denied if tries to edit another user
        $this->denyAccessUnlessGranted('edit', $user);

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Duomenys atnaujinti");

            return $this->redirectToRoute('edit_profile_data', ['id' => $user->getId()]);
        }

        return $this->render('user/change.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'id' => $user->getId(),
        ]);
    }
    /**
     * @Route("/{id}/anketa", name="member_show", methods="GET")
     */
    public function showMember(User $user)
    {
        $this->denyAccessUnlessGranted('edit', $user);

        return $this->render('user/show_member.html.twig', [
            'user' => $user,
        ]);
    }
    /**
     * @Route("/gyvunai", name="animals_list", methods="GET")
     */
    public function listAnimals(Request $request)
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

        return $this->render('animal/list.html.twig', [
            'animals' => $animals,
            'animalFilter' => $animalFilter,
            'availableFilter' => $availableFilter,
        ]);


    }

}
