<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DemandeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DemandeRepository extends EntityRepository
{
    
    public function findDemandesAvalider($superviseurConnecte){
        $qb = $this->createQueryBuilder('d');

        $qb->where('d.valideurEnCours = :superviseur')
           ->setParameter('superviseur', $superviseurConnecte)
               ->andWhere('d.estCloture = 0') 
           ->orderBy('d.dateDemande', 'DESC');


        return $qb->getQuery()->getResult();
    }
    
    public function findDemandesPrecedente($superviseurConnecte){
        $qb = $this->createQueryBuilder('d');

        $qb->where('d.valideurPrecedent = :superviseur')
           ->setParameter('superviseur', $superviseurConnecte)
           ->orderBy('d.dateDemande', 'DESC');


        return $qb->getQuery()->getResult();
    }

    public function findAllDemandes($superviseurConnecte){
        $qb = $this->createQueryBuilder('d');
        
        $qb->where('d.valideurEnCours = :superviseur')
           ->orwhere('d.valideurNiveau1 = :superviseur')     
           ->setParameter('superviseur', $superviseurConnecte)     
           ->orderBy('d.dateDemande', 'DESC');

        return $qb->getQuery()->getResult();
    }
	
    public function findAllDemandesCollaborateur($superviseurConnecte,$collaborateur){
        $qb = $this->createQueryBuilder('d');

        $qb->where('d.valideurEnCours = :superviseur')
           ->andWhere('d.salarie =: salarie')
           ->setParameter('superviseur', $superviseurConnecte)
           ->setParameter('salarie', $collaborateur)     
           ->orderBy('d.dateDemande', 'DESC');


        return $qb->getQuery()->getResult();
    }
	
	public function findDemandesTraitees($superviseurConnecte){
        $qb = $this->createQueryBuilder('d');
        
        $qb->where('d.valideurNiveau1 = :superviseur')
		   ->orwhere('d.valideurNiveau2 = :superviseur')
		   ->orwhere('d.valideurFinal = :superviseur')
           ->setParameter('superviseur', $superviseurConnecte)     
           ->orderBy('d.dateDemande', 'DESC');

        return $qb->getQuery()->getResult();
    }
}