<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImportFeries
 *
 * @ORM\Table(name="import_feries_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\ImportFeriesRepository")
 */
class ImportFeries
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
     * @ORM\Column(name="titreFeries", type="string", length=255)
     */
    private $titreFeries;

    /**
     * @var string
     *
     * @ORM\Column(name="dateDebutFerie", type="string")
     */
    private $dateDebutFerie;

    /**
     * @var string
     *
     * @ORM\Column(name="dateFinFerie", type="string")
     */
    private $dateFinFerie;

    /**
     * @var string
     *
     * @ORM\Column(name="nbJoursFerie", type="string", length=255)
     */
    private $nbJoursFerie;


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
     * Set titreFeries
     *
     * @param string $titreFeries
     * @return ImportFeries
     */
    public function setTitreFeries($titreFeries)
    {
        $this->titreFeries = $titreFeries;

        return $this;
    }

    /**
     * Get titreFeries
     *
     * @return string 
     */
    public function getTitreFeries()
    {
        return $this->titreFeries;
    }

    /**
     * Set dateDebutFerie
     *
     * @param string $dateDebutFerie
     * @return ImportFeries
     */
    public function setDateDebutFerie($dateDebutFerie)
    {
        $this->dateDebutFerie = $dateDebutFerie;

        return $this;
    }

    /**
     * Get dateDebutFerie
     *
     * @return string 
     */
    public function getDateDebutFerie()
    {
        return $this->dateDebutFerie;
    }

    /**
     * Set dateFinFerie
     *
     * @param string $dateFinFerie
     * @return ImportFeries
     */
    public function setDateFinFerie($dateFinFerie)
    {
        $this->dateFinFerie = $dateFinFerie;

        return $this;
    }

    /**
     * Get dateFinFerie
     *
     * @return string 
     */
    public function getDateFinFerie()
    {
        return $this->dateFinFerie;
    }

    /**
     * Set nbJoursFerie
     *
     * @param string $nbJoursFerie
     * @return ImportFeries
     */
    public function setNbJoursFerie($nbJoursFerie)
    {
        $this->nbJoursFerie = $nbJoursFerie;

        return $this;
    }

    /**
     * Get nbJoursFerie
     *
     * @return string 
     */
    public function getNbJoursFerie()
    {
        return $this->nbJoursFerie;
    }
}
