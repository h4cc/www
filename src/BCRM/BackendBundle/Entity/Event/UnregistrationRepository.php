<?php

/**
 * @author    Markus Tacker <m@coderbyheart.de>
 * @copyright 2013 Verein zur Förderung der Netzkultur im Rhein-Main-Gebiet e.V. | http://netzkultur-rheinmain.de/
 */

namespace BCRM\BackendBundle\Entity\Event;

interface UnregistrationRepository
{
    /**
     * @return Unregistration[]
     */
    public function getNewUnregistrations();

    /**
     * @param string $id
     * @param string $key
     *
     * @return \PhpOption\Option
     */
    public function getUnregistrationByIdAndKey($id, $key);

    /**
     * @param Event $event
     *
     * @return Unregistration[]
     */
    public function getUnprocessedUnregistrations(Event $event);
}
