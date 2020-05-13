<?php

declare(strict_types=1);

namespace App\DataFixtures;

use \Exception;
use App\Entity\Article;
use App\Service\GlobalClock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use EasySlugger\SluggerInterface;
use Faker;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @see     https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ArticleFixture extends Fixture implements DependentFixtureInterface
{
    /** @var int public CONST for Articles number in DB. */
    public const ARTICLE_NB_TUPLE = 20;

    /** @var string public CONST for reference, concat to an int [0-ARTICLE_NB_TUPLE]. */
    public const ARTICLE_REFERENCE = 'article-';

    /**
     * Global project's clock.
     *
     * @var GlobalClock
     */
    private $clock;

    /** @var ContainerInterface */
    private $container;

    /** @var SluggerInterface */
    private $slugger;

    /**
     * @see    https://github.com/Innmind/TimeContinuum Global clock
     */
    public function __construct(
        GlobalClock $clock,
        ContainerInterface $container,
        SluggerInterface $slugger
    ) {
        $this->clock = $clock;
        $this->container = $container;
        $this->slugger = $slugger;
    }

    /**
     * Load ARTICLE_NB_TUPLE articles to DB.
     *
     * @see     3 Loop iterator depends on const ARTICLE_NB_TUPLE
     * @see    https://github.com/fzaninotto/Faker
     *
     * @throws Exception Datetime Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create($this->container->getParameter('faker_locale'));

        foreach ($this->getArticleReferences() as [$index, $author]) {
            $article = new Article();

            $article->setUuid(Uuid::uuid4());
            $article->setCreatedAt($this->clock->getNowInDateTime());
            $article->setUpdatedAt(null);

            $articleTitle = $index.' '.$faker->text(32);
            $article->setUser($author)
                ->setTitle($articleTitle)
                ->setSlug($this->slugger::uniqueSlugify($articleTitle))
                ->setSummary($faker->realText(254))
                ->setContent($faker->realText(508))
                ->setPriority(random_int(0, 3))
                ->setIsActive((bool) random_int(0, 1))
            ;

            $manager->persist($article);
            $this->addReference(self::ARTICLE_REFERENCE.$index, $article);
        }

        $manager->flush();
    }

    /**
     * Get an array of User references useful for Article instances.
     *
     * @see     7 See UserFixture::USER_NB_TUPLE - 1 for index 0
     *
     * @throws Exception Random Exception
     */
    private function getArticleReferences(): array
    {
        $references = [];
        for ($index = 0; $index < self::ARTICLE_NB_TUPLE; ++$index) {
            // $article = [$index, $author];
            $references[] = [
                $index,
                $this->getReference(UserFixture::USER_REFERENCE.random_int(0, UserFixture::USER_NB_TUPLE - 1)),
            ];
        }

        return $references;
    }

    /**
     * Get dependencies from entity relations.
     */
    public function getDependencies(): array
    {
        return [UserFixture::class];
    }
}
