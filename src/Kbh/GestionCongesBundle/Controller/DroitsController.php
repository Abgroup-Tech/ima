<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kbh\GestionCongesBundle\Entity\Droits;
use Kbh\GestionCongesBundle\Form\DroitsType;
use Kbh\GestionCongesBundle\Entity\Document;
use Kbh\GestionCongesBundle\Form\DocumentType;
use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Entity\OrganigrammeUnite;

/**
 * Droits controller.
 *
 */
class DroitsController extends Controller {
    /*     * ********************* ADMINISTRATEUR******************************* */

    /**
     * Lists all Droits entities.
     *
     */
    public function indexAction() {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Droits')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Droits:droits-liste.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Creates a new Droits entity.
     *
     */
    public function createAction(Request $request) {
        $salarie = $this->getSalarieByUser();
        $entity = new Droits();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_droits_show', array('id' => $entity->getId())));
        }

        return $this->render('KbhGestionCongesBundle:Admin\Droits:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Droits entity.
     *
     * @param Droits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Droits $entity) {
        $form = $this->createForm(new DroitsType(), $entity, array(
            'action' => $this->generateUrl('admin_droits_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Droits entity.
     *
     */
    public function newAction() {
        $salarie = $this->getSalarieByUser();
        $entity = new Droits();
        $form = $this->createCreateForm($entity);

        return $this->render('KbhGestionCongesBundle:Admin\Droits:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Droits entity.
     *
     */
    public function showAction($id) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Droits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Droits entity.');
        }

        return $this->render('KbhGestionCongesBundle:Admin\Droits:show.html.twig', array(
                    'entity' => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Droits entity.
     *
     */
    public function editAction($id) {
        $salarie_connecte = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Droits')->find($id);

        $editForm = $this->createEditForm($entity);
        $erreur = "";
        
        $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Admin\Droits:edit.html.twig', array(
                    'entity' => $entity,
                    'erreur' => $erreur,
                    'form' => $editForm->createView(),
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Droits:edit.html.twig', array(
                    'entity' => $entity,
                    'erreur' => $erreur,
                    'form' => $editForm->createView(),
                ));
            }
            
    }

    /**
     * Creates a form to edit a Droits entity.
     *
     * @param Droits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Droits $entity) {
        $form = $this->createForm(new DroitsType(), $entity, array(
            'action' => $this->generateUrl('ad_droits_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    
        /**
     * Creates a form to edit a Droits entity.
     *
     * @param Droits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEdit2Form(Droits $entity) {
        $form = $this->createForm(new DroitsType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_droits_update2', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
                //Rajout de champs
        $form->add('cumulDroitsAcquis');
        $form->add('soldePermissions');
        $form->add('totalDroitsAprendre');
        return $form;
    }

    /**
     * Edits an existing Droits entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Droits')->find($id);
        $salarie = $entity->getSalarie();

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setCumulDroitsAcquis();
            $entity->setTotalDroitsAprendre();
            $entity->updateSoldePermissions($entity->getSoldePermissions());

            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "salariés";
            $action = "Modification des droits d'un salarié";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifié les droits du salarié ".$salarie->getNomprenom();
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

             $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_salarie_show', array('id' => $salarie->getId())));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
             return $this->redirect($this->generateUrl('sup_ad_salarie_show', array('id' => $salarie->getId())));
            }
        }

        $erreur = "Erreur de saisie";
        
        $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Admin\Droits:edit.html.twig', array(
                    'entity' => $entity,
                    'erreur' => $erreur,
                    'edit_form' => $editForm->createView(),
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Droits:edit.html.twig', array(
                    'entity' => $entity,
                    'erreur' => $erreur,
                    'edit_form' => $editForm->createView(),
                ));
            }
            
    }
    
        /**
     * Edits an existing Droits entity.
     *
     */
    public function update2Action(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Droits')->find($id);
        $salarie = $entity->getSalarie();

        $editForm = $this->createEdit2Form($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setCumulDroitsAcquis();
            $entity->setTotalDroitsAprendre();
            $entity->updateSoldePermissions($entity->getSoldePermissions());

            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "salariés";
            $action = "Modification des droits d'un salarié";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifié les droits du salarié ".$salarie->getNomprenom();
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

             $user_connected = $this->getUser();
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_droits_list'));
                    }
        }

            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
             return $this->redirect($this->generateUrl('sup_ad_droits_edit2', array('id' => $salarie->getId())));
            }
            
    }

    /*     * ********************* SUPERVISEUR******************************* */

    /**
     * Finds and displays a Droits entity.
     *
     */
    public function showDroitsSuperviseurAction() {
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();

        return $this->render('KbhGestionCongesBundle:Superviseur\Droits:detail-droits.html.twig', array(
                    'entity' => $droits,
                    'salarie' => $salarie
        ));
    }

    /**
     * Finds and displays a Droits entity.
     *
     */
    public function showCollabDroitsAction($salarie_id) {
        $verif = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($salarie_id);
        if (!$salarie) {
            throw $this->createNotFoundException('Unable to find Salarie entity.');
        }
        $droits = $em->getRepository('KbhGestionCongesBundle:Droits')->findOneBySalarie($salarie);

        if (!$droits) {
            throw $this->createNotFoundException('Unable to find Droits entity.');
        }

        return $this->render('KbhGestionCongesBundle:Superviseur\Droits:show.html.twig', array(
                    'entity' => $droits,
                    'salarie' => $salarie,
        ));
    }

    /**
     * Lists all Droits entities.
     *
     */
    public function showCollabsDroitsAction() {
        $salarie_connect = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        
        //Initialisation du formulaire d'import de fichier
        $document = new Document();
        $form = $this->createDocumentForm($document);
                
                
        $listSalarie = "";
        if ($salarie_connect->getPoste() == 'Directeur des Ressources Humaines' || $salarie_connect->getPoste() == 'Chef du service Administration et gestion de la paie' || $salarie_connect->getPoste() == 'Directeur Général') 
            {
            $result = $this->getDoctrine()->getManager()->getRepository('KbhGestionCongesBundle:Salarie')->findAll();
            
            $listSalarie = $result;

            // recupération des données de droits du personnel
            $total_droit_annee_encours = 0;
            $total_droit_annee_anterieurs = 0;
            $cumul_droit = 0;
            $total_droits_pris = 0;
            $total_permissions_prises = 0;
            $solde_permissions = 0;
            $solde_total_conges = 0;
            $nombre_personnel = 0;

            foreach ($result as $result) {
                if ($result->getPoste() != "Super Administrateur IMA"){
                   if ($result->getPoste() != "Administrateur IMA"){
                        if ($result->getStatutEmploi() != "Inactif"){
                            $droit = $result->getDroits();
                            $total_droit_annee_encours += $droit->getDroitsAcquisAnneeEnCours();
                            $total_droit_annee_anterieurs += $droit->getReliquatDroitsAnterieur();
                            $cumul_droit += $droit->getCumulDroitsAcquis();
                            $total_droits_pris += $droit->getDroitsPris();
                            $total_permissions_prises += $droit->getPermissionsPrises();
                            $solde_permissions += $droit->getSoldePermissions();
                            $solde_total_conges += $droit->getTotalDroitsAprendre();
                            $nombre_personnel += 1;
                      }
                   }
              }
            }
            
            if (in_array("ROLE_SUPERVISEUR", $salarie_connect->getUser()->getRoles())) {
               return $this->render('KbhGestionCongesBundle:Superviseur\Supervises:collaborateurs-droits.html.twig', array(
                        'entities' => $listSalarie,
                        'list_salaries' => $listSalarie,
                        'salarie' => $salarie_connect,
                        'total_droit_annee_encours' => $total_droit_annee_encours,
                        'total_droit_annee_anterieurs' => $total_droit_annee_anterieurs,
                        'cumul_droit' => $cumul_droit,
                        'total_droits_pris' => $total_droits_pris,
                        'total_permissions_prises' => $total_permissions_prises,
                        'solde_permissions' => $solde_permissions, 
                        'solde_total_conges' => $solde_total_conges,
                        'nombre_personnel' => $nombre_personnel, 
                        'form' => $form->createView(),
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $salarie_connect->getUser()->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Top-manager\Supervises:collaborateurs-droits.html.twig', array(
                        'entities' => $listSalarie,
                        'list_salaries' => $listSalarie,
                        'salarie' => $salarie_connect,
                        'total_droit_annee_encours' => $total_droit_annee_encours,
                        'total_droit_annee_anterieurs' => $total_droit_annee_anterieurs,
                        'cumul_droit' => $cumul_droit,
                        'total_droits_pris' => $total_droits_pris,
                        'total_permissions_prises' => $total_permissions_prises,
                        'solde_permissions' => $solde_permissions , 
                        'solde_total_conges' => $solde_total_conges,
                        'nombre_personnel' => $nombre_personnel , 
                        'form' => $form->createView(),
            ));
        }
        } 
        else {
            $entities = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();
            $salaries = array();

            //Récupération des salariés rattachés au manager connecté
            $unite = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->find($salarie_connect->getUnite());
            $salaries[0] = $unite->getSalaries();

            //Récupération des salariés rattachés à l'unité du manager connecté
            $cp = 1;
            foreach ($entities as $entity) {
                if ($entity->getUniteSuivante1() == $salarie_connect->getUnite() || $entity->getUniteSuivante2() == $salarie_connect->getUnite() || $entity->getUniteSuivante3() == $salarie_connect->getUnite()) {
                    $salaries[$cp] = $entity->getSalaries();
                }
                $cp += 1;
            }

//            $result = $this->getDoctrine()->getManager()->getRepository('KbhGestionCongesBundle:Salarie')->findAll();
//            $salaries = $result;
//            
            // recupération des données de droits du personnel
            $total_droit_annee_encours = 0;
            $total_droit_annee_anterieurs = 0;
            $cumul_droit = 0;
            $total_droits_pris = 0;
            $total_permissions_prises = 0;
            $solde_permissions = 0;
            $solde_total_conges = 0;
            $nombre_personnel = 0;

            foreach ($salaries as $listeSalaries) {
                 foreach ($listeSalaries as $salarie) {
                     if ($salarie->getPoste() != "Super Administrateur IMA"){
                         if ($salarie->getPoste() != "Administrateur IMA"){
                             if ($salarie->getStatutEmploi() != "Inactif"){
                        $droit = $salarie->getDroits();
                        $total_droit_annee_encours += $droit->getDroitsAcquisAnneeEnCours();
                        $total_droit_annee_anterieurs += $droit->getReliquatDroitsAnterieur();
                        $cumul_droit += $droit->getCumulDroitsAcquis();
                        $total_droits_pris += $droit->getDroitsPris();
                        $total_permissions_prises += $droit->getPermissionsPrises();
                        $solde_permissions += $droit->getSoldePermissions();
                        $solde_total_conges += $droit->getTotalDroitsAprendre();
                        $nombre_personnel += 1;
                              }
                         }
                      }
                 }   
          }  
          
          $user = $this->getUser();
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
               return $this->render('KbhGestionCongesBundle:Superviseur\Supervises:collaborateurs-droits.html.twig', array(
                        'entities' => $salaries,
                        'list_salaries' => $listSalarie,
                        'salarie' => $salarie_connect,
                        'total_droit_annee_encours' => $total_droit_annee_encours,
                        'total_droit_annee_anterieurs' => $total_droit_annee_anterieurs,
                        'cumul_droit' => $cumul_droit,
                        'total_droits_pris' => $total_droits_pris,
                        'total_permissions_prises' => $total_permissions_prises,
                        'solde_permissions' => $solde_permissions, 
                        'solde_total_conges' => $solde_total_conges,
                        'nombre_personnel' => $nombre_personnel, 
                        'form' => $form->createView(),
                ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Top-manager\Supervises:collaborateurs-droits.html.twig', array(
                        'entities' => $salaries,
                        'list_salaries' => $listSalarie,
                        'salarie' => $salarie_connect,
                        'total_droit_annee_encours' => $total_droit_annee_encours,
                        'total_droit_annee_anterieurs' => $total_droit_annee_anterieurs,
                        'cumul_droit' => $cumul_droit,
                        'total_droits_pris' => $total_droits_pris,
                        'total_permissions_prises' => $total_permissions_prises,
                        'solde_permissions' => $solde_permissions, 
                        'solde_total_conges' => $solde_total_conges,
                        'nombre_personnel' => $nombre_personnel, 
                        'form' => $form->createView(),
                ));
        }
        
        }
    }
    

    /**
    * Creates a form to get a Document entity.
    *
    * @param Document $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createDocumentForm(Document $entity)
    {
        $form = $this->createForm(new DocumentType(), $entity);
        $form->add('cible','choice',array(
                            'choices'=>array(
                                        'Calcul allocation'=>'Calcul allocation',
                                )
                ));
        return $form;
    }
    
    /**
     * Creates a new Document entity.
     *
     */
    public function importDocumentAllocationAction(Request $request)
    {
       $salarie = $this->getSalarieByUser(); 
        $entity = new Document();
        $form = $this->createDocumentForm($entity);
        $form->handleRequest($request);
        
//        var_dump($form);
//        die();
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            //création du document dans l'application
            $entity->setDateCreation(new \Datetime);
            $entity->setStatut("En attente");
            
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "documents";
            $action = "Import du fichier pour le calcul de l'allocation";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à importé le document ".$entity->getName();
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            if ($entity->getCible() == "Calcul allocation"){
                return $this->redirect($this->generateUrl('sup_document_allocation_import', array('id' => $entity->getId())));
            }
            
            return $this->redirect($this->generateUrl('ad_salarie_show',array('id'=>$id)));
            
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->redirect($this->generateUrl('collaborateur_droits'));
        }
//          if (in_array("ROLE_ADMIN", $salarie->getUser()->getRoles())) {
//            //Redirection vers la page de confirmation
//            return $this->redirect($this->generateUrl('welcome'));
//        }
    }
    
    
    /*     * ********************* SALARIE ******************************* */

    /**
     * Finds and displays a Droits entity.
     *
     */
    public function showDroitsSalarieAction() {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();

        return $this->render('KbhGestionCongesBundle:Salarie\Droits:details-droits.html.twig', array(
                    'droits' => $droits,
                    'salarie' => $salarie
        ));
    }

    /**
     * Calcul de l'allocation
     *
     */
    public function allocationCongesAction() {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();

        return $this->render('KbhGestionCongesBundle:Superviseur\Supervises:calcul-allocations.html.twig', array(
                    'droits' => $droits,
                    'salarie' => $salarie
        ));
    }
    
        /**
     * Displays a form to edit an existing Droits entity.
     *
     */
    public function edit2Action($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Droits')->find($id);
        $form = $this->createEdit2Form($entity);
        
        $erreur = "";
        
        $user_connected = $this->getUser();
        if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Droits:edit.html.twig', array(
                'entity' => $entity,
                'erreur' => $erreur,
                'form' => $form->createView(),
            ));
        }
            
    }
    
        /**
     * Lists all Droits entities.
     *
     */
    public function transfertAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Droits')->findAll();
        
        foreach($entities as $entity){
            //Récupération des droits
            $totalDroitsAcquis = $entity->getTotalDroitsAprendre(); // les droits restant avant la reinitailisation
            
            //Initialisation des droits
            $entity->setDroitsAcquisAnneeEnCours(0);
            $entity->setReliquatDroitsAnterieur(0);
            $entity->setCumulDroitsAcquis();
            $entity->reinitialisationDroitsPris();
            $entity->reinitialisationSoldePermissions();
            $entity->reinitialisationPermissionsPrises();
            $entity->setTotalDroitsAprendre();
            
            $em->persist($entity);
            $em->flush();
            
            //opération de mise à jours
            $entity->setReliquatDroitsAnterieur($totalDroitsAcquis);
            $entity->setCumulDroitsAcquis();
            $entity->setTotalDroitsAprendre();
           
            $em->persist($entity);
            $em->flush();
            
        }
        
        //Log action effectuée
        $salarieConnecte = $this->getSalarieByUser();
        $cible = "droits";
        $action = "reinitialisation des droits des salariés";
        $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à réinitialaisé les droits des salariés. ";
        $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
         return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
    }
    
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
            $logActivite->setIcon("icon-close");
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
        //8ème cas : cible concernant les unités
        if($cible == "droits"){
            $logActivite->setIcon("icon-calculator");
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
