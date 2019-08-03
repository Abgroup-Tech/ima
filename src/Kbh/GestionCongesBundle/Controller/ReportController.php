<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kbh\GestionCongesBundle\Entity\Report;
use Kbh\GestionCongesBundle\Form\ReportType;
use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Entity\Notification;
use Kbh\GestionCongesBundle\Entity\Conge;

/**
 * Report controller.
 *
 */
class ReportController extends Controller {

    /**
     * Lists all Report entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $managerconnecte = $this->getSalarieByUser();

        $entities = $em->getRepository('KbhGestionCongesBundle:Report')->findAll();
        $conges = $em->getRepository('KbhGestionCongesBundle:Conge')->findAll();
        $user = $this->getUser();

        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Superviseur\Report:index.html.twig', array(
                        'entities' => $entities,
                        'conges' => $conges,
                        'managerConnecte' => $managerconnecte
            ));
        }

        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Top-manager\Report:index.html.twig', array(
                        'entities' => $entities,
                        'conges' => $conges,
                        'managerConnecte' => $managerconnecte
            ));
        }
    }

    /**
     * Creates a new Report entity.
     *
     */
    public function createAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $report = new report();
        $form = $this->createCreateForm($report);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // $data = $form->getData();
            // $salarie = $data->getSalarie();
            $salarie = $report->getSalarie();
            $superviseur = $this->getSalarieByUser();

            // $report->setDateReport(new\Datetime());
            // $report->setConge($data['conge']);
            // $report->setSalarie($data['salarie']);
            // $report->setManager($superviseur);
            // $report->setTypeReport("Conge");
            //Mise à jour des dates du congé
            // $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->find($data['conge']);
            // $conge->setDateDebut($data->getDateDebut());
            // $conge->setDateFin($data->getDateFin());
            //@BHK:19052015
            $report->setDateReport(new\Datetime());

            $report->setManager($superviseur);
            $report->setTypeReport("Conge");

            //Mise à jour des dates du congé
            $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->find($report->getConge());
            $uniteDRH = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findOneByEstDrh(1);
            $report->setAncienneDateDebut($conge->getDateDebut()->format('Y-m-d'));
            $report->setAncienneDateFin($conge->getDateFin());
            $report->setAncienneDuree($conge->getNbJoursOuvrables());

            $conge->setDateDebut($report->getDateDebut());
            $conge->setDateFin($report->getDateFin());

            //notification
            $notification = new Notification();
            $notification->setValideurEncours($superviseur);
            $notification->setObservateur($uniteDRH->getManager());
            $notification->setSalarie($salarie);
            $notification->setDemande($conge->getDemande());
            $notification->setDateEnvoi(new \DateTime());

            $notification->setMessageDemandeur("Votre congé vient d'être reporté par " . $superviseur->getCivilite() . " " . $superviseur->getNomPrenom() . ".");
            $notification->setMessageValideurEncours("Vous avez reporté le congé du salarie " . $salarie . ".");
            $notification->setMessageFinal("M/Mme ".$superviseur." a reporté le congé du salarie " . $salarie . ".");


            $em->persist($conge);
            $em->persist($notification);
            $em->persist($report);
            $em->flush();


            if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
                return $this->redirect($this->generateUrl('report_show', array('id' => $report->getId())));
            }

            if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                return $this->redirect($this->generateUrl('top_manager_report_show', array('id' => $report->getId())));
            }
        }
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->redirect($this->generateUrl('reports')); //redirige vers la page mesdemandes
        }

        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->redirect($this->generateUrl('top_manager_reports')); //redirige vers la page mesdemandes
        }
    }

    /**
     * Displays a form to create a new Report entity.
     *
     */
    public function newAction($id) {
        $entity = new Report();
        $em = $this->getDoctrine()->getManager();
        $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->find($id);
        $manager = $this->getSalarieByUser();

        $entity->setConge($conge);
        $entity->setSalarie($conge->getSalarie());
        $entity->setManager($manager);
        $entity->setDateReport(new \DateTime());
        $entity->setTypeReport("Conge");
        $entity->setAncienneDateDebut($conge->getDateDebut());
        $entity->setAncienneDateFin($conge->getDateFin());
        $entity->setAncienneDuree($conge->getNbJoursOuvrables());
        $entity->setNbJoursOuvrables($entity->getAncienneDuree());

        $form = $this->calculCongeForm($entity);

        if (in_array("ROLE_SUPERVISEUR", $manager->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Superviseur\Report:new.html.twig', array(
                        'entity' => $entity,
                        'conge' => $conge,
                        'form' => $form->createView(),
            )); //redirige vers la page mesdemandes
        }

        if (in_array("ROLE_TOP_MANAGER", $manager->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Top-manager\Report:new.html.twig', array(
                        'entity' => $entity,
                        'conge' => $conge,
                        'form' => $form->createView(),
            )); //redirige vers la page mesdemandes
        }
    }

    /** ########## COTE  SALARIE #################
     * Finds and displays a Report entity .
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Report')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Report entity.');
        }

        return $this->render('KbhGestionCongesBundle:Report:show_2.html.twig', array(
                    'entity' => $entity,
        ));
    }

    /** ############## COTE SUPERVISEUR ################
     * Finds and displays a Report entity.
     *
     */
    public function showSupAction($id) {
        $em = $this->getDoctrine()->getManager();
        $manager = $this->getSalarieByUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:Report')->find($id);


        if (in_array("ROLE_SUPERVISEUR", $manager->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Superviseur\Report:show.html.twig', array(
                        'entity' => $entity,
            ));
        }

        if (in_array("ROLE_TOP_MANAGER", $manager->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Top-manager\Report:show.html.twig', array(
                        'entity' => $entity,
            ));
        }
    }

    /**
     * Calcul d'une demande de congé
     *
     */
    public function calculDemandeReportAction(Request $request, $id) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->find($id);
        $demande = $conge->getDemande();
        $report = new Report();
        $unite = $salarie->getUnite();
        $droits = $salarie->getDroits();
        $dateRetour = "";
        $form = $this->createCreateForm($report);
        $form->handleRequest($request);
        $nbDimanche = 0;
        $joursFeries = array();
        $cp = 0;


        if ($form->isValid()) {
            $droits = $salarie->getDroits();
            $unite = $salarie->getUnite();
            
            //Calcul des données de fin et retour.
            // Recuperation du contenu des champs nombre de jours et date de debut
            $nombreJours = $report->getNbJoursOuvrables() - 1;
            $dateDebut = $report->getDateDebut();

            //Conversion du nombre de jours et de la date de debut en secondes (timestamp)
            $nbSecondes = 60 * 60 * 24;
            //$tms_dateDebut = strtotime($dateDebut);        
            $tms_dateDebut = $dateDebut->getTimestamp();

            //Recuperation du jour de la date de debut
            $jour = getdate($tms_dateDebut); // On obtient ainsi le jour de la semaine 
            $jourDateDebut = $jour['wday'];

            /*             * ********************************************
             *   1ERE ETAPE DE VERIFICATION   *
             * ******************************************* */

            //creation du 1er tableau des jours des congés 
            $joursDesConges[0] = $jourDateDebut;
            $joursSup1 = 0;

            for ($i = 1; $i <= $nombreJours; $i++) {
                $joursDesConges[$i] = $joursDesConges[$i - 1] + 1;
                if ($joursDesConges[$i - 1] === 6) {
                    $joursDesConges[$i] = 0;
                }
            }

            //Verification du nombre de dimanches contenu dans la 1ere période
            for ($i = 1; $i <= $nombreJours; $i++) {
                if ($joursDesConges[$i] == 0) {
                    $joursSup1 +=1;
                    $nbDimanche = $joursSup1;
                }
            }


            //Calcul du nombre de jours total du congé sans les jours féries
            $nombreJoursTotal = $nombreJours + $joursSup1;

            //Conversion du nombre de jours total en timestamp
            $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

            //Addition du nombre de jours total et de la date de debut
            $tms_add = $tms_nbjTotal + $tms_dateDebut;

            //Conversion de la variable $tms_add en date afin d'obtenir la date de fin
            $dateFin = date('Y-m-d', $tms_add);

            /*             * ********************************************
             *   2EME ETAPE DE VERIFICATION   *
             * ******************************************** */

            //verification des jours fériés
            $tms_dateFin = strtotime($dateFin);

            //Recupération des jours fériés dans la base
            $em = $this->getDoctrine()->getManager();
            $feries = $em->getRepository('KbhGestionCongesBundle:Feries')->findAll();

            if (!$feries) {
                throw $this->createNotFoundException('Unable to find Feries entity.');
            }
            $nb_ferie = 0;
            foreach ($feries as $ferie) {
                $dateDebutFerie = $ferie->getDateDebutFerie()->format('Y-m-d');
                $dateFinFerie = $ferie->getDateFinFerie()->format('Y-m-d');

                //Conversion des valeurs récupérée en timestamp
                $tms_DDF = strtotime($dateDebutFerie);
                $tms_DFF = strtotime($dateFinFerie);

                //initialisation du nombre de jours fériés

                if ($tms_dateDebut < $tms_DDF && $tms_dateFin >= $tms_DFF) {
                    $nb_ferie += $ferie->getNbJoursFerie();
                    $titreFerie = $ferie->getTitreFeries();
                    $joursFeries[$cp] = $titreFerie;
                    $cp++;
                }
                if ($tms_dateFin == $tms_DDF && $tms_dateFin < $tms_DFF) {
                    $nb_ferie += $ferie->getNbJoursFerie();
                    $titreFerie = $ferie->getTitreFeries();
                    $joursFeries[$cp] = $titreFerie;
                    $cp++;
                }
            }
            // On rajoute au nombre total de jours le nombre de jours feries
            $nombreJoursTotal += $nb_ferie;

            //creation du tableau final des jours des congés 
            $joursDesConges[0] = $jourDateDebut;
            $joursSup2 = 0;

            for ($i = 1; $i <= $nombreJoursTotal; $i++) {
                $joursDesConges[$i] = $joursDesConges[$i - 1] + 1;
                if ($joursDesConges[$i - 1] === 6) {
                    $joursDesConges[$i] = 0;
                }
            }

            //On reverifit le nombre de dimanches contenu dans la nouvelle période
            for ($i = 1; $i <= $nombreJoursTotal; $i++) {
                if ($joursDesConges[$i] == 0) {
                    $joursSup2 +=1;
                    $nbDimanche = $joursSup2;
                }
            }


            //On rajoute le nombre de dimanches total au nombre de jours totale pour Obtenir la date de 
            if ($joursSup2 > $joursSup1) {
                $nombreJoursTotal = $nombreJoursTotal - $joursSup1;
                $nombreJoursTotal += $joursSup2;
            }

            $nbjf = $nombreJoursTotal;

            //Obtenir le nombre de jours ouvrabe

            $nombreJoursOuvrable = $nombreJoursTotal;

            //Conversion du nombre de jours total en timestamp
            $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

            //Addition du nombre de jours total et de la date de debut
            $tms_add = $tms_nbjTotal + $tms_dateDebut;

            //Conversion de la variable $tms_add pour obtenir la date finale
            $dateRetour = date('Y/m/d H:i:s', $tms_add);

            //vérifions que la date de retour ne soit pas un jour férié
            $nb_jr_ferie = 0;
            foreach ($feries as $ferie) {
                if ($dateRetour == $ferie->getDateDebutFerie()->format('Y-m-d') || $dateRetour == $ferie->getDateFinFerie()->format('Y-m-d')) {
                    $nb_jr_ferie += 1;
                    $titreFerie = $ferie->getTitreFeries();
                    $joursFeries[$cp] = $titreFerie;
                    $cp++;
                }
            }

            //On rajoute le nombre de jours féries au nombre de jours totale pour Obtenir la date de retour
            $nombreJoursTotal += $nb_jr_ferie;

            //Conversion du nombre de jours séparant la date de fin de la date de retour en timestamp
            $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

            //Addition du nombre de jours total et de la date de debut
            $tms_add = ($tms_nbjTotal + $nbSecondes ) + $tms_dateDebut;

            //Conversion de la variable $tms_add pour obtenir la date finale
            $dateRetour = date('Y/m/d', $tms_add);

            //************** DERNIERE VERIFICATION *********************//
            //Recuperation du jour de la date de retour

            $jour = getdate($tms_add); // On obtient ainsi le jour de la semaine 
            $jourDateRetour = $jour['wday'];

            if ($jourDateRetour == 0) {
                //On rajoute le nombre de dimanches total au nombre de jours totale pour Obtenir la date de retour
                $joursSup3 = 1;

                //Conversion du nombre de jours séparant la date de fin de la date de retour en timestamp
                $tms_nbjf = $joursSup3 * $nbSecondes;

                //Addition du nombre de jours total et de la date de debut
                $tms_add_fin = $tms_add + $tms_nbjf;


                //Conversion de la variable $tms_add pour obtenir la date finale
                $dateRetour = date('Y/m/d', $tms_add_fin);

//                var_dump($dateRetour);
                //vérifions que la date de retour ne soit pas un jour férié
                $nb_jr_ferie = 0;
                foreach ($feries as $ferie) {
                    if ($dateRetour == $ferie->getDateDebutFerie()->format('Y-m-d') || $dateRetour == $ferie->getDateFinFerie()->format('Y-m-d')) {
                        $nb_jr_ferie += 1;
                        $titreFerie = $ferie->getTitreFeries();
                        $joursFeries[$cp] = $titreFerie;
                        $cp++;
                    }
                }
                //On rajoute le nombre de dimanches total au nombre de jours totale pour Obtenir la date de retour
                $joursSup3 += $nb_jr_ferie;

                //Conversion du nombre de jours séparant la date de fin de la date de retour en timestamp
                $tms_nbjf = $joursSup3 * $nbSecondes;

                //Addition du nombre de jours total et de la date de debut
                $tms_add_fin = $tms_add + $tms_nbjf;


                //Conversion de la variable $tms_add pour obtenir la date finale
                $dateRetour = date('Y/m/d', $tms_add_fin);

                //*****************************************************************//
            }

            //pour trouver la date de fin
            $nombreJoursFin = $nbjf + $nb_jr_ferie;

            //Conversion du nombre de jours en timestamp
            $tms_nbjFin = $nombreJoursFin * $nbSecondes;

            //Addition du nombre de jours final et de la date de debut
            $tms_add_nbjF = ($tms_nbjFin ) + $tms_dateDebut;

            $datetFinale = date('Y/m/d H:i:s', $tms_add_nbjF);

            //on renseigne les résultats dans le conge
            $report->setDateFin($datetFinale);
            $report->setDateRetour($dateRetour);
            $report->setConge($report->getConge());
            $report->setSalarie($report->getSalarie());
            $report->setManager($report->getSalarie()->getSuperviseur());
            $report->setDateReport(new \DateTime());
            $report->setTypeReport("Conge");
            $report->setAncienneDateDebut($report->getConge()->getDateDebut());
            $report->setAncienneDateFin($report->getConge()->getDateFin());
            $report->setAncienneDuree($report->getNbJoursOuvrables());
            $report->setNbJoursOuvrables($report->getAncienneDuree());
            
//            var_dump($dateDebut);
//            var_dump($datetFinale);
//            var_dump($dateRetour);
//            die();
            
            
            if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Superviseur\Report:confirmation-demande-report.html.twig', array(
                            'entity' => $report,
                            'dateRetour' => $dateRetour,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'conge' => $conge,
                            'form' => $form->createView())
                );
            }

            if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Top-manager\Report:confirmation-demande-report.html.twig', array(
                            'entity' => $report,
                            'dateRetour' => $dateRetour,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'conge' => $conge,
                            'form' => $form->createView())
                );
            }
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Superviseur\Report:confirmation-demande-report.html.twig', array(
                        'entity' => $report,
                        'dateRetour' => $dateRetour,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'conge' => $conge,
                        'form' => $form->createView())
            );
        }

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Top-manager\Report:confirmation-demande-report.html.twig', array(
                        'entity' => $report,
                        'dateRetour' => $dateRetour,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'conge' => $conge,
                        'form' => $form->createView())
            );
        }
    }

//############################# FORMULAIRE DE CALCUL #############################
    /**
     * Créé le formulaire de de calcul du congé
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function calculCongeForm(Report $entity) {
        $form = $this->createForm(new ReportType(), $entity, array(
//            'action' => $this->generateUrl('calcul_demande_report'),
            'method' => 'POST',
        ));
        $form->add('salarie', 'entity', array('class' => 'KbhGestionCongesBundle:Salarie', 'property' => 'id'));
        $form->add('conge', 'entity', array('class' => 'KbhGestionCongesBundle:Conge', 'property' => 'id'));
        $form->add('manager', 'entity', array('class' => 'KbhGestionCongesBundle:Salarie', 'property' => 'id'));
        $form->add('typeReport', 'text');

        return $form;
    }

    /**
     * Creates a form to create a Report entity. 
     *
     * @param Report $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Report $entity) {
        $form = $this->createForm(new ReportType(), $entity, array(
            'action' => $this->generateUrl('report_conge_create'),
            'method' => 'POST',
        ));
        $form->add('salarie', 'entity', array('class' => 'KbhGestionCongesBundle:Salarie', 'property' => 'id'));
        $form->add('conge', 'entity', array('class' => 'KbhGestionCongesBundle:Conge', 'property' => 'id'));
        $form->add('manager', 'entity', array('class' => 'KbhGestionCongesBundle:Salarie', 'property' => 'id'));
        $form->add('typeReport', 'text');
        return $form;
    }

// 
//    /**
//     * Displays a form to edit an existing Report entity.
//     *
//     */
//    public function editAction($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('KbhGestionCongesBundle:Report')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find Report entity.');
//        }
//
//        $editForm = $this->createEditForm($entity);
//        
//
//        return $this->render('KbhGestionCongesBundle:Report:edit.html.twig', array(
//            'entity'      => $entity,
//            'edit_form'   => $editForm->createView(),
//          
//        ));
//    }
//    /**
//    * Creates a form to edit a Report entity.
//    *
//    * @param Report $entity The entity
//    *
//    * @return \Symfony\Component\Form\Form The form
//    */
//    private function createEditForm(Report $entity)
//    {
//        $form = $this->createForm(new ReportType(), $entity, array(
//            'action' => $this->generateUrl('report_update', array('id' => $entity->getId())),
//            'method' => 'PUT',
//        ));
//
//        $form->add('submit', 'submit', array('label' => 'Update'));
//
//        return $form;
//    }
    /**
     * Edits an existing Report entity.
     *
     */
//    public function updateAction(Request $request, $id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('KbhGestionCongesBundle:Report')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find Report entity.');
//        }
//
//        
//        $editForm = $this->createEditForm($entity);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isValid()) {
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('report_edit', array('id' => $id)));
//        }
//
//        return $this->render('KbhGestionCongesBundle:Report:edit.html.twig', array(
//            'entity'      => $entity,
//            'edit_form'   => $editForm->createView(),
//          
//        ));
//    }

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

}
