<?php

namespace Gdev\IsaacBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ManualAchievements
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gdev\IsaacBundle\Entity\ManualAchievementsRepository")
 */
class ManualAchievements
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
     * @ORM\Column(name="playerId", type="string", length=255)
     */
    private $playerId;

    /**
     * @var string
     *
     * @ORM\Column(name="achievementId", type="string", length=255)
     */
    private $achievementId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="value", type="boolean")
     */
    private $value;


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
     * Set playerId
     *
     * @param string $playerId
     * @return ManualAchievements
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;

        return $this;
    }

    /**
     * Get playerId
     *
     * @return string 
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * Set achievementId
     *
     * @param string $achievementId
     * @return ManualAchievements
     */
    public function setAchievementId($achievementId)
    {
        $this->achievementId = $achievementId;

        return $this;
    }

    /**
     * Get achievementId
     *
     * @return string 
     */
    public function getAchievementId()
    {
        return $this->achievementId;
    }

    /**
     * Set value
     *
     * @param boolean $value
     * @return ManualAchievements
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return boolean 
     */
    public function getValue()
    {
        return $this->value;
    }
}
