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
use App\Entity\Reservation;
use App\Entity\UserMessage;
use App\Form\AboutType;
use App\Repository\MessageRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
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
     * @Route("/rezervacijos", name="reservations_index")
     */
    public function reservations(ReservationRepository $repository)
    {
        return $this->render('admin/reservations.html.twig', ['reservations' => $repository->findAllReservationsWhereNotApproved()]);
    }

    /**
     * @Route("/rezervacijos/{id}/patvirtinti", name="reservation_approve")
     */
    public function reservationApprove(Reservation $reservation, ReservationRepository $repository)
    {
        $repository->setReservationAsApproved($reservation->getId());

        $userMessage = new UserMessage();
        $userMessage->setUser($reservation->getUser());
        $userMessage->setSubject('Rezervacijos patvirtinimas');
        $userMessage->setText('Jūsų rezervacija pas gyvūną vardu ' . $reservation->getAnimal()->getName() . ' ' . $reservation->getDate() . " " . $reservation->getHour() . ":00 val. yra patvirtinta. Jeigu negalėtumėt atvykti, prašome mus informuoti el.paštu arba telefonu.");
        $em = $this->getDoctrine()->getManager();
        $em->persist($userMessage);
        $em->flush();

        $this->addFlash('success', "Rezervacija patvirtinta");
        return $this->redirectToRoute('reservations_index');
    }

    /**
     * @Route("/rezervacijos/{id}/atmesti", name="reservation_reject")
     */
    public function rejectReservation(Request $request, Reservation $reservation)
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reservation);
            $em->flush();

            $userMessage = new UserMessage();
            $userMessage->setUser($reservation->getUser());
            $userMessage->setSubject('Rezervacija nepriimta');
            $userMessage->setText('Atsiprašome, bet Jūsų rezervacija pas gyvūną vardu ' . $reservation->getAnimal()->getName() . ' ' . $reservation->getDate() . " " . $reservation->getHour() . ":00 val. yra nepriimta. Prašome registruotis kitu laiku.");
            $em->persist($userMessage);
            $em->flush();

            $this->addFlash('success', "Rezervacija atmesta");
        }

        return $this->redirectToRoute('reservations_index');
    }

    /**
     * @Route("/rezervacijos/{id}/trinti", name="reservation_delete")
     */
    public function deleteReservation(Request $request, Reservation $reservation)
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reservation);
            $em->flush();

            $this->addFlash('success', "Rezervacija ištrinta");
        }

        return $this->redirectToRoute('reservations_index');
    }

    /**
     * @Route("/rezervacijos/patvirtintos", name="approved_reservations")
     */
    public function approvedReservations(ReservationRepository $repository)
    {
        return $this->render('admin/approved_reservations.html.twig', ['reservations' => $repository->findAllReservationsWhereApproved()]);
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