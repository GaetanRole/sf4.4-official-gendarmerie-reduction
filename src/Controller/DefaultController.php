<?php

declare(strict_types=1);

namespace App\Controller;

use \Exception;
use App\Form\ContactType;
use App\Repository\ReductionRepository;
use App\Service\PromoGendMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(name="app_")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class DefaultController extends AbstractController
{
    /** @var int Reduction number per page. */
    private const DEFAULT_PAGE_SIZE = 6;

    /**
     * @throws Exception Datetime Exception
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ReductionRepository $reductionRepository): Response
    {
        return $this->render('default/index.html.twig', [
            'reductions' => $reductionRepository->findLatestBy(null, self::DEFAULT_PAGE_SIZE),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @Route("/contact", name="contact", methods={"GET", "POST"})
     */
    public function contact(
        Request $request,
        PromoGendMailer $mailer,
        TranslatorInterface $translator
    ): Response {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailer->send(
                'Mail de contact via le site.',
                'email/contact.html.twig',
                $form->getData(),
                Email::PRIORITY_HIGH
            );

            $sender = ucfirst($form->getData()['name']);
            $this->addFlash(
                'info',
                $translator->trans('send.flash.success', ['%name%' => $sender], 'flashes')
            );

            return $this->redirectToRoute('app_index');
        }

        return $this->render('default/contact.html.twig', ['form' => $form->createView()]);
    }
}
