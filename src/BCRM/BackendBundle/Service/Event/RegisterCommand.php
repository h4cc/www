<?php

/**
 * @author    Markus Tacker <m@coderbyheart.de>
 * @copyright 2013 Verein zur Förderung der Netzkultur im Rhein-Main-Gebiet e.V. | http://netzkultur-rheinmain.de/
 */

namespace BCRM\BackendBundle\Service\Event;

class RegisterCommand
{
    public $event;

    public $email;

    public $name;

    public $saturday;

    public $sunday;
    
    public $arrival;
    
    public $tags;
}
