<?php

namespace App\Controller\Back;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\OrderArticleRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'default_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository, UserRepository $userRepository,OrderArticleRepository $orderArticleRepository): Response
    {
        $today = new \DateTime('today');

        //dd($today);
        $allOrders = $orderRepository->findAll();
        $allUsers = $userRepository->findAll();
        $allOrderArticle = $orderArticleRepository->findAll();

        $totalSoldToday = 0;
        $newCustomersToday = 0;
        $totalOrdersToday = 0;
        $totalArticleSoldToday = 0;
        $bestEmployee = null;
        $bestEmployeeSales = 0;

        foreach($allOrders as $order) {
            if ($order->getDate()->format('Y-m-d') == $today->format('Y-m-d')) {
                $totalSoldToday += $order->getTotalPrice();
                $totalOrdersToday += 1;
            }
        }
        foreach ($allUsers as $user) {
            if ($user->getCreatedAt()->format('Y-m-d') == $today->format('Y-m-d')) {
                $newCustomersToday++;
            }
        }
        // start from order and look in orderArticle the article by date
        foreach ($allOrderArticle as $orderArticle) {
            if ($orderArticle->getOrder()->getDate()->format('Y-m-d') == $today->format('Y-m-d')) {
                $totalArticleSoldToday += $orderArticle->getQuantity();
            }
        }


        foreach($allOrders as $order) {
            if ($order->getDate()->format('Y-m-d') == $today->format('Y-m-d')) {
                $totalSoldToday += $order->getTotalPrice();
                $totalOrdersToday += 1;
                $employee = $order->getEmployee();
                if ($employee) {
                    if (!isset($employeeSales[$employee->getId()])) {
                        $employeeSales[$employee->getId()] = 0;
                    }
                    $employeeSales[$employee->getId()] += $order->getTotalPrice();
                    if ($employeeSales[$employee->getId()] > $bestEmployeeSales) {
                        $bestEmployee = $employee->getFirstname() . ' ' . $employee->getLastname();
                        $bestEmployeeSales = $employeeSales[$employee->getId()];
                    }
                }
            }
        }

        $stats = [];
        $stats[] = ['label' => 'Nombre de commandes aujourd\'hui', 'value' => $totalOrdersToday];
        $stats[] = ['label' => 'Nombre de nouveaux clients aujourd\'hui', 'value' => $newCustomersToday];
        $stats[] = ['label' => 'Nombre d\'articles vendus aujourd\'hui', 'value' => $totalArticleSoldToday];
        $stats[] = ['label' => 'Revenu de la journ??e', 'value' => $totalSoldToday . " ???"];
        $stats[] = ['label' => 'Meilleur employ?? du jour', 'value' => $bestEmployee];


        return $this->render('back/dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}
