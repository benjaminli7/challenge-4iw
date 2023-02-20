<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_index')]
    public function index(Request $request, CategoryRepository $categoryRepository): Response
    {

        return $this->render('front/default/index.html.twig',  [
            'categories' => $categoryRepository->findAll(),
            'cart_quantity' => $this->getCartItemCount($request)
        ]);
    }



    private function getCartItemCount(Request $request): int
    {
        $cart = $request->getSession()->get('cart', []);

        $count = 0;

        foreach ($cart as $cartItemData) {
            $count += $cartItemData['quantity'];
        }

        return $count;
    }
}
