<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(TranslatorInterface $translator): Response
    {
          return $this->render('user/dashboard.html.twig');
    }
}
