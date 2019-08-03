<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DemandesDeposees
 *
 * @ORM\Table(name="demandes_deposees_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\DemandesDeposeesRepository")
 */
class DemandesDeposees
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
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     *
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id")
     * 
     */
    private $salarie;

    /**
     * @var \Demande
     *
     * @ORM\OneToOne(targetEntity="Demande")
     * @ORM\JoinColumn(name="demande", referencedColumnName="id")
     * 
     */
    private $demande;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnvoi", type="datetime")
     */
    private $dateEnvoi;

    /**
     * @var \OrganigrammeUnite
     *
     * @ORM\ManyToOne(targetEntity="OrganigrammeUnite")
     * @ORM\JoinColumn(name="unite", referencedColumnName="id" ,nullable=true)
     */
    private $unite;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */
    private $statut;



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
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     * @return DemandesDeposees
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
     * Set statut
     *
     * @param string $statut
     * @return DemandesDeposees
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string 
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return DemandesDeposees
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
     * Set demande
     *
     * @param \Kbh\GestionCongesBundle\Entity\Demande $demande
     * @return DemandesDeposees
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
     * Set unite
     *
     * @param \Kbh\GestionCongesBundle\Entity\OrganigrammeUnite $unite
     * @return DemandesDeposees
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
}
