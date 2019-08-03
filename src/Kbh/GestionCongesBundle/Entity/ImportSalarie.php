<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImportSalarie
 *
 * @ORM\Table(name="importSalarie_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\ImportSalarieRepository")
 */
class ImportSalarie
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
     * @ORM\Column(name="civilite", type="string", length=255, nullable=true)
     */
    private $civilite;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="matricule", type="string", length=255, nullable=true)
     */
    private $matricule;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroCnps", type="string", length=255, nullable=true)
     */
    private $numeroCnps;

    /**
     * @var string
     *
     * @ORM\Column(name="dateNaissance", type="string", length=255, nullable=true)
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="statutMarital", type="string", length=255, nullable=true)
     */
    private $statutMarital;

    /**
     * @var string
     *
     * @ORM\Column(name="poste", type="string", length=255, nullable=true)
     */
    private $poste;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="dateEmbauche", type="string", length=255, nullable=true)
     */
    private $dateEmbauche;

    /**
     * @var string
     *
     * @ORM\Column(name="typeContrat", type="string", length=255, nullable=true)
     */
    private $typeContrat;

    /**
     * @var string
     *
     * @ORM\Column(name="statutEmploi", type="string", length=255, nullable=true)
     */
    private $statutEmploi;

    /**
     * @var float
     *
     * @ORM\Column(name="droitsAcquis", type="float", nullable=true)
     */
    private $droitsAcquis;

    /**
     * @var float
     *
     * @ORM\Column(name="droitsAnterieur", type="float", nullable=true)
     */
    private $droitsAnterieur;

    /**
     * @var float
     *
     * @ORM\Column(name="cumulDroits", type="float", nullable=true)
     */
    private $cumulDroits;

    /**
     * @var float
     *
     * @ORM\Column(name="droitsPris", type="float", nullable=true)
     */
    private $droitsPris;

    /**
     * @var float
     *
     * @ORM\Column(name="soldePermission", type="float", nullable=true)
     */
    private $soldePermission;

    /**
     * @var float
     *
     * @ORM\Column(name="permissionsPrises", type="float", nullable=true)
     */
    private $permissionsPrises;

    /**
     * @var float
     *
     * @ORM\Column(name="soldeConges", type="float", nullable=true)
     */
    private $soldeConges;

    /**
     * @var string
     *
     * @ORM\Column(name="unite", type="string", length=255, nullable=true)
     */
    private $unite;

    /**
     * @var string
     *
     * @ORM\Column(name="responsableDirect", type="string", length=255, nullable=true)
     */
    private $responsableDirect;

    /**
     * @var string
     *
     * @ORM\Column(name="posteResponsableDirect", type="string", length=255, nullable=true)
     */
    private $posteResponsableDirect;

    /**
     * @var string
     *
     * @ORM\Column(name="roleUtilisateur", type="string", length=255, nullable=true)
     */
    private $roleUtilisateur;
    
    
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
     * Set civilite
     *
     * @param string $civilite
     *
     * @return ImportSalarie
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
     * @return ImportSalarie
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
     * @return ImportSalarie
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
     * Set matricule
     *
     * @param string $matricule
     *
     * @return ImportSalarie
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
     * @return ImportSalarie
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
     * Set dateNaissance
     *
     * @param string $dateNaissance
     *
     * @return ImportSalarie
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return string
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
     * @return ImportSalarie
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
     * @return ImportSalarie
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
     * @return ImportSalarie
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
     * @return ImportSalarie
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
     * @param string $dateEmbauche
     *
     * @return ImportSalarie
     */
    public function setDateEmbauche($dateEmbauche)
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    /**
     * Get dateEmbauche
     *
     * @return string
     */
    public function getDateEmbauche()
    {
        return $this->dateEmbauche;
    }

    /**
     * Set typeContrat
     *
     * @param string $typeContrat
     *
     * @return ImportSalarie
     */
    public function setTypeContrat($typeContrat)
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    /**
     * Get typeContrat
     *
     * @return string
     */
    public function getTypeContrat()
    {
        return $this->typeContrat;
    }

    /**
     * Set statutEmploi
     *
     * @param string $statutEmploi
     *
     * @return ImportSalarie
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
     * Set droitsAcquis
     *
     * @param float $droitsAcquis
     *
     * @return ImportSalarie
     */
    public function setDroitsAcquis($droitsAcquis)
    {
        $this->droitsAcquis = $droitsAcquis;

        return $this;
    }

    /**
     * Get droitsAcquis
     *
     * @return float
     */
    public function getDroitsAcquis()
    {
        return $this->droitsAcquis;
    }

    /**
     * Set droitsAnterieur
     *
     * @param float $droitsAnterieur
     *
     * @return ImportSalarie
     */
    public function setDroitsAnterieur($droitsAnterieur)
    {
        $this->droitsAnterieur = $droitsAnterieur;

        return $this;
    }

    /**
     * Get droitsAnterieur
     *
     * @return float
     */
    public function getDroitsAnterieur()
    {
        return $this->droitsAnterieur;
    }

    /**
     * Set cumulDroits
     *
     * @param float $cumulDroits
     *
     * @return ImportSalarie
     */
    public function setCumulDroits($cumulDroits)
    {
        $this->cumulDroits = $cumulDroits;

        return $this;
    }

    /**
     * Get cumulDroits
     *
     * @return float
     */
    public function getCumulDroits()
    {
        return $this->cumulDroits;
    }

    /**
     * Set droitsPris
     *
     * @param float $droitsPris
     *
     * @return ImportSalarie
     */
    public function setDroitsPris($droitsPris)
    {
        $this->droitsPris = $droitsPris;

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
     * Set soldePermission
     *
     * @param float $soldePermission
     *
     * @return ImportSalarie
     */
    public function setSoldePermission($soldePermission)
    {
        $this->soldePermission = $soldePermission;

        return $this;
    }

    /**
     * Get soldePermission
     *
     * @return float
     */
    public function getSoldePermission()
    {
        return $this->soldePermission;
    }

    /**
     * Set permissionsPrises
     *
     * @param float $permissionsPrises
     *
     * @return ImportSalarie
     */
    public function setPermissionsPrises($permissionsPrises)
    {
        $this->permissionsPrises = $permissionsPrises;

        return $this;
    }

    /**
     * Get permissionsPrises
     *
     * @return float
     */
    public function getPermissionsPrises()
    {
        return $this->permissionsPrises;
    }

    /**
     * Set soldeConges
     *
     * @param float $soldeConges
     *
     * @return ImportSalarie
     */
    public function setSoldeConges($soldeConges)
    {
        $this->soldeConges = $soldeConges;

        return $this;
    }

    /**
     * Get soldeConges
     *
     * @return float
     */
    public function getSoldeConges()
    {
        return $this->soldeConges;
    }

    /**
     * Set unite
     *
     * @param string $unite
     *
     * @return ImportSalarie
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

    /**
     * Set responsableDirect
     *
     * @param string $responsableDirect
     *
     * @return ImportSalarie
     */
    public function setResponsableDirect($responsableDirect)
    {
        $this->responsableDirect = $responsableDirect;

        return $this;
    }

    /**
     * Get responsableDirect
     *
     * @return string
     */
    public function getResponsableDirect()
    {
        return $this->responsableDirect;
    }

    /**
     * Set posteResponsableDirect
     *
     * @param string $posteResponsableDirect
     *
     * @return ImportSalarie
     */
    public function setPosteResponsableDirect($posteResponsableDirect)
    {
        $this->posteResponsableDirect = $posteResponsableDirect;

        return $this;
    }

    /**
     * Get posteResponsableDirect
     *
     * @return string
     */
    public function getPosteResponsableDirect()
    {
        return $this->posteResponsableDirect;
    }
    
    function getRoleUtilisateur() {
        return $this->roleUtilisateur;
    }

    function setRoleUtilisateur($roleUtilisateur) {
        $this->roleUtilisateur = $roleUtilisateur;
    }


}

