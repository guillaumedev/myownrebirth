<?php

namespace Gdev\IsaacBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gdev\IsaacBundle\Entity\Defy;
use Gdev\IsaacBundle\Entity\DefyResponse;
use Symfony\Component\HttpFoundation\Request;
use Gdev\IsaacBundle\Form\DefyType;
use Gdev\IsaacBundle\Form\DefyResponseType;
use Gdev\IsaacBundle\Form\DefyRechercheType;


class DefyController extends Controller
{
    public function addDefyAction(Request $request)
    {
        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $tokenManager->isConnected($user);

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


        $defy = new Defy();

        $defy->setIdUser($idUser);
        $form = $this->get('form.factory')->create(new DefyType, $defy);

        // On fait le lien Requête <-> Formulaire
        // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
        $form->handleRequest($request);

        // On vérifie que les valeurs entrées sont correctes
        // (Nous verrons la validation des objets en détail dans le prochain chapitre)
        if ($form->isValid()) {
          // On l'enregistre notre objet $advert dans la base de données, par exemple
          $em = $this->getDoctrine()->getManager();

          $defy->upload();

          $em->persist($defy);
          $em->flush();


          // On redirige vers la page de visualisation de l'annonce nouvellement créée
          return $this->redirect($this->generateUrl('gdev_viewDefy', array('id' => $defy->getId())));
        }
        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        $em = $this->getDoctrine()->getManager();
        $generalPath = $defy->getWebPath();
        return $this->render('GdevIsaacBundle:Defy:addDefy.html.twig', array(
              'form' => $form->createView(),
              'generalPath'=>$generalPath
            ));
    }

    public function allDefyAction()
    {
        $repository = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Defy')
        ;

        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $authenticate = $tokenManager->isConnectedNoError($user);

        $listDefy = $repository->findAll();

        $form = $this->container->get('form.factory')->create(new DefyRechercheType());

        return $this->render('GdevIsaacBundle:Defy:allDefy.html.twig', array(
            'listDefy' => $listDefy,
            'authenticate' => $authenticate,
            'form' => $form->createView()
        ));

    }

    public function viewDefyAction(Request $request, $id)
    {
        // On récupère le repository
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Defy')
        ;

        // On récupère l'entité correspondante à l'id $id
        $defy = $repository->find($id);

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $defy) {
          throw new NotFoundHttpException("Le defi d'id ".$id." n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listDefyResponse = $em
          ->getRepository('GdevIsaacBundle:DefyResponse')
          ->findBy(array('defys' => $defy))
        ;

        $idUser = $this->get('security.token_storage')->getToken()->getUser();
        if (strpos($idUser,'http://steamcommunity.com/openid/id/') !== false) {
          $idUser=str_replace("http://steamcommunity.com/openid/id/", "", $idUser);
        } else if (!$this->get('security.context')->isGranted('ROLE_USER')) {
          // Le render ne change pas, on passait avant un tableau, maintenant un objet
          $generalPath = $defy->getWebPath();
          return $this->render('GdevIsaacBundle:Defy:viewDefy.html.twig', array(
            'defy' => $defy,
            'listDefyResponse'=>$listDefyResponse,
            'authenticate' => false,
            'generalPath'=>$generalPath,
            'owner'=>false
          ));
        } else if($this->getUser()->getId() !== ''){
           $idUser = $idUser->getId();
        }

        $defyResponse = new DefyResponse();

        $defyResponse->setIdUser($idUser);
        $defyResponse->setDefys($defy);
        $form = $this->get('form.factory')->create(new DefyResponseType, $defyResponse);
        $form->handleRequest($request);


        if ($form->isValid()) {
          $defyResponse->upload();
          $em = $this->getDoctrine()->getManager();
          $em->persist($defyResponse);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Commentaire bien enregistré.');

          // On redirige vers la page de visualisation de l'annonce nouvellement créée
          return $this->redirect($this->generateUrl('gdev_viewDefy', array('id' => $defy->getId())));
        }

        // Le render ne change pas, on passait avant un tableau, maintenant un objet
        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $owner = $tokenManager->checkOwnerNoError($defy->getIdUser(), $idUser);


        $generalPath = $defy->getWebPath();
        return $this->render('GdevIsaacBundle:Defy:viewDefy.html.twig', array(
          'defy' => $defy,
          'listDefyResponse'=>$listDefyResponse,
          'form' => $form->createView(),
          'generalPath'=>$generalPath,
          'owner'=>$owner,
          'authenticate'=>true
        ));
    }

    public function deleteDefyAction($id)
    {
        // On récupère le service
        $tokenManager = $this->container->get('gdev_isaac.ManageToken');
        $user = $this->get('security.context')->isGranted('ROLE_USER');
        $tokenManager->isConnected($user);

        $repository = $this->getDoctrine()
          ->getManager()
          ->getRepository('GdevIsaacBundle:Defy')
        ;

        // On récupère l'entité correspondante à l'id $id
        $defy = $repository->find($id);

        $idUser = $this->get('security.token_storage')->getToken()->getUser();
        if (strpos($idUser,'http://steamcommunity.com/openid/id/') !== false) {
          $idUser=str_replace("http://steamcommunity.com/openid/id/", "", $idUser);
        } else if($this->getUser()->getId() !== ''){
           $idUser = $idUser->getId();
        }

        $tokenManager->checkOwner($defy->getIdUser(), $idUser);

        $em = $this->getDoctrine()->getManager();

        $listDefyResponse = $em
          ->getRepository('GdevIsaacBundle:DefyResponse')
          ->findBy(array('defys' => $defy))
        ;

        foreach ($listDefyResponse as $response){
            $em->remove($response);
            $em->flush();
        }

        $em->remove($defy);
        $em->flush();

        if (null === $defy) {
          throw new NotFoundHttpException("Le defi d'id ".$id." n'existe pas.");
        }

        return $this->redirect($this->generateUrl('gdev_seeAllDefy', array('id' => $defy->getId())));

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
                      ->from('GdevIsaacBundle:Defy', 'a')
                      ->where("a.seed LIKE :motcle")
                      ->orderBy('a.seed', 'ASC')
                      ->setParameter('motcle', '%'.$motcle.'%');

                   $query = $qb->getQuery();
                   $defy = $query->getResult();
            }
            else {
                $defy = $em->getRepository('GdevIsaacBundle:Defy')->findAll();
            }

            return $this->container->get('templating')->renderResponse('GdevIsaacBundle:Defy:allSearchDefy.html.twig', array(
                'listDefy' => $defy
                ));
        }
        else {
            return $this->allDefyAction();
        }
    }

}