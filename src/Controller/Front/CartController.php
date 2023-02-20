<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Repository\ArticleRepository;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $cart = $request->getSession()->get('cart', []);
        $priceTotal = 0;

        $cartArticles = [];
    
        foreach ($cart as $articleId => $quantity) {
            $article = $articleRepository->find($articleId);
    
            if ($article) {
                $priceTotal += $article->getPrice() * $quantity['quantity'];
                $cartArticles[] = [
                    'article' => $article,
                    'quantity' => $quantity['quantity'],
                ];
            }
        }

        return $this->render('front/cart/index.html.twig', [
            'cartArticles' => $cartArticles,
            'priceTotal' => $priceTotal,
        ]);
    }


    #[Route('/add-to-cart/{id}', name: 'add_to_cart')]
    public function addToCart(Request $request, Article $article): JsonResponse
    {
        $cart = $request->getSession()->get('cart', []);

        if (!isset($cart[$article->getId()])) {
            $cart[$article->getId()] = [
                'article' => $article,
                'quantity' => 0,
            ];
        }

        $cart[$article->getId()]['quantity']++;

        $request->getSession()->set('cart', $cart);

        $cartItemCount = $this->getCartItemCount($request);

        return new JsonResponse([
            'success' => true,
            'cartItemCount' => $cartItemCount,
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
