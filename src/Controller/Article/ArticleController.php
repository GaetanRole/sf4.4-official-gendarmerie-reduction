<?php

declare(strict_types=1);

namespace App\Controller\Article;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article", name="app_article_")
 * @IsGranted("ROLE_USER")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ArticleController extends AbstractController
{
    /** @var ArticleRepository */
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/{page<[1-9]\d*>?1}", name="index", methods="GET")
     */
    public function index(int $page): Response
    {
        return $this->render('article/index.html.twig', [
            'paginator' => $this->articleRepository->findLatestBy($page),
        ]);
    }

    /**
     * @todo    Trans IsGranted message.
     *
     * @IsGranted("view", subject="article", message="You do not have rights to view this disabled article.")
     * @Route("/{slug}/view", name="view", methods="GET")
     */
    public function view(Article $article): Response
    {
        return $this->render('article/view.html.twig', [
            'article' => $article,
        ]);
    }
}
