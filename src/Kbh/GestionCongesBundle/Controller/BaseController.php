<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kbh\GestionCongesBundle\Entity\AbsencesAjustifier;
use Kbh\GestionCongesBundle\Form\AbsJustifSupAdminType;
use Kbh\GestionCongesBundle\Entity\AbsencesAT;
use Kbh\GestionCongesBundle\Form\AbsATSupAdminType;
use Kbh\GestionCongesBundle\Entity\Absence;
use Kbh\GestionCongesBundle\Form\AbsenceSupAdminType;
use Kbh\GestionCongesBundle\Entity\Conge;
use Kbh\GestionCongesBundle\Form\CongeSupAdminType;
use Kbh\GestionCongesBundle\Entity\Demande;
use Kbh\GestionCongesBundle\Form\DemandeSupAdminType;
use Kbh\GestionCongesBundle\Entity\Document;
use Kbh\GestionCongesBundle\Form\DocumentSupAdminType;
use Kbh\GestionCongesBundle\Entity\PiecesJointes;
use Kbh\GestionCongesBundle\Form\PiecesJointesSupAdminType;
use Kbh\GestionCongesBundle\Entity\LogUpdateAnnuel;
use Kbh\GestionCongesBundle\Form\LogUpdateAnnuelSupAdminType;
use Kbh\GestionCongesBundle\Entity\LogUpdateMensuel;
use Kbh\GestionCongesBundle\Form\LogUpdateMensuelSupAdminType;
use Kbh\GestionCongesBundle\Entity\Etats;
use Kbh\GestionCongesBundle\Form\EtatsSupAdminType;
use Kbh\GestionCongesBundle\Entity\HistoriqueDroits;
use Kbh\GestionCongesBundle\Form\HistoriqueDroitsSupAdminType;
use Kbh\GestionCongesBundle\Entity\LogActivites;
use Kbh\GestionCongesBundle\Form\LogActivitesSupAdminType;
use Kbh\GestionCongesBundle\Entity\Report;
use Kbh\GestionCongesBundle\Form\ReportSupAdminType;
use Kbh\GestionCongesBundle\Entity\Notification;
use Kbh\GestionCongesBundle\Form\NotificationsSupAdminType;

/**
 * run a command on the controller.
 *
 */
class BaseController extends Controller
{

    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function updateMensuelListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/UpdateMensuels:updates.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function updateAnnuelListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/UpdateAnnuels:updates.html.twig', array(
            'entities' => $entities,
        ));
    }
    
        /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function notificationsListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Notification')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Notifications:notifications.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function etatsListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Etats')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Etats:etats.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function historiqueDroitsListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/HistoriqueDroits:historique.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function activitesListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:LogActivites')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/LogActivites:activites.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function reportListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Report')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Report:report.html.twig', array(
            'entities' => $entities,
        ));
    }    
    
    // ####################### FONCTIONS D'EDITIONS DES ABSENCES A JUSTIFIER ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editAbsJustifAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAjustifier entity.');
        }

        $editForm = $this->AbsJustifEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/AbsencesAJ:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AbsencesAjustifier entity.
    *
    * @param AbsencesAjustifier $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function AbsJustifEditForm(AbsencesAjustifier $entity)
    {
        $form = $this->createForm(new AbsJustifSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_absencesajustifier_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing AbsencesAjustifier entity.
     *
     */
    public function updateAbsJustifAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAjustifier entity.');
        }
        
        $editForm = $this->AbsJustifEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "justifications des absences";
            $action = "Modification d'une absence à jsutifier";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier une absence à justifiée.";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_absJustif'));
        }

            return $this->redirect($this->generateUrl('sup_ad_absencesajustifier_edit', array('id' => $entity->getId())));
    }
    
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteAbsJustifAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "justifications des absences";
            $action = "Suppression d'une absence à jsutifier";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé une absence à justifiée.";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_absJustif'));
    }
    
     // ####################### FONCTIONS D'EDITIONS DES ABSENCES ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editAbsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:Absence')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Absences entity.');
        }

        $editForm = $this->AbsEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Absences:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param Absence $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function AbsEditForm(Absence $entity)
    {
        $form = $this->createForm(new AbsenceSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_abs_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateAbsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Absence')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Absences entity.');
        }
        
        $editForm = $this->AbsEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "absence";
            $action = "Modification d'une absence";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier une absence. ";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_abs'));
        }

            return $this->redirect($this->generateUrl('sup_ad_abs_edit', array('id' => $entity->getId())));
    }
    
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteAbsAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:Absence')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "absence";
            $action = "Suppression d'une absence";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé une absence.";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_abs'));
    }
    
    // ####################### FONCTIONS D'EDITIONS DES ABSENCES POUR ARRÊT DE TRAVAIL ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editAbsATAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Absences AT entity.');
        }

        $editForm = $this->AbsATEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/AbsencesAT:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AbsencesAjustifier entity.
    *
    * @param AbsencesAT $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function AbsATEditForm(AbsencesAT $entity)
    {
        $form = $this->createForm(new AbsATSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_absAT_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing AbsencesAjustifier entity.
     *
     */
    public function updateAbsATAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Absences AT entity.');
        }
        
        $editForm = $this->AbsATEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "arrêt de travail";
            $action = "Modification d'une absence pour arrêt de travail";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier une absence pour arrêt de travail. ";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_absAT'));
        }

            return $this->redirect($this->generateUrl('sup_ad_absAT_edit', array('id' => $entity->getId())));
    }
    
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteAbsATAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "arrêt de travail";
            $action = "Suppression d'une absence pour arrêt de travail";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé une absence pour arrêt de travail.";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_absAT'));
    }
    
         // ####################### FONCTIONS D'EDITIONS DES CONGES ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editCongeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:Conge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Conges entity.');
        }

        $editForm = $this->CongeEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Conges:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param Conge $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function CongeEditForm(Conge $entity)
    {
        $form = $this->createForm(new CongeSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_conge_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateCongeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Conge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Conges entity.');
        }
        
        $editForm = $this->CongeEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "congé";
            $action = "Modification d'un congé";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier un Congé. ";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_conges'));
        }

            return $this->redirect($this->generateUrl('sup_ad_conge_edit', array('id' => $entity->getId())));
    }
    
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteCongeAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:Conge')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "congé";
            $action = "Suppression d'un congé";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé un congé.";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_conges'));
    }
    
        // ####################### FONCTIONS D'EDITIONS DES DEMANDES ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editDemandeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:Demande')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Demandes entity.');
        }

        $editForm = $this->DemandeEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Demandes:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param Demande $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function DemandeEditForm(Demande $entity)
    {
        $form = $this->createForm(new DemandeSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_demandes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateDemandeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Demande')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Demandes entity.');
        }
        
        $editForm = $this->DemandeEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "demandes";
            $action = "Modification d'une demande";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier une demande. ";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_demandes'));
        }

            return $this->redirect($this->generateUrl('sup_ad_demandes_edit', array('id' => $entity->getId())));
    }
    
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteDemandeAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:Demande')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "demandes";
            $action = "Suppression d'une demande";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé une demande.";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_demandes'));
    }
    
    // ####################### FONCTIONS D'EDITIONS DES DOCUMENTS ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editDocumentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $editForm = $this->DocumentEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Documents:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param Document $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function DocumentEditForm(Document $entity)
    {
        $form = $this->createForm(new DocumentSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_documents_sys_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateDocumentAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Documents entity.');
        }
        
        $editForm = $this->DocumentEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "documents";
            $action = "Modification d'un document";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier un document. ";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_documents_sys'));
        }

            return $this->redirect($this->generateUrl('sup_ad_documents_sys_edit', array('id' => $entity->getId())));
    }
    
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteDocumentAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "documents";
            $action = "Suppression d'un Document";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé un document.";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_documents_sys'));
    }
    
        // ####################### FONCTIONS D'EDITIONS DES PIECES JOINTES ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editPiecesJointesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:PiecesJointes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pieces jointes entity.');
        }

        $editForm = $this->PiecesJointesEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/PiecesJointes:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param PiecesJointes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function PiecesJointesEditForm(PiecesJointes $entity)
    {
        $form = $this->createForm(new PiecesJointesSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_piecesJointes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updatePiecesJointesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:PiecesJointes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pieces jointes entity.');
        }
        
        $editForm = $this->PiecesJointesEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "pieces-jointes";
            $action = "Modification d'une piece jointe";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier un piece jointe. ";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_piecesJointes'));
        }

            return $this->redirect($this->generateUrl('sup_ad_piecesJointes_edit', array('id' => $entity->getId())));
    }
    
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deletePiecesJointesAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:PiecesJointes')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "pieces-jointes";
            $action = "Suppression d'une piece jointe";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé une piece jointe.";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_piecesJointes'));
    }
    
        // ####################### FONCTIONS D'EDITIONS DES MISES A JOURS ANNUELLES ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editUpdateAnnuelAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Update annuel entity.');
        }

        $editForm = $this->UpdateAnnuelEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/UpdateAnnuels:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param LogUpdateAnnuel $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function UpdateAnnuelEditForm(LogUpdateAnnuel $entity)
    {
        $form = $this->createForm(new LogUpdateAnnuelSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_logUpdate_annuel_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateUAnnuelAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find update annuel entity.');
        }
        
        $editForm = $this->UpdateAnnuelEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "mises à jours";
            $action = "Modification d'une mise à jours annuelle";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier la mise à jours N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_logUpdate_annuel'));
        }

            return $this->redirect($this->generateUrl('sup_ad_logUpdate_annuel_edit', array('id' => $entity->getId())));
    }
    
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteUpdateAnnuelAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->find($id);
           $em->remove($entity);
           $em->flush();
           
 //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "mises à jours";
            $action = "Suppression d'une mise à jours annuelle";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé la mise à jours N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_logUpdate_annuel'));
    }
  
        // ####################### FONCTIONS D'EDITIONS DES MISES A JOURS MENSUELLES  ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editUpdateMensuelAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Update mensuel entity.');
        }

        $editForm = $this->UpdateMensuelEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/UpdateMensuels:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param LogUpdateMensuel $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function UpdateMensuelEditForm(LogUpdateMensuel $entity)
    {
        $form = $this->createForm(new LogUpdateMensuelSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_logUpdate_mensuel_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateUMensuelAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find update mensuel entity.');
        }
        
        $editForm = $this->UpdateMensuelEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "mises à jours";
            $action = "Modification d'une mise à jours mensuelle";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier la mise à jours N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_logUpdate_mensuel'));
        }

            return $this->redirect($this->generateUrl('sup_ad_logUpdate_mensuel_edit', array('id' => $entity->getId())));
    }
       
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteUpdateMensuelAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->find($id);
           $em->remove($entity);
           $em->flush();
           
 //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "mises à jours";
            $action = "Suppression d'une mise à jours mensuelle";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé la mise à jours N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_logUpdate_mensuel'));
    }
           
        // ####################### FONCTIONS D'EDITIONS DES ETATS ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editEtatsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:Etats')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Etats entity.');
        }

        $editForm = $this->EtatsEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Etats:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param Etats $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function EtatsEditForm(Etats $entity)
    {
        $form = $this->createForm(new EtatsSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_etats_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateEtatsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Etats')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Etats entity.');
        }
        
        $editForm = $this->EtatsEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "etats";
            $action = "Modification d'un état de l'entreprise";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier l'état N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_etats'));
        }

            return $this->redirect($this->generateUrl('sup_ad_etats_edit', array('id' => $entity->getId())));
    }
          
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteEtatsAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:Etats')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "etats";
            $action = "Suppression d'un état de l'entreprise";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé l'état N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_etats'));
    }
           
        // ####################### FONCTIONS D'EDITIONS DES HISTORIQUES DE DROITS ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editHistoriqueDroitsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Historique droits entity.');
        }

        $editForm = $this->HistoriqueDroitsEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/HistoriqueDroits:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param HistoriqueDroits $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function HistoriqueDroitsEditForm(HistoriqueDroits $entity)
    {
        $form = $this->createForm(new HistoriqueDroitsSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_historique_droits_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateHistoriqueDroitsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Historique droits entity.');
        }
        
        $editForm = $this->HistoriqueDroitsEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "historique droits";
            $action = "Modification d'un historique de droit";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier l'historique N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_historique_droits'));
        }

            return $this->redirect($this->generateUrl('sup_ad_historique_droits_edit', array('id' => $entity->getId())));
    }
           
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteHistoriqueDroitsAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "historique droits";
            $action = "Suppression d'un historique de droit";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé l'historique N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_historique_droits'));
    }
                          
// ####################### FONCTIONS D'EDITIONS DU LOG DES ACTIVITES ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editActivitesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:LogActivites')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Log activites entity.');
        }

        $editForm = $this->LogActivitesEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/LogActivites:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param LogActivites $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function LogActivitesEditForm(LogActivites $entity)
    {
        $form = $this->createForm(new LogActivitesSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_log_activites_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateActivitesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:LogActivites')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Log activites entity.');
        }
        
        $editForm = $this->LogActivitesEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "log activités";
            $action = "Modification d'une activités";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier l'activié ".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_log_activites'));
        }

            return $this->redirect($this->generateUrl('sup_ad_log_activites_edit', array('id' => $entity->getId())));
    }
            
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteActivitesAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:LogActivites')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "log activités";
            $action = "Suppression d'une activités";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à Supprimé l'activié ".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_log_activites'));
    }
                                             
// ####################### FONCTIONS D'EDITIONS DU REPORT ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editReportAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:Report')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Report entity.');
        }

        $editForm = $this->ReportEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Report:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param Report $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function ReportEditForm(Report $entity)
    {
        $form = $this->createForm(new ReportSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_report_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateReportAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Report')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Report entity.');
        }
        
        $editForm = $this->ReportEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "reports";
            $action = "Modification d'un report";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier un report ";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_report'));
        }

            return $this->redirect($this->generateUrl('sup_ad_report_edit', array('id' => $entity->getId())));
    }
              
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteReportAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:Report')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "reports";
            $action = "Suppression d'un reports";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à Supprimé le report N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_report'));
    }
                
// ####################### FONCTIONS D'EDITIONS DES NOTIFICATIONS  ###############################
    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editNotificationsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:Notification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notifications entity.');
        }

        $editForm = $this->NotificationsEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Notifications:edit.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Absence entity.
    *
    * @param Notification $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function NotificationsEditForm(Notification $entity)
    {
        $form = $this->createForm(new NotificationsSupAdminType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_notifications_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    
    /**
     * Edits an existing Absence entity.
     *
     */
    public function updateNotificationsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Notification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notifications entity.');
        }
        
        $editForm = $this->NotificationsEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "notifications";
            $action = "Modification d'une notification";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifier une notification ";        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_notifications'));
        }

            return $this->redirect($this->generateUrl('sup_ad_notifications_edit', array('id' => $entity->getId())));
    }
    
       /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteNotificationsAction($id)
    {
           $em = $this->getDoctrine()->getManager();
           $entity = $em->getRepository('KbhGestionCongesBundle:Notification')->find($id);
           $em->remove($entity);
           $em->flush();
           
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "notifications";
            $action = "Suppression d'une notification";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à Supprimé la notification N°".$id;        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
           
           return $this->redirect($this->generateUrl('sup_ad_notifications'));
    }          
//    ##################### FONCTIONS ADDITIONNELLES ############################
    
     /**
     * Creates a new Document entity.
     *
     */
    public function getSalarieByUser() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!$user) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();

        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($user);

        if (!$salarie) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $salarie;
    }
    
    /**
     * Creates a new Document entity.
     *
     */
    public function reinitialiserDonneesAction($table) {
        $em = $this->getDoctrine()->getManager();
        
        //1er cas : 
        if($table == "Absences-à-justifier"){
             $entities = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->findAll();
             foreach($entities as $entity){
                 //################ VERIFICATION DE PIECES JOINTES EXISTANTE ############################
                 //Vérifions qu'il n'y a pas d'absences reliée a des pièces jointes
                 $piece = $em->getRepository('KbhGestionCongesBundle:PiecesJointes')->find($entity->getJustificatif());
                 if(count($piece)>=1){
                      //Suppression des pièces jointes
                         $entity->setJustificatif(NULL);
                        $em->flush();
                 }
                 $em->remove($entity);
                 $em->flush();
             }
        }
        //2ème cas : 
        if($table == "Absences-arrêt-de-travail"){
             $entities = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findAll();
             foreach($entities as $entity){
                 //################ VERIFICATION DE PIECES JOINTES EXISTANTE ############################
                 //Vérifions qu'il n'y a pas d'absences reliée a des pièces jointes
                 $piece = $em->getRepository('KbhGestionCongesBundle:PiecesJointes')->find($entity->getPieceJustificative()->getId());
                 if(count($piece) == 1){
                      //Suppression des pièces jointes
                        $entity->setPieceJustificative(NULL);
                        $em->flush();
                 }
                 $em->remove($entity);
                 $em->flush();
             }
        }
        //3ème cas : 
        if($table == "Absences"){
             $entities = $em->getRepository('KbhGestionCongesBundle:Absence')->findAll();
             foreach($entities as $entity){
                 //################ VERIFICATION DES DEMANDES EXISTANTE ############################
                 //Vérifions qu'il n'y a pas de demande reliée a l'absence
                 $demandes = $em->getRepository('KbhGestionCongesBundle:Demande')->find($entity->getDemande()->getId());
                 if(count($demandes)>=1){
                      //Suppression des demandes rattachées
                           foreach($demandes as $demande){
                               //Suppression de la demandes
                               $entity->setDemande(NULL);
                               $em->flush();
                           }
                 }
                 $em->remove($entity);
                 $em->flush();
             }
        }
         //4ème cas : 
        if($table == "Congés"){
             $entities = $em->getRepository('KbhGestionCongesBundle:Conge')->findAll();
             foreach($entities as $entity){
                //################ VERIFICATION DES DEMANDES EXISTANTE ############################
                 //Vérifions qu'il n'y a pas de demande reliée au congé
                 $demandes = $em->getRepository('KbhGestionCongesBundle:Demande')->find($entity->getDemande()->getId());
                 if(count($demandes)>=1){
                      //Suppression des demandes rattachées
                           foreach($demandes as $demande){
                               //Suppression du congé
                               $entity->setDemande(NULL);
                               $em->flush();
                           }
                 }
                 $em->remove($entity);
                 $em->flush();
             }
        }
         //5ème cas : 
        if($table == "Demandes"){
             $entities = $em->getRepository('KbhGestionCongesBundle:Demande')->findAll();
             foreach($entities as $entity){
                 
                  //################ VERIFICATION DE CONGES EXISTANT ############################
                        //Vérifions qu'il n'y a pas de congés reliée à la demande
                        $conges = $em->getRepository('KbhGestionCongesBundle:Conge')->findByDemande($entity->getId());
                        if(count($conges)>=1){
                           //Suppression des conges rattachées
                           foreach($conges as $conge){
                               //Suppression du congé
                               $em->remove($conge);
                               $em->flush();
                           }
                        }
                  //################ VERIFICATION D'ABSENCES EXISTANTE ############################
                        //Vérifions qu'il n'y a pas d'absences reliée à la demande
                        $absences = $em->getRepository('KbhGestionCongesBundle:Absence')->findByDemande($entity->getId());
                        if(count($absences)>=1){
                           //Suppression des absences rattachées
                           foreach($absences as $absence){
                               //Suppression de l'absence
                               $em->remove($absence);
                               $em->flush();
                           }
                        }
                  //################ VERIFICATION HISTORIQUE DROITS ############################
                        //Vérifions qu'il n'y a pas d'absences reliée à la demande
                        $historiques = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findByDemande($entity->getId());
                        if(count($historiques)>=1){
                           //Suppression des absences rattachées
                           foreach($historiques as $historique){
                               //Suppression de l'absence
                               $em->remove($historique);
                               $em->flush();
                           }
                        } 
                  //################ VERIFICATION NOTIFICATIONS ############################
                        //Vérifions qu'il n'y a pas d'absences reliée à la demande
                        $notifs = $em->getRepository('KbhGestionCongesBundle:Notification')->findByDemande($entity->getId());
                        if(count($notifs)>=1){
                           //Suppression des absences rattachées
                           foreach($notifs as $notif){
                               //Suppression de l'absence
                               $em->remove($notif);
                               $em->flush();
                           }
                        }       
                        
                 $em->remove($entity);
                 $em->flush();
             }
        }
         //6ème cas : 
        if($table == "Documents"){
             $entities = $em->getRepository('KbhGestionCongesBundle:Document')->findAll();
             foreach($entities as $entity){
                   //################ VERIFICATION DES DONNES RELIEES A L'ENTREPRISE ############################
                        //Vérifions qu'il n'y a pas de congés reliée à la demande
                        $docFeries = $em->getRepository('KbhGestionCongesBundle:Entreprise')->findOneByDocFeries($entity->getId());
                        $docPermissions = $em->getRepository('KbhGestionCongesBundle:Entreprise')->findOneByDocPermissions($entity->getId());
                        
                        if(count($docFeries) ==1){
                           //Suppression du doc rattachées
                          $docFeries->setDocFeries(NULL);
                        }
                        if(count($docPermissions) ==1){
                           //Suppression du doc rattachées
                          $docPermissions->setDocPermissions(NULL);
                        }
                        $em->remove($entity);
                        $em->flush();
             }
        }
       //7ème cas : 
        if($table == "Pièces-jointes"){
             $entities = $em->getRepository('KbhGestionCongesBundle:PiecesJointes')->findAll();
             foreach($entities as $entity){
                
                 //################ VERIFICATION DE DEMANDES EXISTANTE ############################
                 //Vérifions qu'il n'y a pas de demande reliée à la pièce jointe
                 $demandes = $em->getRepository('KbhGestionCongesBundle:Demande')->findByPieceJointe($entity->getId());
                 if(count($demandes)>=1){
                    //Suppression des demandes rattachées
                    foreach($demandes as $demande){
                        //################ VERIFICATION DE CONGES EXISTANT ############################
                        //Vérifions qu'il n'y a pas de congés reliée à la demande
                        $conges = $em->getRepository('KbhGestionCongesBundle:Conge')->findByDemande($demande->getId());
                        if(count($conges)>=1){
                           //Suppression des conges rattachées
                           foreach($conges as $conge){
                               //Suppression du congé
                               $em->remove($conge);
                               $em->flush();
                           }
                        }
                        //################ VERIFICATION D'ABSENCES EXISTANTE ############################
                        //Vérifions qu'il n'y a pas d'absences reliée à la demande
                        $absences = $em->getRepository('KbhGestionCongesBundle:Absence')->findByDemande($demande->getId());
                        if(count($absences)>=1){
                           //Suppression des absences rattachées
                           foreach($absences as $absence){
                               //Suppression de l'absence
                               $em->remove($absence);
                               $em->flush();
                           }
                        }
                       
                        //Suppression de la demande
                        $em->remove($demande);
                        $em->flush();
                    }
                  
                 }
                
                //################ VERIFICATION D'ABSENCES AT EXISTANTE ############################
                    //Vérifions qu'il n'y a pas d'absences AT reliée à la demande
                    $absencesAT = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findByPieceJustificative($entity->getId());
//                    die();
                    if(count($absencesAT)>=1){
                       //Suppression des absences AT rattachées
                       foreach($absencesAT as $absenceAT){
                           //Suppression de l'absence AT
                           $em->remove($absenceAT);
                           $em->flush();
                       }
                    }   
                 
                    //################ VERIFICATION D'ABSENCES A JUSTIFIER EXISTANTE ############################
                            //Vérifions qu'il n'y a pas d'absences à justifier reliée à la demande
                            $absencesAjustifier = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->findByJustificatif($entity->getId());
        //                    die();
                            if(count($absencesAjustifier)>=1){
                               //Suppression des absences à justifier rattachées
                               foreach($absencesAjustifier as $absenceAjustifier){
                                   //Suppression de l'absence à justifier
                                   $em->remove($absenceAjustifier);
                                   $em->flush();
                               }
                            }  
                    
                 //Suppression de la pièce jointe
                 $em->remove($entity);
                 $em->flush();
             }
        }  
     //8ème cas : 
        if($table == "Log-update-mensuel"){
             $entities = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->findAll();
             foreach($entities as $entity){
                 $em->remove($entity);
                 $em->flush();
             }
        }  
       //9ème cas : 
        if($table == "Log-update-annuel"){
             $entities = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->findAll();
             foreach($entities as $entity){
                 $em->remove($entity);
                 $em->flush();
             }
        }   
       //10ème cas : 
        if($table == "Notifications"){
             $entities = $em->getRepository('KbhGestionCongesBundle:Notification')->findAll();
             foreach($entities as $entity){
                 $em->remove($entity);
                 $em->flush();
             }
        }
      //11ème cas : 
        if($table == "Etats"){
             $entities = $em->getRepository('KbhGestionCongesBundle:Etats')->findAll();
             foreach($entities as $entity){
                 
                 //Reinitialisation des etats
                    $entity->setCongesAcquis(0);
                    $entity->setCongesAnterieur(0);
                    $entity->setTotalDroitsAcquis(0);
                    $entity->setTotalCongesConsomme(0);
                    $entity->setCongesPris(0);
                    $entity->setPermissions(0);
                    $entity->setAbsencesEx(0);
                    $entity->setStockConge(0);
                    $entity->setTotalAbsences(0);
                    
                      $em->persist($entity);
                      $em->flush();
             }
        }
     //12ème cas : 
        if($table == "Historique-des-droits"){
             $entities = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findAll();
             foreach($entities as $entity){
                 $em->remove($entity);
                 $em->flush();
             }
        }
      //13ème cas : 
        if($table == "Historique-des-activités"){
             $entities = $em->getRepository('KbhGestionCongesBundle:LogActivites')->findAll();
             foreach($entities as $entity){
                 $em->remove($entity);
                 $em->flush();
             }
        }
      //14ème cas : 
        if($table == "Reports-de-congés"){
             $entities = $em->getRepository('KbhGestionCongesBundle:Report')->findAll();
             foreach($entities as $entity){
                 $em->remove($entity);
                 $em->flush();
             }
        }   
        
        //Log action effectuée
        $salarieConnecte = $this->getSalarieByUser();
        $cible = "reinitialisation";
        $action = "Réinitialisation des données d'une table";
        $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à réinitialisé les données de la table ".$table;        
        $this->logActivite($salarieConnecte, $cible, $action, $msg);
        
        return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
    }

    
    /**
     * Fonction de log des activités des salariés (timeline)
     *
     */
    public function logActivite($salarie, $cible, $action, $message) {
        $em = $this->getDoctrine()->getManager();
        $logActivite = new \Kbh\GestionCongesBundle\Entity\LogActivites;
        
        //1er cas : cible concernant les salariés
        if($cible == "salariés"){
            $logActivite->setIcon("icon-user");
        }
        //2ème cas : cible concernant les notifications
        if($cible == "notifications"){
            $logActivite->setIcon("icon-bell");
        }
        //3ème cas : cible concernant les arrêt de travail
        if($cible == "arrêt de travail"){
            $logActivite->setIcon("icon-ban");
        }
        //3ème cas : cible concernant les arrêt de travail
        if($cible == "arrêt maladie"){
            $logActivite->setIcon("icon-heart");
        }
        //4ème cas : cible concernant les unités
        if($cible == "unités"){
            $logActivite->setIcon("icon-note");
        }
        //5ème cas : cible concernant les justifications des absences
        if($cible == "justifications des absences"){
            $logActivite->setIcon("icon-layers");
        }
        //6ème cas : cible concernant les mises à jours
        if($cible == "mises à jours"){
            $logActivite->setIcon("icon-refresh");
        }
        //7ème cas : cible concernant les documents importés
        if($cible == "documents"){
            $logActivite->setIcon("icon-doc");
        }
        //8ème cas : cible concernant les absences 
        if($cible == "absence"){
            $logActivite->setIcon("icon-logout");
        }
        //9ème cas : cible concernant les congés 
        if($cible == "congé"){
            $logActivite->setIcon("icon-plane");
        }
        //10ème cas : cible concernant les demandes
        if($cible == "demande"){
            $logActivite->setIcon("icon-envelope-letter");
        }
        //10ème cas : cible concernant les pièces jointes
        if($cible == "pieces-jointes"){
            $logActivite->setIcon("icon-paper-clip");
        }
        //11ème cas : cible concernant les états
        if($cible == "etats"){
            $logActivite->setIcon("icon-graph");
        }
        //12ème cas : cible concernant les historiques de droits
        if($cible == "historique droits"){
            $logActivite->setIcon("icon-list");
        }
        //13ème cas : cible concernant les historiques de droits
        if($cible == "log activités"){
            $logActivite->setIcon("icon-equalizer");
        }
        //14ème cas : cible concernant les reports
        if($cible == "reports"){
            $logActivite->setIcon("icon-reload");
        }
        //15ème cas : cible concernant les reinitialisation
        if($cible == "reinitialisation"){
            $logActivite->setIcon("icon-power");
        }
        
        //Hydratation de la table
        $logActivite->setSalarie($salarie);
        $logActivite->setAction($action);
        $logActivite->setCible($cible);
        $logActivite->setMessage($message);
        $logActivite->setDateCreation(new \Datetime());

        $em->persist($logActivite);
        $em->flush();
        
        return $logActivite;
    }
}