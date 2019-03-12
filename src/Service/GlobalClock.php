<?php

/**
 * GlobalClock service file
 *
 * PHP Version 7.2
 *
 * @category    Clock
 * @package     App\Service
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Service;

use Innmind\TimeContinuum\Format\ISO8601;
use Innmind\TimeContinuum\TimeContinuum\Earth;
use Innmind\TimeContinuum\Timezone\Earth\Europe\Paris;

/**
 * GlobalClock service class.
 *
 * @see         https://github.com/Innmind/TimeContinuum
 * @category    Clock
 * @package     App\Service
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class GlobalClock
{
    /**
     * Global clock
     *
     * @var Earth
     */
    private $clock;

    /**
     * Default clock format
     *
     * @var ISO8601
     */
    private $format;

    /**
     * GlobalClock constructor.
     *
     * @param Earth $clock Get global clock
     * @param ISO8601 $format Get a format to $clock
     */
    public function __construct(Earth $clock, ISO8601 $format)
    {
        $this->clock = $clock;
        $this->format = $format;
    }

    /**
     * Clock getter
     *
     * @return Earth
     */
    public function getClock(): Earth
    {
        return $this->clock;
    }

    /**
     * Returning a DateTime obj based on timeZone
     *
     * @return \DateTime DateTime at now
     * @throws \Exception Datetime Exception
     */
    public function getNowInDateTime(): \DateTime
    {
        $now
            = $this->clock->now()->changeTimezone(new Paris())->format($this->format);

        return new \DateTime((string)$now);
    }
}
