<?php

declare(strict_types = 1);

namespace App\Services;

use \DateTime;
use \Exception;
use \DateTimeInterface;
use Innmind\TimeContinuum\Format\ISO8601;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use Innmind\TimeContinuum\Timezone\Earth\Europe\Paris;

/**
 * @see     https://github.com/Innmind/TimeContinuum
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class GlobalClock
{
    /** @var Earth */
    private $clock;

    /**
     * Default clock format.
     *
     * @var ISO8601
     */
    private $format;

    public function __construct(Earth $clock, ISO8601 $format)
    {
        $this->clock = $clock;
        $this->format = $format;
    }

    public function getClock(): Earth
    {
        return $this->clock;
    }

    /**
     * Returning a DateTime obj based on timeZone.
     *
     * @throws  Exception DateTime Emits Exception in case of an error.
     */
    public function getNowInDateTime(): DateTimeInterface
    {
        return new DateTime($this->clock->now()->changeTimezone(new Paris())->format($this->format));
    }
}
