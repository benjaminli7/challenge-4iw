<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;

#[Route('/menu')]
class MenuController extends AbstractController
{
    #[Route('/', name: 'app_menu')]
    public function index(CategoryRepository $categoryRepository, TagRepository $tagRepository): Response
    {
        return $this->render('back/menu/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'tags' => $tagRepository->findAll(),
        ]);
    }
}
