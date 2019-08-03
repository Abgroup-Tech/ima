<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Kbh\GestionCongesBundle\Entity\Demande;
use Kbh\GestionCongesBundle\Entity\Document;
use Kbh\GestionCongesBundle\Form\DocumentType;
use Kbh\GestionCongesBundle\Entity\Conge;
use Kbh\GestionCongesBundle\Entity\LogUpdateMensuel;
use Kbh\GestionCongesBundle\Entity\LogUpdateAnnuel;
use Kbh\GestionCongesBundle\Entity\Absence;
use Kbh\GestionCongesBundle\Entity\Notification;
use Symfony\Component\Validator\Constraints\DateTime;


class DefaultController extends Controller {

    public function welcomeAction(Request $request) {
        //Recherche du salarié connecté
        $session = $request->getSession();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        //Verification de l'existance d'un salarié en base
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();
        $cp = count($salaries);
        if($cp == 0){
            // Il n'existe aucun salarié en base
            return $this->redirect($this->generateUrl('bienvenue'));
        }
        else {
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($user);
        if (!$salarie) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->redirect($this->generateUrl('dashboard_admin'));
        }
        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            return $this->redirect($this->generateUrl('dashboard_super_admin'));
        }
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->redirect($this->generateUrl('dashboard_superviseur'));
        }
        if (in_array("ROLE_SALARIE", $user->getRoles())) {
            return $this->redirect($this->generateUrl('dashboard_salarie'));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->redirect($this->generateUrl('dashboard_top_manager'));
        }
    }
 }

    public function dashboardSuperviseurAction() {
        //Vérification du rôle superviseur de l'utilisateur
        if (!in_array("ROLE_SUPERVISEUR", $this->getUser()->getRoles())) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        } else {
            $salarie = $this->getSalarieByUser();
            $droits = $salarie->getDroits();

            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findNewNotificationsForSalarie($salarie);
            $demandesAvalider = $em->getRepository('KbhGestionCongesBundle:Demande')->findDemandesAvalider($salarie);
            $new_notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findNewNotificationsNumber($salarie);

            //Décompte des nouvelles notifications du salarié connecté
            $cp = 0;
            foreach($new_notifications as $new){

                 if($new->getSalarie() == $salarie && $new->getVuParDemandeur() != 1){
                   $cp += 1;
                }

               //cas où le salarié connecté est le valideur en cours
                if($new->getValideurEnCours() == $salarie && $new->getVuParValideurEnCours() != 1){
                   $cp += 1;
                }

               //cas où le salarié connecté est le valideur précédent
                if($new->getValideurPrecedent() == $salarie && $new->getVuParValideurPrecedent() != 1){
                   $cp += 1;
                }

                //cas où le salarié connecté est l'observateur(DRH)
                if($new->getObservateur() == $salarie && $new->getVuParObservateur() != 1){
                   $cp += 1;
                }

                //cas où le salarié connecté est le responsable direct du demandeur
                if($new->getSuperieurN1() == $salarie && $new->getVuParSupN1() != 1 && $new->getSuperieurN1() != $new->getObservateur()){
                   $cp += 1;
                }

            }
            //Récupération du nombre de nouvelles notifs
            $number = $cp;

            $demandes = $em->getRepository('KbhGestionCongesBundle:Demande')->findBySalarie($salarie);
            //Recherche du dernier congé
            $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->findBySalarie($salarie);
            $count_conge = count($conge);


            $number_2 = count($demandesAvalider);

            // OPERATIONS POUR LES DIFFERENTES PERIODES DE DEPOT ET DE TRAITEMENTS
            //Récupération de la période de depot
            $depot = $em->getRepository('KbhGestionCongesBundle:PeriodeDepotDemandes')->find(1);

            //Récupération de la période de traitement
            $traitement = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseMensuel = $this->updateMensuel(); // Récupération de la reponse retournée
        $this->updateMensuel();

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseAnnuel = $this->updateAnnuel(); // Récupération de la reponse retournée
        $this->updateAnnuel();

            return $this->render('KbhGestionCongesBundle:Superviseur:index-superviseur.html.twig', array(
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'depot' => $depot,
                        'traitement' => $traitement,
                        'conge' => $conge,
                        'cp_conge' => $count_conge,
                        'new' => $number,
                        'new_2' => $number_2,
                        'notifications' => $notifications,
                        'demandesAvalider' => $demandesAvalider,
                        'entities' => $demandes,
                        'reponseMensuel' => $reponseMensuel,
                        'reponseAnnuel' => $reponseAnnuel,
            ));
        }
    }

    public function dashboardSalarieAction() {
        //Vérification du rôle salarie de l'utilisateur
        if (!in_array("ROLE_SALARIE", $this->getUser()->getRoles())) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        } else {
            $salarie = $this->getSalarieByUser();
            $droits = $salarie->getDroits();
            //Derniers évènements
            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findNewNotificationsForSalarie($salarie);
            $demandes = $em->getRepository('KbhGestionCongesBundle:Demande')->findBySalarie($salarie);
            $new_notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findBySalarie($salarie);

            //Décompte des nouvelles notifications du salarié connecté
            $cp = 0;
            foreach($new_notifications as $new){

                 if($new->getVuParDemandeur() != 1){
                   $cp += 1;
                }

            }
            //Récupération du nombre de nouvelles notifs
            $number = $cp;
            //Recherche du dernier congé
            $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->findBySalarie($salarie);
            $count_conge = count($conge);

            // OPERATIONS POUR LES DIFFERENTES PERIODES DE DEPOT ET DE TRAITEMENTS
            //Récupération de la période de depot
            $depot = $em->getRepository('KbhGestionCongesBundle:PeriodeDepotDemandes')->find(1);

            //Récupération de la période de traitement
            $traitement = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseMensuel = $this->updateMensuel(); // Récupération de la reponse retournée
        $this->updateMensuel();

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseAnnuel = $this->updateAnnuel(); // Récupération de la reponse retournée
        $this->updateAnnuel();

            return $this->render('KbhGestionCongesBundle:Salarie:index.html.twig', array(
                        'salarie' => $salarie,
                        'conge' => $conge,
                        'depot' => $depot,
                        'traitement' => $traitement,
                        'cp_conge' => $count_conge,
                        'entities' => $demandes,
                        'new' => $number,
                        'droits' => $droits,
                        'reponseMensuel' => $reponseMensuel,
                        'reponseAnnuel' => $reponseAnnuel,
                        'notifications' => $notifications
            ));
        }
    }

    public function dashboardAdminAction() {
        //Vérification du rôle admin de l'utilisateur
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        } else {
            $salarie = $this->getSalarieByUser();
            $droits = $salarie->getDroits();

            //Liste des unités de l'entreprise
            $em = $this->getDoctrine()->getManager();
            $unites = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();

            $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();
            $notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findNotificationForAdmin($salarie);

            //Formulaire d'import de documents
            $entity = new Document();
            $form   = $this->createDocumentForm($entity);
            $absencesAT = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findAllAbsences();

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseMensuel = $this->updateMensuel(); // Récupération de la reponse retournée
        $this->updateMensuel();

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseAnnuel = $this->updateAnnuel(); // Récupération de la reponse retournée
        $this->updateAnnuel();

        return $this->render('KbhGestionCongesBundle:Admin:index-admin.html.twig', array(
                        'salarie' => $salarie,
                        'salaries' => $salaries,
                        'notification' => $notifications,
                        'absencesAT' => $absencesAT,
                        'form' => $form->createView(),
                        'droits' => $droits,
                        'reponseMensuel' => $reponseMensuel,
                        'reponseAnnuel' => $reponseAnnuel,
                        'unites' => $unites
            ));
        }
    }

    public function dashboardSuperAdminAction() {
        //Vérification du rôle admin de l'utilisateur
        if (!in_array("ROLE_SUPER_ADMIN", $this->getUser()->getRoles())) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        } else {
            $salarie = $this->getSalarieByUser();
            $droits = $salarie->getDroits();

            //Liste des unités de l'entreprise
            $em = $this->getDoctrine()->getManager();
            $unites = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();

            $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();

            //Formulaire d'import de documents
            $entity = new Document();
            $form   = $this->createDocumentForm($entity);

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseMensuel = $this->updateMensuel(); // Récupération de la reponse retournée
        $this->updateMensuel();

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseAnnuel = $this->updateAnnuel(); // Récupération de la reponse retournée
        $this->updateAnnuel();

            return $this->render('KbhGestionCongesBundle:Super-Admin:index-admin.html.twig', array(
                        'salarie' => $salarie,
                        'salaries' => $salaries,
                        'form' => $form->createView(),
                        'droits' => $droits,
                        'reponseMensuel' => $reponseMensuel,
                        'reponseAnnuel' => $reponseAnnuel,
                        'unites' => $unites
            ));
        }
    }

    public function dashboardTopManagerAction() {
        //Vérification du rôle superviseur de l'utilisateur
        if (!in_array("ROLE_TOP_MANAGER", $this->getUser()->getRoles())) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        } else {
            $salarie = $this->getSalarieByUser();
            $droits = $salarie->getDroits();

            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findNewNotificationsForSalarie($salarie);
            $demandesAvalider = $em->getRepository('KbhGestionCongesBundle:Demande')->findDemandesAvalider($salarie);
            $new_notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findNewNotificationsNumber($salarie);

            //Décompte des nouvelles notifications du salarié connecté
            $cp = 0;
            foreach($new_notifications as $new){

                 if($new->getSalarie() == $salarie && $new->getVuParDemandeur() != 1){
                   $cp += 1;
                }

               //cas où le salarié connecté est le valideur en cours
                if($new->getValideurEnCours() == $salarie && $new->getVuParValideurEnCours() != 1){
                   $cp += 1;
                }

               //cas où le salarié connecté est le valideur précédent
                if($new->getValideurPrecedent() == $salarie && $new->getVuParValideurPrecedent() != 1){
                   $cp += 1;
                }

                //cas où le salarié connecté est l'observateur(DRH)
                if($new->getObservateur() == $salarie && $new->getVuParObservateur() != 1){
                   $cp += 1;
                }

                //cas où le salarié connecté est le responsable direct du demandeur
                if($new->getSuperieurN1() == $salarie && $new->getVuParSupN1() != 1 && $new->getSuperieurN1() != $new->getObservateur()){
                   $cp += 1;
                }

            }
            //Récupération du nombre de nouvelles notifs
            $number = $cp;

            $demandes = $em->getRepository('KbhGestionCongesBundle:Demande')->findBySalarie($salarie);
            //Recherche du dernier congé
            $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->findBySalarie($salarie);
            $count_conge = count($conge);


            $number_2 = count($demandesAvalider);

            // OPERATIONS POUR LES DIFFERENTES PERIODES DE DEPOT ET DE TRAITEMENTS
            //Récupération de la période de depot
            $depot = $em->getRepository('KbhGestionCongesBundle:PeriodeDepotDemandes')->find(1);

            //Récupération de la période de traitement
            $traitement = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseMensuel = $this->updateMensuel(); // Récupération de la reponse retournée
        $this->updateMensuel();

        //################## NOTIFICATIONS DES UPDATES MENSUELLES #################
        $reponseAnnuel = $this->updateAnnuel(); // Récupération de la reponse retournée
        $this->updateAnnuel();

            return $this->render('KbhGestionCongesBundle:Top-manager:index-manager.html.twig', array(
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'depot' => $depot,
                        'traitement' => $traitement,
                        'conge' => $conge,
                        'cp_conge' => $count_conge,
                        'new' => $number,
                        'new_2' => $number_2,
                        'notifications' => $notifications,
                        'demandesAvalider' => $demandesAvalider,
                        'entities' => $demandes,
                        'reponseMensuel' => $reponseMensuel,
                        'reponseAnnuel' => $reponseAnnuel,
            ));
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
                                        'Salariés'=>'Salariés',
                                        'Unités'=>'Unités',
//                                      'Calcul allocation'=>'Calcul allocation',
                                )
                ));
        return $form;
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function createDocumentAction(Request $request)
    {
       $salarie = $this->getSalarieByUser();
        $entity = new Document();
        $form = $this->createDocumentForm($entity);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //création du document dans l'application
            $entity->setDateCreation(new \Datetime);
            $entity->setStatut("En attente");

            $em->persist($entity);
            $em->flush();

            if ($entity->getCible() == "Salariés"){
            return $this->redirect($this->generateUrl('ad_document_sa_import', array('id' => $entity->getId())));
            }
            if ($entity->getCible() == "Unités"){
            return $this->redirect($this->generateUrl('ad_document_unit_import', array('id' => $entity->getId())));
           }

        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->redirect($this->generateUrl('collaborateur_droits'));
        }
          if (in_array("ROLE_ADMIN", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->redirect($this->generateUrl('welcome'));
        }
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function saEvenementsAction() {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findAllNotificationForUser($salarie);

        return $this->render('KbhGestionCongesBundle:Salarie\Events:events.html.twig', array(
                    'salarie' => $salarie,
                    'notifications' => $notifications,
        ));
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function historiqueEventAction() {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findAllNotificationForAdmin($salarie);

        return $this->render('KbhGestionCongesBundle:Admin\Events:events.html.twig', array(
                    'salarie' => $salarie,
                    'notifications' => $notifications,
        ));
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function historiqueActivitesAction() {
        $salarie = $this->getSalarieByUser();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $activitesAdmin = $em->getRepository('KbhGestionCongesBundle:LogActivites')->findActivites($salarie);
        $activitesSupAdmin = $em->getRepository('KbhGestionCongesBundle:LogActivites')->findActivitesSupAdmin();

        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Admin\Events:activites.html.twig', array(
                    'salarie' => $salarie,
                    'notifications' => $activitesAdmin,
        ));
        }
        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Super-Admin\Events:activites.html.twig', array(
                    'salarie' => $salarie,
                    'notifications' => $activitesSupAdmin,
        ));
        }

    }

        /**
     */
    public function historiqueMisesAjoursAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $salarie = $this->getSalarieByUser();

        $logAnnuel = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->findAll();
        $logMensuel = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->findAll();
        $logCache = $em->getRepository('KbhGestionCongesBundle:LogCacheClear')->findAll();

        if (in_array("ROLE_ADMIN", $user->getRoles())) {
           return $this->render('KbhGestionCongesBundle:Admin\Events:mises-a-jours.html.twig', array(
                    'salarie' => $salarie,
                    'LogAnnuel' => $logAnnuel,
                    'LogMensuel' => $logMensuel,
                    'LogCache' => $logCache,
        ));
        }
        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Super-Admin\Events:mises-a-jours.html.twig', array(
                    'salarie' => $salarie,
                    'LogAnnuel' => $logAnnuel,
                    'LogMensuel' => $logMensuel,
                    'LogCache' => $logCache,
        ));
        }
    }


     /**
     * Creates a new Document entity.
     *
     */
    public function searchMonthAction($month) {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->findMonthUpdate($month);
        $logAnnuel = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->findAll();

       if (in_array("ROLE_ADMIN", $user->getRoles())) {
           return $this->render('KbhGestionCongesBundle:Admin\Events:search-mises-a-jours.html.twig', array(
                    'LogAnnuel' => $logAnnuel,
                    'LogMensuel' => $result,
                    'month' => $month,
        ));
        }
        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Super-Admin\Events:search-mises-a-jours.html.twig', array(
                    'LogAnnuel' => $logAnnuel,
                    'LogMensuel' => $result,
                    'month' => $month,
        ));
        }
    }

     /**
     * Creates a new Document entity.
     *
     */
    public function searchMonthActivitiesAction($month) {
        $salarie = $this->getSalarieByUser();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository('KbhGestionCongesBundle:LogActivites')->findMonthActivities($month, $salarie);
//        $activitesAdmin = $em->getRepository('KbhGestionCongesBundle:LogActivites')->findActivites($salarie);
//        $activitesSupAdmin = $em->getRepository('KbhGestionCongesBundle:LogActivites')->findActivitesSupAdmin();

//        var_dump(count($result));
//        die();

       if (in_array("ROLE_ADMIN", $user->getRoles())) {
           return $this->render('KbhGestionCongesBundle:Admin\Events:search-activites.html.twig', array(
                    'salarie' => $salarie,
                    'notifications' => $result,
                    'month' => $month,
        ));
        }
        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Super-Admin\Events:search-activites.html.twig', array(
                    'salarie' => $salarie,
                    'notifications' => $result,
                    'month' => $month,
        ));
        }
    }

     /**
     * Creates a new Document entity.
     *
     */
    public function searchYearAction($year) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $logMensuel = $em->getRepository('KbhGestionCongesBundle:LogUpdateMensuel')->findAll();
        $result = $em->getRepository('KbhGestionCongesBundle:LogUpdateAnnuel')->findByAnnee($year);

        if (in_array("ROLE_ADMIN", $user->getRoles())) {
          return $this->render('KbhGestionCongesBundle:Admin\Events:search-mises-a-jours-annuel.html.twig', array(
                    'LogAnnuel' => $result,
                    'LogMensuel' => $logMensuel,
                    'year' => $year,
        ));
        }
        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Super-Admin\Events:search-mises-a-jours-annuel.html.twig', array(
                    'LogAnnuel' => $result,
                    'LogMensuel' => $logMensuel,
                    'year' => $year,
        ));
        }
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function imagesProfilAction() {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $media = $em->getRepository('KbhGestionCongesBundle:Media')->findAll();
        $unite = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();

        return $this->render('KbhGestionCongesBundle:Admin\Entreprise:medias.html.twig', array(
                    'salarie' => $salarie,
                    'unite' => $unite,
                    'media' => $media,
                    'salaries' =>$salaries,
        ));
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function supEvenementsAction() {   // Affiche la page des évenements du superviseur + ses collaborateurs
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository('KbhGestionCongesBundle:Notification')->findAllNewNotificationsForSuperviseur($salarie);

        return $this->render('KbhGestionCongesBundle:Superviseur\Events:events.html.twig', array(
                    'salarie' => $salarie,
                    'notifications' => $notifications,
        ));
    }

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

    /*     * ********************************************************************
     *                        PLANNING DES CONGES
     * ******************************************************************** */

    /**
     */
    public function planningAction() {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $superviseur = $this->getSalarieByUser();
        $conges = $em->getRepository('KbhGestionCongesBundle:Conge')->findAll();
        $absences = $em->getRepository('KbhGestionCongesBundle:Absence')->findAll();
        $absencesAT = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findAll();
        $demandes = $em->getRepository('KbhGestionCongesBundle:Demande')->findByEstEnCours(1);
        $feries = $em->getRepository('KbhGestionCongesBundle:Feries')->findAll();

        //Cas spéciaux
            $listeSalaries = "";
            $salaries = "";
            $nb_conges2 = "";
            $nb_demandes2 = "";
            $nb_permissions2 = "";
            $nb_absAT2 = "";
            $demandesSalaries = "";
            $congesSalaries = "";
            $permissionsSalaries = "";
            $absencesATSalaries = "";

            if ($superviseur->getPoste() == 'Directeur des Ressources Humaines' || $superviseur->getPoste() == 'Chef du service Administration et gestion de la paie' || $superviseur->getPoste()  == 'Directeur Général'){
            $listeSalaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();
            }
            else {
           $entities = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();
            $salaries= array();

            //Récupération des salariés rattachés au manager connecté
            $unite = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->find($superviseur->getUnite());
            $salaries[0] = $unite->getSalaries();

            //Récupération des salariés rattachés à l'unité du manager connecté
            $cp=1;
            foreach ($entities as $entity)
               {
                     if($entity->getUniteSuivante1() == $superviseur->getUnite() || $entity->getUniteSuivante2() == $superviseur->getUnite() || $entity->getUniteSuivante3() == $superviseur->getUnite())
                    {
                         $salaries[$cp] = $entity->getSalaries();
                     }
                    $cp += 1;
                }
//            var_dump(count($salaries));

            //TRAITEMENT ADDITIONNEL DU 02/10/2015
            //Recherchons les demandes, conges et permissions liées aux salariés en dessous du responsable connecté
            $listeDesSalaries = $salaries; // un ensemble de tableau contenant des liste de salariés
            $demandesSalaries = array();
            $congesSalaries = array();
            $permissionsSalaries = array();
            $absencesATSalaries = array();
            $i = 0;
            $e = 0;
            $o = 0;
            $z = 0;

            //Recherchons la liste des demandes des salariés en dessous de responsable connecté
            foreach ($listeDesSalaries as $salaries){// pour chaque elements du tableau contenant lui même un tableau de valeurs
                 foreach ($salaries as $salarie){
                     foreach ($demandes as $demande){ //parcourons la listes des demandes et verifions que le salarié possède une demande à son nom
                         if($demande->getSalarie()->getId() == $salarie->getId() ){
                            $demandesSalaries[$i] = $demande; // La liste des demandes des salariés en dessous du reponsable connecté
                            $i += 1;
                         }
                     }
                }
            }
            //Recherchons la liste des conges des salariés en dessous de responsable connecté
            foreach ($listeDesSalaries as $salaries){// pour chaque elements du tableau contenant lui même un tableau de valeurs
//                var_dump($salaries[1]);
                foreach ($salaries as $salarie){
//                    var_dump($salarie->getNom());
                     foreach ($conges as $conge){ //parcourons la listes des conges et verifions que le salarié possède une demande à son nom
                         if($conge->getSalarie()->getId() == $salarie->getId() ){
                            $congesSalaries[$e] = $conge; // La liste des conges des salariés en dessous du reponsable connecté
                            $e += 1;
//                            var_dump("conge de ".$conge->getSalarie());
                         }
                     }
                }
            }
//            var_dump(count($congesSalaries));
//            var_dump($e);
//            die();
            //Recherchons la liste des permissions des salariés en dessous de responsable connecté
            foreach ($listeDesSalaries as $salaries){// pour chaque elements du tableau contenant lui même un tableau de valeurs
                 foreach ($salaries as $salarie){
                     foreach ($absences as $absence){ //parcourons la listes des permissions et verifions que le salarié possède une demande à son nom
                         if($absence->getSalarie()->getId() == $salarie->getId() ){
                            $permissionsSalaries[$o] = $absence; // La liste des permissions des salariés en dessous du reponsable connecté
                            $o += 1;
                         }
                     }
                }
            }

            //Recherchons la liste des absences liées aux arrêt de travail des salariés en dessous de responsable connecté
            foreach ($listeDesSalaries as $salaries){// pour chaque elements du tableau contenant lui même un tableau de valeurs
                 foreach ($salaries as $salarie){
                     foreach ($absencesAT as $absenceAT){ //parcourons la listes des permissions et verifions que le salarié possède une demande à son nom
                         if($absenceAT->getSalarie()->getId() == $salarie->getId() ){
                            $absencesATSalaries[$z] = $absenceAT; // La liste des absences liées aux arrêt de travail des salariés en dessous du reponsable connecté
                            $z += 1;
                         }
                     }
                }
            }

            //nombre de conges, de permissions, et de demandes coté Superviseur
            $nb_conges2 = count($congesSalaries);
            $nb_demandes2 = count($demandesSalaries);
            $nb_permissions2 = count($permissionsSalaries);
            $nb_absAT2 = count($absencesATSalaries);

           }

            //nombre de conges, de permissions, et de demandes coté top manager
            $nb_conges = count($conges);
            $nb_demandes = count($demandes);
            $nb_permissions = count($absences);
            $nb_absAT = count($absencesAT);
            $nb_feries = count($feries);

//            var_dump($nb_absAT2);
//            var_dump($nb_permissions2);
//            var_dump(count($demandesSalaries));
//            var_dump($nb_demandes);
//            var_dump(count($salaries));
//            die();


        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
           return $this->render("KbhGestionCongesBundle:Superviseur\Planning:planning-conges.html.twig", array(
                    'salaries' => $salaries,
                    'nb_feries' => $nb_feries,
                    'nb_conges2' => $nb_conges2,
                    'nb_demandes2' => $nb_demandes2,
                    'nb_permissions2' => $nb_permissions2,
                    'nb_absAT2' => $nb_absAT2,
                    'conges2' => $congesSalaries,
                    'absencesAT2' => $absencesATSalaries,
                    'absences2' => $permissionsSalaries,
                    'demandes2' => $demandesSalaries,
                    'feries' => $feries,
        ));
        }

        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                 return $this->render("KbhGestionCongesBundle:Top-manager\Planning:planning-conges.html.twig", array(
                         'salaries' => $salaries,
                         'collabs_liste' => $listeSalaries,
                         'nb_conges' => $nb_conges,
                         'nb_demandes' => $nb_demandes,
                         'nb_permissions' => $nb_permissions,
                         'nb_feries' => $nb_feries,
                         'nb_absAT' => $nb_absAT,
                         'conges' => $conges,
                         'absences' => $absences,
                         'demandes' => $demandes,
                         'absencesAT' => $absencesAT,
                         'nb_conges2' => $nb_conges2,
                         'nb_demandes2' => $nb_demandes2,
                         'nb_permissions2' => $nb_permissions2,
                         'nb_absAT2' => $nb_absAT2,
                         'conges2' => $congesSalaries,
                         'absencesAT2' => $absencesATSalaries,
                         'absences2' => $permissionsSalaries,
                         'demandes2' => $demandesSalaries,
                          'feries' => $feries,
             ));
        }

    }

    /*     * ********************************************************************
     *                                             CALENDRIER
     * ******************************************************************** */

    /**
     */
    public function calendrierAction() {

        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($user);

        if (!$salarie) {
            throw $this->createNotFoundException('Unable to find Salarie entity.');
        }

        $feries = $em->getRepository('KbhGestionCongesBundle:Feries')->findAll();

        if (!$feries) {
            throw $this->createNotFoundException('Unable to find Feries entity.');
        }
        return $this->render("KbhGestionCongesBundle:Default:calendrier.html.twig", array(
                    'salarie' => $salarie,
                    'feries' => $feries,
        ));
    }

    /**
     * Action pour marquer toutes les notifications comme lues
     */
    public function notificationsLuesAction(){
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $salarie = $this->getSalarieByUser();
        $notificationsSalarie = $em->getRepository('KbhGestionCongesBundle:Notification')->findBySalarie($salarie);
        $notificationsManager = $em->getRepository('KbhGestionCongesBundle:Notification')->findNewNotificationsNumber($salarie);
        $cp = 0;
        //Tri des nouvelles notifications du salarié connecté
        foreach($notificationsSalarie as $new){
            //Update new notifications
            if($new->getVuParDemandeur() != 1){
                $new->setVuParDemandeur(1);
                $em->persist($new);
                $em->flush();
            }
        }

        //Tri des nouvelles notifications du manager connecté
        foreach($notificationsManager as $new){
            //Update new notifications
             if($new->getSalarie() == $salarie && $new->getVuParDemandeur() != 1){
                $new->setVuParDemandeur(1);
                 $em->persist($new);
                 $em->flush();
            }

            //cas où le salarié connecté est le valideur en cours
            if($new->getValideurEnCours() == $salarie && $new->getVuParValideurEnCours() != 1){
                $new->setVuParValideurEnCours(1);
                $em->persist($new);
                $em->flush();
            }

            //cas où le salarié connecté est le valideur précédent
            if($new->getValideurPrecedent() == $salarie && $new->getVuParValideurPrecedent() != 1){
                $new->setVuParValideurPrecedent(1);
                $em->persist($new);
                $em->flush();
            }

            //cas où le salarié connecté est l'observateur(DRH)
            if($new->getObservateur() == $salarie && $new->getVuParObservateur() != 1){
                $new->setVuParObservateur(1);
                $em->persist($new);
                $em->flush();
            }

            //cas où le salarié connecté est le responsable direct du demandeur
            if($new->getSuperieurN1() == $salarie && $new->getVuParSupN1() != 1 && $new->getSuperieurN1() != $new->getObservateur()){
                $new->setVuParSupN1(1);
                $em->persist($new);
                $em->flush();
            }
        }

        if (in_array("ROLE_SALARIE", $user->getRoles())) {
            return $this->redirect($this->generateUrl('sa_events'));
        }
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->redirect($this->generateUrl('sup_events'));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->redirect($this->generateUrl('top_manager_events'));
        }
    }


    //################################ FONCTION DE MISES A JOURS #################################

    /**
     * function for execute the monthly updating .
     *
     */
    public function updateMensuel() {
        $em = $this->getDoctrine()->getManager();
        $paramEntreprise = $em->getRepository('KbhGestionCongesBundle:Entreprise')->find(1);
        $paramCalculsDroit = $em->getRepository('KbhGestionCongesBundle:Paramcalculsdroits')->find(1);


        //Déclaration des variables
        $dureeMensuel = $paramEntreprise->getDelaisMiseAjours(); // 30
        $droitsMensuel = $paramCalculsDroit->getBaseDroitsAcquis(); // 2.2
        $today = new \DateTime();
        $dateMensuel = $paramEntreprise->getDateUpdateMensuel();

        //conversion en timestamp
        $tms_date = strtotime($dateMensuel);
        $tms_today = $today->getTimestamp();
         $resultat = 0;
        //Traitement de la mise à jours mensuelle
        if($tms_today >= $tms_date){
             $resultat = 1;
            //Récupération de tout les salariés actif du système
            $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findByStatutEmploi("Actif");

            //Rajout du nombre de jours mensuel dû à chaque salariés
            foreach ($salaries as $salarie){
                //Log avant la mise à jours
                $logMensuel = new LogUpdateMensuel();
                $logMensuel->setSalarie($salarie);

                $droit = $salarie->getDroits();
                $logMensuel->setAncienSolde($salarie->getDroits()->getTotalDroitsAprendre());
                $logMensuel->setDateUpdate(new \DateTime());

                //Récupération de la valeur des droits acquis l'année en cours
                $droitAcquis = $droit->getDroitsAcquisAnneeEnCours();
                $ajout = $droitAcquis + $droitsMensuel;

                //Initialisation du compteur
                $droit->setDroitsAcquisAnneeEnCours(0);

                //Ajout du nouveau solde
                $droit->setDroitsAcquisAnneeEnCours($ajout);
                $droit->setCumulDroitsAcquis();
                $droit->setTotalDroitsAprendre();
                $logMensuel->setNouveauSolde($salarie->getDroits()->getTotalDroitsAprendre());

                //Détermination de la date suivante de mise à jour mensuelle
                $tms_delaisMensuel = ($dureeMensuel * (60*60*24));
                $tms_nouvelle_date = $tms_delaisMensuel + $tms_date ;

                //nouvelle date de l'update
                $Date = date('Y-m-d', $tms_nouvelle_date);

                //Update de la prochaine date de mise à jours mensuel
                $paramEntreprise->setDateUpdateMensuel($Date);


                $em->persist($logMensuel);
                $em->persist($droit);
                $em->persist($paramEntreprise);
                $em->flush();

            }


                //Log de l'action éffectuée
                $salarieConnecte = $this->getSalarieByUser();
                $cible = "mises à jours";
                $action = "Mise à jours mensuelle";
                $msg = "Mise à jours mensuelle effectuée";
                $this->logActivite($salarieConnecte, $cible, $action, $msg);
        }

        if($resultat == 1){
           $statut= true ;
        }
        if($resultat == 0){
          $statut= false  ;
        }

        return $statut;
    }

       /**
     * function for execute the monthly updating .
     *
     */
    public function updateAnnuel() {
        $em = $this->getDoctrine()->getManager();
        $paramEntreprise = $em->getRepository('KbhGestionCongesBundle:Entreprise')->find(1);
        $baseDroits = $em->getRepository('KbhGestionCongesBundle:BaseDroits')->findAll();


        //Déclaration des variables
        $dureeAnnuel = $paramEntreprise->getDelaisMiseAjoursAnnuel();
//      $droitsMensuel = $paramCalculsDroit->getBaseDroitsAcquis();
        $today = new \DateTime();
        $dateAnnuel = $paramEntreprise->getDateUpdateAnnuel();

        //conversion en timestamp
        $tms_date = strtotime($dateAnnuel);
        $tms_today = $today->getTimestamp();
        $resultat = 0;
        $year = date('Y', $tms_today);

        //Traitement de la mise à jours mensuelle
        if($tms_today >= $tms_date){
            $resultat = 1;
            //Récupération de tout les salariés actif du système
            $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findByStatutEmploi("Actif");

            //Recherche des salariés ayant des droits supplémentaires
            foreach ($baseDroits as $baseDroit){
            //Rajout du nombre de jours annuel dû à chaque salariés
                foreach ($salaries as $salarie){
                    if($baseDroit->getSalarie() == $salarie){
                        //Log avant la mise à jours
                        $droit = $salarie->getDroits();
                        $logAnnuel = new LogUpdateAnnuel();
                        $logAnnuel->setSalarie($salarie);
                        $logAnnuel->setAnnee($year);
                        $logAnnuel->setDateUpdate(new \DateTime());
                        $logAnnuel->setJourSupAnnuel($baseDroit->getJoursSupAnnuel());
                        $logAnnuel->setAncienSolde($droit->getTotalDroitsAprendre());
                        $logAnnuel->setStatus(true);

                        //Récupération de la valeur des droits acquis l'année en cours
                        $droitAcquis = $droit->getDroitsAcquisAnneeEnCours();
                        $ajout = $droitAcquis + $baseDroit->getJoursSupAnnuel();

                         //Initialisation du compteur
                        $droit->setDroitsAcquisAnneeEnCours(0);

                        //Ajout du nouveau solde
                        $droit->setDroitsAcquisAnneeEnCours($ajout);
                        $droit->setCumulDroitsAcquis();
                        $droit->setTotalDroitsAprendre();

                        //Détermination de la date suivante de mise à jour annuelle
                        $tms_delaisAnnuel = ($dureeAnnuel * (60*60*24));
                        $tms_nouvelle_date = $tms_delaisAnnuel + $tms_date ;

                        //nouvelle date de l'update
                        $Date = date('Y-m-d', $tms_nouvelle_date);

                        //Update de la prochaine date de mise à jours mensuel
                        $paramEntreprise->setDateUpdateAnnuel($Date);


                        $em->persist($logAnnuel);
                        $em->persist($droit);
                        $em->persist($paramEntreprise);
                        $em->flush();

                    }

                }
               }
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "mises à jours";
            $action = "Mise à jours annuelle";
            $msg = "Mise à jours annuelle effectuée";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
        }

        if($resultat == 1){
           $statut= true ;
        }
        if($resultat == 0){
          $statut= false  ;
        }

        return $statut;
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
