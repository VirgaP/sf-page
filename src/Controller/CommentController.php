<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/komentarai")
 */
class CommentController extends Controller
{
    /**
     * @Route("/", name="comment_index")
     */
    public function comments(CommentRepository $repository)
    {
        return $this->render('admin/comments.html.twig', ['comments' => $repository->findAllWhereNotApproved()]);
    }

    /**
     * @Route("/patvirtinti", name="comments_approved")
     */
    public function approvedComments(CommentRepository $repository)
    {
        return $this->render('admin/approved_comments.html.twig', ['comments' => $repository->findAllWhereApproved()]);
    }

    /**
     * @Route("/{id}", name="comment_show", methods="GET")
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', ['comment' => $comment]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods="GET|POST")
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_edit', ['id' => $comment->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/patvirtinti", name="comment_approve")
     */
    public function archive(Comment $comment, CommentRepository $repository)
    {
        $repository->setAsApproved($comment->getId());
        $this->addFlash('success', "Komentaras patvirtintas");
        return $this->redirectToRoute('comment_index');
    }

    /**
     * @Route("/{id}", name="comment_delete", methods="DELETE")
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirectToRoute('comment_index');
    }


}
