<?php

declare(strict_types=1);

namespace App\Controller\Reduction;

use App\Entity\Opinion;
use App\Entity\Reduction;
use App\Entity\User;
use App\Form\ReductionType;
use App\Form\SearchType;
use App\Repository\Adapter\RepositoryAdapterInterface;
use App\Service\EntityManager\ReductionManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route(name="index", methods="GET")
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
     * @Route("/post", name="post", methods={"GET", "POST"})
     */
    public function post(Request $request, ReductionManager $reductionManager): Response
    {
        $reduction = new Reduction();
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $this->repositoryAdapter->save(
                $reductionManager->prepare($reduction, $user, $request->getClientIp()),
                $user->isAdmin() ? 'save.flash.success' : 'reduction.save.flash.success'
            );

            return $this->redirectToRoute('app_reduction_index');
        }

        return $this->render('reduction/post.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @todo    Trans IsGranted message.
     *
     * @IsGranted("view", subject="reduction", message="You do not have rights to view this unverified reduction.")
     * @Route("/{slug}/view/{page<[1-9]\d*>?1}", name="view", methods="GET")
     */
    public function view(Reduction $reduction, int $page): Response
    {
        $opinions = $this->repositoryAdapter
            ->getRepository(Opinion::class)
            ->findFirstByReduction($reduction, $page)
        ;

        return $this->render('reduction/view.html.twig', [
            'reduction' => $reduction,
            'paginator' => $opinions,
        ]);
    }
}
