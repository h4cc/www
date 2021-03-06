<?php

/**
 * @author    Markus Tacker <m@coderbyheart.de>
 * @copyright 2013 Verein zur Förderung der Netzkultur im Rhein-Main-Gebiet e.V. | http://netzkultur-rheinmain.de/
 */

namespace BCRM\BackendBundle\Entity\Event;

use PhpOption\Option;

interface EventRepository
{
    /**
     * @return Option
     */
    public function getNextEvent();

    /**
     * @param Event $event
     * @param       $day
     *
     * @return integer
     */
    public function getCapacity(Event $event, $day);
}
