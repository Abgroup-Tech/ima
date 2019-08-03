<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\AbsencesAT;
use Kbh\GestionCongesBundle\Entity\Notification;
use Kbh\GestionCongesBundle\Entity\PiecesJointes;
use Kbh\GestionCongesBundle\Form\AbsencesATType;
use Kbh\GestionCongesBundle\Form\PiecesJointesType;

/**
 * AbsencesAT controller.
 *
 */
class AbsencesATController extends Controller
{

    /**
     * Lists all AbsencesAT entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findAll();

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    
     /**
     * Lists all AbsencesAT entities.
     *
     */
    public function historiqueSalarieAction()
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();  

        $entities = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findBySalarie($salarie);

        return $this->render('KbhGestionCongesBundle:Salarie/AbsencesAT:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    
        /**
     * Finds and displays a AbsencesAT entity.
     *
     */
    public function showSalarieAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->find($id);
        $salarie = $entity->getSalarie();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAT entity.');
        }

        return $this->render('KbhGestionCongesBundle:Salarie/AbsencesAT:show.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
        ));
    }
    
    /**
     * Lists all AbsencesAT entities.
     *
     */
    public function historiqueSupAction()
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();  

        $entities = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findBySalarie($salarie);
        $all = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findAll();
        $unites = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();
        $salaries= array();

        //Récupération des salariés rattachés au manager connecté
        $unite = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->find($salarie->getUnite());
        $salaries[0] = $unite->getSalaries();

        //Récupération des salariés rattachés à l'unité du manager connecté
        $cp=1;            
        foreach ($unites as $entity)
       {
            if($entity->getUniteSuivante1() == $salarie->getUnite() || $entity->getUniteSuivante2() == $salarie->getUnite() || $entity->getUniteSuivante3() == $salarie->getUnite())
             {
                 $salaries[$cp] = $entity->getSalaries();
             }
               $cp += 1;
       }
        
         $user = $this->getUser();
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Superviseur/AbsencesAT:index.html.twig', array(
                'entities' => $entities,
                'collaborateurs' => $salaries, 
                'all' => $all,
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Top-manager/AbsencesAT:index.html.twig', array(
                'entities' => $entities,
                'all' => $all, 
            ));
        }
       
    }
    
        /**
     * Finds and displays a AbsencesAT entity.
     *
     */
    public function showSupAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->find($id);
        $salarie = $entity->getSalarie();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAT entity.');
        }
        
        $user = $this->getUser();
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
        return $this->render('KbhGestionCongesBundle:Superviseur/AbsencesAT:show.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
        ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Top-manager/AbsencesAT:show.html.twig', array(
               'entity'      => $entity,
                'salarie'      => $salarie,
        ));
        }
    }
    
    /**
     * Creates a new AbsencesAT entity.
     *
     */
    public function confirmationAction(Request $request)
    {
        $entity = new AbsencesAT();
        $admin = $this->getSalarieByUser();        
        $form = $this->createAbsencesATForm($entity);
        $form->handleRequest($request);
        $erreur = "Une erreur s'est produite lors du traitement";

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $piece = new PiecesJointes();
            $piece->setSalarie($entity->getSalarie());
            $piece->setAjouterPar($admin);
            
//            $entity->setPieceJustificative($piece);
//            $entity->setPieceJustificative()->setAjouterPar($admin);
//            $entity->setPieceJustificative()->setSalarie($entity->getSalarie());
//            $em->persist($entity);
            $erreur = "";
            $formConfirme   = $this->createCreateForm($entity);
//            var_dump($piece);
//            die();
        
        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:confirmation.html.twig', array(
            'entity' => $entity,
            'piece' => $piece,
            'admin' => $admin,
            'erreur' => $erreur,
            'form'   => $formConfirme->createView(),
             ));
        }
        
        $formErreur   = $this->createAbsencesATForm($entity);
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:new.html.twig', array(
            'entity' => $entity,
            'erreur' => $erreur,
            'admin' => $admin,
            'salaries' => $salaries,
            'form'   => $formErreur->createView(),
        ));
    }
    
    /**
     * Creates a new AbsencesAT entity.
     *
     */
    public function confirmationAMAction(Request $request, $id)
    {
        $entity = new AbsencesAT();
        $em = $this->getDoctrine()->getManager();
        $admin = $this->getSalarieByUser();        
        $form = $this->createAbsencesAMForm($entity);
        $form->handleRequest($request);
        $erreur = "Une erreur s'est produite lors du traitement";
        $absenceJustifiee = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);

        if ($form->isValid()) {
            $entity->setMotif('Maladie');
            $em->persist($entity);
            $erreur = "";
            $formConfirme   = $this->createAbsencesAMForm($entity);
        
        
        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:confirmation2.html.twig', array(
            'entity' => $entity,
            'admin' => $admin,
            'erreur' => $erreur,
             'abs' => $absenceJustifiee,
            'form'   => $formConfirme->createView(),
             ));
        }
        
        $formErreur   = $this->createAbsencesATForm($entity);
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:new2.html.twig', array(
            'entity' => $entity,
            'erreur' => $erreur,
            'admin' => $admin,
            'salaries' => $salaries,
            'abs' => $absenceJustifiee,
            'form'   => $formErreur->createView(),
        ));
    }    
    
    /**
     * Creates a new AbsencesAT entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AbsencesAT();
        $admin = $this->getSalarieByUser();        
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //Mise à jour absences arrêt travail
            $entity->setDateCreation(new \DateTime());
            $entity->setAdmin($admin);
            $entity->setInfoCabinetMedical("NULL");
            $entity->setMedecin("NULL");
            
            //Mise à jour de la pièce justificative
            $piece = $entity->getPieceJustificative();
            $piece->setSalarie($entity->getSalarie());
            $piece->setAjouterPar($admin);
            $piece->setDateCreation(new \DateTime());
            $piece->preUpload();
            $piece->upload();
            $piece->setDownloadPath($piece->getAssetPath());
            
//            // Création des notifications
//            $notif_salarie = new Notification();
//            
//            $notif_salarie->setSalarie();
//            $notif_salarie->setObservateur();
//            $notif_salarie->setSuperieurN1();
//            $notif_salarie->setMessageDemandeur();
//            $notif_salarie->setMessageFinal();
//            $notif_salarie->setMessageValideurPrecedent();
            
            $entity->setPieceJustificative($piece);
            $em->persist($entity);
            $em->persist($piece);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "arrêt de travail";
            $action = "Déclaration d'un arrêt de travail";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à déclaré un nouvel arrêt de travail au nom de ".$entity->getSalarie()->getNomprenom();        
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('ad_absencesat'));
        }

        $formErreur   = $this->createAbsencesATForm($entity);
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();
        $erreur = "Une erreur s'est produite lors du traitement";

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:new.html.twig', array(
            'entity' => $entity,
            'erreur' => $erreur,
            'admin' => $admin,
            'salaries' => $salaries,
            'form'   => $formErreur->createView(),
        ));
    }
    
        /**
     * Creates a new AbsencesAT entity.
     *
     */
    public function createAMAction(Request $request)
     {
        $entity = new AbsencesAT();
        $em = $this->getDoctrine()->getManager();
        $admin = $this->getSalarieByUser();        
        $form = $this->createAMForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //Mise à jour absences arrêt travail
            $entity->setDateCreation(new \DateTime());
            $entity->setAdmin($admin);
            $entity->setMotif('Maladie');
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "arrêt maladie";
            $action = "Déclaration d'un arrêt de travail pour raison(s) médicale";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à déclaré un nouvel arrêt de travail pour raison(s) médicale(s) au nom de ".$entity->getSalarie()->getNomprenom();    
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            return $this->redirect($this->generateUrl('ad_absencesat'));
        }

        $formErreur   = $this->createAbsencesAMForm($entity);
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();
        $erreur = "Une erreur s'est produite lors du traitement";

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:new2.html.twig', array(
            'entity' => $entity,
            'erreur' => $erreur,
            'admin' => $admin,
            'salaries' => $salaries,
            'form'   => $formErreur->createView(),
        ));
    }
    /**
     * Creates a form to create a AbsencesAT entity.
     *
     * @param AbsencesAT $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AbsencesAT $entity)
    {
        $form = $this->createForm(new AbsencesATType(), $entity, array(
            'action' => $this->generateUrl('ad_absencesat_create'),
            'method' => 'POST',
        ));
        
        $form->add('pieceJustificative', new PiecesJointesType());
        $form ->add('motif');


        return $form;
    }
    
    /**
     * Creates a form to create a AbsencesAT entity.
     *
     * @param AbsencesAT $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAMForm(AbsencesAT $entity)
    {
        $form = $this->createForm(new AbsencesATType(), $entity, array(
//            'action' => $this->generateUrl('ad_absencesat_maladie_create'),
            'method' => 'POST',
        ));
        
        $form ->add('medecin');
        $form ->add('infoCabinetMedical','textarea');
        $form->add('pieceJustificative');


        return $form;
    }    
    
     /**
     * Creates a form to create a AbsencesAT entity.
     *
     * @param AbsencesAT $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAbsencesATForm(AbsencesAT $entity)
    {
        $form = $this->createForm(new AbsencesATType(), $entity, array(
            'action' => $this->generateUrl('ad_absencesat_confirmation'),
            'method' => 'POST',
        ));
        $form ->add('motif','choice',array(
                            'choices'=>array(
                                        'Maladie professionnelle'=>'Maladie professionnelle',
                                        'Accident de travail'=>'Accident de travail',
                                        'Congé maternité'=>'Congé maternité')
                ));

        return $form;
    }
    
    /**
     * Creates a form to create a AbsencesAT entity.
     *
     * @param AbsencesAT $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAbsencesAMForm(AbsencesAT $entity)
    {
        $form = $this->createForm(new AbsencesATType(), $entity, array(
//            'action' => $this->generateUrl('ad_absencesat_maladie_confirmation'),
            'method' => 'POST',
        ));
        $form ->add('medecin');
        $form ->add('infoCabinetMedical','textarea');
        $form->add('pieceJustificative');

        return $form;
    }

    /**
     * Displays a form to create a new AbsencesAT entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $admin = $this->getSalarieByUser();
        $entity = new AbsencesAT();
        $form   = $this->createAbsencesATForm($entity);
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();
        $erreur = "";

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:new.html.twig', array(
            'entity' => $entity,
            'erreur' => $erreur,
            'admin' => $admin,
            'salaries' => $salaries,
            'form'   => $form->createView(),
        ));
    }
    
     /**
     * Displays a form to create a new AbsencesAT entity.
     *
     */
    public function newAMAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $admin = $this->getSalarieByUser();
        $absenceJustifiee = $em->getRepository('KbhGestionCongesBundle:AbsencesAjustifier')->find($id);
        $entity = new AbsencesAT();
        $entity->setPieceJustificative($absenceJustifiee->getJustificatif());
        $entity->setSalarie($absenceJustifiee->getDemande()->getSalarie());
        $form   = $this->createAbsencesAMForm($entity);
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();
        $erreur = "";

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:new2.html.twig', array(
            'entity' => $entity,
            'erreur' => $erreur,
            'admin' => $admin,
            'salaries' => $salaries,
            'abs' => $absenceJustifiee,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AbsencesAT entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->find($id);
        $salarie = $entity->getSalarie();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAT entity.');
        }

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:show.html.twig', array(
            'entity'      => $entity,
            'salarie'      => $salarie,
        ));
    }

    /**
     * Displays a form to edit an existing AbsencesAT entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAT entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AbsencesAT entity.
    *
    * @param AbsencesAT $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AbsencesAT $entity)
    {
        $form = $this->createForm(new AbsencesATType(), $entity, array(
            'action' => $this->generateUrl('ad_absencesat_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing AbsencesAT entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AbsencesAT entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ad_absencesat_edit', array('id' => $id)));
        }

        return $this->render('KbhGestionCongesBundle:Admin/AbsencesAT:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a AbsencesAT entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AbsencesAT entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ad_absencesat'));
    }

    /**
     * Creates a form to delete a AbsencesAT entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('absencesat_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    //    ############################## BASE DE DONNEES #############################
    
     /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function absATListeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/AbsencesAT:absencesAT.html.twig', array(
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
