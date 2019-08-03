<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Organigramme
 *
 * @ORM\Table(name="organigramme")
 * @ORM\Entity
 */
class Organigramme
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
     * @var integer
     *
     * @ORM\Column(name="nombreNiveaux", type="integer", nullable=false)
     */
    private $nombreNiveaux;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $unites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->unites = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombreNiveaux
     *
     * @param integer $nombreNiveaux
     * @return Organigramme
     */
    public function setNombreNiveaux($nombreNiveaux)
    {
        $this->nombreNiveaux = $nombreNiveaux;

        return $this;
    }

    /**
     * Get nombreNiveaux
     *
     * @return integer 
     */
    public function getNombreNiveaux()
    {
        return $this->nombreNiveaux;
    }

    /**
     * Add unites
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $unites
     * @return Organigramme
     */
    public function addUnite(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $unites)
    {
        $this->unites[] = $unites;

        return $this;
    }

    /**
     * Remove unites
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $unites
     */
    public function removeUnite(\Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $unites)
    {
        $this->unites->removeElement($unites);
    }

    /**
     * Get unites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUnites()
    {
        return $this->unites;
    }
}
