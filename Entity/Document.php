<?php

namespace NomayaCandidaturesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="NomayaCandidaturesBundle\Entity\DocumentRepository")
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\Length(
     *      min=2,
     *      max=255,
     *      minMessage="Le nom doit faire au moins {{ limit }} caractères.",
     *      maxMessage="Le nom ne peut pas dépasser {{ limit }} caractères"
     * )     
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=10, nullable=true)
     */
    private $extension;
    
    /**
     * @Assert\File(
     *      maxSize="50M",
     *      maxSizeMessage="Le fichier ne doit pas excéder 50 Mo",
     *      mimeTypes={"application/pdf", "application/x-pdf", "application/vnd.oasis.opendocument.*", "application/msword*", "application/vnd.ms-*", "image/*", "text/html", "text/rtf", "text/xml", "text/csv"}
     *      )
     */
    public $file;

    /**
     * @ORM\ManyToOne(targetEntity="Candidature", inversedBy="documents")
     */
    private $candidature;

    /**
     * @ORM\ManyToOne(targetEntity="Evenement", inversedBy="documents")
     */
    private $evenement;


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
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $this->getWebPath();

        return $this;
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

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/documents';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // faites ce que vous voulez pour générer un nom unique
            $this->path = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // s'il y a une erreur lors du déplacement du fichier, une exception
        // va automatiquement être lancée par la méthode move(). Cela va empêcher
        // proprement l'entité d'être persistée dans la base de données si
        // erreur il y a
        $this->file->move($this->getUploadRootDir(), $this->path);

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }


    /**
     * Set candidature
     *
     * @param \NomayaCandidaturesBundle\Entity\Candidature $candidature
     * @return Document
     */
    public function setCandidature(\NomayaCandidaturesBundle\Entity\Candidature $candidature = null)
    {
        $this->candidature = $candidature;

        return $this;
    }

    /**
     * Get candidature
     *
     * @return \NomayaCandidaturesBundle\Entity\Candidature 
     */
    public function getCandidature()
    {
        return $this->candidature;
    }


    /**
     * Set evenement
     *
     * @param \NomayaCandidaturesBundle\Entity\Evenement $evenement
     * @return Document
     */
    public function setEvenement(\NomayaCandidaturesBundle\Entity\Evenement $evenement = null)
    {
        $this->evenement = $evenement;

        return $this;
    }

    /**
     * Get evenement
     *
     * @return \NomayaCandidaturesBundle\Entity\Evenement 
     */
    public function getEvenement()
    {
        return $this->evenement;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return Document
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @ORM\PrePersist
     */
    public function setExtensionValue()
    {
        $this->setExtension( $this->file->guessExtension() );
    }
}
