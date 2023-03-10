<?php

namespace App\Controller\Front;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ReviewController extends AbstractController
{
    #[Route('/post_review', name: 'post_review')]
    public function index(Request $request, ReviewRepository $reviewRepository): Response
    {
        $review = new Review();
        $user = $this->getUser();
        $review->setUser($user); // set the user ID
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reviewRepository->save($review, true);

            $this->addFlash('success', 'Merci pour votre avis, il sera publié après validation par le propriétaire 😊');
            return $this->redirectToRoute('client_default_index');
        }

        return $this->redirectToRoute('client_default_index');
    }
}

