<?php

namespace Gdev\IsaacBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DefyResponse
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gdev\IsaacBundle\Entity\DefyResponseRepository")
 */
class DefyResponse
{

    /**
     * @ORM\ManyToOne(targetEntity="Gdev\IsaacBundle\Entity\Defy", inversedBy="defyResponses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $defys;

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
     * @var string
     *
     * @ORM\Column(name="record", type="string", length=255)
     */
    private $record;

    /**
     * @var string
     *
     * @ORM\Column(name="urlImage", type="string", length=255)
     */
    private $urlImage;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    public function __construct()
    {
        $this->date = new \Datetime();
    }

    public function setDefys(Defy $defys)
    {
        $this->defys = $defys;
        return $this;
    }

    public function getDefys()
    {
        return $this->defys;
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
     * Set idUser
     *
     * @param string $idUser
     * @return DefyResponse
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
     * @return DefyResponse
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

    /**
     * Set record
     *
     * @param string $record
     * @return DefyResponse
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
     * Set urlImage
     *
     * @param string $urlImage
     * @return DefyResponse
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
