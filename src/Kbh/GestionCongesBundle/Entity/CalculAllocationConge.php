<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * calculaAllocationconge
 *
 * @ORM\Table(name="calculallocationconge_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\CalculAllocationCongeRepository")
 */
class CalculAllocationConge
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
     * @ORM\Column(name="matricule", type="string", length=255, nullable=true)
     */
    private $matricule;
    
    /**
     * @var string
     *
     * @ORM\Column(name="unite", type="string", length=255, nullable=true)
     */
    private $unite;    

    /**
     * @var float
     *
     * @ORM\Column(name="salaireMoyenJournalier", type="float")
     */
    private $salaireMoyenJournalier;

    /**
     * @var float
     *
     * @ORM\Column(name="droitsLegaux", type="float")
     */
    private $droitsLegaux;

    /**
     * @var float
     *
     * @ORM\Column(name="droitsReels", type="float")
     */
    private $droitsReels;

    /**
     * @var float
     *
     * @ORM\Column(name="allocationConge", type="float")
     */
    private $allocationConge;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCalcul", type="datetime")
     */
    private $dateCalcul;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEffet", type="datetime")
     */
    private $dateEffet;
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id" , nullable=true)
     */
    private $salarie;



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
     * Set matricule
     *
     * @param string $matricule
     * @return CalculAllocationConge
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return string 
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set salaireMoyenJournalier
     *
     * @param string $salaireMoyenJournalier
     * @return CalculAllocationConge
     */
    public function setSalaireMoyenJournalier($salaireMoyenJournalier)
    {
        $this->salaireMoyenJournalier = $salaireMoyenJournalier;

        return $this;
    }

    /**
     * Get salaireMoyenJournalier
     *
     * @return string 
     */
    public function getSalaireMoyenJournalier()
    {
        return $this->salaireMoyenJournalier;
    }

    /**
     * Set droitsLegaux
     *
     * @param string $droitsLegaux
     * @return CalculAllocationConge
     */
    public function setDroitsLegaux($droitsLegaux)
    {
        $this->droitsLegaux = $droitsLegaux;

        return $this;
    }

    /**
     * Get droitsLegaux
     *
     * @return string 
     */
    public function getDroitsLegaux()
    {
        return $this->droitsLegaux;
    }

    /**
     * Set droitsReels
     *
     * @param string $droitsReels
     * @return CalculAllocationConge
     */
    public function setDroitsReels($droitsReels)
    {
        $this->droitsReels = $droitsReels;

        return $this;
    }

    /**
     * Get droitsReels
     *
     * @return string 
     */
    public function getDroitsReels()
    {
        return $this->droitsReels;
    }

    /**
     * Set allocationConge
     *
     * @return CalculAllocationConge
     */
    public function setAllocationConge()
    {
        $this->allocationConge = $this->droitsReels *$this->salaireMoyenJournalier;

        return $this;
    }

    /**
     * Get allocationConge
     *
     * @return string 
     */
    public function getAllocationConge()
    {
        return $this->allocationConge;
    }

    /**
     * Set dateCalcul
     *
     * @param \DateTime $dateCalcul
     * @return CalculAllocationConge
     */
    public function setDateCalcul($dateCalcul)
    {
        $this->dateCalcul = $dateCalcul;

        return $this;
    }

    /**
     * Get dateCalcul
     *
     * @return \DateTime 
     */
    public function getDateCalcul()
    {
        return $this->dateCalcul;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return CalculAllocationConge
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
     * Set dateEffet
     *
     * @param \DateTime $dateEffet
     * @return CalculAllocationConge
     */
    public function setDateEffet($dateEffet)
    {
        $this->dateEffet = $dateEffet;

        return $this;
    }

    /**
     * Get dateEffet
     *
     * @return \DateTime 
     */
    public function getDateEffet()
    {
        return $this->dateEffet;
    }

    /**
     * Set unite
     *
     * @param string $unite
     * @return CalculAllocationConge
     */
    public function setUnite($unite)
    {
        $this->unite = $unite;

        return $this;
    }

    /**
     * Get unite
     *
     * @return string 
     */
    public function getUnite()
    {
        return $this->unite;
    }
}
