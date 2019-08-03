<?php

namespace Kbh\GestionCongesBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kbh\GestionCongesBundle\Entity\Demande;
use Kbh\GestionCongesBundle\Entity\Conge;
use Kbh\GestionCongesBundle\Entity\Absence;
use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Entity\Droits;
use Kbh\GestionCongesBundle\Entity\HistoriqueDroits;

/**
 * HistoriqueDroits controller.
 *
 */
class HistoriqueDroitsController extends Controller
{

    /**
     * Lists all HistoriqueDroits entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findAll();

        $Conges = $em->getRepository('KbhGestionCongesBundle:Conge')->findAll();
        $Absences = $em->getRepository('KbhGestionCongesBundle:Absence')->findAll();

        foreach ($Conges as $Conge) {
        	/*if(isset($Conge)){*/
        		$HistoriqueDroits = new HistoriqueDroits;
        		$HistoriqueDroits->setSalarie($Conge->getSalarie());
        		$HistoriqueDroits->setDemande($Conge->getDemande());
        		$HistoriqueDroits->setDroits($Conge->getSalarie()->getDroits());
        		$HistoriqueDroits->setConge($Conge->getId());
        		$HistoriqueDroits->setDateModification(new \DateTime());
        	/*}*/
            $em->persist($HistoriqueDroits);
                $em->flush();
        }

        foreach ($Absences as $Absence) {
        	/*if(isset($Absence)){*/
        		$HistoriqueDroits = new HistoriqueDroits;
        		$HistoriqueDroits->setSalarie($Absence->getSalarie());
        		$HistoriqueDroits->setDemande($Absence->getDemande());
        		$HistoriqueDroits->setDroits($Absence->getSalarie()->getDroits());
        		$HistoriqueDroits->setAbsence($Absence->getId());
        		$HistoriqueDroits->setDateModification(new \DateTime());
        	/*}*/
            $em->persist($HistoriqueDroits);
                $em->flush();
        }



        return $this->redirect($this->generateUrl('welcome'));
    }

    /**
     * Finds and displays a HistoriqueDroits entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HistoriqueDroits entity.');
        }

        return $this->render('KbhGestionCongesBundle:HistoriqueDroits:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
 
    /**
     * Finds and displays a HistoriqueDroits entity.
     *
     */ 
        public function showLogDroitAction()
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findBySalarie($salarie);
        $logAnnuel = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->findBySalarie($salarie);
        $logMensuel = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->findBySalarie($salarie);

        return $this->render('KbhGestionCongesBundle:Salarie\HistoriqueDroits:show.html.twig', array(
            'entities'      => $entity,
            'LogAnnuel' => $logAnnuel,
            'LogMensuel' => $logMensuel,
        ));
    }
    
     /**
     * Finds and displays a HistoriqueDroits entity.
     *
     */ 
        public function showSupLogDroitAction()
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findBySalarie($salarie);
        $logAnnuel = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->findBySalarie($salarie);
        $logMensuel = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->findBySalarie($salarie);
        
        
         $user = $this->getUser();
         if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
                    return $this->render('KbhGestionCongesBundle:Superviseur\HistoriqueDroits:show.html.twig', array(
                        'entities'      => $entity,
                        'LogAnnuel' => $logAnnuel,
                        'LogMensuel' => $logMensuel,
                    ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                    return $this->render('KbhGestionCongesBundle:Top-manager\HistoriqueDroits:show.html.twig', array(
                        'entities'      => $entity,
                        'LogAnnuel' => $logAnnuel,
                        'LogMensuel' => $logMensuel,
                    ));
        }
        
    }
    
      public function getSalarieByUser(){
        $user = $this->container->get('security.context')->getToken()->getUser();
        if(!$user){
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($user);
        
        if (!$salarie) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $salarie;
    }
}
