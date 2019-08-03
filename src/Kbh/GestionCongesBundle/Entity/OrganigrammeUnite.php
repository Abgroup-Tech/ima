<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrganigrammeUnite
 *
 * @ORM\Table(name="organigrammeunite_premium")
 * @ORM\Entity
 */
class OrganigrammeUnite
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="sigle", type="string", length=255, nullable=false)
     */
    private $sigle;
 
    /**
     * @var string
     *
     * @ORM\Column(name="situationGeographique", type="string", length=255, nullable=true)
     */
    private $situationGeographique; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="lieuTravail", type="string", length=255, nullable=true)
     */
    private $lieuTravail;  
    
    /**
     * @ORM\OneToMany(targetEntity="Kbh\GestionCongesBundle\Entity\Salarie", mappedBy="unite", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="salaries", nullable=true)
     */
    private $salaries;    
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="manager", referencedColumnName="id")
     */
    private $manager;
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="manager", referencedColumnName="id")
     */
    private $suppleant;

    /**
     * @var boolean
     *
     * @ORM\Column(name="estService", type="boolean", nullable=true)
     */
    private $estService;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="estCellule", type="boolean", nullable=true)
     */
    private $estCellule;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="estDépartement", type="boolean", nullable=true)
     */
    private $estDepartement;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="estDirection", type="boolean", nullable=true)
     */
    private $estDirection;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="estDga", type="boolean", nullable=true)
     */
    private $estDga;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="estDg", type="boolean", nullable=true)
     */
    private $estDg;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="estDrh", type="boolean", nullable=true)
     */
    private $estDrh;

    /**
     * @var \OrganigrammeUnite
     *
     * @ORM\ManyToOne(targetEntity="OrganigrammeUnite")
     * @ORM\JoinColumn(name="uniteSuivante1", referencedColumnName="id",nullable=true)
     * 
     */
    private $uniteSuivante1;
    
     /**
     * @var \OrganigrammeUnite
     *
     * @ORM\ManyToOne(targetEntity="OrganigrammeUnite")
     * @ORM\JoinColumn(name="uniteSuivante2", referencedColumnName="id",nullable=true)
     * 
     */
    private $uniteSuivante2 ;
    
     /**
     * @var \OrganigrammeUnite
     *
     * @ORM\ManyToOne(targetEntity="OrganigrammeUnite")
     * @ORM\JoinColumn(name="uniteSuivante3", referencedColumnName="id",nullable=true)
     * 
     */
    private $uniteSuivante3 ;
    
    /**
     * @var \OrganigrammeUnite
     *
     * @ORM\ManyToOne(targetEntity="OrganigrammeUnite")
     * @ORM\JoinColumn(name="valideurPourManager1", referencedColumnName="id",nullable=true)
     * 
     */
    private $valideurPourManager1 ;
    
    /**
     *@var \OrganigrammeUnite
     *
     * @ORM\ManyToOne(targetEntity="OrganigrammeUnite")
     * @ORM\JoinColumn(name="valideurPourManager2", referencedColumnName="id",nullable=true)
     * 
     */
    private $valideurPourManager2 ;
    
    /**
     * @var \OrganigrammeUnite
     *
     * @ORM\ManyToOne(targetEntity="OrganigrammeUnite")
     * @ORM\JoinColumn(name="valideurPourManager3", referencedColumnName="id",nullable=true)
     * 
     */
    private $valideurPourManager3 ;
     
     /**
     * @var integer
     *
     * @ORM\Column(name="nbNiveauxValidation", type="integer", nullable=true)
     * 
     */
    private $nbNiveauxValidation ;
    
//     /**
//     * @var \Kbh\GestionCongesBundle\Entity\Salarie
//     * @ORM\OneToMany(targetEntity="Salarie", mappedBy="unite")
//     * @ORM\JoinColumn(name="salaries", referencedColumnName="id", nullable=true)
//     */
//    private $salaries;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->salaries = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     * @return OrganigrammeUnite
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set sigle
     *
     * @param string $sigle
     * @return OrganigrammeUnite
     */
    public function setSigle($sigle)
    {
        $this->sigle = $sigle;

        return $this;
    }

    /**
     * Get sigle
     *
     * @return string 
     */
    public function getSigle()
    {
        return $this->sigle;
    }

    /**
     * Set estService
     *
     * @param boolean $estService
     * @return OrganigrammeUnite
     */
    public function setEstService($estService)
    {
        $this->estService = $estService;

        return $this;
    }

    /**
     * Get estService
     *
     * @return boolean 
     */
    public function getEstService()
    {
        return $this->estService;
    }

    /**
     * Set estCellule
     *
     * @param boolean $estCellule
     * @return OrganigrammeUnite
     */
    public function setEstCellule($estCellule)
    {
        $this->estCellule = $estCellule;

        return $this;
    }

    /**
     * Get estCellule
     *
     * @return boolean 
     */
    public function getEstCellule()
    {
        return $this->estCellule;
    }

    /**
     * Set estDepartement
     *
     * @param boolean $estDepartement
     * @return OrganigrammeUnite
     */
    public function setEstDepartement($estDepartement)
    {
        $this->estDepartement = $estDepartement;

        return $this;
    }

    /**
     * Get estDepartement
     *
     * @return boolean 
     */
    public function getEstDepartement()
    {
        return $this->estDepartement;
    }

    /**
     * Set estDirection
     *
     * @param boolean $estDirection
     * @return OrganigrammeUnite
     */
    public function setEstDirection($estDirection)
    {
        $this->estDirection = $estDirection;

        return $this;
    }

    /**
     * Get estDirection
     *
     * @return boolean 
     */
    public function getEstDirection()
    {
        return $this->estDirection;
    }

    /**
     * Set estDga
     *
     * @param boolean $estDga
     * @return OrganigrammeUnite
     */
    public function setEstDga($estDga)
    {
        $this->estDga = $estDga;

        return $this;
    }

    /**
     * Get estDga
     *
     * @return boolean 
     */
    public function getEstDga()
    {
        return $this->estDga;
    }

    /**
     * Set estDg
     *
     * @param boolean $estDg
     * @return OrganigrammeUnite
     */
    public function setEstDg($estDg)
    {
        $this->estDg = $estDg;

        return $this;
    }

    /**
     * Get estDg
     *
     * @return boolean 
     */
    public function getEstDg()
    {
        return $this->estDg;
    }

    /**
     * Set estDrh
     *
     * @param boolean $estDrh
     * @return OrganigrammeUnite
     */
    public function setEstDrh($estDrh)
    {
        $this->estDrh = $estDrh;

        return $this;
    }

    /**
     * Get estDrh
     *
     * @return boolean 
     */
    public function getEstDrh()
    {
        return $this->estDrh;
    }

    /**
     * Set nbNiveauxValidation
     *
     * @param integer $nbNiveauxValidation
     * @return OrganigrammeUnite
     */
    public function setNbNiveauxValidation($nbNiveauxValidation)
    {
        $this->nbNiveauxValidation = $nbNiveauxValidation;

        return $this;
    }

    /**
     * Get nbNiveauxValidation
     *
     * @return integer 
     */
    public function getNbNiveauxValidation()
    {
        return $this->nbNiveauxValidation;
    }

    /**
     * Set manager
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $manager
     * @return OrganigrammeUnite
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
     * Set suppleant
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $suppleant
     * @return OrganigrammeUnite
     */
    public function setSuppleant(\Kbh\GestionCongesBundle\Entity\Salarie $suppleant = null)
    {
        $this->suppleant = $suppleant;

        return $this;
    }

    /**
     * Get suppleant
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie 
     */
    public function getSuppleant()
    {
        return $this->suppleant;
    }

    /**
     * Set uniteSuivante1
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $uniteSuivante1
     * @return OrganigrammeUnite
     */
    public function setUniteSuivante1(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $uniteSuivante1 = null)
    {
        $this->uniteSuivante1 = $uniteSuivante1;

        return $this;
    }

    /**
     * Get uniteSuivante1
     *
     * @return \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite 
     */
    public function getUniteSuivante1()
    {
        return $this->uniteSuivante1;
    }

    /**
     * Set uniteSuivante2
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $uniteSuivante2
     * @return OrganigrammeUnite
     */
    public function setUniteSuivante2(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $uniteSuivante2 = null)
    {
        $this->uniteSuivante2 = $uniteSuivante2;

        return $this;
    }

    /**
     * Get uniteSuivante2
     *
     * @return \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite 
     */
    public function getUniteSuivante2()
    {
        return $this->uniteSuivante2;
    }

    /**
     * Set uniteSuivante3
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $uniteSuivante3
     * @return OrganigrammeUnite
     */
    public function setUniteSuivante3(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $uniteSuivante3 = null)
    {
        $this->uniteSuivante3 = $uniteSuivante3;

        return $this;
    }

    /**
     * Get uniteSuivante3
     *
     * @return \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite 
     */
    public function getUniteSuivante3()
    {
        return $this->uniteSuivante3;
    }

    /**
     * Set valideurPourManager1
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $valideurPourManager1
     * @return OrganigrammeUnite
     */
    public function setValideurPourManager1(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $valideurPourManager1 = null)
    {
        $this->valideurPourManager1 = $valideurPourManager1;

        return $this;
    }

    /**
     * Get valideurPourManager1
     *
     * @return \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite 
     */
    public function getValideurPourManager1()
    {
        return $this->valideurPourManager1;
    }

    /**
     * Set valideurPourManager2
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $valideurPourManager2
     * @return OrganigrammeUnite
     */
    public function setValideurPourManager2(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $valideurPourManager2 = null)
    {
        $this->valideurPourManager2 = $valideurPourManager2;

        return $this;
    }
    

    /**
     * Get valideurPourManager2
     *
     * @return \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite 
     */
    public function getValideurPourManager2()
    {
        return $this->valideurPourManager2;
    }

    
    /**
     * Set valideurPourManager3
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $valideurPourManager3
     * @return OrganigrammeUnite
     */
    public function setValideurPourManager3(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $valideurPourManager3 = null)
    {
        $this->valideurPourManager3 = $valideurPourManager3;

        return $this;
    }

    /**
     * Get valideurPourManager3
     *
     * @return \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite 
     */
    public function getValideurPourManager3()
    {
        return $this->valideurPourManager3;
    }
    
    /**
     * Add Salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return OrganigrammeUnite
     */
    public function addSalarie(\Kbh\GestionCongesBundle\Entity\Salarie $salarie)
    {
        $this->salaries[] = $salarie;

        return $this;
    }

    /**
     * Remove Salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     */
    public function removeSalarie(\Kbh\GestionCongesBundle\Entity\Salarie $salarie)
    {
        $this->salaries->removeElement($salarie);
    }

    /**
     * Get Salaries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSalaries()
    {
        return $this->salaries;
    }
    
    public function __toString() {
        return $this->nom;
    }

    

    /**
     * Add salaries
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salaries
     * @return OrganigrammeUnite
     */
    public function addSalary(\Kbh\GestionCongesBundle\Entity\Salarie $salaries)
    {
        $this->salaries[] = $salaries;

        return $this;
    }

    /**
     * Remove salaries
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salaries
     */
    public function removeSalary(\Kbh\GestionCongesBundle\Entity\Salarie $salaries)
    {
        $this->salaries->removeElement($salaries);
    }

    /**
     * Set situationGeographique
     *
     * @param string $situationGeographique
     *
     * @return OrganigrammeUnite
     */
    public function setSituationGeographique($situationGeographique)
    {
        $this->situationGeographique = $situationGeographique;

        return $this;
    }

    /**
     * Get situationGeographique
     *
     * @return string
     */
    public function getSituationGeographique()
    {
        return $this->situationGeographique;
    }

    /**
     * Set lieuTravail
     *
     * @param string $lieuTravail
     *
     * @return OrganigrammeUnite
     */
    public function setLieuTravail($lieuTravail)
    {
        $this->lieuTravail = $lieuTravail;

        return $this;
    }

    /**
     * Get lieuTravail
     *
     * @return string
     */
    public function getLieuTravail()
    {
        return $this->lieuTravail;
    }
}
