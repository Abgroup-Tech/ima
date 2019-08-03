<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AbsencesAjustifier
 *
 * @ORM\Table(name="absences_a_justifier_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\AbsencesAjustifierRepository")
 */
class AbsencesAjustifier
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
     * @var \Kbh\GestionCongesBundle\Entity\Demande
     * @ORM\ManyToOne(targetEntity="Demande")
     * @ORM\JoinColumn(name="demande", referencedColumnName="id", nullable=true)
     */
    private $demande;
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Absence
     * @ORM\ManyToOne(targetEntity="Absence")
     * @ORM\JoinColumn(name="absence", referencedColumnName="id", nullable=true)
     */
    private $absence;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateMax", type="datetime", nullable=true)
     */
    private $dateMax;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */
    private $statut;

    /**
     * @var boolean
     *
     * @ORM\Column(name="justifieAtemps", type="boolean")
     */
    private $justifieAtemps;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="absenceJustifiable", type="boolean", nullable=true)
     */
    private $absenceJustifiable;

    /**
     * @var \Kbh\GestionCongesBundle\Entity\PiecesJointes
     * @ORM\ManyToOne(targetEntity="PiecesJointes", cascade={"persist"} ) 
     * @ORM\JoinColumn(name="justificatif", referencedColumnName="id" , nullable=true)
     */
    private $justificatif;


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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return AbsencesAjustifier
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateMax
     *
     * @param \DateTime $dateMax
     * @return AbsencesAjustifier
     */
    public function setDateMax($dateMax)
    {
        $this->dateMax = $dateMax;

        return $this;
    }

    /**
     * Get dateMax
     *
     * @return \DateTime 
     */
    public function getDateMax()
    {
        return $this->dateMax;
    }

    /**
     * Set statut
     *
     * @param string $statut
     * @return AbsencesAjustifier
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string 
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set justifieAtemps
     *
     * @param boolean $justifieAtemps
     * @return AbsencesAjustifier
     */
    public function setJustifieAtemps($justifieAtemps)
    {
        $this->justifieAtemps = $justifieAtemps;

        return $this;
    }

    /**
     * Get justifieAtemps
     *
     * @return boolean 
     */
    public function getJustifieAtemps()
    {
        return $this->justifieAtemps;
    }

    /**
     * Set demande
     *
     * @param \Kbh\GestionCongesBundle\Entity\Demande $demande
     * @return AbsencesAjustifier
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
     * Set absence
     *
     * @param \Kbh\GestionCongesBundle\Entity\absence $absence
     * @return AbsencesAjustifier
     */
    public function setAbsence(\Kbh\GestionCongesBundle\Entity\absence $absence = null)
    {
        $this->absence = $absence;

        return $this;
    }

    /**
     * Get absence
     *
     * @return \Kbh\GestionCongesBundle\Entity\absence 
     */
    public function getAbsence()
    {
        return $this->absence;
    }

    /**
     * Set justificatif
     *
     * @param \Kbh\GestionCongesBundle\Entity\PiecesJointes $justificatif
     * @return AbsencesAjustifier
     */
    public function setJustificatif(\Kbh\GestionCongesBundle\Entity\PiecesJointes $justificatif = null)
    {
        $this->justificatif = $justificatif;

        return $this;
    }

    /**
     * Get justificatif
     *
     * @return \Kbh\GestionCongesBundle\Entity\PiecesJointes 
     */
    public function getJustificatif()
    {
        return $this->justificatif;
    }

    /**
     * Set absenceJustifiable
     *
     * @param boolean $absenceJustifiable
     * @return AbsencesAjustifier
     */
    public function setAbsenceJustifiable($absenceJustifiable)
    {
        $this->absenceJustifiable = $absenceJustifiable;

        return $this;
    }

    /**
     * Get absenceJustifiable
     *
     * @return boolean 
     */
    public function getAbsenceJustifiable()
    {
        return $this->absenceJustifiable;
    }
}
