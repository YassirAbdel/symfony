<?php

namespace OC\PlatformBundle\Purgeadvert;

class OCPurgeadvert
{
    /**
     * Purger les annonces qui n'ont pas de candidatures
     * 
     * @param init $day
     * @return bool = true si les annonces ont été purgées
     */
    private $manager;


    public function __construct(\Doctrine\ORM\EntityManager $manager) 
    {
        $this->manager = $manager;
    }
    
    public function purgeAdvertWithNoApplications($day)
    {
        
     $em = $this->manager; 
     $repo = $this->manager->getRepository(\OC\PlatformBundle\Entity\Advert::class);
     $listAdverts = $repo->getAdvertWithNoApplications($day);
     
     // On vérifie si le tableau est vide. Si oui, on ne fait rien
     if (empty($listAdverts))
     {
        // On crée un code de traitement 
        $code = "0";
        // On retourne le code
        return $code;
     }
     // Sinon, on supprime les annonces
     // On fait une boucle pour supprimer les ligne du tableau $listAdverts
     foreach ($listAdverts as $advert)
     {
         $query = $em->createQuery('DELETE OCPlatformBundle:Advert a WHERE a.id = :advertId');
         $query->setParameter('advertId', $advert->getId());
         $query->execute();
     }
    // On crée un code de traitement 
     $code = "1";
     // On retourne le code
     return $code;
    }  
}
