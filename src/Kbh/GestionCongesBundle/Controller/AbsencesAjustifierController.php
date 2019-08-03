<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\AbsencesAjustifier;
use Kbh\GestionCongesBundle\Form\AbsencesAjustifierType;
use Kbh\GestionCongesBundle\Entity\Demande;
use Kbh\GestionCongesBundle\Entity\HistoriqueDroits;
use Kbh\GestionCongesBundle\Entity\Absence;

/**
 * AbsencesAjustifier controller.
 *
 */
class AbsencesAjustifierController extends Controller
{

    /**
     * Lists all AbsencesAjustifier entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $AbsencesAjustifier = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->findAll();
        $en_attente = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->findByStatut("En attente");
        $justifiees = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->findByStatut("Justifiée");
        $injustifiees = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->findByStatut("Injustifiable");
        $notifs = $em->getRepository('KbhGestionCongesBundle:Notification')->findByAdmin($salarie);
        
        //traitement notifs
        foreach ($notifs as $notif){
            foreach ($AbsencesAjustifier as $abs){
                if($notif->getDemande() == $abs->getDemande()){
                    $notif->setVuParAdmin(1);
                    $em->persist($notif);
                    $em->flush();
                }
             }
        }
        //Nombre de données existantes
        $nb1 = count($en_attente);
        $nb2 = count($justifiees);
        
        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAjustifier:index.html.twig', array(
            'en_attentes' => $en_attente,
            'justifiees' => $justifiees,
            'injustifiees' => $injustifiees,
            'nb1' => $nb1,
            'nb2' => $nb2,
        ));
    }
    /**
     * Creates a new AbsencesAjustifier entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AbsencesAjustifier();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('absencesajustifier_show', array('id' => $entity->getId())));
        }

        return $this->render('KbhGestionCongesBundle:AbsencesAjustifier:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AbsencesAjustifier entity.
     *
     * @param AbsencesAjustifier $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AbsencesAjustifier $entity)
    {
        $form = $this->createForm(new AbsencesAjustifierType(), $entity, array(
            'action' => $this->generateUrl('absencesajustifier_create'),
            'method' => 'POST',
        ));


        return $form;
    }

    /**
     * Displays a form to create a new AbsencesAjustifier entity.
     *
     */
    public function newAction()
    {
        $entity = new AbsencesAjustifier();
        $form   = $this->createCreateForm($entity);

        return $this->render('KbhGestionCongesBundle:AbsencesAjustifier:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AbsencesAjustifier entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAjustifier entity.');
        }


        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAjustifier:show.html.twig', array(
            'entity'      => $entity,
        ));
    }
    
     /**
     * Finds and displays a AbsencesAjustifier entity.
     *
     */
    public function show2Action($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAjustifier entity.');
        }
        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAjustifier:show2.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing AbsencesAjustifier entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser(); 

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAjustifier entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAjustifier:edit.html.twig', array(
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
    private function createEditForm(AbsencesAjustifier $entity)
    {
        $form = $this->createForm(new AbsencesAjustifierType(), $entity, array(
            'action' => $this->generateUrl('ad_absencesajustifier_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }
    
    /**
     * Finds and displays a AbsencesAjustifier entity.
     *
     */
    public function absenceInjustifiableAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAjustifier entity.');
        }
        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAjustifier:absenceInjustifiable.html.twig', array(
            'entity'      => $entity,
        ));
    }
    
    /**
     * Finds and displays a AbsencesAjustifier entity.
     *
     */
    public function debiterAbsenceAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $admin = $this->getSalarieByUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);
        $salarie = $entity->getDemande()->getSalarie();
        $droit = $salarie->getDroits();
        
        //Récupération du contenu du formulaire
        $nbJours = $_REQUEST['nbJours'];
        
        //Calcul de la date de fin
        $dateRetourTms = strtotime($_REQUEST['dateRetour']);
        $dateDebutTms = strtotime($_REQUEST['dateDebut']);
        $dateFinTms = $dateRetourTms - (60*60*24);
        
        //Conversion en datetime
        $dateDebut = date('Y/m/d H:i:s', $dateDebutTms);
        $dateFin = date('Y/m/d H:i:s', $dateFinTms);
        $dateRetour = date('Y/m/d H:i:s', $dateRetourTms);  
        
        //Création de la nouvelle demande
        $demande =  new Demande();
        
        $demande->setSalarie($salarie);
        $demande->setDateDemande(new \Datetime());
        $demande->setDateDebut(new \Datetime($dateDebut));
        $demande->setDateFin($dateFin);
        $demande->setDateRetour($dateRetour);
        $demande->setTypeDemande("Absence exceptionnelle");
        $demande->setNbjoursOuvrables($nbJours);
        $demande->setMotif("Autres");
        $demande->setDateValidation1(new \Datetime());
        $demande->setNbNiveauxValidation(1);
        $demande->setEstCloture(1);
        $demande->setDateCloture(new \Datetime());
        $demande->setEstValide(1);
        $demande->setEstEnCours(Null);
        $demande->setDateValidation(new \Datetime());
        $demande->setSoldeDroits($droit->getTotalDroitsAprendre());
        $demande->setValideurFinal($admin);
        $demande->setValideurNiveau1($admin);
        
        //Création de l'absence
        $absence = new Absence();

        $absence->setMotif("Absence exceptionnelle");
        $absence->setDateDebut($demande->getDateDebut());
        $absence->setDateFin($demande->getDateFin());
        $absence->setNbJoursOuvrables($nbJours);
        $absence->setSalarie($salarie);
        $absence->setDemande($demande);
        
        //Ajout de la gestion de l'historique des droits. 
        $historiqueDroits = new HistoriqueDroits();
        
        $historiqueDroits->setSalarie($salarie);
        $historiqueDroits->setDroits($droit);
        $historiqueDroits->setDemande($demande);
        $historiqueDroits->setSoldeCongeAncien($droit->getTotalDroitsAprendre());
        $historiqueDroits->setSoldePermissionAncien($droit->getSoldePermissions());
        
        //Droits mis à jours
        $droit->setDroitsPris($nbJours);
        $droit->setTotalDroitsAprendre();
        
        //Historique
        $historiqueDroits->setSoldeCongeNouveau($droit->getTotalDroitsAprendre());
        $historiqueDroits->setSoldePermissionNouveau($droit->getSoldePermissions());
        $historiqueDroits->setDateModification(new \DateTime());
        
        $em->persist($demande);
        $em->persist($absence);
        $em->persist($droit);
        $em->persist($entity);
        $em->persist($historiqueDroits);

        $em->flush();
        
       return $this->redirect($this->generateUrl('ad_absencesajustifier_show', array('id' => $id)));
    }
    
    /**
     * Edits an existing AbsencesAjustifier entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $admin = $this->getSalarieByUser();   
        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAjustifier entity.');
        }
        
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            
            $entity->getJustificatif()->setSalarie($entity->getDemande()->getSalarie());
            $entity->getJustificatif()->setAjouterPar($admin);
            $entity->getJustificatif()->setDateCreation(new \DateTime());
            $entity->getJustificatif()->preUpload();
            $entity->getJustificatif()->upload();
            $entity->getJustificatif()->setDownloadPath($entity->getJustificatif()->getAssetPath());
            
             //Absence Justifiable
            if ($entity->getAbsenceJustifiable() == true){
            $entity->setStatut('Justifiée');

            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "justifications des absences";
            $action = "Justification d'une absence";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à justifié l'absence pour raison médical de ".$entity->getJustificatif()->getSalarie()->getNomprenom();        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('ad_absencesajustifier_show_2', array('id' => $id)));
            
            }
            
            //Absence injustifiable
            if ($entity->getAbsenceJustifiable() == false){
            $entity->setStatut('Injustifiable');

            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "justifications des absences";
            $action = "Absence injustifiable";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à déclaré une absence injustifiable pour raison médical du salarié ".$entity->getJustificatif()->getSalarie()->getNomprenom();        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('ad_absencesinjustifiable_show', array('id' => $id)));
            }
            
        }

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAjustifier:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a AbsencesAjustifier entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AbsencesAjustifier entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('absencesajustifier'));
    }

    /**
     * Creates a form to delete a AbsencesAjustifier entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('absencesajustifier_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
//    ############################## BASE DE DONNEES #############################
    
     /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function absJustifListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/AbsencesAJ:absencesAjustifier.html.twig', array(
            'entities' => $entities,
        ));
    }
    
//    ######################## FONCTION SUPPLEMENTAIRES #########################
    
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
