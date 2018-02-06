<?php
// src/OC/PlatformBundle/Controller/DoctrineController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DoctrineController extends Controller
{
  public function viewAction()
  {
    $advert = new Advert;
    $advert->setContent("Recherche développeur Symfony3.");
	
    return $this->render('OCPlatformBundle:Advert:viewDoctrine.html.twig', array(
      'advert' => $advert
    ));
  }
}