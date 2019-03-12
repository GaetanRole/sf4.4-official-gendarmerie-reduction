<?php

/**
 * Opinion Controller File
 *
 * PHP Version 7.2
 *
 * @category    Opinion
 * @package     App\Controller
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller;

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

/**
 * Opinion Controller Class
 *
 * @todo Add patterns on new() method (mediator, adapter...)
 *
 * @category    Opinion
 * @package     App\Controller
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/opinion")
 * @IsGranted("ROLE_USER")
 */
class OpinionController extends AbstractController
{
    /**
     * Adding one Opinion on a existing Reduction
     *
     * @todo Probably have to add a dynamic form below a Reduction
     *
     * @link https://github.com/Innmind/TimeContinuum Global clock
     * @param Request $request POST'ed data
     * @param Reduction $reduction According to one reduction
     * @param EntityManagerInterface $em Entity Manager
     * @param GlobalClock $clock Global project's clock
     *
     * @Route("/new/{slug}", name="opinion_new", methods={"GET","POST"})
     * @return RedirectResponse|Response A Response instance
     * @throws \Exception Datetime Exception
     */
    public function new(
        Request $request,
        Reduction $reduction,
        EntityManagerInterface $em,
        GlobalClock $clock
    ): Response {
        $opinion = new Opinion();
        $form = $this->createForm(OpinionType::class, $opinion);
        $form->handleRequest($request);

        if ($reduction && $form->isSubmitted() && $form->isValid()) {
            $opinion->setClientIp($request->getClientIp());
            $opinion->setCreationDate($clock->getNowInDateTime());
            $opinion->setUser($this->getUser());
            $opinion->setReduction($reduction);

            $em->persist($opinion);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a bien été ajouté.');

            return $this->redirectToRoute('reduction_show', ['slug' => $reduction->getSlug()]);
        }

        return $this->render('opinion/new.html.twig', [
            'opinion' => $opinion,
            'form' => $form->createView(),
        ]);
    }
}
