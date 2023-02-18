<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {

        return $this->render('front/default/index.html.twig',  [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
