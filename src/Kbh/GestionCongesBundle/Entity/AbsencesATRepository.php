<?php

namespace Kbh\GestionCongesBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AbsencesATRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AbsencesATRepository extends EntityRepository
{
    public function findAllAbsences(){
       $qb = $this->createQueryBuilder('n');

        $qb->setMaxResults(5)
              ->orderBy('n.dateCreation', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
