<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
          return $this->render('admin/dashboard.html.twig');
    }
}
