<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Table(name="report_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\ReportRepository")
 */
class Report
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateReport", type="datetime")
     */
    private $dateReport;

    /**
     * @var string
     *
     * @ORM\Column(name="typeReport", type="string", length=255)
     */
    private $typeReport;

    /**
     * @var string
     *
     * @ORM\Column(name="AncienneDateDebut", type="string")
     */
    private $ancienneDateDebut;

    /**
     * @var string
     *
     * @ORM\Column(name="ancienneDateFin", type="string")
     */
    private $ancienneDateFin;

    /**
     * @var integer
     *
     * @ORM\Column(name="ancienneDuree", type="integer")
     */
    private $ancienneDuree;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetime")
     */
    private $dateDebut;

    /**
     * @var string
     *
     * @ORM\Column(name="dateFin", type="string")
     */
    private $dateFin;

     /**
     * @var string
     *
     * @ORM\Column(name="dateRetour", type="string", length=255, nullable=true)
     */
    private $dateRetour;	
	
    /**
     * @var integer
     *
     * @ORM\Column(name="nbJoursOuvrables", type="integer")
     */
    private $nbJoursOuvrables;

    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id" , nullable=true)
     */
    private $salarie;

    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="manager", referencedColumnName="id" , nullable=true)
     */
    private $manager;

    /**
     * @var string
     *
     * @ORM\Column(name="motifReport", type="string", length=255)
     */
    private $motifReport;
    
      /**
     * @var \Kbh\GestionCongesBundle\Entity\Conge
     * @ORM\OneToOne(targetEntity="Conge")
     * @ORM\JoinColumn(name="conge", referencedColumnName="id" , nullable=true)
     */
    private $conge;
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Absence
     * @ORM\OneToOne(targetEntity="Absence")
     * @ORM\JoinColumn(name="absence", referencedColumnName="id" , nullable=true)
     */
    private $permission;


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
     * Set dateReport
     *
     * @param \DateTime $dateReport
     * @return Report
     */
    public function setDateReport($dateReport)
    {
        $this->dateReport = $dateReport;

        return $this;
    }

    /**
     * Get dateReport
     *
     * @return \DateTime 
     */
    public function getDateReport()
    {
        return $this->dateReport;
    }

    /**
     * Set typeReport
     *
     * @param string $typeReport
     * @return Report
     */
    public function setTypeReport($typeReport)
    {
        $this->typeReport = $typeReport;

        return $this;
    }

    /**
     * Get typeReport
     *
     * @return string 
     */
    public function getTypeReport()
    {
        return $this->typeReport;
    }

    /**
     * Set ancienneDateDebut
     *
     * @param string $ancienneDateDebut
     * @return Report
     */
    public function setAncienneDateDebut($ancienneDateDebut)
    {
        $this->ancienneDateDebut = $ancienneDateDebut;

        return $this;
    }

    /**
     * Get ancienneDateDebut
     *
     * @return string
     */
    public function getAncienneDateDebut()
    {
        return $this->ancienneDateDebut;
    }

    /**
     * Set ancienneDateFin
     *
     * @param string $ancienneDateFin
     * @return Report
     */
    public function setAncienneDateFin($ancienneDateFin)
    {
        $this->ancienneDateFin = $ancienneDateFin;

        return $this;
    }

    /**
     * Get ancienneDateFin
     *
     * @return string 
     */
    public function getAncienneDateFin()
    {
        return $this->ancienneDateFin;
    }

    /**
     * Set ancienneDuree
     *
     * @param integer $ancienneDuree
     * @return Report
     */
    public function setAncienneDuree($ancienneDuree)
    {
        $this->ancienneDuree = $ancienneDuree;

        return $this;
    }

    /**
     * Get ancienneDuree
     *
     * @return integer 
     */
    public function getAncienneDuree()
    {
        return $this->ancienneDuree;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Report
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
     * @param string $dateFin
     * @return Report
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
     * Set dateRetour
     *
     * @param string $dateRetour
     * @return Report
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
     * @param integer $duree
     * @return Report
     */
    public function setNbJoursOuvrables($duree)
    {
        $this->nbJoursOuvrables = $duree;

        return $this;
    }

    /**
     * Get nbJoursOuvrables
     *
     * @return integer 
     */
    public function getNbJoursOuvrables()
    {
        return $this->nbJoursOuvrables;
    }

    /**
     * Set motifReport
     *
     * @param string $motifReport
     * @return Report
     */
    public function setMotifReport($motifReport)
    {
        $this->motifReport = $motifReport;

        return $this;
    }

    /**
     * Get motifReport
     *
     * @return string 
     */
    public function getMotifReport()
    {
        return $this->motifReport;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return Report
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
     * Set manager
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $manager
     * @return Report
     */
    public function setManager(\Kbh\GestionCongesBundle\Entity\Salarie $manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie 
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set conge
     *
     * @param \Kbh\GestionCongesBundle\Entity\Conge $conge
     * @return Report
     */
    public function setConge(\Kbh\GestionCongesBundle\Entity\Conge $conge = null)
    {
        $this->conge = $conge;

        return $this;
    }

    /**
     * Get conge
     *
     * @return \Kbh\GestionCongesBundle\Entity\Conge 
     */
    public function getConge()
    {
        return $this->conge;
    }

    /**
     * Set permission
     *
     * @param \Kbh\GestionCongesBundle\Entity\Absence $permission
     * @return Report
     */
    public function setPermission(\Kbh\GestionCongesBundle\Entity\Absence $permission = null)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return \Kbh\GestionCongesBundle\Entity\Absence 
     */
    public function getPermission()
    {
        return $this->permission;
    }
	
	public function __toString() {
        return $this->getTypeReport();
    }
	
}
