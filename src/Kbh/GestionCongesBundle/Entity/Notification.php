<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="notification_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\NotificationRepository")
 */
class Notification
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
     * @var boolean
     *
     * @ORM\Column(name="estTraitee", type="boolean",nullable=true)
     */
    private $estTraitee;

     /**
     * @var boolean
     *
     * @ORM\Column(name="vuParDemandeur", type="boolean",nullable=true)
     */
    private $vuParDemandeur;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="vuParValideurEnCours", type="boolean",nullable=true)
     */
    private $vuParValideurEnCours;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="vuParValideurPrecedent", type="boolean",nullable=true)
     */
    private $vuParValideurPrecedent;   
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="vuParObservateur", type="boolean",nullable=true)
     */
    private $vuParObservateur; 
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="vuParSupN1", type="boolean",nullable=true)
     */
    private $vuParSupN1;     

    /**
     * @var boolean
     *
     * @ORM\Column(name="vuParAdmin", type="boolean",nullable=true)
     */
    private $vuParAdmin;         

    /**
     * @var string
     *
     * @ORM\Column(name="messageDemandeur", type="string", length=255 ,nullable=true)
     */
    private $messageDemandeur;
    
    /**
     * @var string
     *
     * @ORM\Column(name="messageValideurEnCours", type="string", length=255 ,nullable=true)
     */
    private $messageValideurEnCours;

    
    /**
     * @var string
     *
     * @ORM\Column(name="messageValideurPrecedent", type="string", length=255 ,nullable=true)
     */
    private $messageValideurPrecedent;

    /**
     * @var string
     *
     * @ORM\Column(name="messageValideurSuivant", type="string", length=255 ,nullable=true)
     */
    private $messageValideurSuivant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnvoi", type="datetime")
     */
    private $dateEnvoi;

    /**
     * @var \Kbh\GestionCongesBundle\Entity\Demande
     * @ORM\ManyToOne(targetEntity="Demande")
     * @ORM\JoinColumn(name="demande", referencedColumnName="id", nullable=true)
     */
    private $demande;

   /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id" , nullable=true)
     */
    private $salarie;

    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="valideurEnCours", referencedColumnName="id" , nullable=true)
     */
    private $valideurEnCours;
   
        /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="valideurPrecedent", referencedColumnName="id" , nullable=true)
     */
    private $valideurPrecedent;

        /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="valideurSuivant", referencedColumnName="id" , nullable=true)
     */
    private $valideurSuivant;
    
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="observateur", referencedColumnName="id" , nullable=true)
     */
    private $observateur;
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="superieurN1", referencedColumnName="id" , nullable=true)
     */
    private $superieurN1;
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="admin", referencedColumnName="id" , nullable=true)
     */
    private $admin;    
  
   /**
     * @var string
     *
     * @ORM\Column(name="messageFinal", type="string", length=255 ,nullable=true)
     */
    private $messageFinal;

    
    public function __construct() {
        $this->dateEnvoi = new \Datetime();
        $this->estTraitee = false;
        $this->estVue = false;
        
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
     * Set messageDemandeur
     *
     * @param string $messageD
     * @return Notification
     */
    public function setMessageDemandeur($messageD)
    {
        $this->messageDemandeur = $messageD;

        return $this;
    }

    /**
     * Get messageDemandeur
     *
     * @return string 
     */
    public function getMessageDemandeur()
    {
        return $this->messageDemandeur;
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     * @return Notification
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime 
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }

    /**
     * Set demande
     *
     * @param \Kbh\GestionCongesBundle\Entity\Demande $demande
     * @return Notification
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
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return Notification
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
     * Set valideurEnCours
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $valideurEnCours
     * @return Notification
     */
    public function setValideurEnCours(\Kbh\GestionCongesBundle\Entity\Salarie $valideurEnCours = null)
    {
        $this->valideurEnCours = $valideurEnCours;

        return $this;
    }

    /**
     * Get valideurEnCours
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie 
     */
    public function getValideurEnCours()
    {
        return $this->valideurEnCours;
    }

    /**
     * Set valideurPrecedent
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $valideurPrecedent
     * @return Notification
     */
    public function setValideurPrecedent(\Kbh\GestionCongesBundle\Entity\Salarie $valideurPrecedent = null)
    {
        $this->valideurPrecedent = $valideurPrecedent;

        return $this;
    }

    /**
     * Get valideurPrecedent
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie 
     */
    public function getValideurPrecedent()
    {
        return $this->valideurPrecedent;
    }

    /**
     * Set valideurSuivant
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $valideurSuivant
     * @return Notification
     */
    public function setValideurSuivant(\Kbh\GestionCongesBundle\Entity\Salarie $valideurSuivant = null)
    {
        $this->valideurSuivant = $valideurSuivant;

        return $this;
    }

    /**
     * Get valideurSuivant
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie 
     */
    public function getValideurSuivant()
    {
        return $this->valideurSuivant;
    }

    /**
     * Set estTraitee
     *
     * @param boolean $estTraitee
     * @return Demande
     */
    public function setEstTraitee($estTraitee)
    {
        $this->estTraitee = $estTraitee;

        return $this;
    }

    /**
     * Get estTraitee
     *
     * @return boolean 
     */
    public function getEstTraitee()
    {
        return $this->estTraitee;
    }

    /**
     * Set estVue
     *
     * @param boolean $estVue
     * @return Demande
     */
    public function setEstVue($estVue)
    {
        $this->estVue = $estVue;

        return $this;
    }

    /**
     * Get estVue
     *
     * @return boolean 
     */
    public function getEstVue()
    {
        return $this->estVue;
    }


    /**
     * Set observateur
     *
     * @return Notification 
     */
    public function setObservateur($observateur)
    {
       $this->observateur =$observateur;
    }

        /**
     * Get observateur
     *
     * @return Salarie 
     */
    public function getObservateur()
    {
        return $this->observateur;
    }

    /**
     * Set messageValideur
     *
     * @param string $messageValideur
     * @return Notification
     */
    public function setMessageValideur($messageValideur)
    {
        $this->messageValideur = $messageValideur;

        return $this;
    }

    /**
     * Get messageValideur
     *
     * @return string 
     */
    public function getMessageValideur()
    {
        return $this->messageValideur;
    }

    /**
     * Set messageValideurEnCours
     *
     * @param string $messageValideurEnCours
     * @return Notification
     */
    public function setMessageValideurEnCours($messageValideurEnCours)
    {
        $this->messageValideurEnCours = $messageValideurEnCours;

        return $this;
    }

    /**
     * Get messageValideurEnCours
     *
     * @return string 
     */
    public function getMessageValideurEnCours()
    {
        return $this->messageValideurEnCours;
    }

    /**
     * Set messageValideurSuivant
     *
     * @param string $messageValideurSuivant
     * @return Notification
     */
    public function setMessageValideurSuivant($messageValideurSuivant)
    {
        $this->messageValideurSuivant = $messageValideurSuivant;

        return $this;
    }

    /**
     * Get messageValideurSuivant
     *
     * @return string 
     */
    public function getMessageValideurSuivant()
    {
        return $this->messageValideurSuivant;
    }

     /**
     * Set messageValideurPrecedent
     *
     * @param string $messageValideurPrecedent
     * @return Notification
     */
    public function setMessageValideurPrecedent($messageValideurPrecedent)
    {
        $this->messageValideurPrecedent = $messageValideurPrecedent;

        return $this;
    }

    function getMessageFinal() {
        return $this->messageFinal;
    }

    function setMessageFinal($messageFinal) {
        $this->messageFinal = $messageFinal;
    }



    /**
     * Get messageValideurPrecedent
     *
     * @return string 
     */
    public function getMessageValideurPrecedent()
    {
        return $this->messageValideurPrecedent;
    }
    
    public function getSuperieurN1() {
        return $this->superieurN1;
    }

    public function setSuperieurN1(\Kbh\GestionCongesBundle\Entity\Salarie $superieurN1) {
        $this->superieurN1 = $superieurN1;
    }


    

    /**
     * Set vuParDemandeur
     *
     * @param boolean $vuParDemandeur
     *
     * @return Notification
     */
    public function setVuParDemandeur($vuParDemandeur)
    {
        $this->vuParDemandeur = $vuParDemandeur;

        return $this;
    }

    /**
     * Get vuParDemandeur
     *
     * @return boolean
     */
    public function getVuParDemandeur()
    {
        return $this->vuParDemandeur;
    }

    /**
     * Set vuParValideurEnCours
     *
     * @param boolean $vuParValideurEnCours
     *
     * @return Notification
     */
    public function setVuParValideurEnCours($vuParValideurEnCours)
    {
        $this->vuParValideurEnCours = $vuParValideurEnCours;

        return $this;
    }

    /**
     * Get vuParValideurEnCours
     *
     * @return boolean
     */
    public function getVuParValideurEnCours()
    {
        return $this->vuParValideurEnCours;
    }

    /**
     * Set vuParValideurPrecedent
     *
     * @param boolean $vuParValideurPrecedent
     *
     * @return Notification
     */
    public function setVuParValideurPrecedent($vuParValideurPrecedent)
    {
        $this->vuParValideurPrecedent = $vuParValideurPrecedent;

        return $this;
    }

    /**
     * Get vuParValideurPrecedent
     *
     * @return boolean
     */
    public function getVuParValideurPrecedent()
    {
        return $this->vuParValideurPrecedent;
    }

    /**
     * Set vuParObservateur
     *
     * @param boolean $vuParObservateur
     *
     * @return Notification
     */
    public function setVuParObservateur($vuParObservateur)
    {
        $this->vuParObservateur = $vuParObservateur;

        return $this;
    }

    /**
     * Get vuParObservateur
     *
     * @return boolean
     */
    public function getVuParObservateur()
    {
        return $this->vuParObservateur;
    }

    /**
     * Set vuParSupN1
     *
     * @param boolean $vuParSupN1
     *
     * @return Notification
     */
    public function setVuParSupN1($vuParSupN1)
    {
        $this->vuParSupN1 = $vuParSupN1;

        return $this;
    }

    /**
     * Get vuParSupN1
     *
     * @return boolean
     */
    public function getVuParSupN1()
    {
        return $this->vuParSupN1;
    }

    /**
     * Set vuParAdmin
     *
     * @param boolean $vuParAdmin
     * @return Notification
     */
    public function setVuParAdmin($vuParAdmin)
    {
        $this->vuParAdmin = $vuParAdmin;

        return $this;
    }

    /**
     * Get vuParAdmin
     *
     * @return boolean 
     */
    public function getVuParAdmin()
    {
        return $this->vuParAdmin;
    }

    /**
     * Set admin
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $admin
     * @return Notification
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
}
