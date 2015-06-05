<?php

namespace Gdev\IsaacBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ItemPosition
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gdev\IsaacBundle\Entity\ItemPositionRepository")
 */
class ItemPosition
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="floor", type="string", length=255)
     */
    private $floor;


    /**
     * @var string
     *
     * @ORM\Column(name="nameItem", type="string", length=255)
     */
    private $nameItem;

    /**
     * @ORM\ManyToOne(targetEntity="Gdev\IsaacBundle\Entity\Seeds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $seeds;

    /**
     * @ORM\ManyToOne(targetEntity="Gdev\IsaacBundle\Entity\Item")
     * @ORM\JoinColumn(nullable=false)
     */
    private $items;

    public function __construct()
    {
        $this->date = new \Datetime();
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
     * @return ItemPosition
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
     * Set floor
     *
     * @param string $floor
     * @return ItemPosition
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;

        return $this;
    }

    /**
     * Get floor
     *
     * @return string 
     */
    public function getFloor()
    {
        return $this->floor;
    }


    public function setSeeds(Seeds $seeds)
    {
        $this->seeds = $seeds;

        return $this;
    }

    public function getSeeds()
    {
        return $this->seeds;
    }

    public function setItem(Item $items)
    {
        $this->items = $items;

        return $this;
    }

    public function getItem()
    {
        return $this->items;
    }

  /**
     * Set floor
     *
     * @param string $floor
     * @return ItemPosition
     */
    public function setNameItem($nameItem)
    {
        $this->nameItem = $nameItem;

        return $this;
    }

    /**
     * Get floor
     *
     * @return string 
     */
    public function getNameItem()
    {
        return $this->nameItem;
    }

}
