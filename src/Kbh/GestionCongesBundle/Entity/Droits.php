<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droits
 *
 * @ORM\Table(name="droits_premium")
 * @ORM\Entity
 */
class Droits
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
     * @var float
     *
     * @ORM\Column(name="droitsAcquisAnneeEnCours", type="float",nullable=false)
     */
    private $droitsAcquisAnneeEnCours;

    /**
     * @var float
     *
     * @ORM\Column(name="reliquatDroitsAnterieur", type="float",nullable=false)
     */
    private $reliquatDroitsAnterieur;

    /**
     * @var float
     *
     * @ORM\Column(name="cumulDroitsAcquis", type="float",nullable=false)
     */
    private $cumulDroitsAcquis;

    /**
     * @var float
     *
     * @ORM\Column(name="droitsPris", type="float",nullable=false)
     */
    private $droitsPris;

    /**
     * @var float
     * 
     * @ORM\Column(name="soldePermissions", type="float",nullable=false)
     */
    private $soldePermissions;
    
    /**
     * @var float
     *
     * @ORM\Column(name="permissionsPrises", type="float",nullable=false)
     */
    private $permissionsPrises;
    

    /**
     * @var float
     *
     * @ORM\Column(name="totalDroitsAprendre", type="float",nullable=false)
     */
    private $totalDroitsAprendre;

    /**
     * @var \Salarie
     *
     * @ORM\OneToOne(targetEntity="Salarie", inversedBy="droits")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id")
     * 
     */
    private $salarie;

    public function __construct() {
        $this->droitsAcquisAnneeEnCours = 0;
        $this->droitsPris = 0;
        $this->reliquatDroitsAnterieur=0;
        $this->setTotalDroitsAprendre();
        $this->setCumulDroitsAcquis();
        $this->permissionsPrises=0;
        $this->soldePermissions=10;

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
     * Set droitsAcquisAnneeEnCours
     *
     *@param float $droitsAcquisAnneeEnCours
     * @return Droits
     */
    public function setDroitsAcquisAnneeEnCours($droitsAcquisAnneeEnCours)
    {
        $this->droitsAcquisAnneeEnCours = $droitsAcquisAnneeEnCours;

        return $this;
    }

    /**
     * Get droitsAcquisAnneeEnCours
     *
     * @return float
     */
    public function getDroitsAcquisAnneeEnCours()
    {
        return $this->droitsAcquisAnneeEnCours;
    }

    /**
     * Set reliquatDroitsAnterieur
     *
     *@param float $reliquatDroitsAnterieur
     * @return Droits
     */
    public function setReliquatDroitsAnterieur($reliquatDroitsAnterieur)
    {
        $this->reliquatDroitsAnterieur = $reliquatDroitsAnterieur;

        return $this;
    }

    /**
     * Get reliquatDroitsAnterieur
     *
     * @return float
     */
    public function getReliquatDroitsAnterieur()
    {
        return $this->reliquatDroitsAnterieur;
    }

    /**
     * Set cumulDroitsAcquis
     *
     * @return Droits
     */
    public function setCumulDroitsAcquis()
    {
        $this->cumulDroitsAcquis= $this->droitsAcquisAnneeEnCours + $this->reliquatDroitsAnterieur;

        return $this;
    }

    /**
     * Get cumulDroitsAcquis
     *
     * @return float
     */
    public function getCumulDroitsAcquis()
    {
        return $this->cumulDroitsAcquis;
    }

    /**
     * Set droitsPris
     *
     *@param float $nbjours
     * @return Droits
     */
    public function setDroitsPris($nbjours)
    {
        $this->droitsPris += $nbjours;

        return $this;
    }
    
    /**
     * Reinitialisation droitsPris
     *
     *@param float $nbjours
     * @return Droits
     */
    public function reinitialisationDroitsPris()
    {
        $this->droitsPris = 0;

        return $this;
    }

    /**
     * Get droitsPris
     *
     * @return float
     */
    public function getDroitsPris()
    {
        return $this->droitsPris;
    }

    /**
     * Set totalDroitsAprendre
     *
     * @return Droits
     */
    public function setTotalDroitsAprendre()
    {
        $this->totalDroitsAprendre = $this->cumulDroitsAcquis - $this->droitsPris;

        return $this;
    }

    /**
     * Get totalDroitsAprendre
     *
     * @return float
     */
    public function getTotalDroitsAprendre()
    {
        return $this->totalDroitsAprendre;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return Droits
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
    
    
    public function getSoldePermissions() {
        return $this->soldePermissions;
    }

    public function getPermissionsPrises() {
        return $this->permissionsPrises;
    }

    /**
     * Set PermissionsPrises
     *
     * @return Droits
     */
    public function setPermissionsPrises($nbjours) {
        $this->permissionsPrises = $nbjours;
    }
    
    /**
     * Set PermissionsPrises
     *
     * @return Droits
     */
    public function updatePermissionsPrises($nbjours) {
        $this->permissionsPrises += $nbjours;
    }
    
    /**
     * Reinitialisation PermissionsPrises
     *
     *@param float $nbjours
     * @return Droits
     */
    public function reinitialisationPermissionsPrises()
    {
        $this->permissionsPrises = 0;

        return $this;
    }
    
    /**
     * Set soldePermissions
     *
     * @return Droits
     */
    public function setSoldePermissions($nbjours)
    {
        $this->soldePermissions = $nbjours;

        return $this;
    }
    
        /**
     * Set soldePermissions
     *
     * @return Droits
     */
    public function updateSoldePermissions()
    {
        $this->soldePermissions = 10 - $this->permissionsPrises;

        return $this;
    }
    
    /**
     * Reinitialisation soldePermissions
     *
     *@param float $nbjours
     * @return Droits
     */
    public function reinitialisationSoldePermissions()
    {
        $this->soldePermissions = 10;

        return $this;
    }
    
    public function __toString() {
        return "Droits ".$this->getId().' de '.$this->getSalarie();
    }
    

    
}
