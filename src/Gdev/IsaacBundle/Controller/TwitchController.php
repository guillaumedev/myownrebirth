<?php

namespace Gdev\IsaacBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gdev\IsaacBundle\Entity\Twitch;
use Symfony\Component\HttpFoundation\Request;
use Gdev\IsaacBundle\Form\TwitchType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Gdev\IsaacBundle\Form\TwitchRechercheType;



class TwitchController extends Controller
{
    public function addTwitchAction(Request $request)
    {
        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $tokenManager->isConnected($user);

        $idUser = $this->get('security.token_storage')->getToken()->getUser();
        if (strpos($idUser,'http://steamcommunity.com/openid/id/') !== false) {
          $idUser=str_replace("http://steamcommunity.com/openid/id/", "", $idUser);
        }
        else if($this->getUser()->getId() !== ''){
           $idUser = $idUser->getId();
        }


        $twitch = new Twitch();

        $twitch->setAuthor($idUser);
        $form = $this->get('form.factory')->create(new TwitchType, $twitch);

        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
          // On l'enregistre notre objet $advert dans la base de données, par exemple
          $em = $this->getDoctrine()->getManager();
          $em->persist($twitch);
          $em->flush();


          // On redirige vers la page de visualisation de l'annonce nouvellement créée
          return $this->redirect($this->generateUrl('gdev_viewTwitch', array('id' => $twitch->getId())));
        }
       // print_r($_POST);
        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        $em = $this->getDoctrine()->getManager();
        return $this->render('GdevIsaacBundle:Twitch:addTwitch.html.twig', array(
              'form' => $form->createView()
            ));
    }

    public function allTWitchAction()
    {
        $repository = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Twitch')
        ;

        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $authenticate = $tokenManager->isConnectedNoError($user);

        $listTwitch = $repository->findAll();

        $form = $this->container->get('form.factory')->create(new TwitchRechercheType());

        return $this->render('GdevIsaacBundle:Twitch:allTwitch.html.twig', array(
        // Tout l'intérêt est ici : le contrôleur passe
        // les variables nécessaires au template !
            'listTwitch' => $listTwitch,
            'authenticate' => $authenticate,
            'form' => $form->createView()
        ));

    }

    public function viewTwitchAction(Request $request, $id)
    {


        // On récupère le repository
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Twitch')
        ;

        // On récupère l'entité correspondante à l'id $id
        $twitch = $repository->find($id);

        $idUser = $this->get('security.token_storage')->getToken()->getUser();
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
          if (strpos($idUser,'http://steamcommunity.com/openid/id/') !== false) {
            $idUser=str_replace("http://steamcommunity.com/openid/id/", "", $idUser);
          }
          else if($this->getUser()->getId() !== ''){
            $idUser = $idUser->getId();
          } else {
             return $this->render('GdevIsaacBundle:Twitch:viewTwitch.html.twig', array(
              'twitch' => $twitch,
              'authorize' => false
            ));
          }
        } else {
           return $this->render('GdevIsaacBundle:Twitch:viewTwitch.html.twig', array(
            'twitch' => $twitch,
            'authorize' => false
          ));
        }

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $twitch) {
          throw new NotFoundHttpException("Le live d'id ".$id." n'existe pas.");
        }


        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $authorize = $tokenManager->checkOwnerNoError($twitch->getAuthor(), $idUser);

        return $this->render('GdevIsaacBundle:Twitch:viewTwitch.html.twig', array(
          'twitch' => $twitch,
          'authorize' => $authorize
        ));
    }

    public function deleteTwitchAction($id)
    {
        // On récupère le service
        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $tokenManager->isConnected($user);

        $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Twitch')
        ;

        // On récupère l'entité correspondante à l'id $id
        $twitch = $repository->find($id);

        $idUser = $this->get('security.token_storage')->getToken()->getUser();
        if (strpos($idUser,'http://steamcommunity.com/openid/id/') !== false) {
          $idUser=str_replace("http://steamcommunity.com/openid/id/", "", $idUser);
        } else if($this->getUser()->getId() !== ''){
           $idUser = $idUser->getId();
        }

        $tokenManager->checkOwner($twitch->getAuthor(), $idUser);

        $em = $this->getDoctrine()->getManager();

        $em->remove($twitch);
        $em->flush();

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $twitch) {
          throw new NotFoundHttpException("Le live d'id ".$id." n'existe pas.");
        }

        return $this->redirect($this->generateUrl('gdev_seeAllTwitch', array('id' => $twitch->getId())));

    }

    public function launchTimerAction()
    {
        $id = '';
        $request = $this->container->get('request');
        $id = $request->request->get('id');
        $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Twitch')
        ;

        // On récupère l'entité correspondante à l'id $id
        $twitch = $repository->find($id);
        $date = new \Datetime();
        $twitch->setDateTimer($date);
        $twitch->setIsStarted(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($twitch);
        $em->flush();
        return new Response('Ok');
    }

    public function getDateLaunchedAction()
    {
        $request = $this->container->get('request');
        $id = $request->request->get('id');
        $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Twitch')
        ;

        // On récupère l'entité correspondante à l'id $id
        $twitch = $repository->find($id);
        $date = $twitch->getDateTimer();

        if(is_null($date)){
          return new Response('null');
        } else {
          $date = $date->format('Y-m-d H:i:s');
          return new Response($date);
        }
    }

    public function rechercherAction()
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
                      ->from('GdevIsaacBundle:Twitch', 'a')
                      ->where("a.seed LIKE :motcle OR a.firstPlayer LIKE :motcle OR a.secondPlayer LIKE :motcle")
                      ->orderBy('a.seed', 'ASC')
                      ->setParameter('motcle', '%'.$motcle.'%');

                   $query = $qb->getQuery();
                   $twitch = $query->getResult();
            }
            else {
                $twitch = $em->getRepository('GdevIsaacBundle:Twitch')->findAll();
            }

            return $this->container->get('templating')->renderResponse('GdevIsaacBundle:Twitch:allSearchTwitch.html.twig', array(
                'listTwitch' => $twitch
                ));
        }
        else {
            return $this->allSeedsAction();
        }
    }

}