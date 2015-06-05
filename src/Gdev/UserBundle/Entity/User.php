<?php

namespace Gdev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Fp\OpenIdBundle\Model\UserIdentityInterface;



/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gdev\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="steamId", type="text", length=255)
     */
  protected $steamId;

    /**
     * @var string
     *
     * @ORM\Column(name="openIdToken", type="text", length=255, nullable=true)
     */
  protected $openIdToken;


  public function getSteamId(){
  	return $this->steamId;
  }

  public function setSteamId($steamId){
  	$this->steamId = $steamId;
  	return true;
  }

  public function getOpenIdToken(){
    return $this->openIdToken;
  }

  public function setOpenIdToken($openIdToken){
    $this->openIdToken = $openIdToken;
    return true;
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

  public function __construct()
  {
        parent::__construct();
        // your own logic (nothing for this example)
  }
}
