<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AbsencesAT
 *
 * @ORM\Table(name="absences_at_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\AbsencesATRepository")
 */
class AbsencesAT
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
     * @var \Salarie
     *
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id")
     * 
     */
    private $salarie;

    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="string", length=255)
     */
    private $motif;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetime")
     */
    private $dateDebut;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetime")
     */
    private $dateFin;

    /**
     * @var \Salarie
     *
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="admin", referencedColumnName="id")
     * 
     */
    private $admin;
    
     /**
     * @var \Kbh\GestionCongesBundle\Entity\PiecesJointes
     * @ORM\ManyToOne(targetEntity="PiecesJointes") 
     * @ORM\JoinColumn(name="pieceJustificative", referencedColumnName="id" , nullable=true)
     */
    private $pieceJustificative;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;
    
    /**
     * @var string
     *
     * @ORM\Column(name="medecin", type="string", length=255)
     */
    private $medecin;
    
   /**
     * @var string
     *
     * @ORM\Column(name="infoCabinetMedical", type="string", length=555)
     */
    private $infoCabinetMedical;

   
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
     * Set motif
     *
     * @param string $motif
     * @return AbsencesAT
     */
    public function setMotif($motif)
    {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get motif
     *
     * @return string 
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return AbsencesAT
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
     * @param \DateTime $dateFin
     * @return AbsencesAT
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return AbsencesAT
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
     * Set medecin
     *
     * @param string $medecin
     * @return AbsencesAT
     */
    public function setMedecin($medecin)
    {
        $this->medecin = $medecin;

        return $this;
    }

    /**
     * Get medecin
     *
     * @return string 
     */
    public function getMedecin()
    {
        return $this->medecin;
    }

    /**
     * Set infoCabinetMedical
     *
     * @param string $infoCabinetMedical
     * @return AbsencesAT
     */
    public function setInfoCabinetMedical($infoCabinetMedical)
    {
        $this->infoCabinetMedical = $infoCabinetMedical;

        return $this;
    }

    /**
     * Get infoCabinetMedical
     *
     * @return string 
     */
    public function getInfoCabinetMedical()
    {
        return $this->infoCabinetMedical;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return AbsencesAT
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
     * Set admin
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $admin
     * @return AbsencesAT
     */
    public function setAdmin(\Kbh\GestionCongesBundle\Entity\Salarie $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set pieceJustificative
     *
     * @param \Kbh\GestionCongesBundle\Entity\PiecesJointes $pieceJustificative
     * @return AbsencesAT
     */
    public function setPieceJustificative(\Kbh\GestionCongesBundle\Entity\PiecesJointes $pieceJustificative = null)
    {
        $this->pieceJustificative = $pieceJustificative;

        return $this;
    }

    /**
     * Get pieceJustificative
     *
     * @return \Kbh\GestionCongesBundle\Entity\PiecesJointes 
     */
    public function getPieceJustificative()
    {
        return $this->pieceJustificative;
    }
}
