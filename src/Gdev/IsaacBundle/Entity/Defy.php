<?php

namespace Gdev\IsaacBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Defy
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gdev\IsaacBundle\Entity\DefyRepository")
 */
class Defy
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
     * @ORM\OneToMany(targetEntity="Gdev\IsaacBundle\Entity\DefyResponse", mappedBy="defys")
     */
    private $defyResponses; // Notez le « s », une annonce est liée à plusieurs candidatures

    /**
     * @var string
     *
     * @ORM\Column(name="seed", type="string", length=255)
     */
    private $seed;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="urlImage", type="string", length=255)
     */
    private $urlImage;

    /**
     * @var string
     *
     * @ORM\Column(name="record", type="string", length=255)
     */
    private $record;

    /**
     * @var string
     *
     * @ORM\Column(name="idUser", type="string", length=255)
     */
    private $idUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;


    public function __construct()
    {
        // Par défaut, la date de l'annonce est la date d'aujourd'hui
        $this->date = new \Datetime();
        $this->defyResponses = new ArrayCollection();

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
     * Set seed
     *
     * @param string $seed
     * @return Defy
     */
    public function setSeed($seed)
    {
        $this->seed = $seed;

        return $this;
    }

    /**
     * Get seed
     *
     * @return string 
     */
    public function getSeed()
    {
        return $this->seed;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Defy
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set urlImage
     *
     * @param string $urlImage
     * @return Defy
     */
    public function setUrlImage($urlImage)
    {
        $this->urlImage = $urlImage;

        return $this;
    }

    /**
     * Get urlImage
     *
     * @return string 
     */
    public function getUrlImage()
    {
        return $this->urlImage;
    }

    /**
     * Set record
     *
     * @param string $record
     * @return Defy
     */
    public function setRecord($record)
    {
        $this->record = $record;

        return $this;
    }

    /**
     * Get record
     *
     * @return string 
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Set idUser
     *
     * @param string $idUser
     * @return Defy
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return string 
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Defy
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }


    public function addDefyResponse(DefyResponse $defyResponse)
    {
        $this->defyResponses[] = $defyResponse;
        $defyResponse->setDefys($this);

        return $this;
    }

    public function removeDefyResponse(DefyResponse $defyResponse)
    {
        $this->defyResponses->removeElement($defyResponse);
    }

    public function getDefyResponses()
    {
        return $this->defyResponses;
    }







    public function getAbsolutePath()
    {
        return null === $this->urlImage ? null : $this->getUploadRootDir().'/'.$this->urlImage;
    }

    public function getWebPath()
    {
        return null === $this->urlImage ? null : $this->getUploadDir().'/'.$this->urlImage;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/documents';
    }

    public function upload()
    {
        // la propriété « file » peut être vide si le champ n'est pas requis
        if (null === $this->file) {
            return;
        }

        // utilisez le nom de fichier original ici mais
        // vous devriez « l'assainir » pour au moins éviter
        // quelconques problèmes de sécurité

        // la méthode « move » prend comme arguments le répertoire cible et
        // le nom de fichier cible où le fichier doit être déplacé
        $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());

        // définit la propriété « path » comme étant le nom de fichier où vous
        // avez stocké le fichier
        $this->urlImage = $this->file->getClientOriginalName();

        // « nettoie » la propriété « file » comme vous n'en aurez plus besoin
        $this->file = null;
    }
}
