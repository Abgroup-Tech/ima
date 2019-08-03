<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogUpdateAnnuel
 *
 * @ORM\Table(name="log_update_annuel_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\LogUpdateAnnuelRepository")
 */
class LogUpdateAnnuel
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
     * @ORM\Column(name="annee", type="string", length=255)
     */
    private $annee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdate", type="datetime")
     */
    private $dateUpdate;

    /** 
     * @var integer
     *
     * @ORM\Column(name="jourSupAnnuel", type="integer")
     */
    private $jourSupAnnuel;

    /**
     * @var float
     *
     * @ORM\Column(name="ancienSolde", type="float")
     */
    private $ancienSolde;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;


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
     * Set annee
     *
     * @param string $annee
     * @return LogUpdateAnnuel
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return string 
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     * @return LogUpdateAnnuel
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime 
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set jourSupAnnuel
     *
     * @param integer $jourSupAnnuel
     * @return LogUpdateAnnuel
     */
    public function setJourSupAnnuel($jourSupAnnuel)
    {
        $this->jourSupAnnuel = $jourSupAnnuel;

        return $this;
    }

    /**
     * Get jourSupAnnuel
     *
     * @return integer 
     */
    public function getJourSupAnnuel()
    {
        return $this->jourSupAnnuel;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return LogUpdateAnnuel
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return LogUpdateAnnuel
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
     * Set ancienSolde
     *
     * @param float $ancienSolde
     * @return LogUpdateAnnuel
     */
    public function setAncienSolde($ancienSolde)
    {
        $this->ancienSolde = $ancienSolde;

        return $this;
    }

    /**
     * Get ancienSolde
     *
     * @return float 
     */
    public function getAncienSolde()
    {
        return $this->ancienSolde;
    }
}
