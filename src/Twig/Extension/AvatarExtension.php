<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\User;
use Symfony\Component\Asset\Context\RequestStackContext;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class AvatarExtension extends AbstractExtension
{
    public const AVATAR_DIRECTORY = 'assets/images/avatars';

    /** @var RequestStackContext */
    private $requestStackContext;

    public function __construct(RequestStackContext $requestStackContext)
    {
        $this->requestStackContext = $requestStackContext;
    }

    public function getFilters(): array
    {
        return [new TwigFilter('avatar', [$this, 'getAvatar'], ['is_safe' => ['html']])];
    }

    public function getAvatar(User $user): string
    {
        return $this->requestStackContext->getBasePath().'/'.self::AVATAR_DIRECTORY.'/'.$user->getAvatar();
    }
}
