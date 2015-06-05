<?php

namespace Gdev\IsaacBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Gdev\IsaacBundle\Form\ItemRechercheType;

class DefaultController extends Controller
{

    public function homeAction()
    {
        return $this->render('GdevIsaacBundle:Default:index.html.twig', array(
          'name' => "vouvou"
        ));
    }

    public function contactAction()
    {
        return $this->render('GdevIsaacBundle:Default:contact.html.twig', array(
        ));
    }


    public function seeItemsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $listItems = $em->getRepository('GdevIsaacBundle:Item')->findAll();
        $form = $this->container->get('form.factory')->create(new ItemRechercheType());
        return $this->render('GdevIsaacBundle:Default:item.html.twig', array(
            'items' => $listItems,
            'form' => $form->createView()
        ));
    }

    public function achievementAction()
    {
        $playerId = $this->get('security.token_storage')->getToken()->getUser();
        if (strpos($playerId,'http://steamcommunity.com/openid/id/') !== false) {
          $playerId=str_replace("http://steamcommunity.com/openid/id/", "", $playerId);
        }

        else if (!$this->get('security.context')->isGranted('ROLE_USER')) {
          $finalArray = self::seeAchievementWithoutSteam();
          return $this->render('GdevIsaacBundle:Default:achievement.html.twig', array('finalArray'=>$finalArray));
        }

        else if($this->getUser()->getSteamId() !== ''){
           $playerId=$this->getUser()->getSteamId();
        }


        $appId = "XXXXXXXX"; //ISAAC
        $apiKey = "XXXXXXXXXXXXXXXXXXXXXXX";

        $globalArray;
        $url = "http://api.steampowered.com/ISteamUserStats/GetUserStatsForGame/v0002/?appid=".$appId."&key=".$apiKey."&steamid=".$playerId;
         // print_r($url);

        function get_http_response_code($url) {
          $headers = get_headers($url);
          return substr($headers[0], 9, 3);
        }

        if(get_http_response_code($url) != "200"){
          $finalArray = self::seeAchievementWithoutSteam();
          return $this->render('GdevIsaacBundle:Default:achievement.html.twig', array('finalArray'=>$finalArray));

        } else {
          $json_object= file_get_contents($url);
          $json_decoded = json_decode($json_object);
          foreach($json_decoded->playerstats->achievements as $key){
            $globalArray[$key->name-1] = $key->achieved;
          }

          $tempAchievementArray = array();

          $em = $this->getDoctrine()->getManager();
          $listAchievements = $em->getRepository('GdevIsaacBundle:Achievements')->findAll();
          $arraySize = sizeof($listAchievements);

         //     {{ dump(list[0].id) }}

          $numberOfCompleted = 0;
          $numberToCompleted = 0;

          for($i=0; $i<$arraySize; $i++){
            if(isset($globalArray[$i])){
              $tempAchievementArray = [
                "class" => "green",
                "icon" => $listAchievements[$i]->getImage(),
                "name" => $listAchievements[$i]->getName(),
                "description" => $listAchievements[$i]->getDescription(),
                "howto" => $listAchievements[$i]->getHowTo()
              ];
              $numberOfCompleted++;
            }

            else{
                $tempAchievementArray = [
                  "class" => "red",
                  "icon" => $listAchievements[$i]->getImage(),
                  "name" => $listAchievements[$i]->getName(),
                  "description" => $listAchievements[$i]->getDescription(),
                  "howto" => $listAchievements[$i]->getHowTo()
                ];
            }


            $numberToCompleted++;

            $finalArray[] = $tempAchievementArray;

          }

          $percentage = floor(($numberOfCompleted/$numberToCompleted)*100);

          return $this->render('GdevIsaacBundle:Default:achievement.html.twig', array('finalArray'=>$finalArray, 'percentage'=>$percentage));
        }
      // }
    }


    public function rechercherItemAction()
    {
        $request = $this->container->get('request');

        if($request->isXmlHttpRequest())
        {
            $motcle = '';
            $motcle = $request->request->get('motcle');

            $em = $this->container->get('doctrine')->getManager();

            if($motcle != '')
            {
                   $qb = $em->createQueryBuilder();

                   $qb->select('a')
                      ->from('GdevIsaacBundle:Item', 'a')
                      ->where("a.name LIKE :motcle")
                      ->orderBy('a.name', 'ASC')
                      ->setParameter('motcle', '%'.$motcle.'%');

                   $query = $qb->getQuery();
                   $item = $query->getResult();
            }
            else {
                $item = $em->getRepository('GdevIsaacBundle:Item')->findAll();
            }

            return $this->container->get('templating')->renderResponse('GdevIsaacBundle:Default:allSearchItem.html.twig', array(
                'items' => $item
                ));
        }
        else {
            return $this->seeItemsAction();
        }
    }

    public function seeAchievementWithoutSteam(){

      $tempAchievementArray = array();

          $em = $this->getDoctrine()->getManager();
          $listAchievements = $em->getRepository('GdevIsaacBundle:Achievements')->findAll();
          $arraySize = sizeof($listAchievements);

         //     {{ dump(list[0].id) }}

          $numberOfCompleted = 0;
          $numberToCompleted = 0;

          for($i=0; $i<$arraySize; $i++){
              $tempAchievementArray = [
                "class" => "grey",
                "icon" => $listAchievements[$i]->getImage(),
                "name" => $listAchievements[$i]->getName(),
                "description" => $listAchievements[$i]->getDescription(),
                "howto" => $listAchievements[$i]->getHowTo()
              ];
              $numberOfCompleted++;

            $numberToCompleted++;

            $finalArray[] = $tempAchievementArray;

          }
      return $finalArray;
    }
}
