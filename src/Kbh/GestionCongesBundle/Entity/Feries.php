<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feries
 *
 * @ORM\Table(name="feries_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\FeriesRepository")
 */
class Feries
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
     * @var string
     *
     * @ORM\Column(name="titreFeries", type="text")
     */
    private $titreFeries;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebutFerie", type="datetime")
     */
    private $dateDebutFerie;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFinFerie", type="datetime")
     */
    private $dateFinFerie;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbJoursFerie", type="integer")
     */
    private $nbJoursFerie;


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
     * Set titreFeries
     *
     * @param string $titreFeries
     * @return Feries
     */
    public function setTitreFeries($titreFeries)
    {
        $this->titreFeries = $titreFeries;

        return $this;
    }

    /**
     * Get titreFeries
     *
     * @return string 
     */
    public function getTitreFeries()
    {
        return $this->titreFeries;
    }

    /**
     * Set dateDebutFerie
     *
     * @param \DateTime $dateDebutFerie
     * @return Feries
     */
    public function setDateDebutFerie($dateDebutFerie)
    {
        $this->dateDebutFerie = $dateDebutFerie;

        return $this;
    }

    /**
     * Get dateDebutFerie
     *
     * @return \DateTime 
     */
    public function getDateDebutFerie()
    {
        return $this->dateDebutFerie;
    }

    /**
     * Set dateFinFerie
     *
     * @param \DateTime $dateFinFerie
     * @return Feries
     */
    public function setDateFinFerie($dateFinFerie)
    {
        $this->dateFinFerie = $dateFinFerie;

        return $this;
    }

    /**
     * Get dateFinFerie
     *
     * @return \DateTime 
     */
    public function getDateFinFerie()
    {
        return $this->dateFinFerie;
    }

    /**
     * Set nbJoursFerie
     *
     * @param integer $nbJoursFerie
     * @return Feries
     */
    public function setNbJoursFerie($nbJoursFerie)
    {
        $this->nbJoursFerie = $nbJoursFerie;

        return $this;
    }

    /**
     * Get nbJoursFerie
     *
     * @return integer 
     */
    public function getNbJoursFerie()
    {
        return $this->nbJoursFerie;
    }
}
