<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Image;
use Liip\ImagineBundle\Templating\FilterExtension;
use Symfony\Component\Asset\Context\RequestStackContext;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * A Twig extension completed by Imagine filter extension,
 * to store and get the resolve path with asset versioning.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UploadedImagesExtension extends AbstractExtension
{
    public const UPLOAD_DIRECTORY = 'uploads';
    public const DEFAULT_IMAGE = 'default-reduction-image.jpeg';

    /** @var RequestStackContext */
    private $requestStackContext;

    /** @var FilterExtension */
    private $filterExtension;

    public function __construct(RequestStackContext $requestStackContext, FilterExtension $filterExtension)
    {
        $this->requestStackContext = $requestStackContext;
        $this->filterExtension = $filterExtension;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [$this, 'getUploadedAndCachedAssetPath'])
        ];
    }

    public function getUploadedAndCachedAssetPath(?Image $image, string $filter = 'thumbnail'): string
    {
        // '?v='.time() to constantly refresh the cache and resolve URL to avoid 301 and then 404.
        // @see https://github.com/liip/LiipImagineBundle/issues/850
        return $this->filterExtension->filter($this->getDefaultOrRealFilePath($image), $filter).'?v='.time();
    }

    private function getDefaultOrRealFilePath(?Image $image): string
    {
        $file = (!$image || !$image->getFile()) ? '/'.self::DEFAULT_IMAGE : '/images'.$image->getFilePath();

        return $this->requestStackContext->getBasePath().'/'.self::UPLOAD_DIRECTORY.$file;
    }
}
