<?php

namespace App\Controller\Back;

use App\Repository\OrderArticleRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'default_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository, OrderArticleRepository $orderArticleRepository): Response
    {
        $orders = $orderRepository->findAll();
        $salesByMonth = []; // tableau associatif qui contiendra les ventes par mois

        // boucler sur les commandes pour calculer les ventes par mois
        foreach ($orders as $order) {
            $month = $order->getDate()->format('M Y'); // format "Jan 2022", par exemple
            $amount = $order->getTotalPrice();

            if (isset($salesByMonth[$month])) {
                $salesByMonth[$month] += $amount;
            } else {
                $salesByMonth[$month] = $amount;
            }
        }

        // Best seller
        $bestSeller = $orderArticleRepository->findBestSeller();

        return $this->render('back/dashboard/index.html.twig', [
            'sales_by_month' => $salesByMonth,
            'best_seller' => $bestSeller
        ]);
    }
}
