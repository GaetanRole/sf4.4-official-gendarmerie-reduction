<?php

/**
 * Reduction Controller File
 *
 * PHP Version 7.2
 *
 * @category    Reduction
 * @package     App\Controller
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller;

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

/**
 * Reduction Controller Class
 *
 * @todo Add patterns on each methods (mediator, adapter...)
 *
 * @category    Reduction
 * @package     App\Controller
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/reduction")
 * @IsGranted("ROLE_USER")
 */
class ReductionController extends AbstractController
{
    /**
     * Reduction home page
     *
     * @todo Add paginator PagerFanta
     * @todo Add search bar and filters
     *
     * @param ReductionRepository $reductionRepository Reduction manager
     *
     * @Route("/", name="reduction_index", methods={"GET"})
     * @return Response A Response instance
     * @throws \Exception Datetime Exception
     */
    public function index(ReductionRepository $reductionRepository): Response
    {
        return $this->render('reduction/index.html.twig', [
            'reductions' => $reductionRepository->findLatest(),
        ]);
    }

    /**
     * Adding one Reduction
     *
     * @todo Add Image Upload ?
     *
     * @link https://github.com/Innmind/TimeContinuum Global clock
     * @param Request $request POST'ed data
     * @param EntityManagerInterface $em Entity Manager
     * @param GlobalClock $clock Global project's clock
     *
     * @Route("/new", name="reduction_new", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     * @throws \Exception Datetime Exception
     */
    public function new(
        Request $request,
        EntityManagerInterface $em,
        GlobalClock $clock
    ): Response {
        $reduction = new Reduction();
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Call api geo gouv
            $reduction->setRegion('');
            $reduction->setSlug(Slugger::slugify($reduction->getTitle()));
            $reduction->setClientIp($request->getClientIp());
            $reduction->setCreationDate($clock->getNowInDateTime());
            $reduction->setUser($this->getUser());

            $em->persist($reduction);
            $em->flush();

            $this->addFlash('success', 'La réduction a bien été ajoutée.');

            return $this->redirectToRoute('reduction_index');
        }

        return $this->render('reduction/new.html.twig', [
            'reduction' => $reduction,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Find and display a Reduction and all Opinions
     *
     * @todo Add all related Opinions and PagerFanta
     *
     * @param Reduction $reduction Reduction given by an id
     *
     * @see https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
     * @Route("/{slug}", name="reduction_show", methods={"GET"})
     * @return     Response A Response instance
     */
    public function show(Reduction $reduction): Response
    {
        return $this->render('reduction/show.html.twig', [
            'reduction' => $reduction,
        ]);
    }
}
