<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entreprise
 *
 * @ORM\Table(name="entreprise_premium")
 * @ORM\Entity
 */
class Entreprise
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
     * @var integer
     *
     * @ORM\Column(name="nbSalaries", type="integer", nullable=false)
     */
    private $nbSalaries;

    /**
     * @ORM\OneToOne(targetEntity="Kbh\GestionCongesBundle\Entity\Media", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="logo", nullable=true)
     */
    private $logo;

    /**
     * @var datetime
     *
     * @ORM\Column(name="dateImplementation", type="datetime", length=255, nullable=false)
     */
    private $dateImplementation;
    
    /**
     * @var string
     *
     * @ORM\Column(name="dateLivraison", type="datetime", length=255, nullable=false)
     */
    private $dateLivraison;

     /**
     * @var integer
     *
     * @ORM\Column(name="delaisMiseAjours", type="integer", nullable=false)
     */
    private $delaisMiseAjours;

     /**
     * @var integer
     *
     * @ORM\Column(name="delaisMiseAjoursAnnuel", type="integer", nullable=false)
     */
    private $delaisMiseAjoursAnnuel;    
    
        /**
     * @var integer
     *
     * @ORM\Column(name="delaisClearCache", type="integer", nullable=true)
     */
    private $delaisClearCache;
    
    /**
     * @var string
     *
     * @ORM\Column(name="dateUpdateMensuel", type="string")
     */
    private $dateUpdateMensuel;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="dateUpdateAnnuel", type="string")
     */
    private $dateUpdateAnnuel;     
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Document
     * @ORM\ManyToOne(targetEntity="Document", cascade={"persist"}) 
     * @ORM\JoinColumn(name="docFeries", referencedColumnName="id" , nullable=true)
     */
    private $docFeries;
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Document
     * @ORM\ManyToOne(targetEntity="Document", cascade={"persist"})  
     * @ORM\JoinColumn(name="docPermissions", referencedColumnName="id" , nullable=true)
     */
    private $docPermissions;
    

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
     * @return Entreprise
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
     * Set nbSalaries
     *
     * @param integer $nbSalaries
     * @return Entreprise
     */
    public function setNbSalaries($nbSalaries)
    {
        $this->nbSalaries = $nbSalaries;

        return $this;
    }

    /**
     * Get nbSalaries
     *
     * @return integer 
     */
    public function getNbSalaries()
    {
        return $this->nbSalaries;
    }

    /**
     * Set dateImplementation
     *
     * @param \DateTime $dateImplementation
     * @return Entreprise
     */
    public function setDateImplementation($dateImplementation)
    {
        $this->dateImplementation = $dateImplementation;

        return $this;
    }

    /**
     * Get dateImplementation
     *
     * @return \DateTime 
     */
    public function getDateImplementation()
    {
        return $this->dateImplementation;
    }

    /**
     * Set dateLivraison
     *
     * @param \DateTime $dateLivraison
     * @return Entreprise
     */
    public function setDateLivraison($dateLivraison)
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    /**
     * Get dateLivraison
     *
     * @return \DateTime 
     */
    public function getDateLivraison()
    {
        return $this->dateLivraison;
    }

    /**
     * Set logo
     *
     * @param \Kbh\GestionCongesBundle\Entity\Media $logo
     * @return Entreprise
     */
    public function setLogo(\Kbh\GestionCongesBundle\Entity\Media $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return \Kbh\GestionCongesBundle\Entity\Media 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set delaisMiseAjours
     *
     * @param integer $delaisMiseAjours
     * @return Entreprise
     */
    public function setDelaisMiseAjours($delaisMiseAjours)
    {
        $this->delaisMiseAjours = $delaisMiseAjours;

        return $this;
    }

    /**
     * Get delaisMiseAjours
     *
     * @return integer 
     */
    public function getDelaisMiseAjours()
    {
        return $this->delaisMiseAjours;
    }

    /**
     * Set delaisClearCache
     *
     * @param integer $delaisClearCache
     * @return Entreprise
     */
    public function setDelaisClearCache($delaisClearCache)
    {
        $this->delaisClearCache = $delaisClearCache;

        return $this;
    }

    /**
     * Get delaisClearCache
     *
     * @return integer 
     */
    public function getDelaisClearCache()
    {
        return $this->delaisClearCache;
    }

    /**
     * Set delaisMiseAjoursAnnuel
     *
     * @param integer $delaisMiseAjoursAnnuel
     * @return Entreprise
     */
    public function setDelaisMiseAjoursAnnuel($delaisMiseAjoursAnnuel)
    {
        $this->delaisMiseAjoursAnnuel = $delaisMiseAjoursAnnuel;

        return $this;
    }

    /**
     * Get delaisMiseAjoursAnnuel
     *
     * @return integer 
     */
    public function getDelaisMiseAjoursAnnuel()
    {
        return $this->delaisMiseAjoursAnnuel;
    }


    /**
     * Set dateUpdateMensuel
     *
     * @param string $dateUpdateMensuel
     * @return Entreprise
     */
    public function setDateUpdateMensuel($dateUpdateMensuel)
    {
        $this->dateUpdateMensuel = $dateUpdateMensuel;

        return $this;
    }

    /**
     * Get dateUpdateMensuel
     *
     * @return string 
     */
    public function getDateUpdateMensuel()
    {
        return $this->dateUpdateMensuel;
    }

    /**
     * Set dateUpdateAnnuel
     *
     * @param string $dateUpdateAnnuel
     * @return Entreprise
     */
    public function setDateUpdateAnnuel($dateUpdateAnnuel)
    {
        $this->dateUpdateAnnuel = $dateUpdateAnnuel;

        return $this;
    }

    /**
     * Get dateUpdateAnnuel
     *
     * @return string 
     */
    public function getDateUpdateAnnuel()
    {
        return $this->dateUpdateAnnuel;
    }

    /**
     * Set docFeries
     *
     * @param \Kbh\GestionCongesBundle\Entity\Document $docFeries
     * @return Entreprise
     */
    public function setDocFeries(\Kbh\GestionCongesBundle\Entity\Document $docFeries = null)
    {
        $this->docFeries = $docFeries;

        return $this;
    }

    /**
     * Get docFeries
     *
     * @return \Kbh\GestionCongesBundle\Entity\Document 
     */
    public function getDocFeries()
    {
        return $this->docFeries;
    }

    /**
     * Set docPermissions
     *
     * @param \Kbh\GestionCongesBundle\Entity\Document $docPermissions
     * @return Entreprise
     */
    public function setDocPermissions(\Kbh\GestionCongesBundle\Entity\Document $docPermissions = null)
    {
        $this->docPermissions = $docPermissions;

        return $this;
    }

    /**
     * Get docPermissions
     *
     * @return \Kbh\GestionCongesBundle\Entity\Document 
     */
    public function getDocPermissions()
    {
        return $this->docPermissions;
    }
}
