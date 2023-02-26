<?php

namespace App\Controller\Back;

use App\Entity\Promotion;
use App\Form\PromotionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PromotionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/admin/promotions/creer", name="admin_promotions_create")
     */
    public function create(Request $request): Response
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($promotion);
            $this->entityManager->flush();

            $this->addFlash('success', 'La promotion a été créée avec succès.');

            return $this->redirectToRoute('admin_promotions_list');
        }

        return $this->render('back/promotion/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/admin/promotions/{id}/modifier", name="admin_promotions_edit")
     */
    public function edit(Promotion $promotion, Request $request): Response
    {
        $form = $this->createForm(PromotionType::class, $promotion);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La promotion a été modifiée avec succès.');

            return $this->redirectToRoute('admin_promotions_list');
        }

        return $this->render('back/promotion/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/promotions/{id}/supprimer", name="admin_promotions_delete")
     */
    public function delete(Promotion $promotion): Response
    {
        $this->entityManager->remove($promotion);
        $this->entityManager->flush();

        $this->addFlash('success', 'La promotion a été supprimée avec succès.');

        return $this->redirectToRoute('admin_promotions_list');
    }

    /**
     * @Route("/admin/promotions", name="admin_promotions_list")
     */
    public function list(): Response
    {
        $promotions = $this->entityManager->getRepository(Promotion::class)->findAll();

        return $this->render('back/promotion/list.html.twig', [
            'promotions' => $promotions,
        ]);
    }
}
