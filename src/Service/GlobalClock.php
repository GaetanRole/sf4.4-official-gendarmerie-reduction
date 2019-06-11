<?php

declare(strict_types = 1);

namespace App\Service;

use DateTime;
use Exception;
use Innmind\TimeContinuum\Format\ISO8601;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use Innmind\TimeContinuum\Timezone\Earth\Europe\Paris;

/**
 * @see     https://github.com/Innmind/TimeContinuum
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class GlobalClock
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
     * @return  DateTime DateTime at now
     * @throws  Exception Datetime Exception
     */
    public function getNowInDateTime(): DateTime
    {
        $now = $this->clock->now()->changeTimezone(new Paris())->format($this->format);

        return new DateTime($now);
    }
}
