<?php

declare(strict_types = 1);

namespace App\Twig\Extension;

use App\Entity\Image;
use Symfony\Component\Asset\Context\RequestStackContext;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UploadedImagesExtension extends AbstractExtension
{
    public const UPLOAD_DIRECTORY = 'uploads';
    public const DEFAULT_IMAGE = 'default-reduction-image.jpeg';

    /** @var RequestStackContext */
    private $requestStackContext;

    public function __construct(RequestStackContext $requestStackContext)
    {
        $this->requestStackContext = $requestStackContext;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploaded_asset', [$this, 'getUploadedAssetPath'])
        ];
    }

    public function getUploadedAssetPath(?Image $image): string
    {
        $path = !$image ? self::UPLOAD_DIRECTORY.'/'.self::DEFAULT_IMAGE : $image->getFilePath();

        return $this->requestStackContext
                ->getBasePath().'/'.self::UPLOAD_DIRECTORY.'/images/'.$path;
    }
}
