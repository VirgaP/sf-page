<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request)
    {
        $form = $this->createForm(LoginType::class);
        return $this->render('security/login.html.twig', [
            'login_form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(Request $request)
    {
        return $this->redirectToRoute('home');
    }
}
