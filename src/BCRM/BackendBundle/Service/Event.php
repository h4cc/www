<?php

/**
 * @author    Markus Tacker <m@coderbyheart.de>
 * @copyright 2013 Verein zur Förderung der Netzkultur im Rhein-Main-Gebiet e.V. | http://netzkultur-rheinmain.de/
 */

namespace BCRM\BackendBundle\Service;

use BCRM\BackendBundle\Entity\Event\RegistrationRepository;
use BCRM\BackendBundle\Entity\Event\UnregistrationRepository;
use BCRM\BackendBundle\Event\Event\TicketMailSentEvent;
use BCRM\BackendBundle\Service\Event\ConfirmUnregistrationCommand;
use BCRM\BackendBundle\Service\Event\CreateTicketCommand;
use BCRM\BackendBundle\Service\Event\RegisterCommand;
use BCRM\BackendBundle\Service\Event\SendRegistrationConfirmationMailCommand;
use BCRM\BackendBundle\Service\Event\SendTicketMailCommand;
use BCRM\BackendBundle\Service\Event\SendUnregistrationConfirmationMailCommand;
use BCRM\BackendBundle\Service\Event\UnregisterCommand;
use BCRM\BackendBundle\Service\Mail\SendTemplateMailCommand;
use BCRM\BackendBundle\Service\Event\ConfirmRegistrationCommand;
use LiteCQRS\Bus\CommandBus;
use LiteCQRS\Bus\EventMessageBus;
use LiteCQRS\Plugin\CRUD\Model\Commands\CreateResourceCommand;
use LiteCQRS\Plugin\CRUD\Model\Commands\UpdateResourceCommand;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

class Event
{
    /**
     * @var \LiteCQRS\Bus\CommandBus
     */
    private $commandBus;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \BCRM\BackendBundle\Entity\Event\RegistrationRepository
     */
    private $registrationRepo;

    /**
     * @var \LiteCQRS\Bus\EventMessageBus
     */
    private $eventMessageBus;

    /**
     * @var \BCRM\BackendBundle\Entity\Event\UnregistrationRepository
     */
    private $unregistrationRepo;

    /**
     * @param CommandBus      $commandBus
     * @param RouterInterface $router
     */
    public function __construct(CommandBus $commandBus, EventMessageBus $eventMessageBus, RouterInterface $router, RegistrationRepository $registrationRepo, UnregistrationRepository $unregistrationRepo)
    {
        $this->commandBus         = $commandBus;
        $this->eventMessageBus    = $eventMessageBus;
        $this->router             = $router;
        $this->registrationRepo   = $registrationRepo;
        $this->unregistrationRepo = $unregistrationRepo;
    }

    public function register(RegisterCommand $command)
    {
        $createRegistrationCommand        = new CreateResourceCommand();
        $createRegistrationCommand->class = '\BCRM\BackendBundle\Entity\Event\Registration';
        $createRegistrationCommand->data  = array('event' => $command->event, 'email' => $command->email, 'name' => $command->name, 'saturday' => $command->saturday, 'sunday' => $command->sunday, 'arrival' => $command->arrival);
        $this->commandBus->handle($createRegistrationCommand);
    }

    public function sendRegistrationConfirmationMail(SendRegistrationConfirmationMailCommand $command)
    {
        $sr  = new SecureRandom();
        $key = sha1($sr->nextBytes(256), false);
        $this->registrationRepo->initConfirmation($command->registration, $key);

        $emailCommand               = new SendTemplateMailCommand();
        $emailCommand->email        = $command->registration->getEmail();
        $emailCommand->template     = 'RegistrationConfirmation';
        $emailCommand->templateData = array(
            'registration'      => $command->registration,
            'confirmation_link' => rtrim($command->schemeAndHost, '/') . $this->router->generate('bcrm_registration_confirm', array('id' => $command->registration->getId(), 'key' => $key))
        );
        $this->commandBus->handle($emailCommand);
    }

    public function confirmRegistration(ConfirmRegistrationCommand $command)
    {
        $this->registrationRepo->confirmRegistration($command->registration);
    }

    public function createTicket(CreateTicketCommand $command)
    {
        $createSubscriptionCommand        = new CreateResourceCommand();
        $createSubscriptionCommand->class = '\BCRM\BackendBundle\Entity\Event\Ticket';
        $createSubscriptionCommand->data  = array('event' => $command->event, 'email' => $command->registration->getEmail(), 'name' => $command->registration->getName(), 'day' => $command->day);
        $this->commandBus->handle($createSubscriptionCommand);
    }

    public function sendTicketMail(SendTicketMailCommand $command)
    {
        $emailCommand               = new SendTemplateMailCommand();
        $emailCommand->email        = $command->ticket->getEmail();
        $emailCommand->template     = 'Ticket';
        $emailCommand->templateData = array(
            'ticket' => $command->ticket,
            'event'  => $command->event,
        );
        $this->commandBus->handle($emailCommand);

        $event         = new TicketMailSentEvent();
        $event->ticket = $command->ticket;
        $this->eventMessageBus->publish($event);
    }

    public function unregister(UnregisterCommand $command)
    {
        $createUnregistrationCommand        = new CreateResourceCommand();
        $createUnregistrationCommand->class = '\BCRM\BackendBundle\Entity\Event\Unregistration';
        $createUnregistrationCommand->data  = array('event' => $command->event, 'email' => $command->email, 'saturday' => $command->saturday, 'sunday' => $command->sunday);
        $this->commandBus->handle($createUnregistrationCommand);
    }

    public function sendUnregistrationConfirmationMail(SendUnregistrationConfirmationMailCommand $command)
    {
        $sr  = new SecureRandom();
        $key = sha1($sr->nextBytes(256), false);
        $this->unregistrationRepo->initConfirmation($command->unregistration, $key);

        $emailCommand               = new SendTemplateMailCommand();
        $emailCommand->email        = $command->unregistration->getEmail();
        $emailCommand->template     = 'UnregistrationConfirmation';
        $emailCommand->templateData = array(
            'unregistration'    => $command->unregistration,
            'confirmation_link' => rtrim($command->schemeAndHost, '/') . $this->router->generate('bcrm_unregistration_confirm', array('id' => $command->unregistration->getId(), 'key' => $key))
        );
        $this->commandBus->handle($emailCommand);
    }

    public function confirmUnregistration(ConfirmUnregistrationCommand $command)
    {
        $this->unregistrationRepo->confirmUnregistration($command->unregistration);
    }
}
