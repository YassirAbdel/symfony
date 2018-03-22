<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;


class AdvertController extends Controller
{
  public function indexAction($page)
  {
    if ($page < 1) {
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }

    // Ici je fixe le nombre d'annonces par page à 3
    // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
    $nbPerPage = 3;

    // On récupère notre objet Paginator
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts) / $nbPerPage);

    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page,
    ));
  }

  public function viewAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    // Pour récupérer une seule annonce, on utilise la méthode find($id)
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id n'existe pas, d'où ce if :
    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // Récupération de la liste des candidatures de l'annonce
    $listApplications = $em
      ->getRepository('OCPlatformBundle:Application')
      ->findBy(array('advert' => $advert))
    ;

    // Récupération des AdvertSkill de l'annonce
    $listAdvertSkills = $em
      ->getRepository('OCPlatformBundle:AdvertSkill')
      ->findBy(array('advert' => $advert))
    ;

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert'           => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills,
    ));
  }

  public function addAction(Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    // On ne sait toujours pas gérer le formulaire, patience cela vient dans la prochaine partie !

    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:add.html.twig');
  }

  public function edit1Action($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // Ici encore, il faudra mettre la gestion du formulaire

    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert
    ));
  }

  public function deleteAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On boucle sur les catégories de l'annonce pour les supprimer
    foreach ($advert->getCategories() as $category) {
      $advert->removeCategory($category);
    }

    $em->flush();
    
    return $this->render('OCPlatformBundle:Advert:delete.html.twig');
  }

  public function menuAction($limit)
  {
    $em = $this->getDoctrine()->getManager();

    $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
      array(),                 // Pas de critère
      array('date' => 'desc'), // On trie par date décroissante
      $limit,                  // On sélectionne $limit annonces
      0                        // À partir du premier
    );

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }
  
  public function purgeAction()
  {
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
       // On récupère les annonces qui n'ont pas de candidatures
      ->getAdvertWithNoApplications(50)
    ;
    
    $em = $this->getDoctrine()->getManager();
    $response = new Response();
    
    
    // On vérifie si le tableau est vide. Si oui, on fait rien
    if (empty($listAdverts))
    {
        $response->setContent("Pas d'annonces à supprimer !");
    }
    // Si non, on supprime les annonces
    else
    {
    // On fait une boucle pour supprimer les ligne du tableau $listAdverts
    foreach ($listAdverts as $advert)
     {
        
         $query = $em->createQuery('DELETE OCPlatformBundle:Advert a WHERE a.id = :advertId');
         $query->setParameter('advertId', $advert->getId());
         $query->execute();
         
      }
      // On crée la réponse sans lui donner de contenu pour le moment
      $response->setContent("Annonces supprimées !");
    }
    // On retourne la réponse
    return $response;
  }
  
  public function purge1Action()
  { 
    // On récupère le service
    $purgeadvert = $this->container->get('oc_platform.purgeadvert');
    
    // On appele le fonction purgeAdvertWithNoApplications($day) et on récupère le code du traitement
    $code = $purgeadvert->purgeAdvertWithNoApplications(50);
    
    // Selon le code de traiment, on envoie un message
    if($code == '1')
    {
        $reponse = "Annonces supprimées";
    }
    elseif ($code == '0')
    {
        $reponse = "Pas d'annonces supprimées";
    }
    
    return new Response("<h1>$reponse</h1>") ;
   
  }
  
  public function add1Action(Request $request, $id)
  {
    // On crée un objet Advert
    $advert = new Advert();
    
    // Ici, on préremplit avec la date d'aujourd'hui, par exemple
    // Cette date sera donc préaffichée dans le formulaire, cela facilite le travail de l'utilisateur
    //$advert->setDate(new \Datetime());
    
    // Récupération d'une annonce déjà existante, d'id $id.
    $advert = $this->getDoctrine()
        ->getManager()
        ->getRepository('OCPlatformBundle:Advert')
        ->find($id)
;
   // On crée le FormBuilder grâce au service form factory
    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert);

    // On ajoute les champs de l'entité que l'on veut à notre formulaire
    $formBuilder
      ->add('date',      DateType::class)
      ->add('title',     TextType::class)
      ->add('content',   TextareaType::class)
      ->add('author',    TextType::class)
      ->add('published', CheckboxType::class,  array('required' => false))
      ->add('save',      SubmitType::class)
    ;
    // Pour l'instant, pas de candidatures, catégories, etc., on les gérera plus tard
    // À partir du formBuilder, on génère le formulaire
    $form = $formBuilder->getForm();

    // Si la requête est en POST
    if ($request->isMethod('POST')) {
      // On fait le lien Requête <-> Formulaire
      // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
      $form->handleRequest($request);
    
    // On vérifie que les valeurs entrées sont correctes
      // (Nous verrons la validation des objets en détail dans le prochain chapitre)
      if ($form->isValid()) {
        // On enregistre notre objet $advert dans la base de données, par exemple
        $em = $this->getDoctrine()->getManager();
        $em->persist($advert);
        $em->flush();
    
     $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      
     // On redirige vers la page de visualisation de l'annonce nouvellement créée
        return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
      }
    }
    
    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }
  
  public function add2Action(Request $request)
  {
    $advert = new Advert();
    $form   = $this->get('form.factory')->create(AdvertType::class, $advert);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }
  
  public function editAction(Request $request, $id)
  {
    // On crée un objet Advert
    $advert = new Advert();
    
    // Ici, on préremplit avec la date d'aujourd'hui, par exemple
    // Cette date sera donc préaffichée dans le formulaire, cela facilite le travail de l'utilisateur
    //$advert->setDate(new \Datetime());
    
    // Récupération d'une annonce déjà existante, d'id $id.
    $advert = $this->getDoctrine()
        ->getManager()
        ->getRepository('OCPlatformBundle:Advert')
        ->find($id)
;
   $form   = $this->get('form.factory')->create(AdvertEditType::class, $advert);

    // Si la requête est en POST
    if ($request->isMethod('POST')) {
      // On fait le lien Requête <-> Formulaire
      // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
      $form->handleRequest($request);
    
    // On vérifie que les valeurs entrées sont correctes
      // (Nous verrons la validation des objets en détail dans le prochain chapitre)
      if ($form->isValid()) {
        // On enregistre notre objet $advert dans la base de données, par exemple
        $em = $this->getDoctrine()->getManager();
        $em->persist($advert);
        $em->flush();
    
     $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      
     // On redirige vers la page de visualisation de l'annonce nouvellement créée
        return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
      }
    }
    
    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
  }
}

