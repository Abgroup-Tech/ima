<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\EventRepository")
 */
class Event
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $event;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $beginDate;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $endDate;

    function getId() {
        return $this->id;
    }

    function getEvent() {
        return $this->event;
    }

    function getBeginDate() {
        return $this->beginDate;
    }

    function getEndDate() {
        return $this->endDate;
    }

    function setEvent($event) {
        $this->event = $event;
    }

    function setBeginDate($beginDate) {
        $this->beginDate = $beginDate;
    }

    function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    public function __toString() {
        return $this->getId();
    }
}