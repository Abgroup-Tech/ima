<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Salarie
 *
 * @ORM\Table(name="salarie_premium")
 * @ORM\Entity
 */
class Salarie
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
     * @ORM\Column(name="matricule", type="string", length=255, nullable=false)
     */
    private $matricule;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroCnps", type="string", length=255, nullable=false)
     */
    private $numeroCnps;

    /**
     * @var string
     *
     * @ORM\Column(name="civilite", type="string", length=15, nullable=false)
     */
    private $civilite;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;
	
    /**
     * @ORM\OneToOne(targetEntity="Kbh\GestionCongesBundle\Entity\Media", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="image", nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=false)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nomprenom", type="string", length=255, nullable=false)
     */
    private $nomprenom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNaissance", type="datetime", nullable=false)
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="statutMarital", type="string", length=255, nullable=false)
     */
    private $statutMarital;

    /**
     * @var string
     *
     * @ORM\Column(name="poste", type="string", length=255, nullable=false)
     */
    private $poste;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     *
     */
    private $telephone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEmbauche", type="datetime", nullable=false)
     */
    private $dateEmbauche;

    /**
     * @var string
     *
     * @ORM\Column(name="typeContratTravail", type="string", length=255, nullable=false)
     */
    private $typeContratTravail;

    /**
     * @var integer
     *
     * @ORM\Column(name="dureeContrat", type="integer", nullable=true)
     */
    private $dureeContrat;

    /**
     * @var string
     *
     * @ORM\Column(name="statutEmploi", type="string", length=255, nullable=false)
     */
    private $statutEmploi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDepartEntreprise", type="datetime", nullable=true)
     */
    private $dateDepartEntreprise;

    /**
     * @var \OrganigrammeUnite
     *
     * @ORM\ManyToOne(targetEntity="OrganigrammeUnite", inversedBy="salaries", cascade={"persist"})
     * @ORM\JoinColumn(name="unite", referencedColumnName="id" ,nullable=true)
     */
    private $unite;

    /**
     * @var \Droits
     *
     * @ORM\OneToOne(targetEntity="Droits" ,mappedBy="salarie")
     * @ORM\JoinColumn(name="droits", referencedColumnName="id")
     * 
     */
    private $droits;

    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="superviseur", referencedColumnName="id", nullable=true)
     */
    private $superviseur;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="delegationActive", type="boolean", nullable=true)
     *
     */
    private $delegationActive;

    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="suppleant", referencedColumnName="id", nullable=true)
     */
    private $suppleant;
    
    /**
     * @var \Kbh\UserBundle\Entity\User
     * 
     * @ORM\OneToOne(targetEntity="Kbh\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     * 
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setNomprenom();
        $this->statutEmploi = 'actif';
    }
    
    /**
     * Generate username
     *
     * @return String
     */
    public function generateUsername()
    {
        $username = strtolower($this->nom.'.'.$this->prenom);

        return $username;
    }
    
    public function __toString() {
        return $this->getNomprenom();
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
     * Set matricule
     *
     * @param string $matricule
     *
     * @return Salarie
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
     * Set numeroCnps
     *
     * @param string $numeroCnps
     *
     * @return Salarie
     */
    public function setNumeroCnps($numeroCnps)
    {
        $this->numeroCnps = $numeroCnps;

        return $this;
    }

    /**
     * Get numeroCnps
     *
     * @return string
     */
    public function getNumeroCnps()
    {
        return $this->numeroCnps;
    }

    /**
     * Set civilite
     *
     * @param string $civilite
     *
     * @return Salarie
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return string
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Salarie
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Salarie
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nomprenom
     *
     * @return Salarie
     */
    public function setNomprenom()
    {
        $this->nomprenom = $this->nom.' '.$this->prenom;
		
        return $this;
    }

    /**
     * Get nomprenom
     *
     * @return string
     */
    public function getNomprenom()
    {
        return $this->nomprenom;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Salarie
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set statutMarital
     *
     * @param string $statutMarital
     *
     * @return Salarie
     */
    public function setStatutMarital($statutMarital)
    {
        $this->statutMarital = $statutMarital;

        return $this;
    }

    /**
     * Get statutMarital
     *
     * @return string
     */
    public function getStatutMarital()
    {
        return $this->statutMarital;
    }

    /**
     * Set poste
     *
     * @param string $poste
     *
     * @return Salarie
     */
    public function setPoste($poste)
    {
        $this->poste = $poste;

        return $this;
    }

    /**
     * Get poste
     *
     * @return string
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Salarie
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Salarie
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set dateEmbauche
     *
     * @param \DateTime $dateEmbauche
     *
     * @return Salarie
     */
    public function setDateEmbauche($dateEmbauche)
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    /**
     * Get dateEmbauche
     *
     * @return \DateTime
     */
    public function getDateEmbauche()
    {
        return $this->dateEmbauche;
    }

    /**
     * Set typeContratTravail
     *
     * @param string $typeContratTravail
     *
     * @return Salarie
     */
    public function setTypeContratTravail($typeContratTravail)
    {
        $this->typeContratTravail = $typeContratTravail;

        return $this;
    }

    /**
     * Get typeContratTravail
     *
     * @return string
     */
    public function getTypeContratTravail()
    {
        return $this->typeContratTravail;
    }

    /**
     * Set dureeContrat
     *
     * @param integer $dureeContrat
     *
     * @return Salarie
     */
    public function setDureeContrat($dureeContrat)
    {
        $this->dureeContrat = $dureeContrat;

        return $this;
    }

    /**
     * Get dureeContrat
     *
     * @return integer
     */
    public function getDureeContrat()
    {
        return $this->dureeContrat;
    }

    /**
     * Set statutEmploi
     *
     * @param string $statutEmploi
     *
     * @return Salarie
     */
    public function setStatutEmploi($statutEmploi)
    {
        $this->statutEmploi = $statutEmploi;

        return $this;
    }

    /**
     * Get statutEmploi
     *
     * @return string
     */
    public function getStatutEmploi()
    {
        return $this->statutEmploi;
    }

    /**
     * Set dateDepartEntreprise
     *
     * @param \DateTime $dateDepartEntreprise
     *
     * @return Salarie
     */
    public function setDateDepartEntreprise($dateDepartEntreprise)
    {
        $this->dateDepartEntreprise = $dateDepartEntreprise;

        return $this;
    }

    /**
     * Get dateDepartEntreprise
     *
     * @return \DateTime
     */
    public function getDateDepartEntreprise()
    {
        return $this->dateDepartEntreprise;
    }

    /**
     * Set delegationActive
     *
     * @param boolean $delegationActive
     *
     * @return Salarie
     */
    public function setDelegationActive($delegationActive)
    {
        $this->delegationActive = $delegationActive;

        return $this;
    }

    /**
     * Get delegationActive
     *
     * @return boolean
     */
    public function getDelegationActive()
    {
        return $this->delegationActive;
    }

    /**
     * Set image
     *
     * @param \Kbh\GestionCongesBundle\Entity\Media $image
     *
     * @return Salarie
     */
    public function setImage(\Kbh\GestionCongesBundle\Entity\Media $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Kbh\GestionCongesBundle\Entity\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set unite
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $unite
     *
     * @return Salarie
     */
    public function setUnite(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $unite = null)
    {
        $this->unite = $unite;

        return $this;
    }

    /**
     * Get unite
     *
     * @return \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite
     */
    public function getUnite()
    {
        return $this->unite;
    }

    /**
     * Set droits
     *
     * @param \Kbh\GestionCongesBundle\Entity\Droits $droits
     *
     * @return Salarie
     */
    public function setDroits(\Kbh\GestionCongesBundle\Entity\Droits $droits = null)
    {
        $this->droits = $droits;

        return $this;
    }

    /**
     * Get droits
     *
     * @return \Kbh\GestionCongesBundle\Entity\Droits
     */
    public function getDroits()
    {
        return $this->droits;
    }

    /**
     * Set superviseur
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $superviseur
     *
     * @return Salarie
     */
    public function setSuperviseur(\Kbh\GestionCongesBundle\Entity\Salarie $superviseur = null)
    {
        $this->superviseur = $superviseur;

        return $this;
    }

    /**
     * Get superviseur
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie
     */
    public function getSuperviseur()
    {
        return $this->superviseur;
    }

    /**
     * Set suppleant
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $suppleant
     *
     * @return Salarie
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
     * Set user
     *
     * @param \Kbh\UserBundle\Entity\User $user
     *
     * @return Salarie
     */
    public function setUser(\Kbh\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Kbh\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
