<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImportUnite
 *
 * @ORM\Table(name="import_unite_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\ImportUniteRepository")
 */
class ImportUnite
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
     * @ORM\Column(name="nomUnite", type="string", length=255, nullable=true)
     */
    private $nomUnite;

    /**
     * @var string
     *
     * @ORM\Column(name="sigle", type="string", length=255, nullable=true)
     */
    private $sigle;

    /**
     * @var string
     *
     * @ORM\Column(name="nomManager", type="string", length=255, nullable=true)
     */
    private $nomManager;
    
    /**
     * @var string
     *
     * @ORM\Column(name="matriculeManager", type="string", length=255, nullable=true)
     */
    private $matriculeManager;

    /**
     * @var string
     *
     * @ORM\Column(name="posteManager", type="string", length=255, nullable=true)
     */
    private $posteManager;

    /**
     * @var string
     *
     * @ORM\Column(name="typeUnite", type="string", length=255, nullable=true)
     */
    private $typeUnite;

    /**
     * @var string
     *
     * @ORM\Column(name="uniteSuivante1", type="string", length=255, nullable=true)
     */
    private $uniteSuivante1;

    /**
     * @var string
     *
     * @ORM\Column(name="managerUnite1", type="string", length=255, nullable=true)
     */
    private $managerUnite1;

    /**
     * @var string
     *
     * @ORM\Column(name="uniteSuivante2", type="string", length=255, nullable=true)
     */
    private $uniteSuivante2;

    /**
     * @var string
     *
     * @ORM\Column(name="managerUnite2", type="string", length=255, nullable=true)
     */
    private $managerUnite2;

    /**
     * @var string
     *
     * @ORM\Column(name="uniteSuivante3", type="string", length=255, nullable=true)
     */
    private $uniteSuivante3;

    /**
     * @var string
     *
     * @ORM\Column(name="managerUnite3", type="string", length=255, nullable=true)
     */
    private $managerUnite3;


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
     * Set nomUnite
     *
     * @param string $nomUnite
     *
     * @return ImportUnite
     */
    public function setNomUnite($nomUnite)
    {
        $this->nomUnite = $nomUnite;

        return $this;
    }

    /**
     * Get nomUnite
     *
     * @return string
     */
    public function getNomUnite()
    {
        return $this->nomUnite;
    }

    /**
     * Set sigle
     *
     * @param string $sigle
     *
     * @return ImportUnite
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
     * Set nomManager
     *
     * @param string $nomManager
     *
     * @return ImportUnite
     */
    public function setNomManager($nomManager)
    {
        $this->nomManager = $nomManager;

        return $this;
    }

    /**
     * Get nomManager
     *
     * @return string
     */
    public function getNomManager()
    {
        return $this->nomManager;
    }

    /**
     * Set posteManager
     *
     * @param string $posteManager
     *
     * @return ImportUnite
     */
    public function setPosteManager($posteManager)
    {
        $this->posteManager = $posteManager;

        return $this;
    }

    /**
     * Get posteManager
     *
     * @return string
     */
    public function getPosteManager()
    {
        return $this->posteManager;
    }

    /**
     * Set typeUnite
     *
     * @param string $typeUnite
     *
     * @return ImportUnite
     */
    public function setTypeUnite($typeUnite)
    {
        $this->typeUnite = $typeUnite;

        return $this;
    }

    /**
     * Get typeUnite
     *
     * @return string
     */
    public function getTypeUnite()
    {
        return $this->typeUnite;
    }

    /**
     * Set uniteSuivante1
     *
     * @param string $uniteSuivante1
     *
     * @return ImportUnite
     */
    public function setUniteSuivante1($uniteSuivante1)
    {
        $this->uniteSuivante1 = $uniteSuivante1;

        return $this;
    }

    /**
     * Get uniteSuivante1
     *
     * @return string
     */
    public function getUniteSuivante1()
    {
        return $this->uniteSuivante1;
    }

    /**
     * Set managerUnite1
     *
     * @param string $managerUnite1
     *
     * @return ImportUnite
     */
    public function setManagerUnite1($managerUnite1)
    {
        $this->managerUnite1 = $managerUnite1;

        return $this;
    }

    /**
     * Get managerUnite1
     *
     * @return string
     */
    public function getManagerUnite1()
    {
        return $this->managerUnite1;
    }

    /**
     * Set uniteSuivante2
     *
     * @param string $uniteSuivante2
     *
     * @return ImportUnite
     */
    public function setUniteSuivante2($uniteSuivante2)
    {
        $this->uniteSuivante2 = $uniteSuivante2;

        return $this;
    }

    /**
     * Get uniteSuivante2
     *
     * @return string
     */
    public function getUniteSuivante2()
    {
        return $this->uniteSuivante2;
    }

    /**
     * Set managerUnite2
     *
     * @param string $managerUnite2
     *
     * @return ImportUnite
     */
    public function setManagerUnite2($managerUnite2)
    {
        $this->managerUnite2 = $managerUnite2;

        return $this;
    }

    /**
     * Get managerUnite2
     *
     * @return string
     */
    public function getManagerUnite2()
    {
        return $this->managerUnite2;
    }

    /**
     * Set uniteSuivante3
     *
     * @param string $uniteSuivante3
     *
     * @return ImportUnite
     */
    public function setUniteSuivante3($uniteSuivante3)
    {
        $this->uniteSuivante3 = $uniteSuivante3;

        return $this;
    }

    /**
     * Get uniteSuivante3
     *
     * @return string
     */
    public function getUniteSuivante3()
    {
        return $this->uniteSuivante3;
    }

    /**
     * Set managerUnite3
     *
     * @param string $managerUnite3
     *
     * @return ImportUnite
     */
    public function setManagerUnite3($managerUnite3)
    {
        $this->managerUnite3 = $managerUnite3;

        return $this;
    }

    /**
     * Get managerUnite3
     *
     * @return string
     */
    public function getManagerUnite3()
    {
        return $this->managerUnite3;
    }
    
    function getMatriculeManager() {
        return $this->matriculeManager;
    }

    function setMatriculeManager($matriculeManager) {
        $this->matriculeManager = $matriculeManager;
    }


}

