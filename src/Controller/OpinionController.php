<?php

namespace App\Controller;

use Exception;
use App\Entity\Opinion;
use App\Entity\Reduction;
use App\Form\OpinionType;
use App\Service\GlobalClock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo    Add patterns on new() method (mediator, adapter...).
 *
 * @Route("/opinion", name="app_opinion_")
 * @IsGranted("ROLE_USER")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class OpinionController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var GlobalClock */
    private $clock;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(EntityManagerInterface $em, GlobalClock $clock, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->clock = $clock;
        $this->translator = $translator;
    }

    /**
     * Adding one Opinion on an existing Reduction.
     * @todo    Probably have to add a dynamic form below a Reduction.
     *
     * @Route("/new/{slug}", name="new", methods={"GET","POST"})
     * @return  RedirectResponse|Response A Response instance
     * @throws  Exception Datetime Exception
     */
    public function new(Request $request, Reduction $reduction)
    {
        $opinion = new Opinion();
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($reduction && $form->isSubmitted() && $form->isValid()) {
            $opinion->setClientIp($request->getClientIp());
            $opinion->setCreationDate($this->clock->getNowInDateTime());
            $opinion->setUser($this->getUser());
            $opinion->setReduction($reduction);

            $this->em->persist($opinion);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('opinion.new.flash.success', [], 'flashes'));
            return $this->redirectToRoute('app_reduction_show', ['slug' => $reduction->getSlug()]);
        }

        return $this->render('opinion/new.html.twig', [
            'opinion' => $opinion,
            'form' => $form->createView(),
        ]);
    }
}
