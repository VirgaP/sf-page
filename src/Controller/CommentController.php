<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/komentarai")
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
     * @Route("/{id}/patvirtinti", name="comment_approve")
     */
    public function approve(Comment $comment, CommentRepository $repository)
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
