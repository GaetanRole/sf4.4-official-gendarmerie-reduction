<?php

declare(strict_types=1);

namespace App\Controller\Article;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\Adapter\RepositoryAdapterInterface;
use App\Service\EntityManager\ArticleManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/article", name="app_admin_article_")
 * @IsGranted("ROLE_ADMIN")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class AdminController extends AbstractController
{
    /** @var RepositoryAdapterInterface */
    private $repositoryAdapter;

    /** @var ArticleManager */
    private $articleManager;

    public function __construct(RepositoryAdapterInterface $repositoryAdapter, ArticleManager $articleManager)
    {
        $this->repositoryAdapter = $repositoryAdapter;
        $this->articleManager = $articleManager;
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(ArticleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $this->repositoryAdapter->save($this->articleManager->prepare($form->getData(), $user));

            return $this->redirectToRoute('app_article_index');
        }

        return $this->render('article/admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        /** @var string $previousTitle Keep the same title between updates. */
        $previousTitle = $article->getTitle();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repositoryAdapter->update(
                $this->articleManager->handleSlug($article, $previousTitle)
            );

            return $this->redirectToRoute('app_article_index');
        }

        return $this->render('article/admin/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="change_status", methods="PUT")
     */
    public function changeStatus(Request $request, Article $article): RedirectResponse
    {
        if ($this->isCsrfTokenValid('status'.$article->getSlug(), $request->request->get('_token'))) {
            $this->repositoryAdapter->update($this->articleManager->changeStatus($article));
        }

        return $this->redirectToRoute('app_article_index');
    }

    /**
     * @Route("/{slug}", name="delete", methods="DELETE")
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getSlug(), $request->request->get('_token'))) {
            $this->repositoryAdapter->delete($article);
        }

        return $this->redirectToRoute('app_article_index');
    }
}
