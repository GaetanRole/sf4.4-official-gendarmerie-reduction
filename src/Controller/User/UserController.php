<?php

declare(strict_types=1);

namespace App\Controller\User;

use \Exception;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\OpinionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="app_user_")
 * @IsGranted("ROLE_USER")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UserController extends AbstractController
{
    /** @var int Article show per user. */
    private const USER_ARTICLE_PAGE_SIZE = 5;

    /**
     * @Route("/{uuid<^.{36}$>}", name="show", methods="GET")
     *
     * @throws Exception Datetime Exception
     */
    public function show(
        User $user,
        OpinionRepository $opinionRepository,
        ArticleRepository $articleRepository
    ): Response {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $opinions = $currentUser->isAdmin() ?
            $opinionRepository->findBy(['user' => $user], ['createdAt' => 'DESC']) :
            $opinionRepository->findLatestByUser($user);

        $articles = $currentUser->isAdmin() ?
            $articleRepository->findBy(['user' => $user], ['createdAt' => 'DESC']) :
            $articleRepository->findLatestByUser($user, self::USER_ARTICLE_PAGE_SIZE);


        return $this->render('user/show.html.twig', [
            'user' => $user,
            'opinions' => $opinions,
            'articles' => $articles,
        ]);
    }
}
