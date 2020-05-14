<?php

declare(strict_types=1);

namespace App\Controller;

use \Exception;
use App\Form\ContactType;
use App\Repository\ArticleRepository;
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
    /** @var int Article number per page. */
    private const DEFAULT_ARTICLE_PAGE_SIZE = 3;

    /** @var int Reduction number per page. */
    private const DEFAULT_REDUCTION_PAGE_SIZE = 6;

    /**
     * @todo Think about render(controller()) and to use cache.
     *
     * @throws Exception Datetime Exception
     * @Route("/", name="index", methods="GET")
     */
    public function index(ArticleRepository $articleRepository, ReductionRepository $reductionRepository): Response
    {
        return $this->render('default/index.html.twig', [
            'articles' => $articleRepository->findLatestImportant(self::DEFAULT_ARTICLE_PAGE_SIZE),
            'reductions' => $reductionRepository->findLatestBy(null, self::DEFAULT_REDUCTION_PAGE_SIZE),
        ]);
    }

    /**
     * Emails are written for french people, translations are not required.
     *
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

            $this->addFlash(
                'info',
                $translator->trans('send.flash.success', ['%name%' => ucfirst($form->getData()['name'])], 'flashes')
            );

            return $this->redirectToRoute('app_index');
        }

        return $this->render('default/contact.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @todo Think about cache.
     *
     * @Route("/about/terms", name="about_terms", methods="GET")
     */
    public function terms(): Response
    {
        return $this->render('default/terms.html.twig');
    }

    /**
     * @todo Think about cache.
     *
     * @Route("/about/privacy", name="about_privacy", methods="GET")
     */
    public function privacy(): Response
    {
        return $this->render('default/privacy.html.twig');
    }
}
