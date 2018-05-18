<?php
/**
 * Created by PhpStorm.
 * User: tadas
 * Date: 2018-05-18
 * Time: 09:31
 */

namespace App\Controller;


use App\Entity\About;
use App\Entity\Message;
use App\Form\AboutType;
use App\Repository\CommentRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    /**
     * @Route("/zinutes", name="messages_index")
     */
    public function messages(MessageRepository $repository)
    {
        return $this->render('admin/messages.html.twig', ['messages' => $repository->findAllWhereIsNotSeen()]);
    }

    /**
     * @Route("/zinutes/skaitytos", name="messages_seen")
     */
    public function seenMessages(MessageRepository $repository)
    {
        return $this->render('admin/seen_messages.html.twig', ['messages' => $repository->findAllWhereIsSeen()]);
    }

    /**
     * @Route("/zinutes/skaitytos/{id}", name="message_delete")
     */
    public function deleteMessage(Request $request, Message $message)
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();

            $this->addFlash('success', "Žinutė ištrinta");
        }

        return $this->redirectToRoute('messages_index');
    }

    /**
     * @Route("/zinutes/perkelti/{id}", name="messages_archive")
     */
    public function archive(Message $message, MessageRepository $repository)
    {
        $repository->setAsSeen($message->getId());
        $this->addFlash('success', "Žinutė perkelta į archyvą");
        return $this->redirectToRoute('messages_index');
    }

    /**
     * @Route("/apie/koreguoti/{id}", name="about_edit")
     */
    public function editAbout(Request $request, About $about)
    {
        $form = $this->createForm(AboutType::class, $about);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Puslapis pakoreguotas");
            return $this->redirectToRoute('about_edit', ['id' => $about->getId()]);
        }

        return $this->render('admin/about_edit.html.twig', [
            'about' => $about,
            'form' => $form->createView(),
        ]);
    }

}