<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NotificationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationRepository extends EntityRepository
{
      
    public function findNewNotificationsForSuperviseur($superviseur){
        $qb = $this->createQueryBuilder('n');

        $qb->where('n.valideurEnCours = :superviseur')
           ->setParameter('superviseur', $superviseur)
           ->orderBy('n.dateEnvoi', 'ASC');

        return $qb->getQuery()->getResult();
    }

     public function findNotificationsForSuperviseur($superviseur){
        $qb = $this->createQueryBuilder('n');

        $qb->where('n.salarie = :superviseur')
           ->setParameter('superviseur', $superviseur)
           ->setMaxResults(2)
           ->orderBy('n.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    }
    
     public function findDemandesAvalider($superviseur){
        $qb = $this->createQueryBuilder('n');

        $qb->where('n.valideurEnCours = :superviseur')
           ->andWhere('n.estTraitee = false')
           ->setParameter('superviseur', $superviseur)
           ->setMaxResults(2)
           ->orderBy('n.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    }

     public function findDemandesPrecedente($superviseur){
        $qb = $this->createQueryBuilder('n');

        $qb->where('n.valideurPrecedent = :superviseur')
           ->setParameter('superviseur', $superviseur)
           ->setMaxResults(2)
           ->orderBy('n.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findNewNotificationsForSalarie($salarie){
        $qb = $this->createQueryBuilder('n');

        $qb->where('n.salarie = :salarie')
            ->orWhere('n.valideurPrecedent = :salarie')  
            ->orWhere('n.valideurEnCours = :salarie')
            ->orWhere('n.observateur = :salarie')
	    ->orWhere('n.superieurN1 = :salarie')
           ->setParameter('salarie', $salarie) 
           ->setMaxResults(5)
           ->orderBy('n.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    }
    
   public function findAllNewNotificationsForSuperviseur($salarie){
       $qb = $this->createQueryBuilder('n');

        $qb->where('n.salarie = :salarie')
                ->orWhere('n.valideurPrecedent = :salarie')
                ->orWhere('n.observateur = :salarie')
                ->orWhere('n.superieurN1 = :salarie')
                ->orWhere('n.valideurEnCours = :salarie')
                ->orWhere('n.valideurSuivant = :salarie')
                ->setParameter('salarie', $salarie)
                ->orderBy('n.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    }
	
	
    public function findNewNotificationsNumber($salarie){
        $qb = $this->createQueryBuilder('n');

      $qb->where('n.salarie = :salarie')
         ->orWhere('n.valideurPrecedent = :salarie')  
         ->orWhere('n.valideurEnCours = :salarie')
         ->orWhere('n.observateur = :salarie')
         ->orWhere('n.superieurN1 = :salarie')
//         ->andWhere('n.vuParDemandeur = false')
//         ->orWhere('n.vuParDemandeur = true')      
//         ->orWhere('n.vuParValideurEnCours = false') 
//         ->orWhere('n.vuParValideurEnCours = true')      
//         ->orWhere('n.vuParValideurPrecedent = false')
//         ->orWhere('n.vuParValideurPrecedent = true')      
//         ->orWhere('n.vuParObservateur = false')
//         ->orWhere('n.vuParObservateur = true')     
//         ->orWhere('n.vuParSupN1 = false') 
//        ->orWhere('n.vuParSupN1 = true')       
         ->setParameter('salarie', $salarie)
         ->orderBy('n.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    } 
    
    public function findNotificationForAdmin($salarie){
       $qb = $this->createQueryBuilder('n');

        $qb->where('n.admin = :salarie')
                ->setParameter('salarie', $salarie)
                ->setMaxResults(5)
                ->orderBy('n.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    }
    
    public function findAllNotificationForAdmin($salarie){
       $qb = $this->createQueryBuilder('n');

        $qb->where('n.admin = :salarie')
                ->setParameter('salarie', $salarie)
                ->orderBy('n.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    }
    
        public function findAllNotificationForUser($salarie){
       $qb = $this->createQueryBuilder('n');

        $qb->where('n.salarie = :salarie')
                ->setParameter('salarie', $salarie)
                ->orderBy('n.dateEnvoi', 'DESC');

        return $qb->getQuery()->getResult();
    }
}