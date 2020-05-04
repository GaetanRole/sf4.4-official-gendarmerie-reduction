<?php

declare(strict_types=1);

namespace App\Controller\Reduction;

use App\Form\SearchType;
use App\Service\EntityManager\ReductionManager;
use \Exception;
use App\Entity\Reduction;
use App\Form\ReductionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Adapter\RepositoryAdapterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/reduction", name="app_reduction_")
 * @IsGranted("ROLE_USER")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ReductionController extends AbstractController
{
    /** @var RepositoryAdapterInterface */
    private $repositoryAdapter;

    public function __construct(RepositoryAdapterInterface $repositoryAdapter)
    {
        $this->repositoryAdapter = $repositoryAdapter;
    }

    /**
     * Array instance to receive form data related to GeoApiFieldsSubscriber.
     *
     * @see GeoApiFieldsSubscriber
     *
     * @Route(name="index", methods={"GET"})
     */
    public function index(): Response
    {
        $form = $this->createForm(
            SearchType::class,
            ['region' => null, 'department' => null, 'municipality' => null],
            ['action' => $this->generateUrl('app_reduction_search_handle'), 'method' => 'GET']
        );

        return $this->render('reduction/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @see     ImageUploadListener
     *
     * @Route("/post", name="post", methods={"GET","POST"})
     * @throws  Exception Datetime Exception
     */
    public function post(Request $request, ReductionManager $reductionManager): Response
    {
        $reduction = new Reduction();
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repositoryAdapter->save(
                $reductionManager->prepareAPostedReduction($reduction, $request->getClientIp(), $this->getUser()),
                'reduction.save.flash.success'
            );

            return $this->redirectToRoute('app_reduction_index');
        }

        return $this->render('reduction/post.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @todo    Add all related Opinions (PagerFanta) and trans IsGranted message.
     *
     * @IsGranted("view", subject="reduction", message="You do not have rights to view this unverified reduction.")
     * @Route("/{slug}/view", name="view", methods={"GET"})
     */
    public function view(Reduction $reduction): Response
    {
        return $this->render('reduction/view.html.twig', ['reduction' => $reduction]);
    }
}
