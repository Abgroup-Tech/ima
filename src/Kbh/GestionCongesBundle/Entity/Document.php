<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table(name="document_premium")
 * @ORM\Entity(repositoryClass="Kbh\GestionCongesBundle\Entity\DocumentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Document
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
     * @ORM\COlumn(name="updated_at",type="datetime", nullable=true) 
     */
    private $updateAt;
    
    /**
     * @var \DateTime
     * 
     * @ORM\COlumn(name="dateCreation",type="datetime", nullable=true) 
     */
    private $dateCreation;

//    /**
//     * @ORM\PostLoad()
//     */
//    public function postLoad()
//    {
//        $this->updateAt = new \DateTime();
//    }    

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
     * @ORM\Column(name="cible", type="string", length=255 ,nullable=true)
     * 
     */
    public $cible;
    
    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255 ,nullable=true)
     * 
     */
    public $statut;
    
    /**
     * @Assert\File(maxSize="1200000000000000")
     */
    public $file;
    

    public function getUploadRootDir()
    {
        return __dir__.'/../../../../web/documents';
    }
    
    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }
    
    public function getAssetPath()
    {
        return 'documents/'.$this->path;
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
     * Construct
     *
     */
    public function __construct() {
        $this->statut = "En attente";
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
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    function getUpdateAt() {
        return $this->updateAt;
    }
    
    function getDateCreation() {
        return $this->dateCreation;
    }

    function getCible() {
        return $this->cible;
    }

    function getStatut() {
        return $this->statut;
    }

    function setDateCreation(\DateTime $dateCreation) {
        $this->dateCreation = $dateCreation;
    }

    function setCible($cible) {
        $this->cible = $cible;
    }

    function setStatut($statut) {
        $this->statut = $statut;
    }


}
