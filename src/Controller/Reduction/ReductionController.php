<?php

declare(strict_types=1);

namespace App\Controller\Reduction;

use \Exception;
use App\Entity\Reduction;
use App\Form\ReductionType;
use EasySlugger\SluggerInterface;
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
     * @todo    Add paginator PagerFanta and search bar with filters.
     *
     * @Route("/", name="index", methods={"GET"})
     * @throws  Exception Datetime Exception
     */
    public function index(): Response
    {
        return $this->render('reduction/index.html.twig', [
            'reductions' => $this->repositoryAdapter->getRepository(Reduction::class)->findLatestBy()
        ]);
    }

    /**
     * @see     ImageUploadListener
     *
     * @Route("/post", name="post", methods={"GET","POST"})
     * @throws  Exception Datetime Exception
     */
    public function post(Request $request, SluggerInterface $slugger): Response
    {
        $reduction = new Reduction();
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reduction->setClientIp($request->getClientIp());
            $reduction->setSlug($slugger->uniqueSlugify($reduction->getTitle()));
            $reduction->setUser($this->getUser());

            $this->repositoryAdapter->save($reduction);
            return $this->redirectToRoute('app_reduction_index');
        }

        return $this->render('reduction/post.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @todo    Add all related Opinions and PagerFanta.
     *
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Reduction $reduction): Response
    {
        return $this->render('reduction/show.html.twig', ['reduction' => $reduction]);
    }
}
