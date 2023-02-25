<?php

namespace App\Controller\Back;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
         $test = $articleRepository->findAll();

        return $this->render('back/article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository, SluggerInterface $slugger): Response
    {
        $article = new Article();
        // only keep fields price and name

        $form = $this->createForm(ArticleType::class, $article);
        //remove created_at and updated_at fields
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('imageFile')->getData();
            foreach ($article->getTags() as $tag) {
                $article->addTag($tag);
            }   
            if($file){
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($fileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('article_directory'),
                        $newFileName
                    );
                } catch (FileException $e){
                    // ... handle exception if something happens during file uploads
                }
                $article->setImage($newFileName);
            }

            $articleRepository->save($article, true);

            return $this->redirectToRoute('admin_app_menu', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('back/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageFile')->getData();

            if($file){
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($fileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('article_directory'),
                        $newFileName
                    );
                } catch (FileException $e){
                    // ... handle exception if something happens during file uploads
                }
                $article->setImage($newFileName);
            }


            $articleRepository->save($article, true);

            return $this->redirectToRoute('admin_app_menu', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('admin_app_menu', [], Response::HTTP_SEE_OTHER);
    }
}
