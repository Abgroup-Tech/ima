<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogUpdateMensuel
 *
 * @ORM\Table(name="log_update_mensuel_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\LogUpdateMensuelRepository")
 */
class LogUpdateMensuel
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateUpdate", type="datetime")
     */
    private $dateUpdate;
    

    /**
     * @var \Salarie
     *
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id")
     * 
     */
    private $salarie;   

    /**
     * @var float
     *
     * @ORM\Column(name="ancienSolde", type="float")
     */
    private $ancienSolde;

    /**
     * @var float
     *
     * @ORM\Column(name="nouveauSolde", type="float")
     */
    private $nouveauSolde;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255)
     */
    private $message;

public function __construct() {
    $this->message = "Mise à jours mensuelle éffectuée avec succès!!";
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
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     * @return LogUpdateMensuel
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime 
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }


    /**
     * Set ancienSolde
     *
     * @param float $ancienSolde
     * @return LogUpdateMensuel
     */
    public function setAncienSolde($ancienSolde)
    {
        $this->ancienSolde = $ancienSolde;

        return $this;
    }

    /**
     * Get ancienSolde
     *
     * @return float 
     */
    public function getAncienSolde()
    {
        return $this->ancienSolde;
    }

    /**
     * Set nouveauSolde
     *
     * @param float $nouveauSolde
     * @return LogUpdateMensuel
     */
    public function setNouveauSolde($nouveauSolde)
    {
        $this->nouveauSolde = $nouveauSolde;

        return $this;
    }

    /**
     * Get nouveauSolde
     *
     * @return float 
     */
    public function getNouveauSolde()
    {
        return $this->nouveauSolde;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return LogUpdateMensuel
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return LogUpdateMensuel
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
}
