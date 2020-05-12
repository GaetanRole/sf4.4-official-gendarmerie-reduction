<?php

declare(strict_types=1);

namespace App\Controller\Opinion;

use App\Entity\Reduction;
use App\Form\OpinionType;
use App\Repository\Adapter\RepositoryAdapterInterface;
use App\Service\EntityManager\OpinionManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/opinion", name="app_opinion_")
 * @IsGranted("ROLE_USER")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class OpinionController extends AbstractController
{
    /** @var RepositoryAdapterInterface */
    private $repositoryAdapter;

    /** @var OpinionManager */
    private $opinionManager;

    public function __construct(
        RepositoryAdapterInterface $repositoryAdapter,
        OpinionManager $opinionManager
    ) {
        $this->repositoryAdapter = $repositoryAdapter;
        $this->opinionManager = $opinionManager;
    }

    /**
     * This method is called via a Twig render() function in reduction/view.html.twig.
     * Ajax is used to submit this one.
     */
    public function commentForm(Reduction $reduction): Response
    {
        $form = $this->createForm(
            OpinionType::class,
            null,
            ['action' => $this->generateUrl('app_opinion_comment', ['slug' => $reduction->getSlug()])]
        );

        return $this->render('opinion/_comment_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Adding one Opinion on an existing Reduction and return a response (handled by AJAX).
     * If the form is valid, data are persisted and the page is reloaded (a simple POST action).
     * If not, form errors are sent in error Ajax function.
     *
     * @Route("/comment/{slug}", name="comment", methods={"GET", "POST"})
     */
    public function comment(Request $request, Reduction $reduction): Response
    {
        $form = $this->createForm(OpinionType::class);
        $form->handleRequest($request);

        if ($reduction && $form->isSubmitted() && $form->isValid()) {
            $this->repositoryAdapter->save(
                $this->opinionManager->prepare(
                    $form->getData(),
                    $reduction,
                    $request->getClientIp(),
                    $this->getUser()
                ),
            );

            return new Response('Page is reloaded for flash messages and comments.');
        }

        return new Response(
            $this->renderView('opinion/_comment_form.html.twig', [
                'form' => $form->createView(),
            ]),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
