<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;


#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $clients = $userRepository->findUsersByRole('ROLE_CLIENT');
        return $this->render('back/client/index.html.twig', [
            'clients' => $clients
        ]);
    }


    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(User $client): Response
    {
        return $this->render('back/client/show.html.twig', [
            'user' => $client,
        ]);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, User $client, UserRepository $userRepository): Response
    {
        try{
            if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
                $userRepository->remove($client, true);
            }
        }
        catch(ForeignKeyConstraintViolationException $e){
            $this->addFlash('danger', 'Impossible de supprimer un client qui a des commandes liés');
        }

        return $this->redirectToRoute('admin_app_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
