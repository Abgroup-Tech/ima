<?php

namespace Kbh\GestionCongesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kbh\GestionCongesBundle\Entity\Salarie;

class LoadSalarieData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $salarie1 = new Salarie();
        $salarie1->setMatricule('1234578');
        $salarie1->setNumeroCnps('345KKLMDC');
        $salarie1->setCivilite('Mr');
        $salarie1->setNom('ABB');
        $salarie1->setPrenom('Jacques');
        $salarie1->setDateNaissance(new \Datetime("1980-05-20"));
        $salarie1->setStatutMarital('célibataire');
        $salarie1->setPoste('Directeur QSE');
        $salarie1->setEmail('abb.jacques@energiesa.ci');
        $salarie1->setTelephone('41780404');
        $salarie1->setDateEmbauche(new \Datetime("2012-05-01"));
        $salarie1->setTypeContratTravail('CDI');
        $salarie1->setUnite(4);
        $manager->persist($salarie1);
        
        $salarie2 = new Salarie();
        $salarie2->setMatricule('1234579');
        $salarie2->setNumeroCnps('345KKLYMDC');
        $salarie2->setCivilite('Mme');
        $salarie2->setNom('ABC');
        $salarie2->setPrenom('Julie');
        $salarie2->setDateNaissance(new \Datetime("1985-03-21"));
        $salarie2->setStatutMarital('marié(e)');
        $salarie2->setPoste('Directrice Administratif et Financier');
        $salarie2->setEmail('abc.julie@energiesa.ci');
        $salarie2->setTelephone('41780202');
        $salarie2->setDateEmbauche(new \Datetime("2012-01-05"));
        $salarie2->setTypeContratTravail('CDI');
        $salarie2->setUnite(2);
        $manager->persist($salarie2);
        
        
        $salarie3 = new Salarie();
        $salarie3->setMatricule('1234579');
        $salarie3->setNumeroCnps('345KKLYMDC');
        $salarie3->setCivilite('Mme');
        $salarie3->setNom('ABC');
        $salarie3->setPrenom('Julie');
        $salarie3->setDateNaissance(new \Datetime("1985-03-21"));
        $salarie3->setStatutMarital('marié(e)');
        $salarie3->setPoste('Directrice Administratif et Financier');
        $salarie3->setEmail('abc.julie@energiesa.ci');
        $salarie3->setTelephone('41780202');
        $salarie3->setDateEmbauche(new \Datetime("2012-01-05"));
        $salarie3->setTypeContratTravail('CDI');
        $salarie3->setUnite(2);
        $manager->persist($salarie3);
        
        $manager->flush();
    }
}
