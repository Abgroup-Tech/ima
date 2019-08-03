<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\Absence;
use Kbh\GestionCongesBundle\Form\AbsenceType;
use Kbh\GestionCongesBundle\Form\Absence2Type;
use Kbh\GestionCongesBundle\Form\Absence3Type;

/**
 * Absence controller.
 *
 */
class AbsenceController extends Controller
{

    /**************************** ADMINISTRATEUR *******************************/
    
    /**
     * Lists all Absence entities.
     *
     */
    public function indexAction()
    {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Absence')->findAll();

        return $this->render('KbhGestionCongesBundle:Absence:index.html.twig', array(
            'entities' => $entities,
            'salarie' => $salarie
        ));
    }
   
   
   
    /**************************** SUPERVISEUR *******************************/
    /**
     * Liste les absences des supervisés.
     *
     */
    public function absencesCollabsAction($salarie_id)
    {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Absence')->findAll();

        return $this->render('KbhGestionCongesBundle:Superviseur\Supervises\Absence:index.html.twig', array(
            'entities' => $entities,
            'salarie' => $salarie
        ));
    }
    
    /**
     * Liste les absences d'un supervisé.
     *
     */
    public function absencesCollabAction($salarie_id)
    {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Absence')->findBySalarie($salarie_id);
        $collaborateur = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($salarie_id);
        
        return $this->render('KbhGestionCongesBundle:Superviseur\Absence:show.html.twig', array(
            'entity'             => $entity,
            'collaborateur'      => $collaborateur,           
        
        ));
    }

    /**************************** SALARIE *******************************/
    
    /**
     * Liste des absences du salarié
     *
     */
    public function absListAction()
    {
       
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();
        
        $em = $this->getDoctrine()->getManager();
        $absences_list = $em->getRepository('KbhGestionCongesBundle:Absence')->findBySalarie($salarie); 

        if (!$absences_list) {
            throw $this->createNotFoundException('Vous n\'avez aucune absence.');
        }

        return $this->render('KbhGestionCongesBundle:Salarie\Absence:absences_list.html.twig', array(
            'entities'      => $absences_list,
            'salarie'       => $salarie,
            'droits'        => $droits)
                
        );
    }
    
    
        /**
     * Trouve et affiche toutes les demandes 
     * des collaborateurs.
     *
     */
    public function historiqueAbsencesAction()
    {
		
        $superviseur = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();


        $validees = $em->getRepository('KbhGestionCongesBundle:Demande')->findByEstValide(1);
        $refusees = $em->getRepository('KbhGestionCongesBundle:Demande')->findByEstRefuse(1);

        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:historiques-absences.html.twig', array(
                'salarie' => $superviseur,
                'valides' => $validees,
                'refuses' => $refusees,
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:historiques-absences.html.twig', array(
                'salarie' => $superviseur,
                'valides' => $validees,
                'refuses' => $refusees,
            ));
        }
    }  
    
    
    
    /**
     * Creates a new Absence entity.
     *
     */
    public function absCreateAction(Request $request)
    {
        $entity = new Absence();
        $form = $this->createAbsCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $salarie = $this->getSalarieByUser();
            $entity->setSalarie($salarie);
            // Notifications
            $superviseur = $salarie->getValideur()->getSalarie();
            $notification = new Notification();
            $notification->setSuperviseur($superviseur);
            $notification->setSalarie($salarie);
            $notification->setMessage("Demande d'absence n°".$entity->getId()." créée avec succès");
            $notification->setAbsence($entity);
            
            $em->persist($notification);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sa_absences'));
        }

        return $this->render('KbhGestionCongesBundle:Salarie\Absence:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Absence entity.
     *
     * @param Absence $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAbsCreateForm(Absence $entity)
    {
        $form = $this->createForm(new Absence3Type(), $entity, array(
            'action' => $this->generateUrl('sa_absence_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Absence entity.
     *
     */
    public function absNewAction()
    { 
        $salarie = $this->getSalarieByUser();
        $entity = new Absence();
        $form   = $this->createAbsCreateForm($entity);
        
    // Chargeons les éléments nécessaires
        $em = $this->getDoctrine()->getManager();        
        
        $droits = $salarie->getDroits();        
        $parampermissions = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->findAll();
        
        if (!$parampermissions) {
            throw $this->createNotFoundException('Erreur chargement des paramètres de permissions exceptionnelles.');
        }

        $entity->setSalarie($salarie);
        
        return $this->render('KbhGestionCongesBundle:Salarie\Absence:demander-absence.html.twig', array(
            'entity' => $entity,
            'salarie'=> $salarie,
            'droits' => $droits,
            'types' => $parampermissions,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Displays a form to create a new Absence entity.
     *
     */
    public function absJustifAction()
    {
        $salarie = $this->getSalarieByUser();
        
        $entity = new Absence();
        $form   = $this->createAbsCreateForm($entity);
        
    // Chargeons les éléments nécessaires
        $em = $this->getDoctrine()->getManager();        
       
        $droits = $salarie->getDroits();        
        $parampermissions = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->findAll();
        
        if (!$parampermissions) {
            throw $this->createNotFoundException('Erreur chargement des paramètres de permissions exceptionnelles.');
        }

        $entity->setSalarie($salarie);
        
        return $this->render('KbhGestionCongesBundle:Salarie\Absence:justifier-absence.html.twig', array(
            'entity' => $entity,
            'salarie'=> $salarie,
            'droits' => $droits,
            'types' => $parampermissions,
            'form'   => $form->createView(),
        ));
    }

       //    ############################## BASE DE DONNEES #############################
    
     /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function absListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Absence')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Absences:absences.html.twig', array(
            'entities' => $entities,
        ));
    }
    
//    ############################## FONCTIONS ADDITIONNELLES #############################
    
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
