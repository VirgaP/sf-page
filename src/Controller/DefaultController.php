<?php

namespace App\Controller;

use App\Entity\About;
use App\Entity\Message;
use App\Entity\UserMessage;
use App\Form\MessageType;
use App\Repository\UserMessageRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('default/home.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

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

            return $this->redirectToRoute('message_new');
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
        $userMessage->setIsSeen(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($userMessage);
        $em->flush();
        return $this->render('default/message_show.html.twig', ['userMessage' => $userMessage]);
    }

    /**
     * @Route("/zinutes/{id}", name="user_message_delete", methods="DELETE")
     */
    public function delete(Request $request, UserMessage $userMessage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userMessage->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userMessage);
            $em->flush();
        }

        return $this->redirectToRoute('user_messages_index');
    }

}
