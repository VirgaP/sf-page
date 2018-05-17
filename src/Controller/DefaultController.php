<?php
/**
 * Created by PhpStorm.
 * User: tadas
 * Date: 2018-05-17
 * Time: 09:59
 */

namespace App\Controller;


use App\Entity\About;
use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

}