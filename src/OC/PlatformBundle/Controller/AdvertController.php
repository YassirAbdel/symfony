<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;


class AdvertController extends Controller
{
    public function indexAction()
    {
        // Pour tester : http://localhost/Symfony/web/app_dev.php/platform/
        
        //1. return new Response("Notre propre Hello Word !");
        /*
        $content = $this
            ->get('templating')
            ->render('OCPlatformBundle:Advert:index.html.twig', array('nom' => 'winzou'));
        
        return new Response($content);
        */
        
        //2. On veut avoir l'URL de l'annonce d'id 5.
        
        $url1= $this->get('router')->generate(
            'oc_platform_view', // 1er argument : le nom de la route
            array('id' => 5) // 2e argument : les des paramètres
                                             
        );
        
        // $url vaut « /platform/advet/5 »
        
        // l'URL absolue
        
        $url2 = $this->get('router')->generate(
            'oc_platform_home', // 1er argument : le nom de la route
            array(), // 2e argument : les des paramètres
            UrlGeneratorInterface::ABSOLUTE_URL
                                             
        );
        
        $url3 = $this->get('router')->generate(
            'oc_platform_home' // 1 seul argument Méthode longue
        );
        
        /*JE N'AI PAS COMPRIS L'ERREUR :
        Attempted to call an undefined method named "generateUrl" of class "Symfony\Bundle\FrameworkBundle\Routing\Router".
        Did you mean to call "generate"?
        */
        /*
        $url4 = $this->get('router')->generateUrl(
            'oc_platform_home' // 1 seul argument Méthode courte
        
        );
        */
        
        
        //return new Response("l'URL1 est : " . $url1 . "____" . " L'URL2 est : " . $url2 . "______ L'URL3 est : " . $url3 . "______ L'URL4 : ");
        //return $this->render('OCPlatformBundle:Advert:index.html.twig');
    $listAdverts = array(
      array(
        'title'   => 'Recherche développpeur Symfony',
        'id'      => 1,
        'author'  => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Mission de webmaster',
        'id'      => 2,
        'author'  => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Offre de stage webdesigner',
        'id'      => 3,
        'author'  => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date'    => new \Datetime())
    );

    // Et modifiez le 2nd argument pour injecter notre liste
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts
    ));
        
    }
    
   //public function viewAction($id, Request $request)
    
   public function viewAction($id)
    {
        
        // 1. test du parametre envoyé dans le routeur
        
        // http://localhost/Symfony/web/app_dev.php/platform/advert/5
        
        //return new Response("Affichage de l'annonce d'id : " . $id);
        // OU vers une vue
        /*
        $content = $this
            ->get('templating')
            ->render('OCPlatformBundle:Advert:annonce.html.twig', array('id' => $id));
        
        return new Response($content);
        */
        
        
        // 2. parametres GET
        
        // http://localhost/Symfony/web/app_dev.php/platform/advert/9?tag=vacances
        
        //$tag = $request->query->get('tag');
       
        /*
        return new Response(
            "Affichage de l'annonce d'id : " . $id . ", avec le tag : " . $tag
            );
        */
        // OU vers une vue
        /*
        return $this->get('templating')->renderResponse(
        'OCPlatformBundle:Advert:view.html.twig',
        array ('id' => $id, 'tag' => $tag)
        );
        */
        
        // 4. Recourci de la méthode renderResponse
        
        /*return $this->render('OCPlatformBundle:Advert:view.html.twig',
                             array(
                                'id' => $id,
                                'tag' => $tag
                             ));
        */
    
        // 3. Manipuler l'objet Response
        
        // http://localhost/Symfony/web/app_dev.php/platform/advert/5
        
        // On crée la réponse sans lui donner du contenu
        $response = new Response();
        
        // On définit le contenu
       $response->setContent("Ceci est une page d'erreur 404");
       
        
        // On définit le code HTTP à " Not Found " (erreur 404)
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        
        // On retourne le réponse
        
        // On envoie la réponse
        return $this->render('OCPlatformBundle:Advert:view.html.twig', array('erreur' => $response));
       //return $response;
        
        
    }
    
    public function viewSlugAction($year, $slug, $_format)
    {
        // http://localhost/Symfony/web/app_dev.php/platform/2011/webmaster.xml
        // http://localhost/Symfony/web/app_dev.php/platform/2011/webmaster.html
        
        return new Response("On pourrait afficher l'annonce correspondant au slug" . $slug . ", créé en " . $year . " et au format " . $_format . ".");
    }
    
    // La redirection
    public function redirectAction($id)
    {
        $url = $this->get('router')->generate('oc_platform_home');
        
        // syntaxe longue
        //return new RedirectResponse($url);
        
        // racourci 1
        //return $this->redirect($url);
        
        //recouci 2
        return $this->redirectToRoute('oc_platform_home');
    }
    
    // changer le content-type de la réponse
    public function jsonAction($id)
    {
        // on crée la réponse json grâce à la fonction json_encode()
        $response = new Response(json_encode(array('id' => $id)));
        
        // on définit le Content-type pour dire au navigateur que l'on renvoie du JSON et non du HTML
       
        // version longue
        /*
        $response->headers->set('Content-Type','application/json');
        
        return $response;
        */
        
        // version courte
        
        return new JsonResponse(array('id' => $id));
        
    }
    
    // Gérer les sessions
    public function sessionsAction($id, Request $request)
    {
        // Récupération de la session
        $session = $request->getSession();
        
        // Récupération du contenu de la variable user_id
        $userId = $session->get('users_id');
        
        // Définition d'une nouvelle valeur pour la vaiable user_id
        $session->set('user_id', $id);
        
        // Renvoie d'une réponse
        return $this->render('OCPlatformBundle:Advert:sessions.html.twig', array(
            'userId' => $userId
            )
       );
    }
    
    public function addAction(Request $request)
    {
        //$session = $request->getSession();
        
        // Bien sûr, cette méthode devra réellement ajouter l'annonce
    
        // Mais faisons comme si c'était le cas
        //$session->getFlashBag()->add('info', 'Annonce bien enregistrée');
        
        // Le " flashBag " est ce qui contient les message flash dans la session
        // Il peut contenir plusieurs messages
         //$session->getFlashBag()->add('info', 'Oui oui, elle est bien enregistrée !');
        
        // Redirection vers la page de visualisation de cette annonce.
        //return $this->redirectToRoute('oc_platform_view', array('id' => 5, 'tag' => 'Annonce'));
        
        // On récupère le service
        //$antispam = $this->container->get('oc_platform.antispam');

        // Je pars du principe que $text contient le texte d'un message quelconque
        //$text = '...';
        //if ($antispam->isSpam($text)) {
            //throw new \Exception('Votre message a été détecté comme spam !');
        //}
    
        // Ici le message n'est pas un spam
        
        // Création de l'entité Advert
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

        // Création de l'entité Image
        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        // On lie l'image à l'annonce
        $advert->setImage($image);

        // On récupère l'EntityManager
         $em = $this->getDoctrine()->getManager();

        // Étape 1 : On « persiste » l'entité
        $em->persist($advert);

        // Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
        // on devrait persister à la main l'entité $image
        // $em->persist($image);

        // Étape 2 : On déclenche l'enregistrement
        $em->flush();

    
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cettte annonce
            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

            // Si on n'est pas en POST, alors on affiche le formulaire
            return $this->render('OCPlatformBundle:Advert:add.html.twig', array('advert' => $advert));
  }
    
    
    public function menuAction($limit)
    {
        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche développeur Symfony'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage webdesigner')
        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      // Tout l'intérêt est ici : le contrôleur passe
      // les variables nécessaires au template !
        'listAdverts' => $listAdverts
        ));
    }
    
    // Test message flash
 public function addMessageFlashAction (Request $request) 
  {
    $session = $request->getSession();
    
    // Bien sûr, cette méthode devra réellement ajouter l'annonce
    
    // Mais faisons comme si c'était le cas
    $session->getFlashBag()->add('info', 'Annonce bien enregistrée');

    // Le « flashBag » est ce qui contient les messages flash dans la session
    // Il peut bien sûr contenir plusieurs messages :
    $session->getFlashBag()->add('info', 'Oui oui, elle est bien enregistrée !');

    // Puis on redirige vers la page de visualisation de cette annonce
    return $this->redirectToRoute('oc_platform_message_view', array('id' => 5));
  }
  
 public function viewMessageAction ($id)
  {
    return $this->render('OCPlatformBundle:Advert:messageFlash.html.twig',
    	array(
      		'id' => $id
    	));
  }

public function viewAnnonce()
  {
    $advert = new Advert;
    $advert->setContent("Recherche développeur Symfony3.");
	
    return $this->render('OCPlatformBundle:Advert:viewAnnonce.html.twig', array(
      'advert' => $advert
    ));
  }
 
}