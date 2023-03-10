<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Entity\Order;
use App\Repository\ArticleRepository;
use App\Repository\OrderRepository;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Service\SmsService;

#[Route('/cart')]
class CartController extends AbstractController
{
    private SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    #[Route('/', name: 'app_cart')]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $cart = $request->getSession()->get('cart', []);
        $priceTotal = 0;

        $cartArticles = [];

        foreach ($cart as $articleId => $quantity) {
            $article = $articleRepository->find($articleId);

            if ($quantity['quantity'] > 0) {
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

    #[Route('/remove-from-cart/{id}', name: 'remove_from_cart')]
    public function removeFromCart(Request $request, Article $article): JsonResponse
    {
        $cart = $request->getSession()->get('cart', []);

        if(isset($cart[$article->getId()])) {
            if($cart[$article->getId()]['quantity'] > 0) {
                $cart[$article->getId()]['quantity']--;
            }
        }


        $request->getSession()->set('cart', $cart);

        $cartItemCount = $this->getCartItemCount($request);

        return new JsonResponse([
            'success' => true,
            'cartItemCount' => $cartItemCount,
            'itemCount' => $cart[$article->getId()]['quantity'],
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
            'itemCount' => $cart[$article->getId()]['quantity'],
        ]);
    }

    #[Route('/checkout', name: 'checkout')]
    public function checkout(Request $request): Response
    {
        $cart = $request->getSession()->get('cart', []);

        if(!$this->getLineItems($cart)) {
            return $this->redirectToRoute('client_app_cart');
        }

        $baseUrl = $request->getSchemeAndHttpHost();

        Stripe::setApiKey($this->getParameter('STRIPE_SECRET'));


        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $this->getLineItems($cart),
            'mode' => 'payment',
            'success_url' => $baseUrl . $this->generateUrl('client_checkout_success') . "?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => $baseUrl . $this->generateUrl('client_checkout_cancel'),
        ]);

        return $this->redirect($session->url);
    }

    #[Route('/checkout/success', name: 'checkout_success')]
    public function checkoutSuccess(Request $request, ArticleRepository $articleRepository, OrderRepository $orderRepository): Response
    {
        Stripe::setApiKey($this->getParameter('STRIPE_SECRET'));
        try {
            $session = Session::retrieve($_GET['session_id']);
        } catch (\Exception $e) {
            return $this->redirectToRoute('client_checkout_cancel');
        }


        $cart = $request->getSession()->get('cart', []);

        $user = $this->getUser();

        $order = (new Order())
            ->setStatus('ONGOING')
            ->setDate(new \DateTime())
            ->setClient($user);

        foreach ($cart as $articleId => $quantity) {
            $article = $articleRepository->find($articleId);

            if ($article) {
                $order->addArticle($article, $quantity['quantity']);
            }
        }

        $articles = $order->getOrderArticles()->getValues();
        $totalPrice = 0;

        foreach ($articles as $article) {

            $article->getArticle()->setOrderCount($article->getQuantity());
            $totalPrice += $article->getArticle()->getPrice() * $article->getQuantity();
        }

        $order->setTotalPrice($totalPrice);


        $orderRepository->save($order, true);

        $this->smsService->sendSms($order);
        $request->getSession()->remove('cart');

        return $this->render('front/cart/checkout_success.html.twig', [
            'orderId' => $order->getId(),
        ]);
    }

    #[Route('/checkout/cancel', name: 'checkout_cancel')]
    public function checkoutCancel(): Response
    {
        return $this->render('front/cart/checkout_cancel.html.twig');
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


    private function getLineItems(array $cart): array
    {
        $lineItems = [];

        foreach ($cart as $cartItemData) {
            $article = $cartItemData['article'];
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) ($article->getPrice() * 100),
                    'product_data' => [
                        'name' => $article->getName(),
                    ],
                ],
                'quantity' => $cartItemData['quantity'],
            ];
        }

        return $lineItems;
    }
}
