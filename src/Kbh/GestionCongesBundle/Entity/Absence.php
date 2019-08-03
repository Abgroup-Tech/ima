<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Absence
 *
 * @ORM\Table(name="absence_premium")
 * @ORM\Entity
 */
class Absence
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var String
     * 
     * @ORM\Column(name="motif", type="string", nullable=false)
     */
    private $motif;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="date", nullable=false)
     */
    private $dateDebut;

    /**
     * @var string
     *
     * @ORM\Column(name="dateFin", type="string", length=255, nullable=true)
     */
    private $dateFin;
	
     /**
     * @var string
     *
     * @ORM\Column(name="dateRetour", type="string", length=255, nullable=true)
     */
    private $dateRetour;

    /**
     * @var float
     *
     * @ORM\Column(name="nbJoursOuvrables", type="float", nullable=true)
     */
    private $nbJoursOuvrables;

    /**
     * @var \Salarie
     *
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id")
     * 
     */
    private $salarie;

        /**
     * @var \Demande
     *
     * @ORM\OneToOne(targetEntity="Demande")
     * @ORM\JoinColumn(name="demande", referencedColumnName="id")
     * 
     */
    private $demande;
    
    public function __construct() {

    }   
  
    public function __toString() {
        return "Absence pour ".$this->getMotif()." de ".$this->getSalarie();
    }


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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Absence
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param string  $dateFin
     * @return Absence
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return string 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set motif
     *
     * @param String $motif
     * @return Absence
     */
    public function setMotif($motif)
    {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get motif
     *
     * @return String 
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return Absence
     */
    public function setSalarie(\Kbh\GestionCongesBundle\Entity\Salarie $salarie = null)
    {
        $this->salarie = $salarie;

        return $this;
    }

    /**
     * Get salarie
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie 
     */
    public function getSalarie()
    {
        return $this->salarie;
    }

    /**
     * Set demande
     *
     * @param \Kbh\GestionCongesBundle\Entity\Demande $demande
     * @return Absence
     */
    public function setDemande(\Kbh\GestionCongesBundle\Entity\Demande $demande = null)
    {
        $this->demande = $demande;

        return $this;
    }

    /**
     * Get demande
     *
     * @return \Kbh\GestionCongesBundle\Entity\Demande 
     */
    public function getDemande()
    {
        return $this->demande;
    }

    /**
     * Set dateRetour
     *
     * @param string $dateRetour
     * @return Absence
     */
    public function setDateRetour($dateRetour)
    {
        $this->dateRetour = $dateRetour;

        return $this;
    }
	/**
     * Get dateRetour
     *
     * @return string 
     */
    public function getDateRetour()
    {
        return $this->dateRetour;
    }

    /**
     * Set nbJoursOuvrables
     *
     * @param float $nbJoursOuvrables
     * @return Absence
     */
    public function setNbJoursOuvrables($nbJoursOuvrables)
    {
        $this->nbJoursOuvrables = $nbJoursOuvrables;

        return $this;
    }

    /**
     * Get nbJoursOuvrables
     *
     * @return float 
     */
    public function getNbJoursOuvrables()
    {
        return $this->nbJoursOuvrables;
    }
}
