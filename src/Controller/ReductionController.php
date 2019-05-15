<?php

namespace App\Controller;

use Exception;
use App\Entity\Reduction;
use App\Form\ReductionType;
use App\Repository\ReductionRepository;
use App\Utils\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GlobalClock;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo    Add patterns on each methods (mediator, adapter...).
 *
 * @Route("/reduction")
 * @IsGranted("ROLE_USER")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ReductionController extends AbstractController
{
    /**
     * @todo Add paginator PagerFanta.
     * @todo Add search bar and filters.
     *
     * @Route("/", methods={"GET"})
     * @throws Exception Datetime Exception
     */
    public function index(ReductionRepository $reductionRepository): Response
    {
        return $this->render('reduction/index.html.twig', [
            'reductions' => $reductionRepository->findLatest(),
        ]);
    }

    /**
     * @todo Add Image Upload ?
     * @link https://github.com/Innmind/TimeContinuum Global clock
     *
     * @Route("/new", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     * @throws Exception Datetime Exception
     */
    public function new(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        GlobalClock $clock
    ) {
        $reduction = new Reduction();
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reduction->setSlug(Slugger::slugify($reduction->getTitle()));
            $reduction->setClientIp($request->getClientIp());
            $reduction->setCreationDate($clock->getNowInDateTime());
            $reduction->setUser($this->getUser());

            $em->persist($reduction);
            $em->flush();

            $this->addFlash('success', $translator->trans('reduction.new.flash.success', [], 'flashes'));

            return $this->redirectToRoute('app_reduction_index');
        }

        return $this->render('reduction/new.html.twig', [
            'reduction' => $reduction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @todo Add all related Opinions and PagerFanta.
     *
     * @Route("/{slug}", methods={"GET"})
     */
    public function show(Reduction $reduction): Response
    {
        return $this->render('reduction/show.html.twig', [
            'reduction' => $reduction,
        ]);
    }
}
