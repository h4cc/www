<?php

namespace BCRM\BackendBundle\Entity\Event;

use BCRM\BackendBundle\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use LiteCQRS\Plugin\CRUD\AggregateResource;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="BCRM\BackendBundle\Entity\Event\DoctrineTicketRepository")
 */
class Ticket extends AggregateResource
{
    const DAY_SATURDAY = 1;

    const DAY_SUNDAY = 2;

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="BCRM\BackendBundle\Entity\Event\Event", inversedBy="tickets")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=false)
     * @var Event
     */
    protected $event;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="text")
     */
    protected $email;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Range(min=1,max=2)
     * @ORM\Column(type="integer")
     */
    protected $day;

    /**
     * @ORM\Column(type="string", nullable=false, name="code")
     * @var string Ticket code
     */
    protected $code;

    /**
     * @var boolean
     * @Assert\NotBlank()
     * @Assert\Type(type="boolean")
     * @ORM\Column(type="boolean")
     */
    protected $notified = 0;

    /**
     * @var boolean
     * @Assert\NotBlank()
     * @Assert\Type(type="boolean")
     * @ORM\Column(type="boolean", name="checked_in")
     */
    protected $checkedIn = 0;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $updated;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $str  = $this->email;
        $days = array();
        if ($this->day === static::DAY_SATURDAY) {
            $days[] = 'SA';
        } else {
            $days[] = 'SU';
        }
        $str .= ' (' . join('+', $days) . ')';
        return $str;
    }

    /**
     * @return bool
     */
    public function isSaturday()
    {
        return $this->getDay() === static::DAY_SATURDAY;
    }

    /**
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param int $day
     */
    public function setDay($day)
    {
        if (!in_array($day, array(static::DAY_SATURDAY, static::DAY_SUNDAY))) {
            throw new InvalidArgumentException(sprintf('Invalid day: %d', $day));
        }
        $this->day = $day;
    }

    /**
     * @return bool
     */
    public function isSunday()
    {
        return $this->getDay() === static::DAY_SUNDAY;
    }

    /**
     * @return \BCRM\BackendBundle\Entity\Event\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param \BCRM\BackendBundle\Entity\Event\Event $event
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function isCheckedIn()
    {
        return (boolean)$this->checkedIn;
    }

    /**
     * @param boolean $checkedIn
     */
    public function setCheckedIn($checkedIn)
    {
        $this->checkedIn = (boolean)$checkedIn;
    }

}


