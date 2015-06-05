<?php

namespace Gdev\IsaacBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gdev\IsaacBundle\Entity\Seeds;
use Gdev\IsaacBundle\Entity\SeedResponse;
use Gdev\IsaacBundle\Entity\ItemPosition;
use Symfony\Component\HttpFoundation\Request;
use Gdev\IsaacBundle\Form\SeedsType;
use Gdev\IsaacBundle\Form\SeedResponseType;
use Gdev\IsaacBundle\Form\SeedRechercheType;


class SeedsController extends Controller
{
    public function addAction(Request $request)
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


        $seeds = new Seeds();

        $seeds->setIdUser($idUser);
        $form = $this->get('form.factory')->create(new SeedsType, $seeds);

        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
          // On l'enregistre notre objet $advert dans la base de données, par exemple
          $em = $this->getDoctrine()->getManager();
          $em->persist($seeds);
          $em->flush();

          $tabFloor;
          foreach($_POST as $item){
            if(!is_array($item)){
              if(!\is_numeric($item)){
                $tabFloor[]=$item;
              }
            }
          }

          $tabDefaultFloor=['Basement 1', 'Basement 1','Basement 2','Basement 2', 'Caves 1', 'Caves 1', 'Caves 2', 'Caves 2', 'Depths 1', 'Depths 1', 'Depths 2', 'Depths 2', 'Womb 1', 'Womb 1', 'Womb2', 'Womb2', 'Dark 1', 'Dark 1', 'Dark 2', 'Dark 2'];

          $i=0;
          $passed=false;
          foreach($_POST as $item){
            if(!is_array($item)){
              if(\is_numeric($item)){
                $floor = $item;
                $item = $em->getRepository('GdevIsaacBundle:Item')->findOneBy(
                  array('id' => $item)
                );

                $itemPosition = new ItemPosition();

                // On la lie à l'annonce, qui est ici toujours la même
                $itemPosition->setSeeds($seeds);
                // On la lie à la compétence, qui change ici dans la boucle foreach

                $itemPosition->setItem($item);

                $itemPosition->setNameItem($item->getName());

                // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
                if(isset($tabFloor[$i])){
                  $itemPosition->setFloor($tabFloor[$i]);
                } else {
                   $itemPosition->setFloor($tabDefaultFloor[$i]);
                }


                $em->persist($itemPosition);

                $passed=true;
              } elseif($passed) {
                $i++;
              }
            }
          }
          $em->flush();


          // On redirige vers la page de visualisation de l'annonce nouvellement créée
          return $this->redirect($this->generateUrl('gdev_viewSeed', array('id' => $seeds->getId())));
        }

        $em = $this->getDoctrine()->getManager();



        $tabFloor[0]=['Basement 1', 'Cellar 1'];
        $tabFloor[1]=['Basement 2', 'Cellar 2'];
        $tabFloor[2]=['Caves 1', 'Catacombs 1'];
        $tabFloor[3]=['Caves 2', 'Catacombs 2'];
        $tabFloor[4]=['Depths 1', 'Necropolis 1'];
        $tabFloor[5]=['Depths 2', 'Necropolis 2'];
        $tabFloor[6]=['Womb 1', 'Utero 1'];
        $tabFloor[7]=['Womb 2', 'Utero 2'];
        $tabFloor[8]=['Cathedral 1', 'Sheol 1'];
        $tabFloor[9]=['Chest 2', 'Dark 2'];

        $listItems = $em->getRepository('GdevIsaacBundle:Item')->findAll();
        return $this->render('GdevIsaacBundle:Seeds:addSeeds.html.twig', array(
              'form' => $form->createView(),
              'listItems'=>$listItems,
              'tabFloor'=>$tabFloor
            ));
    }

    public function allSeedsAction()
    {
        $repository = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Seeds')
        ;


        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $authenticate = $tokenManager->isConnectedNoError($user);

        $listSeeds = $repository->findAll();

        $form = $this->container->get('form.factory')->create(new SeedRechercheType());

        return $this->render('GdevIsaacBundle:Seeds:allSeeds.html.twig', array(
        // Tout l'intérêt est ici : le contrôleur passe
        // les variables nécessaires au template !
            'listSeeds' => $listSeeds,
            'authenticate'=>$authenticate,
            'form' => $form->createView()
        ));

    }

    public function viewSeedAction(Request $request, $id)
    {
        // On récupère le repository
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Seeds')
        ;

        // On récupère l'entité correspondante à l'id $id
        $seeds = $repository->find($id);

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $seeds) {
          throw new NotFoundHttpException("La seed d'id ".$id." n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listSeedResponse = $em
          ->getRepository('GdevIsaacBundle:SeedResponse')
          ->findBy(array('seeds' => $seeds))
        ;

        $listItems = $em
          ->getRepository('GdevIsaacBundle:ItemPosition')
          ->findBy(array('seeds' => $seeds))
        ;


        $idUser = $this->get('security.token_storage')->getToken()->getUser();
        if (strpos($idUser,'http://steamcommunity.com/openid/id/') !== false) {
          $idUser=str_replace("http://steamcommunity.com/openid/id/", "", $idUser);
        } else if (!$this->get('security.context')->isGranted('ROLE_USER')) {
          // Le render ne change pas, on passait avant un tableau, maintenant un objet
          return $this->render('GdevIsaacBundle:Seeds:viewSeed.html.twig', array(
          'seed' => $seeds,
          'listSeedResponse'=>$listSeedResponse,
          'authenticate'=>false,
          'listItems' => $listItems,
          'owner'=>false
        ));
        } else if($this->getUser()->getId() !== ''){
           $idUser = $idUser->getId();
        }

        $seedResponse = new SeedResponse();

        $seedResponse->setIdUser($idUser);
        $seedResponse->setSeeds($seeds);
        $form = $this->get('form.factory')->create(new SeedResponseType, $seedResponse);


        // On récupère maintenant la liste des AdvertSkill

        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
          // On l'enregistre notre objet $advert dans la base de données, par exemple
          $em = $this->getDoctrine()->getManager();
          $em->persist($seedResponse);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Commentaire bien enregistré.');

          // On redirige vers la page de visualisation de l'annonce nouvellement créée
          return $this->redirect($this->generateUrl('gdev_viewSeed', array('id' => $seeds->getId())));
        }


        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $owner = $tokenManager->checkOwnerNoError($seeds->getIdUser(), $idUser);

        // Le render ne change pas, on passait avant un tableau, maintenant un objet
        return $this->render('GdevIsaacBundle:Seeds:viewSeed.html.twig', array(
          'seed' => $seeds,
          'listSeedResponse'=>$listSeedResponse,
          'form' => $form->createView(),
          'listItems' => $listItems,
          'authenticate'=>true,
          'owner'=>$owner
        ));
    }

    public function deleteSeedAction($id)
    {
        // On récupère le service
        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $tokenManager->isConnected($user);

        $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Seeds')
        ;


        // On récupère l'entité correspondante à l'id $id
        $seed = $repository->find($id);

        $idUser = $this->get('security.token_storage')->getToken()->getUser();
        if (strpos($idUser,'http://steamcommunity.com/openid/id/') !== false) {
          $idUser=str_replace("http://steamcommunity.com/openid/id/", "", $idUser);
        } else if (!$this->get('security.context')->isGranted('ROLE_USER')) {
        // Sinon on déclenche une exception « Accès interdit »
          //throw new AccessDeniedException('Accès limité aux utilisateurs connectés.');
          return $this->render('GdevIsaacBundle:Default:index.html.twig', array(
            'name' => "vouvou"
          ));
        } else if($this->getUser()->getId() !== ''){
           $idUser = $idUser->getId();
        }

        $tokenManager->checkOwner($seed->getIdUser(), $idUser);

        $em = $this->getDoctrine()->getManager();

        $listSeedResponse = $em
          ->getRepository('GdevIsaacBundle:SeedResponse')
          ->findBy(array('seeds' => $seed))
        ;

        $listItemPosition = $em
          ->getRepository('GdevIsaacBundle:ItemPosition')
          ->findBy(array('seeds' => $seed))
        ;

        foreach ($listSeedResponse as $response){
            $em->remove($response);
            $em->flush();
        }

        foreach ($listItemPosition as $itemPosition){
            $em->remove($itemPosition);
            $em->flush();
        }

        $em->remove($seed);
        $em->flush();

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $seed) {
          throw new NotFoundHttpException("La seed d'id ".$id." n'existe pas.");
        }

        return $this->redirect($this->generateUrl('gdev_seeAllSeeds', array('id' => $seed->getId())));

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
                      ->from('GdevIsaacBundle:Seeds', 'a')
                      ->where("a.code LIKE :motcle")
                      ->orderBy('a.code', 'ASC')
                      ->setParameter('motcle', '%'.$motcle.'%');

                   $query = $qb->getQuery();
                   $seeds = $query->getResult();
            }
            else {
                $seeds = $em->getRepository('GdevIsaacBundle:Seeds')->findAll();
            }

            return $this->container->get('templating')->renderResponse('GdevIsaacBundle:Seeds:allSearchSeeds.html.twig', array(
                'listSeeds' => $seeds
                ));
        }
        else {
            return $this->allSeedsAction();
        }
    }

}