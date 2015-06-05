<?php

namespace Gdev\IsaacBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Twitch
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gdev\IsaacBundle\Entity\TwitchRepository")
 */
class Twitch
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
     * @ORM\Column(name="seed", type="string", length=255)
     */
    private $seed;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="firstPlayer", type="string", length=255)
     */
    private $firstPlayer;

    /**
     * @var string
     *
     * @ORM\Column(name="secondPlayer", type="string", length=255)
     */
    private $secondPlayer;

   /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDefy", type="datetime")
     */
    private $dateDefy;

   /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTimer", type="datetime", nullable=true)
     */
    private $dateTimer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isStarted", type="boolean")
     */
    private $isStarted;

    public function __construct()
    {
        // Par défaut, la date de l'annonce est la date d'aujourd'hui
        $this->dateDefy = new \Datetime();
        //$this->dateTimer = new \Datetime();
        $this->isStarted = false;

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
     * @return Twitch
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
     * Set author
     *
     * @param string $author
     * @return Twitch
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set firstPlayer
     *
     * @param string $firstPlayer
     * @return Twitch
     */
    public function setFirstPlayer($firstPlayer)
    {
        $this->firstPlayer = $firstPlayer;

        return $this;
    }

    /**
     * Get firstPlayer
     *
     * @return string 
     */
    public function getFirstPlayer()
    {
        return $this->firstPlayer;
    }

    /**
     * Set secondPlayer
     *
     * @param string $secondPlayer
     * @return Twitch
     */
    public function setSecondPlayer($secondPlayer)
    {
        $this->secondPlayer = $secondPlayer;

        return $this;
    }

    /**
     * Get secondPlayer
     *
     * @return string 
     */
    public function getSecondPlayer()
    {
        return $this->secondPlayer;
    }

    /**
     * Set dateDefy
     *
     * @param string $dateDefy
     * @return Twitch
     */
    public function setDateDefy($dateDefy)
    {
        $this->dateDefy = $dateDefy;

        return $this;
    }

    /**
     * Get dateDefy
     *
     * @return string 
     */
    public function getDateDefy()
    {
        return $this->dateDefy;
    }

    /**
     * Set dateTimer
     *
     * @param string $dateTimer
     * @return Twitch
     */
    public function setDateTimer($dateTimer)
    {
        $this->dateTimer = $dateTimer;

        return $this;
    }

    /**
     * Get dateTimer
     *
     * @return string 
     */
    public function getDateTimer()
    {
        return $this->dateTimer;
    }

    /**
     * Set isStarted
     *
     * @param integer $isStarted
     * @return Twitch
     */
    public function setIsStarted($isStarted)
    {
        $this->isStarted = $isStarted;

        return $this;
    }

    /**
     * Get isStarted
     *
     * @return integer 
     */
    public function getIsStarted()
    {
        return $this->isStarted;
    }
}
