<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\ReductionRepository;
use \Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(name="app_")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class DefaultController extends AbstractController
{
    /**
     * @throws Exception Datetime Exception
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ReductionRepository $reductionRepository): Response
    {
        return $this->render('default/index.html.twig', ['reductions' => $reductionRepository->findLatestBy(null, 6)]);
    }

    /**
     * @Route("/contact", name="contact", methods={"GET", "POST"})
     */
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // @TODO Calling Emailing service here
            $this->addFlash('success', $form->getData()['name'].' your email has been sent.');

            return $this->redirectToRoute('app_index');
        }

        return $this->render('default/contact.html.twig', ['form' => $form->createView()]);
    }
}
