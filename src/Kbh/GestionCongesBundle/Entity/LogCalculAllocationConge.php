<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * logcalculallocationconge
 *
 * @ORM\Table(name="logcalculallocationconge_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\LogCalculAllocationCongeRepository")
 */
class LogCalculAllocationConge
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
     * @ORM\Column(name="salarie", type="string", length=255)
     */
    private $salarie;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="matricule", type="string", length=255)
     */
    private $matricule;

    /**
     * @var float
     *
     * @ORM\Column(name="salaireMensuel", type="float")
     */
    private $salaireMensuel;

    /**
     * @var float
     *
     * @ORM\Column(name="gratification", type="float")
     */
    private $gratification;
    
    /**
     * @var string
     *
     * @ORM\Column(name="dateEffet", type="string")
     */
    private $dateEffet;    


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
     * Set salarie
     *
     * @param string $salarie
     * @return LogCalculAllocationConge
     */
    public function setSalarie($salarie)
    {
        $this->salarie = $salarie;

        return $this;
    }

    /**
     * Get salarie
     *
     * @return string 
     */
    public function getSalarie()
    {
        return $this->salarie;
    }

    /**
     * Set matricule
     *
     * @param string $matricule
     * @return LogCalculAllocationConge
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
     * Set salaireMensuel
     *
     * @param float $salaireMensuel
     * @return LogCalculAllocationConge
     */
    public function setSalaireMensuel($salaireMensuel)
    {
        $this->salaireMensuel = $salaireMensuel;

        return $this;
    }

    /**
     * Get salaireMensuel
     *
     * @return float 
     */
    public function getSalaireMensuel()
    {
        return $this->salaireMensuel;
    }

    /**
     * Set gratification
     *
     * @param float $gratification
     * @return LogCalculAllocationConge
     */
    public function setGratification($gratification)
    {
        $this->gratification = $gratification;

        return $this;
    }

    /**
     * Get gratification
     *
     * @return float 
     */
    public function getGratification()
    {
        return $this->gratification;
    }

    /**
     * Set dateEffet
     *
     * @param \DateTime $dateEffet
     * @return LogCalculAllocationConge
     */
    public function setDateEffet($dateEffet)
    {
        $this->dateEffet = $dateEffet;

        return $this;
    }

    /**
     * Get dateEffet
     *
     * @return \DateTime 
     */
    public function getDateEffet()
    {
        return $this->dateEffet;
    }
}
