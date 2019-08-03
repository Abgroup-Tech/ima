<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * salairemoyenjournalier
 *
 * @ORM\Table(name="salairemoyenjournalier_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\SalaireMoyenJournalierRepository")
 */
class SalaireMoyenJournalier
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
     * @ORM\Column(name="matricule", type="string", length=255, nullable=true)
     */
    private $matricule;

    /**
     * @var string
     *
     * @ORM\Column(name="salaireMoyenJournalier", type="decimal")
     */
    private $salaireMoyenJournalier;
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id" , nullable=true)
     */
    private $salarie;



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
     * @return SalaireMoyenJournalier
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
     * Set salaireMoyenJournalier
     *
     * @param string $salaireMoyenJournalier
     * @return SalaireMoyenJournalier
     */
    public function setSalaireMoyenJournalier($salaireMoyenJournalier)
    {
        $this->salaireMoyenJournalier = $salaireMoyenJournalier;

        return $this;
    }

    /**
     * Get salaireMoyenJournalier
     *
     * @return string 
     */
    public function getSalaireMoyenJournalier()
    {
        return $this->salaireMoyenJournalier;
    }

    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return SalaireMoyenJournalier
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
