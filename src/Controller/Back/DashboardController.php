<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'default_index')]
    public function index(): Response
    {
        return $this->render('back/dashboard/index.html.twig');
    }
}
