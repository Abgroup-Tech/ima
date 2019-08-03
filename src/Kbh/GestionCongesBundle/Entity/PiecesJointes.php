<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PiecesJointes
 *
 * @ORM\Table(name="piecesJointes_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\PiecesJointesRepository")
 */
class PiecesJointes
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
     * @ORM\ManyToOne(targetEntity="Kbh\GestionCongesBundle\Entity\Salarie")
     * @ORM\JoinColumn(name="salarie", referencedColumnName="id" , nullable=true)
     */
    private $salarie;    
      
    
    /**
     * @var \Kbh\GestionCongesBundle\Entity\Salarie
     * @ORM\ManyToOne(targetEntity="Kbh\GestionCongesBundle\Entity\Salarie")
     * @ORM\JoinColumn(name="ajouterPar", referencedColumnName="id", nullable=true)
     */
    private $ajouterPar;    

    /**
     * @var \DateTime
     * 
     * @ORM\COlumn(name="updated_at",type="datetime", nullable=true) 
     */
    private $updateAt;
    
    /**
     * @var \DateTime
     * 
     * @ORM\COlumn(name="dateCreation",type="datetime", nullable=true) 
     */
    private $dateCreation;

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->updateAt = new \DateTime();
    }    

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255 ,nullable=true)
     * 
     */
    public $path;
    
    /**
     * @var string
     *
     * @ORM\Column(name="downloadPath", type="string", length=255 ,nullable=true)
     * 
     */
    public $downloadPath;
    
    /**
     * @Assert\File(maxSize="2000000000000")
     */
    public $file;
    

    public function getUploadRootDir()
    {
        return __dir__.'/../../../../web/Fichiers_Salaries/'.$this->salarie->getNom().'-'.$this->salarie->getPrenom();
    }
    
    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }
    
    public function getAssetPath()
    {
        return 'Fichiers_Salaries/'.$this->salarie->getNom().'-'.$this->salarie->getPrenom().'/'.$this->path;
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate() 
     */
    public function preUpload()
    {
        $this->tempFile = $this->getAbsolutePath();
        $this->oldFile = $this->getPath();
        $this->updateAt = new \DateTime();
        
        if (null !== $this->file) 
            $this->path = sha1(uniqid(mt_rand(),true)).'.'.$this->file->guessExtension();
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate() 
     */
    public function upload()
    {
        if (null !== $this->file) {
            $this->file->move($this->getUploadRootDir(),$this->path);
            unset($this->file);
            
            if ($this->oldFile != null) unlink($this->tempFile);
        }
    }
    
    /**
     * @ORM\PreRemove() 
     */
    public function preRemoveUpload()
    {
        $this->tempFile = $this->getAbsolutePath();
    }
    
    /**
     * @ORM\PostRemove() 
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFile)) unlink($this->tempFile);
    }
    
    /**
     * __toString() function
     */
    public function __toString()
    {
      return  $this->getName();
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
     * Get updateAt
     *
     * @return \DateTime 
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return PiecesJointes
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }


    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
    
    function getDownloadPath() {
          return $this->downloadPath;
    }

    function setDownloadPath($downloadPath) {
          $this->downloadPath = $downloadPath;
    }

        
    /**
     * Set salarie
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $salarie
     * @return PiecesJointes
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
     * Set name
     *
     * @param string $name
     * @return PiecesJointes
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    

    /**
     * Set ajouterPar
     *
     * @param \Kbh\GestionCongesBundle\Entity\Salarie $ajouterPar
     * @return PiecesJointes
     */
    public function setAjouterPar(\Kbh\GestionCongesBundle\Entity\Salarie $ajouterPar = null)
    {
        $this->ajouterPar = $ajouterPar;

        return $this;
    }

    /**
     * Get ajouterPar
     *
     * @return \Kbh\GestionCongesBundle\Entity\Salarie 
     */
    public function getAjouterPar()
    {
        return $this->ajouterPar;
    }
}
