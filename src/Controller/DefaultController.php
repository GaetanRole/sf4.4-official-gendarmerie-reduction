<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\ReductionRepository;
use App\Service\PromoGendMailer;
use \Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
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
     * @todo Add translator for flash email message
     *
     * @throws TransportExceptionInterface
     * @Route("/contact", name="contact", methods={"GET", "POST"})
     */
    public function contact(Request $request, PromoGendMailer $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailer->send(
                'Mail de contact via le site.',
                'email/contact.html.twig',
                $form->getData(),
                Email::PRIORITY_HIGH
            );

            $this->addFlash('success', $form->getData()['name'].' your email has been sent.');
            return $this->redirectToRoute('app_index');
        }

        return $this->render('default/contact.html.twig', ['form' => $form->createView()]);
    }
}
