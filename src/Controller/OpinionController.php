<?php

declare(strict_types = 1);

namespace App\Controller;

use \Exception;
use App\Entity\Opinion;
use App\Entity\Reduction;
use App\Form\OpinionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\ModelAdapter\EntityRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/opinion", name="app_opinion_")
 * @IsGranted("ROLE_USER")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class OpinionController extends AbstractController
{
    /** @var EntityRepositoryInterface */
    private $entityRepository;

    public function __construct(EntityRepositoryInterface $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     * Adding one Opinion on an existing Reduction.
     * @todo    Probably have to add a dynamic form below a Reduction.
     *
     * @Route("/new/{slug}", name="new", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function new(Request $request, Reduction $reduction): Response
    {
        $opinion = new Opinion();
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($reduction && $form->isSubmitted() && $form->isValid()) {
            $opinion->setClientIp($request->getClientIp());
            $opinion->setUser($this->getUser());
            $opinion->setReduction($reduction);

            $this->entityRepository->save($opinion);
            return $this->redirectToRoute('app_reduction_show', ['slug' => $reduction->getSlug()]);
        }

        return $this->render('opinion/new.html.twig', ['opinion' => $opinion, 'form' => $form->createView()]);
    }
}
