<?php

namespace OC\HomepageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OCHomepageBundle::layout.html.twig');
    }
    
    public function lastAdvertsAction($limit)
    {
        $listAdverts = array(
            array('id' => 1, 'title' => 'Offre emploi chez Openclassroom'),
            array('id' => 2, 'title' => 'Offre de stage chez CADIC'),
            array('id' => 3, 'title' => 'Mission chez le client : profil symfony')
        );
        
        return $this->render('OCHomepageBundle:Default:index.html.twig', array('listAdverts' => $listAdverts));
    }
    
    public function formcontactAction()
    {
    	$mess = 'La page de contact n’est pas encore disponible';
    	
    	return $this->redirectToRoute('oc_url_redirect', 
    		array(
    			'mess' => $mess
    		));
    	
    }
    
   public function redirectAction(Request $request)
   { 
       
    	$mess = $request->query->get('mess');
    	
    	return $this->render('OCHomepageBundle::layout.html.twig', array('erreur' => $mess));
    	
    }
  
  public function menuAction()
  {
   	// Pas encore de BD
   	$listPages = array(
            array('id' => 0, 'title' => 'Accueil', 'url' => 'oc_core_home'),
            array('id' => 1, 'title' => 'Contact', 'url' => 'oc_core_contact'),
            array('id' => 2, 'title' => 'Les annonces', 'url' => 'oc_platform_home')
        );
  	return $this->render('OCHomepageBundle:Default:menu.html.twig', array('listPages' => $listPages));
  }
}
