<?php

declare(strict_types=1);

namespace App\Controller\Opinion;

use \Exception;
use App\Entity\Reduction;
use App\Form\OpinionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Adapter\RepositoryAdapterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    public function __construct(RepositoryAdapterInterface $repositoryAdapter)
    {
        $this->repositoryAdapter = $repositoryAdapter;
    }

    /**
     * Adding one Opinion on an existing Reduction (get by slug).
     * @todo    Probably have to add a dynamic form below a Reduction.
     *
     * @Route("/comment/{slug}", name="comment", methods={"GET","POST"})
     * @throws  Exception Datetime Exception
     */
    public function comment(Request $request, Reduction $reduction): Response
    {
        $form = $this->createForm(OpinionType::class);
        $form->handleRequest($request);

        if ($reduction && $form->isSubmitted() && $form->isValid()) {
            $opinion = $form->getData();
            $opinion->setClientIp($request->getClientIp());
            $opinion->setUser($this->getUser());
            $opinion->setReduction($reduction);

            $this->repositoryAdapter->save($opinion);
            return $this->redirectToRoute('app_reduction_show', ['slug' => $reduction->getSlug()]);
        }

        return $this->render('opinion/comment.html.twig', ['form' => $form->createView()]);
    }
}
