<?php

namespace BCRM\BackendBundle\Entity\Event;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use LiteCQRS\Plugin\CRUD\AggregateResource;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Registration
 *
 * @ORM\Table(name="registration")
 * @ORM\Entity(repositoryClass="BCRM\BackendBundle\Entity\Event\DoctrineRegistrationRepository")
 */
class Registration extends AggregateResource
{
    const ARRIVAL_PUBLIC = 'public';

    const ARRIVAL_PRIVATE = 'private';

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="BCRM\BackendBundle\Entity\Event\Event", inversedBy="registrations")
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
     * @var boolean
     * @Assert\NotBlank()
     * @Assert\Type(type="boolean")
     * @ORM\Column(type="boolean")
     */
    protected $saturday;

    /**
     * @var boolean
     * @Assert\NotBlank()
     * @Assert\Type(type="boolean")
     * @ORM\Column(type="boolean")
     */
    protected $sunday;

    /**
     * @ORM\Column(type="string", nullable=true, name="confirmation_key")
     * @var string Login-Key
     */
    protected $confirmationKey;

    /**
     * @var string Arrival
     * @ORM\Column(type="text", nullable=true)
     */
    protected $arrival;

    /**
     * @var boolean
     * @Assert\NotBlank()
     * @Assert\Type(type="boolean")
     * @ORM\Column(type="boolean")
     */
    protected $confirmed = 0;

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
        $this->email = strtolower($email);
    }

    /**
     * @return string
     */
    public function getConfirmationKey()
    {
        return $this->confirmationKey;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $str  = $this->email;
        $days = array();
        if ($this->saturday) {
            $days[] = 'SA';
        }
        if ($this->sunday) {
            $days[] = 'SU';
        }
        $str .= ' (' . join('+', $days) . ')';
        return $str;
    }

    /**
     * @return boolean
     */
    public function getSunday()
    {
        return $this->sunday;
    }

    /**
     * @return boolean
     */
    public function getSaturday()
    {
        return $this->saturday;
    }

    /**
     * @return string
     */
    public function getArrival()
    {
        return $this->arrival;
    }

    /**
     * @param string $arrival
     */
    public function setArrival($arrival)
    {
        if (!in_array($arrival, array(self::ARRIVAL_PRIVATE, self::ARRIVAL_PUBLIC))) {
            throw new \InvalidArgumentException("Invalid arrival");
        }
        $this->arrival = $arrival;
    }


}

