<?php
// src/OC/PlatformBundle/Antispam/OCAntispam.php

namespace Gdev\IsaacBundle\ManageToken;

class GdevManageToken
{
  /**
   * Vérifie si le texte est un spam ou non
   *
   * @param string $text
   * @return bool
   */
  public function manageToken($text)
  {
    return strlen($text) < 50;
  }

  public function isConnected($user){
    if($user){
      return true;
    } else {
      throw new \Exception('Vous ne pouvez pas effectuer cette action !');
      return false;
    }
  }

  public function isConnectedNoError($user){
    if($user){
      return true;
    } else {
      return false;
    }
  }

  public function checkOwner($idProduct, $idUser){
    if($idProduct == $idUser){
      return true;
    } else {
      throw new \Exception('Vous ne pouvez pas effectuer cette action !');
      return false;
    }
  }

  public function checkOwnerNoError($idProduct, $idUser){
    if($idProduct == $idUser){
      return true;
    } else {
      return false;
    }
  }
}