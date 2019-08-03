<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statistiques
 *
 * @ORM\Table(name="statistiques_premium")
 * @ORM\Entity
 */
class Statistiques
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
     * @ORM\Column(name="nombreDemandes", type="float",nullable=true)
     */
    private $nombreDemandes;

    /**
     * @var float
     *
     * @ORM\Column(name="nbDemandesConges", type="float",nullable=true)
     */
    private $nbDemandesConges;

    /**
     * @var float
     *
     * @ORM\Column(name="nbDemandesPermissions", type="float",nullable=true)
     */
    private $nbDemandesPermissions;

    /**
     * @var float
     *
     * @ORM\Column(name="nbDemandesEnCours", type="float",nullable=true)
     */
    private $nbDemandesEnCours;

    /**
     * @var float
     * 
     * @ORM\Column(name="nbDemandesValides", type="float",nullable=true)
     */
    private $nbDemandesValides;
    
    /**
     * @var float
     *
     * @ORM\Column(name="nbDemandesRefuses", type="float",nullable=true)
     */
    private $nbDemandesRefuses;
    

    /**
     * @var float
     *
     * @ORM\Column(name="nbDemandesCongesRefuses", type="float",nullable=true)
     */
    private $nbDemandesCongesRefuses;

     /**
     * @var float
     *
     * @ORM\Column(name="nbDemandesPermissionsRefuses", type="float",nullable=true)
     */
    private $nbDemandesPermissionsRefuses;

        /**
     * @var float
     *
     * @ORM\Column(name="nbDemandesCongesValides", type="float",nullable=true)
     */
    private $nbDemandesCongesValides;

     /**
     * @var float
     *
     * @ORM\Column(name="nbDemandesPermissionsValides", type="float",nullable=true)
     */
    private $nbDemandesPermissionsValides;
   

    /**
     * @var \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite
     * @ORM\ManyToOne(targetEntity="OrganigrammeUnite")
     * @ORM\JoinColumn(name="unite", referencedColumnName="id")
     *
     */
    private $unite;
    
    /**
     * @var \Salarie
     *
     * @ORM\OneToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id")
     * 
     */
    private $salarie;
    
    /**
     * @var float
     * 
     * @ORM\Column(name="taux_conge", type="float",nullable=true)
     */
    private $taux_conge;
    
    /**
     * @var float
     *
     * @ORM\Column(name="taux_absence", type="float",nullable=true)
     */
    private $taux_absence;
    

    /**
     * @var float
     *
     * @ORM\Column(name="taux_conge_refuse", type="float",nullable=true)
     */
    private $taux_conge_refuse;

     /**
     * @var float
     *
     * @ORM\Column(name="taux_absence_refuse", type="float",nullable=true)
     */
    private $taux_absence_refuse;

        /**
     * @var float
     *
     * @ORM\Column(name="taux_demande_encours", type="float",nullable=true)
     */
    private $taux_demande_encours;

     /**
     * @var float
     *
     * @ORM\Column(name="taux_demande_refusee", type="float",nullable=true)
     */
    private $taux_demande_refusee;

    public function __construct() {

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
     * Get salarie
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie 
     */
    public function getSalarie()
    {
        return $this->salarie;
    }
  
    public function getNombreDemandes() {
        return $this->nombreDemandes;
    }

    public function getNbDemandesConges() {
        return $this->nbDemandesConges;
    }

    public function getNbDemandesPermissions() {
        return $this->nbDemandesPermissions;
    }

    public function getNbDemandesEnCours() {
        return $this->nbDemandesEnCours;
    }

    public function getNbDemandesValides() {
        return $this->nbDemandesValides;
    }

    public function getNbDemandesRefuses() {
        return $this->nbDemandesRefuses;
    }

    public function getNbDemandesCongesRefuses() {
        return $this->nbDemandesCongesRefuses;
    }

    public function getNbDemandesPermissionsRefuses() {
        return $this->nbDemandesPermissionsRefuses;
    }

    public function getNbDemandesCongesValides() {
        return $this->nbDemandesCongesValides;
    }

    public function getNbDemandesPermissionsValides() {
        return $this->nbDemandesPermissionsValides;
    }
    /**
     * Get salarie
     *
     * @return \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite 
     */
    public function getUnite() {
        return $this->unite;
    }
    
    public function getTaux_conge() {
        return $this->taux_conge;
    }

    public function getTaux_absence() {
        return $this->taux_absence;
    }

    public function getTaux_conge_refuse() {
        return $this->taux_conge_refuse;
    }

    public function getTaux_absence_refuse() {
        return $this->taux_absence_refuse;
    }

    public function getTaux_demande_encours() {
        return $this->taux_demande_encours;
    }

    public function getTaux_demande_refusee() {
        return $this->taux_demande_refusee;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTaux_conge($taux_conge) {
        $this->taux_conge = $taux_conge;
    }

    public function setTaux_absence($taux_absence) {
        $this->taux_absence = $taux_absence;
    }

    public function setTaux_conge_refuse($taux_conge_refuse) {
        $this->taux_conge_refuse = $taux_conge_refuse;
    }

    public function setTaux_absence_refuse($taux_absence_refuse) {
        $this->taux_absence_refuse = $taux_absence_refuse;
    }

    public function setTaux_demande_encours($taux_demande_encours) {
        $this->taux_demande_encours = $taux_demande_encours;
    }

    public function setTaux_demande_refusee($taux_demande_refusee) {
        $this->taux_demande_refusee = $taux_demande_refusee;
    }

        
    public function setNombreDemandes($nombreDemandes) {
        $this->nombreDemandes = $nombreDemandes;
    }

    public function setNbDemandesConges($nbDemandesConges) {
        $this->nbDemandesConges = $nbDemandesConges;
    }

    public function setNbDemandesPermissions($nbDemandesPermissions) {
        $this->nbDemandesPermissions = $nbDemandesPermissions;
    }

    public function setNbDemandesEnCours($nbDemandesEnCours) {
        $this->nbDemandesEnCours = $nbDemandesEnCours;
    }

    public function setNbDemandesValides($nbDemandesValides) {
        $this->nbDemandesValides = $nbDemandesValides;
    }

    public function setNbDemandesRefuses($nbDemandesRefuses) {
        $this->nbDemandesRefuses = $nbDemandesRefuses;
    }

    public function setNbDemandesCongesRefuses($nbDemandesCongesRefuses) {
        $this->nbDemandesCongesRefuses = $nbDemandesCongesRefuses;
    }

    public function setNbDemandesPermissionsRefuses($nbDemandesPermissionsRefuses) {
        $this->nbDemandesPermissionsRefuses = $nbDemandesPermissionsRefuses;
    }

    public function setNbDemandesCongesValides($nbDemandesCongesValides) {
        $this->nbDemandesCongesValides = $nbDemandesCongesValides;
    }

    public function setNbDemandesPermissionsValides($nbDemandesPermissionsValides) {
        $this->nbDemandesPermissionsValides = $nbDemandesPermissionsValides;
    }
      /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return Statistiques
     */
    public function setSalarie(\Kbh\GestionCongesBundle\Entity\Salarie $salarie = null)
    {
        $this->salarie = $salarie;

        return $this;
    }
    
    /**
     * Set unite
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $unite
     * @return Statistiques
     */
    public function setUnite(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $unite) {
        $this->unite = $unite;
    }

        
    public function __toString() {
        return "Droits ".$this->getId().' de '.$this->getSalarie();
    }
        

    /**
     * Set taux_conge
     *
     * @param float $tauxConge
     * @return Statistiques
     */
    public function setTauxConge($tauxConge)
    {
        $this->taux_conge = $tauxConge;
    
        return $this;
    }

    /**
     * Get taux_conge
     *
     * @return float 
     */
    public function getTauxConge()
    {
        return $this->taux_conge;
    }

    /**
     * Set taux_absence
     *
     * @param float $tauxAbsence
     * @return Statistiques
     */
    public function setTauxAbsence($tauxAbsence)
    {
        $this->taux_absence = $tauxAbsence;
    
        return $this;
    }

    /**
     * Get taux_absence
     *
     * @return float 
     */
    public function getTauxAbsence()
    {
        return $this->taux_absence;
    }

    /**
     * Set taux_conge_refuse
     *
     * @param float $tauxCongeRefuse
     * @return Statistiques
     */
    public function setTauxCongeRefuse($tauxCongeRefuse)
    {
        $this->taux_conge_refuse = $tauxCongeRefuse;
    
        return $this;
    }

    /**
     * Get taux_conge_refuse
     *
     * @return float 
     */
    public function getTauxCongeRefuse()
    {
        return $this->taux_conge_refuse;
    }

    /**
     * Set taux_absence_refuse
     *
     * @param float $tauxAbsenceRefuse
     * @return Statistiques
     */
    public function setTauxAbsenceRefuse($tauxAbsenceRefuse)
    {
        $this->taux_absence_refuse = $tauxAbsenceRefuse;
    
        return $this;
    }

    /**
     * Get taux_absence_refuse
     *
     * @return float 
     */
    public function getTauxAbsenceRefuse()
    {
        return $this->taux_absence_refuse;
    }

    /**
     * Set taux_demande_encours
     *
     * @param float $tauxDemandeEncours
     * @return Statistiques
     */
    public function setTauxDemandeEncours($tauxDemandeEncours)
    {
        $this->taux_demande_encours = $tauxDemandeEncours;
    
        return $this;
    }

    /**
     * Get taux_demande_encours
     *
     * @return float 
     */
    public function getTauxDemandeEncours()
    {
        return $this->taux_demande_encours;
    }

    /**
     * Set taux_demande_refusee
     *
     * @param float $tauxDemandeRefusee
     * @return Statistiques
     */
    public function setTauxDemandeRefusee($tauxDemandeRefusee)
    {
        $this->taux_demande_refusee = $tauxDemandeRefusee;
    
        return $this;
    }

    /**
     * Get taux_demande_refusee
     *
     * @return float 
     */
    public function getTauxDemandeRefusee()
    {
        return $this->taux_demande_refusee;
    }
}
