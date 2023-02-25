<?php

namespace App\Controller\Back;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/review')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'app_review_index', methods: ['GET'])]
    public function index(ReviewRepository $reviewRepository): Response
    {
        return $this->render('back/review/index.html.twig', [
            'reviews' => $reviewRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_review_delete', methods: ['POST'])]
    public function delete(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $reviewRepository->remove($review, true);
        }

        return $this->redirectToRoute('admin_app_review_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/update-status/{id}', name: 'app_review_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, Review $review, ReviewRepository $reviewRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $isApproved = $data['isApproved'];
        $review->setApproved($isApproved);
        $reviewRepository->save($review, true);

        return new JsonResponse(['success' => true]);
    }
    //delete review
    #[Route('/delete-review/{id}', name: 'review_delete', methods: ['POST'])]
    public function deleteReview(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        // isGranted('ROLE_ADMIN')
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $reviewRepository->remove($review, true);
        }

        return $this->redirectToRoute('admin_app_review_index', [], Response::HTTP_SEE_OTHER);
    }
}