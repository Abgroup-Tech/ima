<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conge
 *
 * @ORM\Table(name="conge_premium")
 * @ORM\Entity
 */
class Conge
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
     * @var decimal
     *
     * @ORM\Column(name="nbJoursOuvrables", type="decimal", precision=10, scale=0, nullable=false)
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
    
    public function __construct(){
        
    }
    
    public function __toString() {
        return "Congé ".$this->getId().' de '.$this->getSalarie();
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
     * @return Conge
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
     * @return Conge
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
     * Set nbJoursOuvrables
     *
     * @param decimal $nbJoursOuvrables
     * @return Conge
     */
    public function setNbJoursOuvrables($nbJoursOuvrables)
    {
        $this->nbJoursOuvrables = $nbJoursOuvrables;

        return $this;
    }

    /**
     * Get nbJoursOuvrables
     *
     * @return decimal 
     */
    public function getNbJoursOuvrables()
    {
        return $this->nbJoursOuvrables;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return Conge
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
     * @return Conge
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
     * @return Conge
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

}
