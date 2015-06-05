<?php

namespace Gdev\IsaacBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Seeds
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gdev\IsaacBundle\Entity\SeedsRepository")
 */
class Seeds
{
    /**
     * @ORM\ManyToMany(targetEntity="Gdev\IsaacBundle\Entity\Item", cascade={"persist"})
     */
    private $items;

    /**
     * @ORM\OneToMany(targetEntity="Gdev\IsaacBundle\Entity\SeedResponse", mappedBy="seeds")
     */
    private $seedResponses; // Notez le « s », une annonce est liée à plusieurs candidatures

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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="idUser", type="string", length=255)
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    public function __construct()
    {
        // Par défaut, la date de l'annonce est la date d'aujourd'hui
        $this->date = new \Datetime();
        $this->seedResponses = new ArrayCollection();
        $this->items = new ArrayCollection();

    }

    // Notez le singulier, on ajoute une seule catégorie à la fois
    public function addItem(Item $item)
    {
        // Ici, on utilise l'ArrayCollection vraiment comme un tableau
        $this->items[] = $item;

        return $this;
    }

    public function removeItem(Item $item)
    {
        // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
        $this->items->removeElement($item);
    }

    // Notez le pluriel, on récupère une liste de catégories ici !
    public function getItems()
    {
        return $this->items;
    }

     public function addSeedResponse(SeedResponse $seedResponse)
    {
        $this->seedResponses[] = $seedResponse;
        $seedResponse->setSeeds($this);

        return $this;
    }

    public function removeSeedResponse(SeedResponse $seedResponse)
    {
        $this->seedResponses->removeElement($seedResponse);
    }

    public function getSeedResponses()
    {
        return $this->seedResponses;
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
     * Set date
     *
     * @param \DateTime $date
     * @return Seeds
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
     * Set code
     *
     * @param string $code
     * @return Seeds
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set idUser
     *
     * @param string $idUser
     * @return Seeds
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
     * Set description
     *
     * @param string $description
     * @return Seeds
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
}
