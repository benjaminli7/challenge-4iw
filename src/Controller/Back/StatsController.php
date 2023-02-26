<?php

namespace App\Controller\Back;

use App\Repository\ArticleRepository;
use App\Repository\OrderRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stats')]
class StatsController extends AbstractController
{
    #[Route('/', name: 'app_stats_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, OrderRepository $orderRepository, UserRepository $userRepository, ReviewRepository $reviewRepository): Response
    {
        $totalArticles = $articleRepository->count([]);

        $totalOrders = $orderRepository->count([]);

        $orders = $orderRepository->findAll();
        $totalRevenue = 0;
        foreach($orders as $order) {
            $totalRevenue += $order->getTotalPrice();
        }
        $totalRevenueString = $totalRevenue . " €";

        $articles = $articleRepository->findAll();
        usort($articles, function ($a, $b) {
            return $b->getOrderCount() - $a->getOrderCount();
        });

        $mostOrderedArticle = reset($articles);
        $mostOrderedArticleString = $mostOrderedArticle->getName() . " (" . $mostOrderedArticle->getPrice() . " €)" ." commandé " . $mostOrderedArticle->getOrderCount() . " fois";

        $users = $userRepository->findAll();
        $usersWithOrdersCount = 0;
        foreach ($users as $user) {
            $orders = $user->getOrders();
            foreach ($orders as $order) {
                if ($order->getClient() === $user) {
                    $usersWithOrdersCount++;
                    break;
                }
            }
        }

        $usersWithOrdersCount = $usersWithOrdersCount;
        $averageOrderValue = $totalRevenue / $totalOrders;
        $averageOrderValue = $averageOrderValue . " €";

        $reviews = $reviewRepository->findAll();
        $reviewsNote = 0;
        foreach($reviews as $review) {
            if($review->getNote() >= 3) {
                $reviewsNote++;
            }
        }

        $stats = [];
        $stats[] = ['label' => 'Nombres d\'article', 'value' => $totalArticles];
        $stats[] = ['label' => 'Nombre de commandes', 'value' => $totalOrders];
        $stats[] = ['label' => 'Revenus', 'value' => $totalRevenueString];
        $stats[] = ['label' => 'Article le plus commandé', 'value' => $mostOrderedArticleString];
        $stats[] = ['label' => 'Nombre de clients ayant passé une commande', 'value' => $usersWithOrdersCount];
        $stats[] = ['label' => 'Prix moyen d\'une commande', 'value' => $averageOrderValue];
        $stats[] = ['label' => 'Nombre de notes supérieur à 2', 'value' => $reviewsNote];

        return $this->render('back/stats/index.html.twig', [
            'stats' => $stats
        ]);
    }
}
