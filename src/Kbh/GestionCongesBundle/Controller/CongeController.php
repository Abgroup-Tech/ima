<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\Conge;
use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Entity\Droits;
use Kbh\GestionCongesBundle\Form\CongeType;
use Kbh\GestionCongesBundle\Form\CongeSaType;
use Kbh\GestionCongesBundle\Form\CongeEditType;

/**
 * Conge controller.
 *
 */
class CongeController extends Controller
{

    /******************* SALARIE*******************/
     /**
     * Liste des conges du salarié
     *
     */
    public function saCongesListAction()
    {
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Conge')->findBySalarie($salarie);
//        if (!$entities) {
//            throw $this->createNotFoundException('Vous n\'avez aucune demande de congé.');
//        }
        return $this->render('KbhGestionCongesBundle:Salarie\Conge:journal-conges.html.twig');
    }
    
    /**
     * Creates a new Conge entity.
     *
     */
    public function saCreateAction(Request $request)
    {
        $salarie = $this->getSalarieByUser();
        $entity = new Conge();
        $em = $this->getDoctrine()->getManager();
        
        $form = $this->saCreateCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
    
           $droits = $salarie->getDroits();
           $entity->setSalarie($salarie);
           $entity->setDroits($droits);
            // Notifications
           $superviseur = $salarie->getValideur()->getSalarie();
           $notification = new Notification();
           $notification->setSuperviseur($superviseur);
           $notification->setSalarie($salarie);
           $notification->setMessage("Demande de congé n°".$entity->getId()." créée avec succès");
           $notification->setConge($entity);
           
           //Envoie d'email aux concernés
                //Email au salarié
           $msgDemandeur = \Swift_Message::newInstance()
                        ->setSubject('Nouvelle demande de congé créée avec succès')
                        ->setFrom('demandes.gdc@entreprise.com')
                        ->setTo($salarie->getEmail())
                        ->setBody("Votre demande de congé n°".$entity->getId()." du ".$entity->getDateDebut()." au ".$entity->getDateFin()." (soit ".$entity->getNbJoursOuvrables()." jours) a été envoyée pour validation.</br>")
                        ;
            $this->get('mailer')->send($msgDemandeur);
           
                //Email à son supérieur N+1 (si validation -> envoie email au validateur suivant)
           $msgSuperviseur = \Swift_Message::newInstance()
                        ->setSubject('Nouvelle demande de congé')
                        ->setFrom($salarie->getEmail())
                        ->setTo($salarie->getValideur()->getSalarie()->getEmail())
                        ->setBody("<h1>Demande de congé n°".$entity->getId()." de ".$salarie->getCivilite()." ".$salarie->getNomprenom()."</h1></br>"
                                ."Durée :".$entity->getNbjoursOuvrables()." jours <br/>"
                                ."Début :".$entity->getDateDebut()."<br/>"
                                ."Fin :".$entity->getDateFin()."</br>"
                                ."Solde de congés du salarié :".$droits->getTotalDroitsAprendre()." jours <br/>"
                                ."Statut : En attente de votre validation.")
                        ;
            $this->get('mailer')->send($msgSuperviseur);
           

           $em->persist($notification);
           $em->persist($entity);
           $em->flush();

            return $this->redirect($this->generateUrl('sa_conges'));
        }

        return $this->render('KbhGestionCongesBundle:Conge:new.html.twig', array(
            'entity' => $entity,
            'droits' => $droits,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Conge entity.
     *
     * @param Conge $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function saCreateCreateForm(Conge $entity)
    {
        $form = $this->createForm(new CongeSaType(), $entity, array(
            'action' => $this->generateUrl('sa_conge_create'),
            'method' => 'POST',
        ));

//        $form->add('submit', 'submit', array('label' => 'Soumettre'));

        return $form;
    }

    /**
     * Displays a form to create a new Conge entity.
     *
     */
    public function saNewAction()
    {
        $entity = new Conge();
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();
        
        $form   = $this->sacreateCreateForm($entity);
        
        if (!$salarie) {
            throw $this->createNotFoundException('Unable to find Salarie entity.');
        }
        
//        $droits = $salarie->getDroits();
        return $this->render('KbhGestionCongesBundle:Salarie\Conge:demande-conges.html.twig', array(
            'salarie' => $salarie,
            'droits' => $droits,
            'form'   => $form->createView(),
            'result' => $this->getRequest()->get('date'),
        ));
    }

    /**
     * Finds and displays a Conge entity.
     *
     */
    public function saShowAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Conge')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Conge entity.');
        }
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();
        
        return $this->render('KbhGestionCongesBundle:Conge:show.html.twig', array(
            'entity'      => $entity,
            'salarie'     => $salarie,
            'droits'      => $droits
        ));
    }

    /******************* SUPERVISEUR *******************/
    /**
     * Affiche la liste des congés d'un collaborateur.
     *
     */
    public function collabCongesListAction($salarie_id)
    {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Conge')->findBySalarie($salarie_id);
        $collaborateur = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($salarie_id);
        
        return $this->render('KbhGestionCongesBundle:Superviseur\Conge:show.html.twig', array(
            'entity'             => $entity,
            'collaborateur'      => $collaborateur,           
        
        ));
    }

        public function collabCongeShowAction($id, $salarie_id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie= $em->getRepository('KbhGestionCongesBundle:Salarie')->find($salarie_id);
        $droits = $salarie->getDroits();

        $entity = $em->getRepository('KbhGestionCongesBundle:Demande')->find($id);


        return $this->render('KbhGestionCongesBundle:Demande\Superviseur:collab-detail-demande.html.twig', array(
            'entity'      => $entity,
            'salarie' => $salarie,
            'droit' =>  $droits,
    
        ));
    }

//    ############################## BASE DE DONNEES #############################
    
     /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function congesListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Conge')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Conges:conges.html.twig', array(
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
