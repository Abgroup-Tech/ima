<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kbh\GestionCongesBundle\Entity\Demande;
use Kbh\GestionCongesBundle\Entity\Conge;
use Kbh\GestionCongesBundle\Entity\Absence;
use Kbh\GestionCongesBundle\Entity\AbsencesAjustifier;
use Kbh\GestionCongesBundle\Entity\Report;
use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Entity\Droits;
use Kbh\GestionCongesBundle\Entity\Parampermissions;
use Kbh\GestionCongesBundle\Entity\DemandesDeposees;
use Kbh\GestionCongesBundle\Entity\Notification;
use Kbh\GestionCongesBundle\Form\DemandeType;
use Kbh\GestionCongesBundle\Form\DemandeForSalarieType;
use Kbh\GestionCongesBundle\Form\DemandeBySuperviseurType;
use Kbh\GestionCongesBundle\Form\ValidationDemandeType;
use Kbh\GestionCongesBundle\Entity\HistoriqueDroits;

/**
 * Demande controller.
 *
 */
class DemandeController extends Controller {
    /*     * ************************ SUPERVISEUR ******************************************************** */

    /**
     * Affiche la liste de toutes ses demandes(congés et absences)
     *    
     */
    public function supDemandesAction() {
        $em = $this->getDoctrine()->getManager();
        $salarieConnecte = $this->getSalarieByUser();
        $superviseurN1 = $salarieConnecte->getSuperviseur();

        $entities = $em->getRepository('KbhGestionCongesBundle:Demande')->findBySalarie($salarieConnecte);
        $reports = $em->getRepository('KbhGestionCongesBundle:Report')->findBySalarie($salarieConnecte);

        if (in_array("ROLE_TOP_MANAGER", $salarieConnecte->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-list.html.twig', array(
                        'entities' => $entities,
                        'reports' => $reports,
                        'superviseurN1' => $superviseurN1,
            ));
        }
        if (in_array("ROLE_SUPERVISEUR", $salarieConnecte->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-list.html.twig', array(
                        'entities' => $entities,
                        'reports' => $reports,
                        'superviseurN1' => $superviseurN1,
            ));
        }
    }

    /**
     * Trouve et Affiche une demande
     * 
     */
    public function showSupDemandeAction($id) {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();

        $entreprise = $em->getRepository('KbhGestionCongesBundle:Entreprise')->find(1);
        $entity = $em->getRepository('KbhGestionCongesBundle:Notification')->find($id);
        $reports = $em->getRepository('KbhGestionCongesBundle:Report')->findAll();
        $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->findAll();
        $demande = $entity->getDemande();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notification entity.');
        }

        //modification de l'état de la notification : cas du demandeur
        if ($entity->getSalarie() == $salarie) {
            $entity->setVuParDemandeur(1);
            $em->flush();
        }

        //cas où le salarié connecté est le valideur en cours 
        if ($entity->getValideurEnCours() == $salarie) {
            $entity->setVuParValideurEnCours(1);
            $em->flush();
        }

        //cas où le salarié connecté est le valideur précédent
        if ($entity->getValideurPrecedent() == $salarie) {
            $entity->setVuParValideurPrecedent(1);
            $em->flush();
        }

        //cas où le salarié connecté est l'observateur(DRH)
        if ($entity->getObservateur() == $salarie) {
            $entity->setVuParObservateur(1);
            $em->flush();
        }

        //cas où le salarié connecté est le responsable direct du demandeur
        if ($entity->getSuperieurN1() == $salarie) {
            $entity->setVuParSupN1(1);
            $em->flush();
        }

        $valide_Form = $this->ValideDemandeForm($demande);
        $refus_Form = $this->RefusDemandeForm($demande);



        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:detail-demande_2.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'reports' => $reports,
                        'conge' => $conge,
                        'entreprise' => $entreprise,
                        'droit' => $droits,
                        'form1' => $valide_Form->createView(),
                        'form2' => $refus_Form->createView(),
            ));
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:detail-demande_2.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'reports' => $reports,
                        'conge' => $conge,
                        'entreprise' => $entreprise,
                        'droit' => $droits,
                        'form1' => $valide_Form->createView(),
                        'form2' => $refus_Form->createView(),
            ));
        }
    }

    /**
     * Trouve et Affiche une demande
     * 
     */
    public function showSupDemandeHistoriqueAction($id) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $droits = $salarie->getDroits();

        $entreprise = $em->getRepository('KbhGestionCongesBundle:Entreprise')->find(1);
        $entity = $em->getRepository('KbhGestionCongesBundle:Demande')->find($id);
        $reports = $em->getRepository('KbhGestionCongesBundle:Report')->findAll();
        $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->findAll();

        $valide_Form = $this->ValideDemandeForm($entity);
        $refus_Form = $this->RefusDemandeForm($entity);

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:detail-demande_2.html.twig', array(
                        'entity' => $entity,
                        'salarie' => $salarie,
                        'reports' => $reports,
                        'conge' => $conge,
                        'entreprise' => $entreprise,
                        'droit' => $droits,
                        'form1' => $valide_Form->createView(),
                        'form2' => $refus_Form->createView(),
            ));
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:detail-demande_2.html.twig', array(
                        'entity' => $entity,
                        'salarie' => $salarie,
                        'reports' => $reports,
                        'conge' => $conge,
                        'entreprise' => $entreprise,
                        'droit' => $droits,
                        'form1' => $valide_Form->createView(),
                        'form2' => $refus_Form->createView(),
            ));
        }
    }

    /**
     * Affiche la page pour effectuer une 
     * nouvelle demande d'absence
     *
     */
    public function supDemandeAbsenceAction() {
        //A faire: Empàªcher d'avoir une date de début antérieure à  la date courante
        // Date de fin ne doit pas excéder la fin de l'année courante  

        $entity = new Demande();
        $form = $this->calculAbsenceForm($entity);

        // Chargeons les éléments nécessaires
        $em = $this->getDoctrine()->getManager();

        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();
        $parampermissions = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->findAll();

        if (!$parampermissions) {
            throw $this->createNotFoundException('Erreur chargement des paramà¨tres de permissions exceptionnelles.');
        }

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-absence.html.twig', array(
                        'entity' => $entity,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'types' => $parampermissions,
                        'form' => $form->createView(),
            ));
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-absence.html.twig', array(
                        'entity' => $entity,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'types' => $parampermissions,
                        'form' => $form->createView(),
            ));
        }
    }

    /**
     * Affiche la page pour effectuer une 
     * nouvelle demande de congé
     *
     */
    public function supDemandeCongeAction() {
        $em = $this->getDoctrine()->getManager();
        $entity = new Demande();
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();

        $form = $this->calculCongeForm($entity);

        // OPERATIONS POUR LES DIFFERENTES PERIODES DE DEPOT ET DE TRAITEMENTS
        //Récupération de la période de depot
        $depot = $em->getRepository('KbhGestionCongesBundle:PeriodeDepotDemandes')->find(1);

        //Récupération de la période de traitement
        $traitement = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);

        //################################### calcul du nombre de jour  pour la période de dépot ####################
//        $debut = $depot->getDateDebut()->format('d-m-Y');
//        $uday = new \DateTime();
//        $fin = $depot->getDateFin()->format('d-m-Y');
//
//        //Conversion en timestamp
//        $tms_debut = strtotime($debut);
//        $tms_uday = strtotime($uday->format('d-m-Y'));
//        $tms_fin = strtotime($fin);
//        $day = 60 * 60 * 24;
//
//        //nombre de jours total
//        $tms_delta = ($tms_fin - $tms_debut); //  rajouter +1 jr si la journée de la fin de période est inclus
//        $nb_jour = round(($tms_delta / $day), 0);
//
//        // nombre de jours restants
//        $tms_delta_restant = ($tms_fin - $tms_uday); //  rajouter +1 jr si la journée de la fin de période est inclus
//        $nb_jour_restant = round(($tms_delta_restant / $day), 0);
//
//        //pourcentage du nombre de jours restant
//        $pourcentage = (( $nb_jour - $nb_jour_restant ) * 100) / $nb_jour;

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-conges.html.twig', array(
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'depot' => $depot,
                        'traitement' => $traitement,
//                        'nbJours' => $nb_jour_restant,
//                        'nbJoursTotal' => $nb_jour,
//                        'pourcentage' => $pourcentage,
                        'form' => $form->createView(),
                        'result' => $this->getRequest()->get('date'),
            ));
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-conges.html.twig', array(
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'depot' => $depot,
                        'traitement' => $traitement,
//                        'nbJours' => $nb_jour_restant,
//                        'nbJoursTotal' => $nb_jour,
//                        'pourcentage' => $pourcentage,
                        'form' => $form->createView(),
                        'result' => $this->getRequest()->get('date'),
            ));
        }
    }

    /*     * **************** LE SALARIE CONNECTE ************************************ */

    /**
     * Affiche la liste de toutes ses demandes(congés et absences)
     *  
     */
    public function mesDemandesAction() {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $superviseurN1 = $salarie->getSuperviseur();
        $salarie_id = $salarie->getId();

        $entities = $em->getRepository('KbhGestionCongesBundle:Demande')->findBySalarie($salarie_id);
        $reports = $em->getRepository('KbhGestionCongesBundle:Report')->findBySalarie($salarie_id);


        return $this->render('KbhGestionCongesBundle:Demande:demande-list.html.twig', array(
                    'entities' => $entities,
                    'reports' => $reports,
                    'superviseurN1' => $superviseurN1,
        ));
    }

    /**
     * Trouve et Affiche une demande
     * 
     */
    public function showDemandeAction($id) {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();

        $entreprise = $em->getRepository('KbhGestionCongesBundle:Entreprise')->find(1);
        $entity = $em->getRepository('KbhGestionCongesBundle:Notification')->find($id);
        $reports = $em->getRepository('KbhGestionCongesBundle:Report')->findAll();
        $conge = $em->getRepository('KbhGestionCongesBundle:Conge')->findAll();
        $demande = $entity->getDemande();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Notification entity.');
        }

        //modification de l'état de la notification : cas du demandeur
        if ($entity->getSalarie() == $salarie) {
            $entity->setVuParDemandeur(1);
            $em->flush();
        }


        return $this->render('KbhGestionCongesBundle:Demande:detail-demande_2.html.twig', array(
                    'entity' => $demande,
                    'salarie' => $salarie,
                    'reports' => $reports,
                    'entreprise' => $entreprise,
                    'conge' => $conge,
                    'droit' => $droits,
        ));
    }

    /**
     * Trouve et Affiche une demande
     * 
     */
    public function showDemandeHistoriqueAction($id) {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();
        //initialisation de la variable congé
        $conge = 0;

        $entreprise = $em->getRepository('KbhGestionCongesBundle:Entreprise')->find(1);
        $entity = $em->getRepository('KbhGestionCongesBundle:Demande')->find($id);
        $reports = $em->getRepository('KbhGestionCongesBundle:Report')->findAll();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Demande entity.');
        }
//         $valide_Form = $this->ValideDemandeForm($entity);
//         $refus_Form = $this->RefusDemandeForm($entity);

        return $this->render('KbhGestionCongesBundle:Demande:detail-demande_2.html.twig', array(
                    'entity' => $entity,
                    'salarie' => $salarie,
                    'entreprise' => $entreprise,
                    'droit' => $droits,
                    'conge' => $conge,
                    'reports' => $reports,
//            'form1' => $valide_Form->createView(),
//            'form2' => $refus_Form->createView(),
        ));
    }

    /**
     * Affiche la page pour effectuer une 
     * nouvelle demande d'absence
     *
     */
    public function newDemandeAbsenceAction() {
        $salarie = $this->getSalarieByUser();
        $entity = new Demande();
        $form = $this->calculAbsenceForm($entity);

        // Chargeons les éléments nécessaires
        $em = $this->getDoctrine()->getManager();

        $droits = $salarie->getDroits();
        $parampermissions = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->findAll();



        return $this->render('KbhGestionCongesBundle:Demande:demande-absence.html.twig', array(
                    'entity' => $entity,
                    'salarie' => $salarie,
                    'droits' => $droits,
                    'types' => $parampermissions,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Affiche la page pour effectuer une 
     * nouvelle demande de congé
     *
     */
    public function newDemandeCongeAction() {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $entity = new Demande();
        $droits = $salarie->getDroits();
        $form = $this->calculCongeForm($entity);
        if (!$salarie) {
            throw $this->createNotFoundException('Unable to find Salarie entity.');
        }

        // OPERATIONS POUR LES DIFFERENTES PERIODES DE DEPOT ET DE TRAITEMENTS
        //Récupération de la période de depot
//        $depot = $em->getRepository('KbhGestionCongesBundle:PeriodeDepotDemandes')->find(1);
//
//        //Récupération de la période de traitement
//        $traitement = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);
//
//        //################################### calcul du nombre de jour  pour la période de dépot ####################
//        $debut = $depot->getDateDebut()->format('d-m-Y');
//        $uday = new \DateTime();
//        $fin = $depot->getDateFin()->format('d-m-Y');
//
//        //Conversion en timestamp
//        $tms_debut = strtotime($debut);
//        $tms_uday = strtotime($uday->format('d-m-Y'));
//        $tms_fin = strtotime($fin);
//        $day = 60 * 60 * 24;
//
//        //nombre de jours total
//        $tms_delta = ($tms_fin - $tms_debut); //  rajouter +1 jr si la journée de la fin de période est inclus
//        $nb_jour = round(($tms_delta / $day), 0);
//
//        // nombre de jours restants
//        $tms_delta_restant = ($tms_fin - $tms_uday); //  rajouter +1 jr si la journée de la fin de période est inclus
//        $nb_jour_restant = round(($tms_delta_restant / $day), 0);
//
//        //pourcentage du nombre de jours restant
//        $pourcentage = (( $nb_jour - $nb_jour_restant ) * 100) / $nb_jour;

        return $this->render('KbhGestionCongesBundle:Demande:demande-conges.html.twig', array(
                    'salarie' => $salarie,
                    'droits' => $droits,
//                    'depot' => $depot,
//                    'traitement' => $traitement,
//                    'nbJours' => $nb_jour_restant,
//                    'nbJoursTotal' => $nb_jour,
//                    'pourcentage' => $pourcentage,
                    'form' => $form->createView(),
                    'result' => $this->getRequest()->get('date'),
        ));
    }

    /*     * *********** UPDATE DU 05052015 **************************** */

    /**
     * Calcul d'une demande d'Absence
     *
     */
    public function calculDemandeAbsenceAction(Request $request) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $demande = new Demande();
        $droits = $salarie->getDroits();

        $form = $this->createCreateAbsenceForm($demande);
        $form->handleRequest($request);

        if ($form->isValid()) {

            //############## UPDATE DES DEMANDES D'ABSENCES PAR HEURES ###################################
            $dureeHeure = $_POST['select-heure'];
            $heureDepart = $_POST['select-heure-depart'];
            if ($dureeHeure != 0){
                // TRAINTEMENT D'UNE DUREE EN HEURE
                $demande->setNbjoursOuvrables($dureeHeure);

                $droits = $salarie->getDroits();
                $unite = $salarie->getUnite();

                //Conversion du nombre d'heure en secondes (timestamp)
                $nbSecondes = 60 * 60 ;

                // Recuperation du nombre d'heures d'absences et de la date de debut
                $nombreHeure = $demande->getNbJoursOuvrables() ;
                $dateDebut = $demande->getDateDebut();
//
//                var_dump("dureeAbsence: ".$dureeHeure);
//                var_dump("HeureDepart: ".$heureDepart);
//                die();


                //calcul en seconde de l'equivalent de l'heure de départ
                $tms_duree_heure_depart = $heureDepart * $nbSecondes;
//                var_dump("TmsHeureDepart: ".$tms_duree_heure_depart);

                //Calcul de la nouvelle date de debut avec l'heure de départ en timestamp
                $tms_dateDebut = $dateDebut->getTimestamp() + $tms_duree_heure_depart ;
//                var_dump("TmsDateDepart - TmsHeureDepart: ".$dateDebut->getTimestamp());
//                var_dump("TmsNewDateDepart: ".$tms_dateDebut);

                //Conversion du nombre d'heure d'absences total en timestamp
                $tms_nbHTotal = $nombreHeure * $nbSecondes;

                //Addition du nombre d'heures total et de la date de debut
                $tms_add = $tms_nbHTotal + $tms_dateDebut;

                //Addition du nombre de jours total et du nombre de secondes pour obtenir la date de retour
                $tms_add_retour = $tms_add ;

                //Conversion de la variable $tms_add en date afin d'obtenir la date de fin
                $datetFinale = date('Y/m/d H:i:s', $tms_add);

                //Conversion de la variable $tms_add pour obtenir la date finale
                $dateRetour = date('Y/m/d H:i:s', $tms_add_retour);

                $newDateDebut = date('Y/m/d H:i:s', $tms_dateDebut);

                //on renseigne les résultats dans la demande
                $demande->setDateFin($datetFinale);
                $demande->setDateRetour($dateRetour);
                $demande->setDateDebut($newDateDebut);
//                var_dump($datetFinale);
//                var_dump($demande->getDateDebut());
//                die();


            }else{

                // CAS 2: TRAITEMENT DE LA DUREE EN JOURS
                $droits = $salarie->getDroits();
                $unite = $salarie->getUnite();

                //Calcul des données de fin et retour.
                // Recuperation du contenu des champs nombre de jours et date de debut
                $nombreJours = abs($demande->getNbJoursOuvrables() - 1);
                $dateDebut = $demande->getDateDebut();

                //Conversion du nombre de jours et de la date de debut en secondes (timestamp)
                $nbSecondes = 60 * 60 * 24;
                //$tms_dateDebut = strtotime($dateDebut);
                $tms_dateDebut = $dateDebut->getTimestamp();

                //Calcul du nombre de jours total du congé sans les jours féries
                $nombreJoursTotal = $nombreJours;

                //Conversion du nombre de jours total en timestamp
                $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

                //Addition du nombre de jours total et de la date de debut
                $tms_add = $tms_nbjTotal + $tms_dateDebut;

                //Addition du nombre de jours total et du nombre de secondes pour obtenir la date de retour
                $tms_add_retour = $tms_add + $nbSecondes;

                //Conversion de la variable $tms_add en date afin d'obtenir la date de fin
                $datetFinale = date('Y/m/d H:i:s', $tms_add);

                //Conversion de la variable $tms_add pour obtenir la date finale
                $dateRetour = date('Y/m/d H:i:s', $tms_add_retour);

                //on renseigne les résultats dans la demande
                $demande->setDateFin($datetFinale);
                $demande->setDateRetour($dateRetour);
            }
//            var_dump($demande->getNbjoursOuvrables());
//            var_dump($dateRetour);
//            die();


            if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande\Top-manager:confirmation-demande-absence.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'form' => $form->createView())
                );
            }
            if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande\Superviseur:confirmation-demande-absence.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'form' => $form->createView())
                );
            } else {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande:confirmation-demande-absence.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'form' => $form->createView())
                );
            }
        }

        // CAS DE NON VALIDITEE DU FORMULAIRE
        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:confirmation-demande-absence.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'form' => $form->createView())
            );
        }

        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:confirmation-demande-absence.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'form' => $form->createView())
            );
        } else {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande:confirmation-demande-absence.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'form' => $form->createView())
            );
        }
    }

    /**
     * Calcul d'une demande de congé
     *
     */
    public function calculDemandeCongeAction(Request $request) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $demande = new Demande();
        $droits = $salarie->getDroits();
        $nbDimanche = 0;
        $joursFeries = array();
        $cp = 0;

        $form = $this->createCreateCongeForm($demande);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $droits = $salarie->getDroits();
            $unite = $salarie->getUnite();

            //Calcul des données de fin et retour.
            // Recuperation du contenu des champs nombre de jours et date de debut
            $nombreJours = $demande->getNbJoursOuvrables() - 1;
            $dateDebut = $demande->getDateDebut();

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

//            var_dump("verif du nombre total de dimanche compris dans la periode de ".$nombreJoursTotal." jours");
//            var_dump($nombreJoursTotal - $joursSup1);
//            var_dump($joursSup1);
            //Conversion du nombre de jours total en timestamp
            $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

            //Addition du nombre de jours total et de la date de debut
            $tms_add = $tms_nbjTotal + $tms_dateDebut;

            //Conversion de la variable $tms_add en date afin d'obtenir la date de fin
            $dateFin = date('Y-m-d', $tms_add);

//            var_dump($dateFin);
//            die();

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
//                    $nb_ferie = 0;
                    $nb_ferie += $ferie->getNbJoursFerie();
                    $titreFerie = $ferie->getTitreFeries();
                    $joursFeries[$cp] = $titreFerie;
                    $cp++;
                }
                if ($tms_dateFin == $tms_DDF && $tms_dateFin < $tms_DFF) {
//                    $nb_ferie = 0;
                    $nb_ferie += $ferie->getNbJoursFerie();
                    $titreFerie = $ferie->getTitreFeries();
                    $joursFeries[$cp] = $titreFerie;
                    $cp++;
                }
            }
            // On rajoute au nombre total de jours le nombre de jours feries
//            $nb_ferie = count($joursFeries);
            $nombreJoursTotal += $nb_ferie;

//            var_dump("verif du nombre total de feries compris dans la periode de ".$nombreJoursTotal." jours");
//            var_dump($nombreJoursTotal - $nb_ferie);
//            var_dump($nb_ferie);
//            var_dump();
//            die();
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

//            var_dump("verif du nombre total de dimanche compris dans la periode de ".$nombreJoursTotal." jours, apres avoir rajoute les feries");
//            var_dump($nombreJoursTotal - $joursSup2);
//            var_dump($joursSup2);
//        $nombreJoursTotal = $nombreJoursTotal - $joursSup1 ;
            $nbjf = $nombreJoursTotal;

            //Obtenir le nombre de jours ouvrabe

            $nombreJoursOuvrable = $nombreJoursTotal;

            //Conversion du nombre de jours total en timestamp
            $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

            //Addition du nombre de jours total et de la date de debut
            $tms_add = $tms_nbjTotal + $tms_dateDebut;

            //Conversion de la variable $tms_add pour obtenir la date finale
            $dateRetour = date('Y/m/d H:i:s', $tms_add);

//            var_dump($dateRetour);
//            die();
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

//            var_dump($nombreJoursTotal);
//            var_dump($nb_jr_ferie);
//            die();
            //Conversion du nombre de jours séparant la date de fin de la date de retour en timestamp
            $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

            //Addition du nombre de jours total et de la date de debut
            $tms_add = ($tms_nbjTotal + $nbSecondes ) + $tms_dateDebut;

            //Conversion de la variable $tms_add pour obtenir la date finale
            $dateRetour = date('Y/m/d', $tms_add);

//            var_dump("Avant derniere verif (date retour == ferie)");
//            var_dump($dateRetour);
//            die();
            //************** DERNIERE VERIFICATION *********************//
            //Recuperation du jour de la date de retour

            $jour = getdate($tms_add); // On obtient ainsi le jour de la semaine 
            $jourDateRetour = $jour['wday'];

            if ($jourDateRetour == 0) {

//                //creation du 1er tableau des jours des congés 
//                $joursDesConges[0] = $jourDateRetour;
//                $joursSup3 = 0;
//                $nombreJours = 1;
//
//                for ($i = 1; $i <= $nombreJours; $i++) {
//                    $joursDesConges[$i] = $joursDesConges[$i - 1] + 1;
//                    if ($joursDesConges[$i - 1] === 6) {
//                        $joursDesConges[$i] = 0;
//                    }
//                }
//
//                //Verification du nombre de dimanches contenu dans la 1ere période
//                for ($i = 1; $i <= $nombreJours; $i++) {
//                    if ($joursDesConges[$i] == 0) {
//                        $joursSup3 +=1;
//                        $nbDimanche = $joursSup3;
//                    }
//                }
                //On rajoute le nombre de dimanches total au nombre de jours totale pour Obtenir la date de retour
                $joursSup3 = 1;

//                var_dump("Derniere verif (date retour == dimanche)");
//                var_dump($joursSup3);
//                die();
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

//                var_dump("Toute derniere fois: verif (date retour == feries)");
//                var_dump($nb_jr_ferie);
                //Conversion du nombre de jours séparant la date de fin de la date de retour en timestamp
                $tms_nbjf = $joursSup3 * $nbSecondes;

                //Addition du nombre de jours total et de la date de debut
                $tms_add_fin = $tms_add + $tms_nbjf;


                //Conversion de la variable $tms_add pour obtenir la date finale
                $dateRetour = date('Y/m/d', $tms_add_fin);

//                var_dump($dateRetour);
                //*****************************************************************//
            }

            //pour trouver la date de fin
            $nombreJoursFin = $nbjf + $nb_jr_ferie;

            //Conversion du nombre de jours en timestamp
            $tms_nbjFin = $nombreJoursFin * $nbSecondes;

            //Addition du nombre de jours final et de la date de debut
            $tms_add_nbjF = ($tms_nbjFin ) + $tms_dateDebut;

            $datetFinale = date('Y/m/d H:i:s', $tms_add_nbjF);

            //on renseigne les résultats dans la demande
            $demande->setDateFin($datetFinale);
            $demande->setDateRetour($dateRetour);

//            var_dump($datetFinale);
//            var_dump($dateRetour);
//            die();

            if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande\Top-manager:confirmation-demande-conge.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'feries' => $joursFeries,
                            'nbDimanche' => $nbDimanche,
                            'nbjTotal' => $nombreJoursFin,
                            'form' => $form->createView())
                );
            }

            if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande\Superviseur:confirmation-demande-conge.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'feries' => $joursFeries,
                            'nbDimanche' => $nbDimanche,
                            'nbjTotal' => $nombreJoursFin,
                            'form' => $form->createView())
                );
            } else {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande:confirmation-demande-conge.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'feries' => $joursFeries,
                            'nbDimanche' => $nbDimanche,
                            'nbjTotal' => $nombreJoursFin,
                            'form' => $form->createView())
                );
            }
        }

        // CAS DE NON VALIDITEE DU FORRMULAIRE
        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:confirmation-demande-conge.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'feries' => $joursFeries,
                        'nbDimanche' => $nbDimanche,
                        'nbjTotal' => $nombreJoursFin,
                        'form' => $form->createView())
            );
        }

        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:confirmation-demande-conge.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'feries' => $joursFeries,
                        'nbDimanche' => $nbDimanche,
                        'nbjTotal' => $nombreJoursFin,
                        'form' => $form->createView())
            );
        } else {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande:confirmation-demande-conge.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'feries' => $joursFeries,
                        'nbDimanche' => $nbDimanche,
                        'nbjTotal' => $nombreJoursFin,
                        'form' => $form->createView())
            );
        }
    }

    //    ####################### UPDATE DU 12/09/2015 PAR DESIRE ##################################  
    /**
     * Calcul d'une demande de congé par un responsable
     *
     */
    public function calculDemandeCongeForSalarieAction(Request $request) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $demande = new Demande();
        $demande->setCreeParSuperviseur(1);

        $droits = $salarie->getDroits();
        $nbDimanche = 0;
        $joursFeries = array();
        $cp = 0;

        $form = $this->createCongeBySupForm($demande);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $droits = $demande->getSalarie()->getDroits();

            //Calcul des données de fin et retour.
            // Recuperation du contenu des champs nombre de jours et date de debut
            $nombreJours = $demande->getNbJoursOuvrables() - 1;
            $dateDebut = $demande->getDateDebut();

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


             /**********************************************
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


            //On rajoute le nombre de dimanches total au nombre de jours totale pour Obtenir la date de retour
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
            //Recuperation du jour de la date de debut

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

            //on renseigne les résultats dans la demande
            $demande->setDateFin($datetFinale);
            $demande->setDateRetour($dateRetour);
//           $demande->getPieceJointe()->setAjouterPar($salarie);
//           $demande->getPieceJointe()->setSalarie($demande->getSalarie());
//           $form = $this->createCongeBySupForm($demande);   

            if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande\Superviseur:confirmation-demande-conge-for-salarie.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'feries' => $joursFeries,
                            'nbDimanche' => $nbDimanche,
                            'nbjTotal' => $nombreJoursFin,
                            'form' => $form->createView())
                );
            }
            if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande\Top-manager:confirmation-demande-conge-for-salarie.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'feries' => $joursFeries,
                            'nbDimanche' => $nbDimanche,
                            'nbjTotal' => $nombreJoursFin,
                            'form' => $form->createView())
                );
            }
            // CAS DE NON VALIDATION DU FORMULAIRE
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-conge-for-salarie.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'feries' => $joursFeries,
                        'nbDimanche' => $nbDimanche,
                        'nbjTotal' => $nombreJoursFin,
                        'form' => $form->createView())
            );
        }
        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-conge-for-salarie.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'feries' => $joursFeries,
                        'nbDimanche' => $nbDimanche,
                        'nbjTotal' => $nombreJoursFin,
                        'form' => $form->createView())
            );
        }
    }

    /**
     * Création d'une demande de Congé par un responsable
     *
     */
    public function createDemandeCongeBySupAction(Request $request) {
        $salarieConnecte = $this->getSalarieByUser();

        $em = $this->getDoctrine()->getManager();
        $demande = new Demande();

        $form = $this->createCongeBySupForm($demande);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $salarie = $demande->getSalarie();
            $auteurDemande = $demande->getAuteurDemande();

            $droits = $salarie->getDroits();
            $unite = $salarie->getUnite();

            $demande->setTypeDemande('Conge');
            $demande->setCreeParSuperviseur(1);
            $demande->setSalarie($salarie);
            $demande->setSoldeDroits($droits->getTotalDroitsAprendre()); //Ajout du 30/04/2015 par BHKONAN
            //Ajout de la gestion de l'historique des droits. @BHKONAN le 30/04/2015
            $historiqueDroits = new HistoriqueDroits();
            $historiqueDroits->setSalarie($salarie);
            $historiqueDroits->setDroits($droits);
            $historiqueDroits->setDemande($demande);
            $historiqueDroits->setSoldeCongeAncien($droits->getTotalDroitsAprendre());
            $historiqueDroits->setSoldePermissionAncien($droits->getSoldePermissions());

            //Ajout du 30/09/2015 par Desire
            //Récupération de la période de depot
//            $depot = $em->getRepository('KbhGestionCongesBundle:PeriodeDepotDemandes')->find(1);
//            $dateDebut = $depot->getDateDebut();
//            $dateFin = $depot->getDateFin();
//            $aujourdhui = new \DateTime();
//
//            //Vérifions que la demande soit faite dans la période de dépot de congé
//            if ($dateDebut->format('d-m-Y') >= $aujourdhui->format('d-m-Y') || $aujourdhui->format('d-m-Y') <= $dateFin->format('d-m-Y')) {
//                $demandesDeposees = new DemandesDeposees();
//                $demandesDeposees->setSalarie($salarie);
//                $demandesDeposees->setDemande($demande);
//                $demandesDeposees->setUnite($salarie->getUnite());
//                $demandesDeposees->setDateEnvoi($aujourdhui);
//                $demandesDeposees->setStatut("En attente");
//
//
//                //############### SI LA DEMANDE EST FAITES DANS LA PERIODE DE DEPOT DE CONGES ###################
//                // Notifications
//                $notification = new Notification();
//                $notification->setSalarie($salarie);
//                $notification->setDemande($demande);
//
//                //code du 7 mars 2015 -étape 1: sans prise en compte des suppléants
//                $nbNiveaux = $unite->getNbNiveauxValidation();
//                $demande->setNbNiveauxValidation($nbNiveaux);
//
//                $notification->setMessageValideurEnCours("Demande de " . $demande->getTypeDemande() . " de " . $salarie->getCivilite() . " " . $salarie . " à  valider.");
//
//                //Cas particuliers
//                //Cas 1: Demandeur est le manager de l'unité
//                if ($salarie == $unite->getManager()) {
//                    if ($unite->getValideurPourManager3()) {
//                        $demande->setValideurFinal($unite->getValideurPourManager3()->getManager());
//                        $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
//                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
//
//                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
//                    }
//                    if ($unite->getValideurPourManager2()) {
//                        $demande->setValideurFinal($unite->getValideurPourManager2()->getManager());
//                        $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
//                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
//                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
//                    } else {
//                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
//                        $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
//                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
//                    }
//
//                    $notification->setValideurEnCours($demande->getValideurEnCours());
//                    $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
//
//                    //$this->envoiEmailDemande($salarie,$demande);
//                    // Notifications de l'auteur de la demande
//                    $notif = new Notification();
//                    $notif->setSalarie($auteurDemande);
//                    $notif->setDemande($demande);
//                    $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
//
//                    //création de la pièce jointe
////               $pieceJointe = new PiecesJointes();
//                    $pieceJointe = $demande->getPieceJointe();
//                    $pieceJointe->setAjouterPar($auteurDemande);
//                    $pieceJointe->setDateCreation(new \DateTime());
//                    $pieceJointe->setSalarie($salarie);
//                    $pieceJointe->preUpload();
//                    $pieceJointe->upload();
//                    $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());
//
//                    $em->persist($notification);
//                    $em->persist($demande);
//                    $em->persist($notif);
//                    $em->persist($pieceJointe);
//                    $em->persist($historiqueDroits);
//                    $em->flush();
//
//                    if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
//                        return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
//                    }
////                    else {
////                        return $this->redirect($this->generateUrl('superviseur_demande_conge_for_salarie')); //redirige vers la page mesdemandes
////                    }
//
//                    if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
//                        return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
//                    }
////                    else {
////                        return $this->redirect($this->generateUrl('top_manager_demande_conge_for_salarie')); //redirige vers la page mesdemandes
////                    }
//                } else {//Cas 2: Le reste des employés
//                    if ($nbNiveaux == 1) {
//                        $demande->setValideurNiveau1($unite->getManager());
//                        $demande->setValideurEnCours($unite->getManager());
//                        $demande->setValideurFinal($unite->getManager());
//
//                        $notification->setValideurEnCours($demande->getValideurEnCours());
//                        $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
//
//                        //$this->envoiEmailDemande($salarie,$demande);
//                        $notif = new Notification();
//                        $notif->setSalarie($auteurDemande);
//                        $notif->setDemande($demande);
//                        $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
//
//                        $pieceJointe = $demande->getPieceJointe();
//                        $pieceJointe->setAjouterPar($auteurDemande);
//                        $pieceJointe->setDateCreation(new \DateTime());
//                        $pieceJointe->setSalarie($salarie);
//                        $pieceJointe->preUpload();
//                        $pieceJointe->upload();
//                        $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());
//
//                        $em->persist($notification);
//                        $em->persist($demande);
//                        $em->persist($notif);
//                        $em->persist($pieceJointe);
//                        $em->persist($historiqueDroits);
//                        $em->flush();
//
//                        if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
//                        }
////                        if (!in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
////                            return $this->redirect($this->generateUrl('superviseur_demande_conge_for_salarie')); //redirige vers la page mesdemandes
////                        }
//
//                        if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
//                        }
////                        else {
////                            return $this->redirect($this->generateUrl('top_manager_demande_conge_for_salarie')); //redirige vers la page mesdemandes
////                        }  
//                    }
//                    if ($nbNiveaux == 2) {
//                        $demande->setValideurNiveau1($unite->getManager());
//                        $demande->setValideurEnCours($unite->getManager());
//                        $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
//                        $demande->setValideurFinal($demande->getValideurNiveau2());
//
//                        $notification->setValideurEnCours($demande->getValideurEnCours());
//                        $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
//
//                        //$this->envoiEmailDemande($salarie,$demande);
//                        $notif = new Notification();
//                        $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
//                        $notif->setSalarie($auteurDemande);
//                        $notif->setDemande($demande);
//
//                        $pieceJointe = $demande->getPieceJointe();
//                        $pieceJointe->setAjouterPar($auteurDemande);
//                        $pieceJointe->setDateCreation(new \DateTime());
//                        $pieceJointe->setSalarie($salarie);
//                        $pieceJointe->preUpload();
//                        $pieceJointe->upload();
//                        $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());
//
//                        $em->persist($notification);
//                        $em->persist($demande);
//                        $em->persist($notif);
//                        $em->persist($pieceJointe);
//                        $em->persist($historiqueDroits);
//                        $em->flush();
//
//                        if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
//                        }
////                        else {
////                            return $this->redirect($this->generateUrl('superviseur_demande_conge_for_salarie')); //redirige vers la page mesdemandes
////                        }
//                        if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
//                        }
//                    }
//                    if ($nbNiveaux == 3) {
//                        $demande->setValideurNiveau1($unite->getManager());
//                        $demande->setValideurEnCours($unite->getManager());
//                        $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
//                        $demande->setValideurFinal($unite->getUniteSuivante2()->getManager());
//
//                        $notification->setValideurEnCours($demande->getValideurEnCours());
//                        $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
//
//                        //$this->envoiEmailDemande($salarie,$demande);
//                        $notif = new Notification();
//                        $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
//                        $notif->setSalarie($auteurDemande);
//                        $notif->setDemande($demande);
//
//                        $pieceJointe = $demande->getPieceJointe();
//                        $pieceJointe->setAjouterPar($auteurDemande);
//                        $pieceJointe->setDateCreation(new \DateTime());
//                        $pieceJointe->setSalarie($salarie);
//                        $pieceJointe->preUpload();
//                        $pieceJointe->upload();
//                        $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());
//
//                        $em->persist($notification);
//                        $em->persist($demande);
//                        $em->persist($notif);
//                        $em->persist($pieceJointe);
//                        $em->persist($historiqueDroits);
//                        $em->flush();
//
//                        if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
//                        }
////                        else {
////                            return $this->redirect($this->generateUrl('superviseur_demande_conge_for_salarie')); //redirige vers la page mesdemandes
////                        }
//                        if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
//                        }
//                    }
//                }
//            }
//            else {
            //################ SI LA DEMANDE N'EST PAS FAITES DANS LA PERIODE DE DEPOT DE CONGES ##################
            // Notifications
            $notification = new Notification();
            $notification->setSalarie($salarie);
            $notification->setDemande($demande);

            //code du 7 mars 2015 -étape 1: sans prise en compte des suppléants
            $nbNiveaux = $unite->getNbNiveauxValidation();
            $demande->setNbNiveauxValidation($nbNiveaux);

            $notification->setMessageValideurEnCours("Demande de " . $demande->getTypeDemande() . " de " . $salarie->getCivilite() . " " . $salarie . " à  valider.");

            //Cas particuliers
            //Cas 1: Demandeur est le manager de l'unité
            if ($salarie == $unite->getManager()) {
                if ($unite->getValideurPourManager3()) {
                    $demande->setValideurFinal($unite->getValideurPourManager3()->getManager());
                    $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
                    $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());

                    $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                }
                if ($unite->getValideurPourManager2()) {
                    $demande->setValideurFinal($unite->getValideurPourManager2()->getManager());
                    $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
                    $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                } else {
                    $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                }

                $notification->setValideurEnCours($demande->getValideurEnCours());
                $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");

                //$this->envoiEmailDemande($salarie,$demande);
                // Notifications de l'auteur de la demande
                $notif = new Notification();
                $notif->setSalarie($auteurDemande);
                $notif->setDemande($demande);
                $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");

                //création de la pièce jointe
//               $pieceJointe = new PiecesJointes();
                $pieceJointe = $demande->getPieceJointe();
                $pieceJointe->setAjouterPar($auteurDemande);
                $pieceJointe->setDateCreation(new \DateTime());
                $pieceJointe->setSalarie($salarie);
                $pieceJointe->preUpload();
                $pieceJointe->upload();
                $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());

                $em->persist($notification);
                $em->persist($demande);
                $em->persist($notif);
                $em->persist($pieceJointe);
                $em->persist($historiqueDroits);
                $em->flush();

                if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
                    return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
                }
//                    else {
//                        return $this->redirect($this->generateUrl('superviseur_demande_conge_for_salarie')); //redirige vers la page mesdemandes
//                    }
                if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
                    return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
                }
            } else {//Cas 2: Le reste des employés
                if ($nbNiveaux == 1) {
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurFinal($unite->getManager());

                    $notification->setValideurEnCours($demande->getValideurEnCours());
                    $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");

                    //$this->envoiEmailDemande($salarie,$demande);
                    $notif = new Notification();
                    $notif->setSalarie($auteurDemande);
                    $notif->setDemande($demande);
                    $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");

                    $pieceJointe = $demande->getPieceJointe();
                    $pieceJointe->setAjouterPar($auteurDemande);
                    $pieceJointe->setDateCreation(new \DateTime());
                    $pieceJointe->setSalarie($salarie);
                    $pieceJointe->preUpload();
                    $pieceJointe->upload();
                    $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($notif);
                    $em->persist($pieceJointe);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
                    }
//                        else {
//                            return $this->redirect($this->generateUrl('superviseur_demande_conge_for_salarie')); //redirige vers la page mesdemandes
//                        }
                    if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
                    }
                }
                if ($nbNiveaux == 2) {
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
                    $demande->setValideurFinal($demande->getValideurNiveau2());

                    $notification->setValideurEnCours($demande->getValideurEnCours());
                    $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");

                    //$this->envoiEmailDemande($salarie,$demande);
                    $notif = new Notification();
                    $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    $notif->setSalarie($auteurDemande);
                    $notif->setDemande($demande);

                    $pieceJointe = $demande->getPieceJointe();
                    $pieceJointe->setAjouterPar($auteurDemande);
                    $pieceJointe->setDateCreation(new \DateTime());
                    $pieceJointe->setSalarie($salarie);
                    $pieceJointe->preUpload();
                    $pieceJointe->upload();
                    $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($notif);
                    $em->persist($pieceJointe);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
                    }
//                        else {
//                            return $this->redirect($this->generateUrl('superviseur_demande_conge_for_salarie')); //redirige vers la page mesdemandes
//                        }
                    if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
                    }
                }
                if ($nbNiveaux == 3) {
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
                    $demande->setValideurFinal($unite->getUniteSuivante2()->getManager());

                    $notification->setValideurEnCours($demande->getValideurEnCours());
                    $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");

                    //$this->envoiEmailDemande($salarie,$demande);
                    $notif = new Notification();
                    $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    $notif->setSalarie($auteurDemande);
                    $notif->setDemande($demande);

                    $pieceJointe = $demande->getPieceJointe();
                    $pieceJointe->setAjouterPar($auteurDemande);
                    $pieceJointe->setDateCreation(new \DateTime());
                    $pieceJointe->setSalarie($salarie);
                    $pieceJointe->preUpload();
                    $pieceJointe->upload();
                    $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($notif);
                    $em->persist($pieceJointe);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
                    }
//                        else {
//                            return $this->redirect($this->generateUrl('superviseur_demande_conge_for_salarie')); //redirige vers la page mesdemandes
//                        }
                    if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
                    }
                }
            }
//            }
        }

        if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-conges-for-salarie.html.twig', array(
                        'entity' => $demande,
                        '$salarie' => $salarie,
                        'droits' => $droits,
                        'form' => $form->createView(),
            ));
        }

        if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-conges-for-salarie.html.twig', array(
                        'entity' => $demande,
                        '$salarie' => $salarie,
                        'droits' => $droits,
                        'form' => $form->createView(),
            ));
        }
    }

    /**
     * Affiche la page pour effectuer une 
     * nouvelle demande de congé
     *
     */
    public function supDemandeCongeForSalarieAction() {
        $em = $this->getDoctrine()->getManager();
        $entity = new Demande();
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();

        //Préparation du formulaire
        $entity->setAuteurDemande($salarie);
        $form = $this->calculCongeForm2($entity);

        //Récupération des collaborateurs dépendant du salarié connecté
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findBySuperviseur($salarie);

        // OPERATIONS POUR LES DIFFERENTES PERIODES DE DEPOT ET DE TRAITEMENTS
        //Récupération de la période de depot
        $depot = $em->getRepository('KbhGestionCongesBundle:PeriodeDepotDemandes')->find(1);

        //Récupération de la période de traitement
        $traitement = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);

        //################################### calcul du nombre de jour  pour la période de dépot ####################
//        $debut = $depot->getDateDebut()->format('d-m-Y');
//        $uday = new \DateTime();
//        $fin = $depot->getDateFin()->format('d-m-Y');
//
//        //Conversion en timestamp
//        $tms_debut = strtotime($debut);
//        $tms_uday = strtotime($uday->format('d-m-Y'));
//        $tms_fin = strtotime($fin);
//        $day = 60 * 60 * 24;
//
//        //nombre de jours total
//        $tms_delta = ($tms_fin - $tms_debut); //  rajouter +1 jr si la journée de la fin de période est inclus
//        $nb_jour = round(($tms_delta / $day), 0);
//
//        // nombre de jours restants
//        $tms_delta_restant = ($tms_fin - $tms_uday); //  rajouter +1 jr si la journée de la fin de période est inclus
//        $nb_jour_restant = round(($tms_delta_restant / $day), 0);
//
//        //pourcentage du nombre de jours restant
//        $pourcentage = (( $nb_jour - $nb_jour_restant ) * 100) / $nb_jour;


        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-conges-for-salarie.html.twig', array(
                        'salarie' => $salarie,
                        'salaries' => $salaries,
                        'droits' => $droits,
                        'depot' => $depot,
                        'traitement' => $traitement,
//                        'nbJours' => $nb_jour_restant,
//                        'nbJoursTotal' => $nb_jour,
//                        'pourcentage' => $pourcentage,
                        'form' => $form->createView(),
                        'result' => $this->getRequest()->get('date'),
            ));
        }

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-conges-for-salarie.html.twig', array(
                        'salarie' => $salarie,
                        'salaries' => $salaries,
                        'droits' => $droits,
                        'depot' => $depot,
                        'traitement' => $traitement,
//                        'nbJours' => $nb_jour_restant,
//                        'nbJoursTotal' => $nb_jour,
//                        'pourcentage' => $pourcentage,
                        'form' => $form->createView(),
                        'result' => $this->getRequest()->get('date'),
            ));
        }
    }

    /**
     * Calcul d'une demande de congé par un responsable
     *
     */
    public function calculDemandePermissionForSalarieAction(Request $request) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $demande = new Demande();
        $demande->setCreeParSuperviseur(1);

        $droits = $salarie->getDroits();
        $nbDimanche = 0;
        $joursFeries = array();
        $cp = 0;

        $form = $this->createAbsenceBySupForm($demande);
        $form->handleRequest($request);
        if ($form->isValid()) {

            //############## UPDATE DES DEMANDES D'ABSENCES PAR HEURES ###################################
            $dureeHeure = $_POST['select-heure'];
            $heureDepart = $_POST['select-heure-depart'];
            $nombreJoursTotal = 0;
            if ($dureeHeure != 0){
                // TRAINTEMENT D'UNE DUREE EN HEURE
                $demande->setNbjoursOuvrables($dureeHeure);

                $droits = $salarie->getDroits();
                $unite = $salarie->getUnite();

                //Conversion du nombre d'heure en secondes (timestamp)
                $nbSecondes = 60 * 60 ;

                // Recuperation du nombre d'heures d'absences et de la date de debut
                $nombreHeure = $demande->getNbJoursOuvrables() ;
                $dateDebut = $demande->getDateDebut();
//
//                var_dump("dureeAbsence: ".$dureeHeure);
//                var_dump("HeureDepart: ".$heureDepart);
//                die();


                //calcul en seconde de l'equivalent de l'heure de départ
                $tms_duree_heure_depart = $heureDepart * $nbSecondes;
//                var_dump("TmsHeureDepart: ".$tms_duree_heure_depart);

                //Calcul de la nouvelle date de debut avec l'heure de départ en timestamp
                $tms_dateDebut = $dateDebut->getTimestamp() + $tms_duree_heure_depart ;
//                var_dump("TmsDateDepart - TmsHeureDepart: ".$dateDebut->getTimestamp());
//                var_dump("TmsNewDateDepart: ".$tms_dateDebut);

                //Conversion du nombre d'heure d'absences total en timestamp
                $tms_nbHTotal = $nombreHeure * $nbSecondes;

                //Addition du nombre d'heures total et de la date de debut
                $tms_add = $tms_nbHTotal + $tms_dateDebut;

                //Addition du nombre de jours total et du nombre de secondes pour obtenir la date de retour
                $tms_add_retour = $tms_add ;

                //Conversion de la variable $tms_add en date afin d'obtenir la date de fin
                $datetFinale = date('Y/m/d H:i:s', $tms_add);

                //Conversion de la variable $tms_add pour obtenir la date finale
                $dateRetour = date('Y/m/d H:i:s', $tms_add_retour);

                $newDateDebut = date('Y/m/d H:i:s', $tms_dateDebut);

                //on renseigne les résultats dans la demande
                $demande->setDateFin($datetFinale);
                $demande->setDateRetour($dateRetour);
                $demande->setDateDebut($newDateDebut);
//                var_dump($datetFinale);
//                var_dump($demande->getDateDebut());
//                die();


            }else{

                // CAS 2: TRAITEMENT DE LA DUREE EN JOURS
                $droits = $salarie->getDroits();
                $unite = $salarie->getUnite();

                //Calcul des données de fin et retour.
                // Recuperation du contenu des champs nombre de jours et date de debut
                $nombreJours = abs($demande->getNbJoursOuvrables() - 1);
                $dateDebut = $demande->getDateDebut();

                //Conversion du nombre de jours et de la date de debut en secondes (timestamp)
                $nbSecondes = 60 * 60 * 24;
                //$tms_dateDebut = strtotime($dateDebut);
                $tms_dateDebut = $dateDebut->getTimestamp();

                //Calcul du nombre de jours total du congé sans les jours féries
                $nombreJoursTotal = $nombreJours;

                //Conversion du nombre de jours total en timestamp
                $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

                //Addition du nombre de jours total et de la date de debut
                $tms_add = $tms_nbjTotal + $tms_dateDebut;

                //Addition du nombre de jours total et du nombre de secondes pour obtenir la date de retour
                $tms_add_retour = $tms_add + $nbSecondes;

                //Conversion de la variable $tms_add en date afin d'obtenir la date de fin
                $datetFinale = date('Y/m/d H:i:s', $tms_add);

                //Conversion de la variable $tms_add pour obtenir la date finale
                $dateRetour = date('Y/m/d H:i:s', $tms_add_retour);

                //on renseigne les résultats dans la demande
                $demande->setDateFin($datetFinale);
                $demande->setDateRetour($dateRetour);
            }
//            var_dump($demande->getNbjoursOuvrables());
//            var_dump($dateRetour);
//            die();


            if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande\Superviseur:confirmation-demande-absence-for-salarie.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'feries' => $joursFeries,
                        'nbDimanche' => $nbDimanche,
                        'nbjTotal' => $nombreJoursTotal,
                        'form' => $form->createView())
                );
            }

            if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Demande\Top-manager:confirmation-demande-absence-for-salarie.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'feries' => $joursFeries,
                        'nbDimanche' => $nbDimanche,
                        'nbjTotal' => $nombreJoursTotal,
                        'form' => $form->createView())
                );
            }

        }
        if ($form->isValid()) {
            $droits = $demande->getSalarie()->getDroits();

            // Recuperation du contenu des champs nombre de jours et date de debut
            $nombreJours = $demande->getNbJoursOuvrables() - 1;
            $dateDebut = $demande->getDateDebut();

            //Conversion du nombre de jours et de la date de debut en secondes (timestamp)
            $nbSecondes = 60 * 60 * 24;
            //$tms_dateDebut = strtotime($dateDebut);        
            $tms_dateDebut = $dateDebut->getTimestamp();

            //Calcul du nombre de jours total du congé sans les jours féries
            $nombreJoursTotal = $nombreJours;

            //Conversion du nombre de jours total en timestamp
            $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

            //Addition du nombre de jours total et de la date de debut
            $tms_add = $tms_nbjTotal + $tms_dateDebut;

            //Addition du nombre de jours total et du nombre de secondes pour obtenir la date de retour
            $tms_add_retour = $tms_add + $nbSecondes;

            //Conversion de la variable $tms_add en date afin d'obtenir la date de fin
            $dateFinale = date('Y-m-d', $tms_add);

            //Conversion de la variable $tms_add pour obtenir la date finale
            $dateRetour = date('Y/m/d H:i:s', $tms_add_retour);

            //on renseigne les résultats dans la demande
            $demande->setDateFin($dateFinale);
            $demande->setDateRetour($dateRetour);
//           $demande->getPieceJointe()->setAjouterPar($salarie);
//           $demande->getPieceJointe()->setSalarie($demande->getSalarie());
//           $form = $this->createCongeBySupForm($demande);   

            if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->render('KbhGestionCongesBundle:Demande\Superviseur:confirmation-demande-absence-for-salarie.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'feries' => $joursFeries,
                            'nbDimanche' => $nbDimanche,
                            'nbjTotal' => $nombreJoursTotal,
                            'form' => $form->createView())
                );
            }

            if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Demande\Top-manager:confirmation-demande-absence-for-salarie.html.twig', array(
                            'entity' => $demande,
                            'salarie' => $salarie,
                            'droits' => $droits,
                            'feries' => $joursFeries,
                            'nbDimanche' => $nbDimanche,
                            'nbjTotal' => $nombreJoursTotal,
                            'form' => $form->createView())
                );
            }
            // CAS DE NON VALIDATION DU FORMULAIRE
        }
        $nombreJoursFin = '';
        //Récupération des collaborateurs dépendant du salarié connecté
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findBySuperviseur($salarie);
        $parampermissions = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->findAll();

        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-absence-for-salarie.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'salaries' => $salaries,
                        'droits' => $droits,
                        'feries' => $joursFeries,
                        'types' => $parampermissions,
                        'nbDimanche' => $nbDimanche,
                        'nbjTotal' => $nombreJoursFin,
                        'form' => $form->createView())
            );
        }

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-absence-for-salarie.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'salaries' => $salaries,
                        'droits' => $droits,
                        'feries' => $joursFeries,
                        'types' => $parampermissions,
                        'nbDimanche' => $nbDimanche,
                        'nbjTotal' => $nombreJoursFin,
                        'form' => $form->createView())
            );
        }
    }

    /**
     * Création d'une demande de Congé par un responsable
     *
     */
    public function createDemandePermissionBySupAction(Request $request) {
        $salarieConnecte = $this->getSalarieByUser();

        $em = $this->getDoctrine()->getManager();
        $demande = new Demande();

        $form = $this->createAbsenceBySupForm($demande);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $salarie = $demande->getSalarie();
            $auteurDemande = $demande->getAuteurDemande();

            $droits = $salarie->getDroits();
            $unite = $salarie->getUnite();



            //On hydrate (un peu) l'entité !!
            if ($demande->getMotif() == "Fêtes religieuses") {
                $demande->setTypeDemande('Férie(s)');
            }
            elseif ($demande->getMotif() == "Autres") {
                $demande->setTypeDemande('Absence exceptionnelle');
            }
            elseif ($demande->getMotif() == "Rendez-vous medical") {
                //Ajout de la déclaration de rdv médicaux. @DESIRE le 15/10/2015
                $demande->setTypeDemande('Arrêt maladie');
            } else {
                $demande->setTypeDemande('Permission');
            }

            $demande->setCreeParSuperviseur(1);
            $demande->setSalarie($salarie);
            $demande->setSoldeDroits($droits->getTotalDroitsAprendre()); //Ajout du 30/04/2015 par BHKONAN
            //Ajout de la gestion de l'historique des droits. @BHKONAN le 30/04/2015
            $historiqueDroits = new HistoriqueDroits();
            $historiqueDroits->setSalarie($salarie);
            $historiqueDroits->setDroits($droits);
            $historiqueDroits->setDemande($demande);
            $historiqueDroits->setSoldeCongeAncien($droits->getTotalDroitsAprendre());
            $historiqueDroits->setSoldePermissionAncien($droits->getSoldePermissions());

            // Notifications
            $notification = new Notification();
            $notification->setSalarie($salarie);
            $notification->setDemande($demande);

            //code du 17 octobre 2015 -étape 1: sans prise en compte des suppléants
            if ($demande->getMotif() == "Rendez-vous medical") {
                $nbNiveaux = 1;
            } else {
                $nbNiveaux = $unite->getNbNiveauxValidation();
            }
            $demande->setNbNiveauxValidation($nbNiveaux);

            //Modification du message du valideur en cours en fonction du type de la demande (Modif par Desire le 16/04/2015)
            if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                $notification->setMessageValideurEnCours("Demande d'" . $demande->getTypeDemande() . " de " . $salarie->getCivilite() . " " . $salarie . " à valider.");
            }
            if ($demande->getTypeDemande() == "Permission") {
                $notification->setMessageValideurEnCours("Demande de " . $demande->getTypeDemande() . " de " . $salarie->getCivilite() . " " . $salarie . " à valider.");
            }
            if ($demande->getTypeDemande() == "Arrêt maladie") {
                $notification->setMessageValideurEnCours("Demande d'absence de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " à valider.");
            }
            if ($demande->getTypeDemande() == "Férie(s)") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                $notification->setMessageValideurEnCours("Demande d'absence de " . $salarie->getCivilite() . " " . $salarie . " à valider.");
            }
            //Cas particuliers
            //Cas 1: Demandeur est le manager de l'unité
            if ($salarie == $unite->getManager()) {
                if ($unite->getValideurPourManager3()) {
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                    } else {
                        $demande->setValideurFinal($unite->getValideurPourManager3()->getManager());
                        $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());

                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                    }
                }
                if ($unite->getValideurPourManager2()) {
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                    } else {
                        $demande->setValideurFinal($unite->getValideurPourManager2()->getManager());
                        $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                    }
                } else {
                    $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                }

                $notification->setValideurEnCours($demande->getValideurEnCours());

                if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                    $notification->setMessageDemandeur("Une demande d'" . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                }
                if ($demande->getTypeDemande() == "Permission") {
                    $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                }
                if ($demande->getTypeDemande() == "Arrêt maladie") {
                    $notification->setMessageDemandeur("Une demande d'" . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " . Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                }
                if ($demande->getTypeDemande() == "Férie(s)") {
                    $notification->setMessageDemandeur("Une demande d'absence à été effectuée par " . $auteurDemande->getCivilite() . " . Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                }

                //$this->envoiEmailDemande($salarie,$demande);
                // Notifications de l'auteur de la demande
                $notif = new Notification();
                $notif->setSalarie($auteurDemande);
                $notif->setDemande($demande);

                if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                    $notif->setMessageDemandeur("Vous venez de formuler une demande d'" . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                }
                if ($demande->getTypeDemande() == "Permission") {
                    $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                }
                if ($demande->getTypeDemande() == "Arrêt maladie") {
                    $notif->setMessageDemandeur("Vous venez de formuler une demande d'" . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                }
                if ($demande->getTypeDemande() == "Férie(s)") {
                    $notif->setMessageDemandeur("Vous venez de formuler une demande d'absence pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                }
                //création de la pièce jointe
//               $pieceJointe = new PiecesJointes();
                $pieceJointe = $demande->getPieceJointe();
                $pieceJointe->setAjouterPar($auteurDemande);
                $pieceJointe->setDateCreation(new \DateTime());
                $pieceJointe->setSalarie($salarie);
                $pieceJointe->preUpload();
                $pieceJointe->upload();
                $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());

                $em->persist($notification);
                $em->persist($demande);
                $em->persist($notif);
                $em->persist($pieceJointe);
                $em->persist($historiqueDroits);
                $em->flush();

                if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
                    return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
                }
//                else {
//                    return $this->redirect($this->generateUrl('superviseur_demande_permission_for_salarie')); //redirige vers la page mesdemandes
//                }
                if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
                    return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
                }
            } else {//Cas 2: Le reste des employés
                if ($nbNiveaux == 1) {
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurFinal($unite->getManager());

                    $notification->setValideurEnCours($demande->getValideurEnCours());

                    if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notification->setMessageDemandeur("Une demande d'" . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                    }
                    if ($demande->getTypeDemande() == "Permission") {
                        $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                    }
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $notification->setMessageDemandeur("Une demande d'" . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " . Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    if ($demande->getTypeDemande() == "Férie(s)") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notification->setMessageDemandeur("Une demande d'absence à été effectuée par " . $auteurDemande->getCivilite() . " . Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }

                    //$this->envoiEmailDemande($salarie,$demande);
                    $notif = new Notification();
                    $notif->setSalarie($auteurDemande);
                    $notif->setDemande($demande);

                    if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notif->setMessageDemandeur("Vous venez de formuler une demande d'" . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }
                    if ($demande->getTypeDemande() == "Permission") {
                        $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $notif->setMessageDemandeur("Vous venez de formuler une demande d'" . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }
                    if ($demande->getTypeDemande() == "Férie(s)") {
                        $notif->setMessageDemandeur("Vous venez de formuler une demande d'absence pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }

                    $pieceJointe = $demande->getPieceJointe();
                    $pieceJointe->setAjouterPar($auteurDemande);
                    $pieceJointe->setDateCreation(new \DateTime());
                    $pieceJointe->setSalarie($salarie);
                    $pieceJointe->preUpload();
                    $pieceJointe->upload();
                    $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($notif);
                    $em->persist($pieceJointe);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
                    }
//                    else {
//                        return $this->redirect($this->generateUrl('superviseur_demande_permission_for_salarie')); //redirige vers la page mesdemandes
//                    }
                    if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
                    }
                }
                if ($nbNiveaux == 2) {
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
                    $demande->setValideurFinal($demande->getValideurNiveau2());

                    $notification->setValideurEnCours($demande->getValideurEnCours());
                    if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notification->setMessageDemandeur("Une demande d'" . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                    }
                    if ($demande->getTypeDemande() == "Permission") {
                        $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                    }
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $notification->setMessageDemandeur("Une demande d'" . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " . Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    if ($demande->getTypeDemande() == "Férie(s)") {
                        $notification->setMessageDemandeur("Une demande d'absence à été effectuée par " . $auteurDemande->getCivilite() . " . Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }

                    //$this->envoiEmailDemande($salarie,$demande);
                    $notif = new Notification();
                    $notif->setSalarie($auteurDemande);
                    $notif->setDemande($demande);

                    if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notif->setMessageDemandeur("Vous venez de formuler une demande d'" . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }
                    if ($demande->getTypeDemande() == "Permission") {
                        $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $notif->setMessageDemandeur("Vous venez de formuler une demande d'" . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }
                    if ($demande->getTypeDemande() == "Férie(s)") {
                        $notif->setMessageDemandeur("Vous venez de formuler une demande d'absence pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }

                    $pieceJointe = $demande->getPieceJointe();
                    $pieceJointe->setAjouterPar($auteurDemande);
                    $pieceJointe->setDateCreation(new \DateTime());
                    $pieceJointe->setSalarie($salarie);
                    $pieceJointe->preUpload();
                    $pieceJointe->upload();
                    $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($notif);
                    $em->persist($pieceJointe);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
                    }
//                    else {
//                        return $this->redirect($this->generateUrl('superviseur_demande_permission_for_salarie')); //redirige vers la page mesdemandes
//                    }
                    if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
                    }
                }
                if ($nbNiveaux == 3) {
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
                    $demande->setValideurFinal($unite->getUniteSuivante2()->getManager());

                    $notification->setValideurEnCours($demande->getValideurEnCours());

                    if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notification->setMessageDemandeur("Une demande d'" . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                    }
                    if ($demande->getTypeDemande() == "Permission") {
                        $notification->setMessageDemandeur("Une demande de " . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " " . $auteurDemande . ". Elle sera transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                    }
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $notification->setMessageDemandeur("Une demande d'" . $demande->getTypeDemande() . " à été effectuée par " . $auteurDemande->getCivilite() . " . Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    if ($demande->getTypeDemande() == "Férie(s)") {
                        $notification->setMessageDemandeur("Une demande d'absence à été effectuée par " . $auteurDemande->getCivilite() . " . Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }

                    //$this->envoiEmailDemande($salarie,$demande);
                    $notif = new Notification();
                    $notif->setSalarie($auteurDemande);
                    $notif->setDemande($demande);

                    if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notif->setMessageDemandeur("Vous venez de formuler une demande d'" . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }
                    if ($demande->getTypeDemande() == "Permission") {
                        $notif->setMessageDemandeur("Vous venez de formuler une demande de " . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $notif->setMessageDemandeur("Vous venez de formuler une demande d'" . $demande->getTypeDemande() . " pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }
                    if ($demande->getTypeDemande() == "Férie(s)") {
                        $notif->setMessageDemandeur("Vous venez de formuler une demande d'absence pour " . $salarie->getCivilite() . " " . $salarie . ". Elle sera transmise à sa hierarchie.");
                    }

                    $pieceJointe = $demande->getPieceJointe();
                    $pieceJointe->setAjouterPar($auteurDemande);
                    $pieceJointe->setDateCreation(new \DateTime());
                    $pieceJointe->setSalarie($salarie);
                    $pieceJointe->preUpload();
                    $pieceJointe->upload();
                    $pieceJointe->setDownloadPath($pieceJointe->getAssetPath());

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($notif);
                    $em->persist($pieceJointe);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateur_demandes', array('id' => $salarie->getId())));
                    }
//                    else {
//                        return $this->redirect($this->generateUrl('superviseur_demande_permission_for_salarie')); //redirige vers la page mesdemandes
//                    }
                    if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateur_demandes', array('id' => $salarie->getId())));
                    }
                }
            }
        }
        //Récupération des collaborateurs dépendant du salarié connecté
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findBySuperviseur($salarie);
        $parampermissions = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->findAll();

        if (in_array("ROLE_SUPERVISEUR", $auteurDemande->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-absence-for-salarie.html.twig', array(
                        'entity' => $demande,
                        '$salarie' => $salarie,
                        'salaries' => $salaries,
                        'droits' => $droits,
                        'types' => $parampermissions,
                        'form' => $form->createView(),
            ));
        }

        if (in_array("ROLE_TOP_MANAGER", $auteurDemande->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-absence-for-salarie.html.twig', array(
                        'entity' => $demande,
                        '$salarie' => $salarie,
                        'salaries' => $salaries,
                        'droits' => $droits,
                        'types' => $parampermissions,
                        'form' => $form->createView(),
            ));
        }
    }

    /**
     * Affiche la page pour effectuer une 
     * nouvelle demande de congé
     *
     */
    public function supDemandePermissionForSalarieAction() {
        $em = $this->getDoctrine()->getManager();
        $entity = new Demande();
        $salarie = $this->getSalarieByUser();
        $droits = $salarie->getDroits();

        //Préparation du formulaire
        $entity->setAuteurDemande($salarie);
        $form = $this->calculAbsenceForm2($entity);

        //Récupération des collaborateurs dépendant du salarié connecté
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findBySuperviseur($salarie);
        $parampermissions = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->findAll();

        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-absence-for-salarie.html.twig', array(
                        'salarie' => $salarie,
                        'salaries' => $salaries,
                        'droits' => $droits,
                        'form' => $form->createView(),
                        'types' => $parampermissions,
                        'result' => $this->getRequest()->get('date'),
            ));
        }

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-absence-for-salarie.html.twig', array(
                        'salarie' => $salarie,
                        'salaries' => $salaries,
                        'droits' => $droits,
                        'form' => $form->createView(),
                        'types' => $parampermissions,
                        'result' => $this->getRequest()->get('date'),
            ));
        }
    }

    //    ####################### ################### ##################################           
    /*     * ********************************************************* */

    /**
     * Création d'une demande d'Absence
     *
     */
    public function createDemandeAbsenceAction(Request $request) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $demande = new Demande();

        //Recherche de l'admin dans la liste des salariés
        $admin = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByPoste("Administrateur IMA");

        //Recherche du DRH
        $DIRH = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findOneBySigle("DRH");
        $drh = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($DIRH->getManager()->getId());

        $form = $this->createCreateAbsenceForm($demande);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $droits = $salarie->getDroits();
            $unite = $salarie->getUnite();


            //On hydrate (un peu) l'entité !!          

            if ($demande->getMotif() == "Autres") {
                $demande->setTypeDemande('Absence exceptionnelle');
            } else {
                $demande->setTypeDemande('Permission');
            }
            if ($demande->getMotif() == "Rendez-vous medical") {
                //Ajout de la déclaration de rdv médicaux. @DESIRE le 15/10/2015
                $demande->setTypeDemande('Arrêt maladie');
            }
            if ($demande->getMotif() == "Fêtes réligieuses" || $demande->getMotif() == "FÃªtes réligieuses") {
                $demande->setTypeDemande('Férie(s)');
            }


            $demande->setSalarie($salarie);
            //Ajout de la gestion de l'historique des droits. @BHKONAN le 30/04/2015
            $historiqueDroits = new HistoriqueDroits();
            $historiqueDroits->setSalarie($salarie);
            $historiqueDroits->setDroits($droits);
            $historiqueDroits->setDemande($demande);
            $historiqueDroits->setSoldeCongeAncien($droits->getTotalDroitsAprendre());
            $historiqueDroits->setSoldePermissionAncien($droits->getSoldePermissions());


            $notification = new Notification();
            $notification->setSalarie($salarie);
            $notification->setDemande($demande);

            //code du 17 octobre 2015 -étape 1: sans prise en compte des suppléants
            if ($demande->getMotif() == "Rendez-vous medical") {
                $nbNiveaux = 1;
            } else {
                $nbNiveaux = $unite->getNbNiveauxValidation();
            }
            $demande->setNbNiveauxValidation($nbNiveaux);

            //Modification du message du valideur en cours en fonction du type de la demande (Modif par Desire le 16/04/2015)
            if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                $notification->setMessageValideurEnCours("Demande d'" . $demande->getTypeDemande() . " à valider.");
            }
            if ($demande->getTypeDemande() == "Permission") {
                $notification->setMessageValideurEnCours("Demande de " . $demande->getTypeDemande() . " à valider.");
            }
            if ($demande->getTypeDemande() == "Arrêt maladie") {
                $notification->setMessageValideurEnCours("Demande d'absence de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " à valider.");
            }
            if ($demande->getTypeDemande() == "Férie(s)") {
                $notification->setMessageValideurEnCours("Demande d'absence de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " à valider.");
            }

            //Cas particuliers
            //Cas 1: Demandeur est le manager de l'unité
            if ($salarie == $unite->getManager()) {
                if ($unite->getValideurPourManager3()) {
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                    } else {
                        $demande->setValideurFinal($unite->getValideurPourManager3()->getManager());
                        $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());

                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                    }
                }
                if ($unite->getValideurPourManager2()) {
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                    } else {
                        $demande->setValideurFinal($unite->getValideurPourManager2()->getManager());
                        $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                    }
                } else {
                    $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                }

                $notification->setValideurEnCours($demande->getValideurEnCours());
                //Modification du message du demandeur en fonction du type de la demande (Modif par Desire le 16/04/2015)
                if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                    $notification->setMessageDemandeur("Demande d'" . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                }
                if ($demande->getTypeDemande() == "Permission") {
                    $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                }
                if ($demande->getTypeDemande() == "Arrêt maladie") {
                    $notification->setMessageDemandeur("Vous avez formulé(e) une demande pour un " . $demande->getTypeDemande() . ". Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                }
                if ($demande->getTypeDemande() == "Férie(s)") {
                    $notification->setMessageDemandeur("Vous avez formulé(e) une demande d'absence pour un " . $demande->getTypeDemande() . ". Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                }

                //$this->envoiEmailDemande($salarie,$demande);
                $em->persist($notification);
                $em->persist($demande);
                $em->persist($historiqueDroits);
                $em->flush();

                if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                    return $this->redirect($this->generateUrl('top_manager_demandes'));
                }
                if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                    return $this->redirect($this->generateUrl('superviseur_demandes'));
                } else {
                    return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
                }
            } else {//Cas 2: Le reste des employés
                if ($nbNiveaux == 1) { //demandeur est soit assistant/conseiller/personnel rattaché du dg-dga-directeur
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurFinal($unite->getManager());

                    $notification->setValideurEnCours($demande->getValideurEnCours());

                    //Modification du message du demandeur en fonction du type de la demande (Modif par Desire le 16/04/2015)
                    if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notification->setMessageDemandeur("Demande d'" . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                    }
                    if ($demande->getTypeDemande() == "Permission") {
                        $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    if ($demande->getTypeDemande() == "Arrêt maladie") {
                        $notification->setMessageDemandeur("Vous avez formulé(e) une demande pour un " . $demande->getTypeDemande() . ". Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    if ($demande->getTypeDemande() == "Férie(s)") {
                        $notification->setMessageDemandeur("Vous avez formulé(e) une demande d'absence pour un " . $demande->getTypeDemande() . ". Elle a été transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    //$this->envoiEmailDemande($salarie,$demande);

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_demandes'));
                    }
                    if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('superviseur_demandes'));
                    } else {
                        return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
                    }
                }
                if ($nbNiveaux == 2) { //demandeur est :
                    //- agent service/cellule rattaché à  une direction
                    //-agent d'un département
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
                    $demande->setValideurFinal($demande->getValideurNiveau2());

                    $notification->setValideurEnCours($demande->getValideurEnCours());
                    //$notification->setMessageDemandeur("Demande de ".$demande->getTypeDemande()." transmise à votre ".$demande->getValideurEnCours()->getPoste()." .");
                    //Modification du message du demandeur en fonction du type de la demande (Modif par Desire le 16/04/2015)
                    if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notification->setMessageDemandeur("Demande d'" . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
                    }
                    if ($demande->getTypeDemande() == "Permission") {
                        $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    if ($demande->getTypeDemande() == "Férie(s)") {
                        $notification->setMessageDemandeur("Demande d'absence transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    //$this->envoiEmailDemande($salarie,$demande);

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($historiqueDroits);
                    $em->flush();
                    if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_demandes'));
                    }
                    if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('superviseur_demandes'));
                    } else {
                        return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
                    }
                }
                if ($nbNiveaux == 3) { //demandeur est agent d'un service (service->département ->direction)
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
                    $demande->setValideurFinal($unite->getUniteSuivante2()->getManager());

                    $notification->setValideurEnCours($demande->getValideurEnCours());
                    //$notification->setMessageDemandeur("Demande de ".$demande->getTypeDemande()." transmise à votre ".$demande->getValideurEnCours()->getPoste()." .");
                    //Modification du message du demandeur en fonction du type de la demande (Modif par Desire le 16/04/2015)
                    if ($demande->getTypeDemande() == "Absence exceptionnelle") { //Si le motif est différent de ceux autorisés, alors on a une absence exceptionnelle
                        $notification->setMessageDemandeur("Demande d'" . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    if ($demande->getTypeDemande() == "Permission") {
                        $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    if ($demande->getTypeDemande() == "Férie(s)") {
                        $notification->setMessageDemandeur("Demande d'absence transmise à votre " . $demande->getValideurEnCours()->getPoste() . ".");
                    }
                    
                    //$this->envoiEmailDemande($salarie,$demande);

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_demandes'));
                    }
                    if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('superviseur_demandes'));
                    } else {
                        return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
                    }
                }
            }
        }

        $parampermissions = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->findAll();


        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-absence.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'types' => $parampermissions,
                        'form' => $form->createView()
            ));
        }

        if (in_array("ROLE_SALARIE", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande:demande-absence.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'types' => $parampermissions,
                        'form' => $form->createView()
            ));
        }

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-absence.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droits' => $droits,
                        'types' => $parampermissions,
                        'form' => $form->createView()
            ));
        }
    }

    /**
     * Création d'une demande de Congé
     *
     */
    public function createDemandeCongeAction(Request $request) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $demande = new Demande();

        $form = $this->createCreateCongeForm($demande);
        $form->handleRequest($request);
//var_dump($demande);
//        die();
        if ($form->isValid()) {

            $droits = $salarie->getDroits();
            $unite = $salarie->getUnite();

            $demande->setTypeDemande('Conge');
            $demande->setSalarie($salarie);
            $demande->setSoldeDroits($droits->getTotalDroitsAprendre()); //Ajout du 30/04/2015 par BHKONAN
            //Ajout du 30/09/2015 par Desire
            //Récupération de la période de depot
//            $depot = $em->getRepository('KbhGestionCongesBundle:PeriodeDepotDemandes')->find(1);
//            $dateDebut = $depot->getDateDebut();
//            $dateFin = $depot->getDateFin();
//            $aujourdhui = new \DateTime();
            //Vérifions que la demande soit faite dans la période de dépot de congé
//            if ($dateDebut->format('d-m-Y') >= $aujourdhui->format('d-m-Y') || $aujourdhui->format('d-m-Y') <= $dateFin->format('d-m-Y')) {
//                $demandesDeposees = new DemandesDeposees();
//                $demandesDeposees->setSalarie($salarie);
//                $demandesDeposees->setDemande($demande);
//                $demandesDeposees->setUnite($salarie->getUnite());
//                $demandesDeposees->setDateEnvoi($aujourdhui);
//                $demandesDeposees->setStatut("En attente");
//
//
//                //############### SI LA DEMANDE EST FAITES DANS LA PERIODE DE DEPOT DE CONGES ###################
//                //Ajout de la gestion de l'historique des droits. @BHKONAN le 30/04/2015
//                $historiqueDroits = new HistoriqueDroits();
//                $historiqueDroits->setSalarie($salarie);
//                $historiqueDroits->setDroits($droits);
//                $historiqueDroits->setDemande($demande);
//                $historiqueDroits->setSoldeCongeAncien($droits->getTotalDroitsAprendre());
//                $historiqueDroits->setSoldePermissionAncien($droits->getSoldePermissions());
//                // Notifications
//                $notification = new Notification();
//                $notification->setSalarie($salarie);
//                $notification->setDemande($demande);
//
//                //code du 7 mars 2015 -étape 1: sans prise en compte des suppléants
//                $nbNiveaux = $unite->getNbNiveauxValidation();
//                $demande->setNbNiveauxValidation($nbNiveaux);
//
//                $notification->setMessageValideurEnCours("Demande de " . $demande->getTypeDemande() . " à  valider.");
//                //Cas particuliers
//                //Cas 1: Demandeur est le manager de l'unité
//                if ($salarie == $unite->getManager()) {
//                    if ($unite->getValideurPourManager3()) {
//                        $demande->setValideurFinal($unite->getValideurPourManager3()->getManager());
//                        $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
//                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
//
//                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
//                    }
//                    if ($unite->getValideurPourManager2()) {
//                        $demande->setValideurFinal($unite->getValideurPourManager2()->getManager());
//                        $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
//                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
//                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
//                    } else {
//                        $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
//                        $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
//                        $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
//                    }
//
//                    $notification->setValideurEnCours($demande->getValideurEnCours());
//                    $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
//
//                    //$this->envoiEmailDemande($salarie,$demande);
//
//                    $em->persist($notification);
//                    $em->persist($demande);
//                    $em->persist($historiqueDroits);
//                    $em->persist($demandesDeposees);
//                    $em->flush();
//
//                    if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
//                        return $this->redirect($this->generateUrl('top_manager_demandes'));
//                    }
//                    if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
//                        return $this->redirect($this->generateUrl('superviseur_demandes'));
//                    } else {
//                        return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
//                    }
//                } else {//Cas 2: Le reste des employés
//                    if ($nbNiveaux == 1) {
//                        $demande->setValideurNiveau1($unite->getManager());
//                        $demande->setValideurEnCours($unite->getManager());
//                        $demande->setValideurFinal($unite->getManager());
//
//                        $notification->setValideurEnCours($demande->getValideurEnCours());
//                        $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
//
//                        //$this->envoiEmailDemande($salarie,$demande);
//                        $em->persist($notification);
//                        $em->persist($demande);
//                        $em->persist($historiqueDroits);
//                        $em->persist($demandesDeposees);
//                        $em->flush();
//
//                        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('top_manager_demandes'));
//                        }
//                        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('superviseur_demandes'));
//                        } else {
//                            return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
//                        }
//                    }
//                    if ($nbNiveaux == 2) {
//                        $demande->setValideurNiveau1($unite->getManager());
//                        $demande->setValideurEnCours($unite->getManager());
//                        $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
//                        $demande->setValideurFinal($demande->getValideurNiveau2());
//
//                        $notification->setValideurEnCours($demande->getValideurEnCours());
//                        $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
//
//                        //$this->envoiEmailDemande($salarie,$demande);
//                        $em->persist($notification);
//                        $em->persist($demande);
//                        $em->persist($demandesDeposees);
//                        $em->persist($historiqueDroits);
//                        $em->flush();
//
//                        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('superviseur_demandes'));
//                        } else {
//                            return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
//                        }
//                    }
//                    if ($nbNiveaux == 3) {
//                        $demande->setValideurNiveau1($unite->getManager());
//                        $demande->setValideurEnCours($unite->getManager());
//                        $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
//                        $demande->setValideurFinal($unite->getUniteSuivante2()->getManager());
//
//                        $notification->setValideurEnCours($demande->getValideurEnCours());
//                        $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");
//
//                        //$this->envoiEmailDemande($salarie,$demande);
//                        $em->persist($notification);
//                        $em->persist($demande);
//                        $em->persist($historiqueDroits);
//                        $em->persist($demandesDeposees);
//                        $em->flush();
//
//                        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('top_manager_demandes'));
//                        }
//                        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
//                            return $this->redirect($this->generateUrl('superviseur_demandes'));
//                        } else {
//                            return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
//                        }
//                    }
//                }
//                //################ SI LA DEMANDE N'EST PAS FAITES DANS LA PERIODE DE DEPOT DE CONGES ##################
//            } 
//            else {

            //Ajout de la gestion de l'historique des droits. @BHKONAN le 30/04/2015
            $historiqueDroits = new HistoriqueDroits();
            $historiqueDroits->setSalarie($salarie);
            $historiqueDroits->setDroits($droits);
            $historiqueDroits->setDemande($demande);
            $historiqueDroits->setSoldeCongeAncien($droits->getTotalDroitsAprendre());
            $historiqueDroits->setSoldePermissionAncien($droits->getSoldePermissions());
            // Notifications
            $notification = new Notification();
            $notification->setSalarie($salarie);
            $notification->setDemande($demande);

            //code du 7 mars 2015 -étape 1: sans prise en compte des suppléants
            $nbNiveaux = $unite->getNbNiveauxValidation();
            $demande->setNbNiveauxValidation($nbNiveaux);

            $notification->setMessageValideurEnCours("Demande de " . $demande->getTypeDemande() . " à  valider.");
            //Cas particuliers
            //Cas 1: Demandeur est le manager de l'unité
            if ($salarie == $unite->getManager()) {
                if ($unite->getValideurPourManager3()) {
                    $demande->setValideurFinal($unite->getValideurPourManager3()->getManager());
                    $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
                    $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());

                    $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                }
                if ($unite->getValideurPourManager2()) {
                    $demande->setValideurFinal($unite->getValideurPourManager2()->getManager());
                    $demande->setValideurNiveau2($unite->getValideurPourManager2()->getManager());
                    $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                } else {
                    $demande->setValideurNiveau1($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurFinal($unite->getValideurPourManager1()->getManager());
                    $demande->setValideurEnCours($unite->getValideurPourManager1()->getManager());
                }

                $notification->setValideurEnCours($demande->getValideurEnCours());
                $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");

                //$this->envoiEmailDemande($salarie,$demande);

                $em->persist($notification);
                $em->persist($demande);
                $em->persist($historiqueDroits);
                $em->flush();

                if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                    return $this->redirect($this->generateUrl('top_manager_demandes'));
                }
                if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                    return $this->redirect($this->generateUrl('superviseur_demandes'));
                } else {
                    return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
                }
            } else {//Cas 2: Le reste des employés
                if ($nbNiveaux == 1) {
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurFinal($unite->getManager());

                    $notification->setValideurEnCours($demande->getValideurEnCours());
                    $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");

                    //$this->envoiEmailDemande($salarie,$demande);
                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_demandes'));
                    }
                    if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('superviseur_demandes'));
                    } else {
                        return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
                    }
                }
                if ($nbNiveaux == 2) {
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
                    $demande->setValideurFinal($demande->getValideurNiveau2());

                    $notification->setValideurEnCours($demande->getValideurEnCours());
                    $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");

                    //$this->envoiEmailDemande($salarie,$demande);
                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_demandes'));
                    }
                    if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('superviseur_demandes'));
                    } else {
                        return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
                    }
                }
                if ($nbNiveaux == 3) {
                    $demande->setValideurNiveau1($unite->getManager());
                    $demande->setValideurEnCours($unite->getManager());
                    $demande->setValideurNiveau2($unite->getUniteSuivante1()->getManager());
                    $demande->setValideurFinal($unite->getUniteSuivante2()->getManager());

                    $notification->setValideurEnCours($demande->getValideurEnCours());
                    $notification->setMessageDemandeur("Demande de " . $demande->getTypeDemande() . " transmise à votre " . $demande->getValideurEnCours()->getPoste() . " .");

                    //$this->envoiEmailDemande($salarie,$demande);
                    $em->persist($notification);
                    $em->persist($demande);
                    $em->persist($historiqueDroits);
                    $em->flush();

                    if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_demandes'));
                    }
                    if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                        return $this->redirect($this->generateUrl('superviseur_demandes'));
                    } else {
                        return $this->redirect($this->generateUrl('sa_demandes')); //redirige vers la page mesdemandes
                    }
                }
            }
//            } 
//Fin du traitement d'une demande hors periode de depot de conge
        }
//Fin de la condition de validité du formulaire

        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demande-conges.html.twig', array(
                        'entity' => $demande,
                        '$salarie' => $salarie,
                        'droits' => $droits,
                        'form' => $form->createView(),
            ));
        }

        if (in_array("ROLE_SALARIE", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande:demande-conges.html.twig', array(
                        'entity' => $demande,
                        '$salarie' => $salarie,
                        'droits' => $droits,
                        'form' => $form->createView(),
            ));
        }

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demande-conges.html.twig', array(
                        'entity' => $demande,
                        '$salarie' => $salarie,
                        'droits' => $droits,
                        'form' => $form->createView(),
            ));
        }
    }

    /* ################################ GESTION DES COLLABORATEURS DU SUPERVISEUR ######################################### */

    /**
     * Trouve et affiche toutes les demandes 
     * des collaborateurs.
     *
     */
    public function collabsDemandesAction() {
        $superviseur = $this->getSalarieByUser();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('KbhGestionCongesBundle:Demande')->findDemandesAvalider($superviseur);
        $number_2 = count($entities);

        $user = $this->getUser();
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demandes-collaborateurs.html.twig', array(
                        'entities' => $entities,
                        'salarie' => $superviseur,
                        'new' => $number_2,
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demandes-collaborateurs.html.twig', array(
                        'entities' => $entities,
                        'salarie' => $superviseur,
                        'new' => $number_2,
            ));
        }
    }

    /**
     * Trouve et affiche toutes les demandes 
     * des collaborateurs.
     *
     */
    public function demandesTraiteesAction() {

        $superviseur = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        if ($superviseur->getPoste() == "Directeur des Ressources Humaines") {

            $validees = $em->getRepository('KbhGestionCongesBundle:Demande')->findByEstValide(1);
            $refusees = $em->getRepository('KbhGestionCongesBundle:Demande')->findByEstRefuse(1);
            $traitees = $em->getRepository('KbhGestionCongesBundle:Demande')->findDemandesTraitees($superviseur);
        } else {

            $validees = $em->getRepository('KbhGestionCongesBundle:Demande')->findByEstValide(1);
            $refusees = $em->getRepository('KbhGestionCongesBundle:Demande')->findByEstRefuse(1);
            $traitees = $em->getRepository('KbhGestionCongesBundle:Demande')->findDemandesTraitees($superviseur);
        }

        $user = $this->getUser();
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demandes-traitees.html.twig', array(
                        'salarie' => $superviseur,
                        'valides' => $validees,
                        'refuses' => $refusees,
                        'traitees' => $traitees,
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demandes-traitees.html.twig', array(
                        'salarie' => $superviseur,
                        'valides' => $validees,
                        'refuses' => $refusees,
                        'traitees' => $traitees,
            ));
        }
    }

    /**
     * Trouve et affiche toutes les demandes 
     * des collaborateurs.
     *
     */
    public function demandesDeposeesAction() {

        $superviseur = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();
        $salaries = array();

        //Récupération des salariés de toutes les unités spécialement pour les top manager
        $collaborateurs = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();

        //Récupération des salariés rattachés au manager connecté
        $unite = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->find($superviseur->getUnite());
        $salaries[0] = $unite->getSalaries();

        //Récupération des salariés rattachés au manager connecté
        $cp = 1;
        foreach ($entities as $entity) {
            if ($entity->getUniteSuivante1() == $superviseur->getUnite() || $entity->getUniteSuivante2() == $superviseur->getUnite() || $entity->getUniteSuivante3() == $superviseur->getUnite()) {
                $salaries[$cp] = $entity->getSalaries();
            }
            $cp += 1;
        }

        $collab_demandes = $em->getRepository('KbhGestionCongesBundle:DemandesDeposees')->findAll();

        //RECUPERATION DES SALARIES AYANT OU N'AYANT PAS FORMULES DE DEMANDES DANS LA PERIODE DE DEPOT COTES RESPONSABLE
        $demandesEnAttente = array(); // un array d'objets salariés
        $demandesFormulees = array(); // un array d'objets salariés
        $i = 0;

        foreach ($collab_demandes as $demande) { // liste des demandes formulées temps
            foreach ($salaries as $salarie) { // Liste des tableaux de collaborateurs rattachés au responsable
                foreach ($salarie as $salarie) { // Liste des collaborateurs de chaque tableau rattachés au responsable
                    // Cas d'une demande formulée à temps
                    if ($salarie->getId() == $demande->getSalarie()->getId()) {
                        $demandesFormulees[$i] = $demande;
                    }

                    // Cas d'une demande n'ayant pas encore été formulée
                    if ($salarie->getId() != $demande->getSalarie()->getId()) {
                        if (!in_array($salarie, $demandesEnAttente)) {
                            $demandesEnAttente[$i] = $salarie;
                        }
                    }
                    $i += 1;
                }
            }
        }

        //RECUPERATION DES SALARIES AYANT OU N'AYANT PAS FORMULES DE DEMANDES DANS LA PERIODE DE DEPOT COTES TOP MANAGER
        $demandesEnAttente2 = array(); // un array d'objets salariés
        $demandesFormulees2 = array(); // un array d'objets salariés
        $retire = array(); // les compte admin et super admin
        $a = 0;
        $e = 0;

        foreach ($collab_demandes as $demande) { // liste des demandes formulées temps
            foreach ($collaborateurs as $salarie) { // Liste des collaborateurs du système
                // Cas d'une demande formulée à temps
                if ($salarie->getId() == $demande->getSalarie()->getId()) {
                    $demandesFormulees2[$a] = $demande;
                }

                // Cas d'une demande n'ayant pas encore été formulée
                if ($salarie->getId() != $demande->getSalarie()->getId() and $salarie->getStatutEmploi() == "Actif") {
                    if (in_array("ROLE_SUPER_ADMIN", $salarie->getUser()->getRoles()) == true || in_array("ROLE_ADMIN", $salarie->getUser()->getRoles()) == true) {
                        if (!in_array($salarie, $retire)) {
                            $retire[$e] = $salarie;
                            $e += 1;
                        }
                    }

                    if (in_array("ROLE_SUPER_ADMIN", $salarie->getUser()->getRoles()) == false || in_array("ROLE_ADMIN", $salarie->getUser()->getRoles()) == false) {
                        if (!in_array($salarie, $demandesEnAttente2)) {
                            $demandesEnAttente2[$a] = $salarie;
                        }
                    }
                }
                $a += 1;
            }
        }
//        var_dump(in_array("ROLE_SUPER_ADMIN", $collaborateurs[12]->getUser()->getRoles()));
//        var_dump($e);
//        var_dump($retire);
//        
//        die();
        // OPERATIONS POUR LES DIFFERENTES PERIODES DE DEPOT ET DE TRAITEMENTS
        //Récupération de la période de depot
        $depot = $em->getRepository('KbhGestionCongesBundle:PeriodeDepotDemandes')->find(1);

        //Récupération de la période de traitement
        $traitement = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);

        //################################### calcul du nombre de jour  pour la période de dépot ####################
        $debut = $depot->getDateDebut()->format('d-m-Y');
        $uday = new \DateTime();
        $fin = $depot->getDateFin()->format('d-m-Y');

        //Conversion en timestamp
        $tms_debut = strtotime($debut);
        $tms_uday = strtotime($uday->format('d-m-Y'));
        $tms_fin = strtotime($fin);
        $day = 60 * 60 * 24;

        //nombre de jours total
        $tms_delta = ($tms_fin - $tms_debut); //  rajouter +1 jr si la journée de la fin de période est inclus
        $nb_jour = round(($tms_delta / $day), 0);

        // nombre de jours restants
        $tms_delta_restant = ($tms_fin - $tms_uday); //  rajouter +1 jr si la journée de la fin de période est inclus
        $nb_jour_restant = round(($tms_delta_restant / $day), 0);

        //pourcentage du nombre de jours restant
        $pourcentage = (( $nb_jour - $nb_jour_restant ) * 100) / $nb_jour;

        //####################### calcul du nombre de jour  pour la période de traitement ##################
        $debut_traitement = $traitement->getDateDebut()->format('d-m-Y');
        $fin_traitement = $traitement->getDateFin()->format('d-m-Y');

        //Conversion en timestamp
        $tms_debut_traitement = strtotime($debut_traitement);
        $tms_fin_traitement = strtotime($fin_traitement);

        //nombre de jours total
        $tms_delta_traitement = ($tms_fin_traitement - $tms_debut_traitement); //  rajouter +1 jr si la journée de la fin de période est inclus
        $nb_jour_traitement = round(($tms_delta_traitement / $day), 0);

        // nombre de jours restants
        $tms_delta_traitement_restant = ($tms_fin_traitement - $tms_uday); //  rajouter +1 jr si la journée de la fin de période est inclus
        $nb_jour_traitement_restant = round(($tms_delta_traitement_restant / $day), 0);

        //pourcentage du nombre de jours restant
        $pourcentage_traitement = (( $nb_jour_traitement - $nb_jour_traitement_restant ) * 100) / $nb_jour_traitement;

        $user = $this->getUser();
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demandes-deposees.html.twig', array(
                        'salarie' => $superviseur,
                        'collab' => $salaries,
                        'collab_demandes' => $collab_demandes,
                        'depot' => $depot,
                        'traitement' => $traitement,
                        'nbJours' => $nb_jour_restant,
                        'nbJoursTotal' => $nb_jour,
                        'pourcentage' => $pourcentage,
                        'nbJoursTraitement' => $nb_jour_traitement_restant,
                        'nbJoursTotalTraitement' => $nb_jour_traitement,
                        'pourcentageTraitement' => $pourcentage_traitement,
                        'demandesEnAttente' => $demandesEnAttente,
                        'demandesFormulees' => $demandesFormulees,
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demandes-deposees-for-manager.html.twig', array(
                        'salarie' => $superviseur,
                        'salaries' => $collaborateurs,
                        'collab' => $salaries,
                        'collab_demandes' => $collab_demandes,
                        'depot' => $depot,
                        'traitement' => $traitement,
                        'nbJours' => $nb_jour_restant,
                        'nbJoursTotal' => $nb_jour,
                        'pourcentage' => $pourcentage,
                        'nbJoursTraitement' => $nb_jour_traitement_restant,
                        'nbJoursTotalTraitement' => $nb_jour_traitement,
                        'pourcentageTraitement' => $pourcentage_traitement,
                        'demandesEnAttente' => $demandesEnAttente2,
                        'demandesFormulees' => $demandesFormulees2,
                        'administrateurs' => $retire,
            ));
        }
    }

    /**
     * Trouve et affiche toutes les demandes 
     * d'un collaborateur.
     *
     */
    public function collabDemandesAction($id) {
        $superviseur = $this->getSalarieByUser();
        //recherche du collaborateur
        $em = $this->getDoctrine()->getManager();
        $collab = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);
        $entities = $em->getRepository('KbhGestionCongesBundle:Demande')->findBySalarie($collab);


        $user = $this->getUser();
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:demandes-collaborateur.html.twig', array(
                        'entities' => $entities,
                        'salarie' => $superviseur,
                        'collab' => $collab,
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:demandes-collaborateur.html.twig', array(
                        'entities' => $entities,
                        'salarie' => $superviseur,
                        'collab' => $collab,
            ));
        }
    }

    /* ################################ TRAITEMENT DES FORMULAIRES DU SUPERVISEUR ######################################### */

    /**
     * Permet au superviseur de mettre à  jour une 
     * demande d'absence d'un de ses collaborateurs
     * specialement le cas dun refus
     */
    public function refusDemandeCollabAction(Request $request, $id) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $demande = $em->getRepository('KbhGestionCongesBundle:Demande')->find($id);

        if (!$demande) {
            throw $this->createNotFoundException('Unable to find Demande entity.');
        }

        $editForm = $this->RefusDemandeForm($demande);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $salarie = $demande->getSalarie();
            $valideur = $this->getSalarieByUser();

            $demande->setEstEnCours(0);
            $demande->setEstRefuse(1);
            $demande->setRefusePar($valideur);
            $demande->setDateRefus(new \DateTime());
            $demande->setEstCloture(1);
            $demande->setDateCloture(new \DateTime());

            // Notifications : demandeur, valideur N1, observateurs

            $notification = new Notification();
            $notification->setValideurPrecedent($valideur);
            $notification->setSalarie($salarie);
            $notification->setMessageDemandeur("Demande rejetée par le " . $valideur->getPoste() . ".");
            if ($demande->getTypeDemande() == "Arrêt maladie" || $demande->getTypeDemande() == "Absence exceptionnelle") {
                $notification->setMessageValideurPrecedent("Vous avez rejeté la demande d'" . $demande->getTypeDemande() . ".");
            }else{
                $notification->setMessageValideurPrecedent("Vous avez rejeté la demande de " . $demande->getTypeDemande() . ".");
            }
            $notification->setDemande($demande);

            //$this->envoiEmailRefus($salarie,$demande);


            $em->persist($notification);
            $em->persist($demande);
            $em->flush();


            $user = $this->getUser();
            if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
                return $this->redirect($this->generateUrl('collaborateurs_demandes'));
            }
            if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                return $this->redirect($this->generateUrl('top_manager_collaborateurs_demandes'));
            }
        }

        $valide_Form = $this->ValideDemandeForm($demande);
        $refus_Form = $this->RefusDemandeForm($demande);
        $droits = $salarie->getDroits();

        $user = $this->getUser();
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:detail-demande_2.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droit' => $droits,
                        'form1' => $valide_Form->createView(),
                        'form2' => $refus_Form->createView(),
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:detail-demande_2.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droit' => $droits,
                        'form1' => $valide_Form->createView(),
                        'form2' => $refus_Form->createView(),
            ));
        }
    }

    /**
     * Permet au superviseur de valider une demande
     */
    public function valideDemandeCollabAction(Request $request, $id) {
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $demande = $em->getRepository('KbhGestionCongesBundle:Demande')->find($id);

        if (!$demande) {
            throw $this->createNotFoundException('Unable to find Demande entity.');
        }

        $editForm = $this->ValideDemandeForm($demande);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $salarie = $demande->getSalarie();
            $valideurConnecte = $this->getSalarieByUser();
            $notification = new Notification();

            $notification->setSalarie($salarie);
            $niveauxValidation = $demande->getNbNiveauxValidation();

            if ($valideurConnecte == $demande->getValideurNiveau1()) {
                //Si c'est la validation finale
                if ($valideurConnecte == $demande->getValideurFinal()) {
                    $demande->setEstValideNiveau1(1);
                    $demande->setDateValidation1(new \DateTime());

//                    var_dump($salarie->getSuperviseur());
//                    var_dump($demande);
                    $this->cloturerDemande($demande, $salarie, $notification, $valideurConnecte);

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->flush();

                    if ($demande->getTypeDemande() == "Arrêt maladie") {

                        // Préparation de l'absence à justifier
                        $now = new \DateTime();
                        $delaiMax = (60 * 60 * 24) * 2; // deux jours
                        $now_tms = $now->getTimestamp(); // conversion en timestamp
                        //determination du delai maximal
                        $delai_tms = $now_tms + $delaiMax;
                        $dateMax = date('Y-m-d H:i:s', $delai_tms);

                        $absenceAjustifier = new AbsencesAjustifier();
                        $absenceAjustifier->setDemande($demande);
                        $absenceAjustifier->setDateCreation(new \DateTime());
//                  $absenceAjustifier->setDateMax($dateMax);
                        $absenceAjustifier->setStatut("En attente");
                        $absenceAjustifier->setJustifieAtemps(false);
                        $em->persist($absenceAjustifier);
                        $em->flush();
                    }

                    if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateurs_demandes'));
                    }
                    if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateurs_demandes'));
                    }
                } else {
                    //On est au 1er niveau de validation
                    $demande->setEstValideNiveau1(1);
                    $demande->setDateValidation1(new \DateTime());
                    $demande->setValideurNiveau1($valideurConnecte);
                    $demande->setValideurEncours($demande->getValideurNiveau2());

                    if ($demande->getTypeDemande() != "Absence exceptionnelle") {
                        $texte = "de";
                    } else {
                        $texte = "d'";
                    }

                    $notification->setValideurPrecedent($valideurConnecte);
                    $notification->setMessageDemandeur("Première validation par le " . $valideurConnecte->getPoste() . " .");
                    $notification->setMessageValideurPrecedent("Vous avez validé la demande " . $texte . "  " . $demande->getTypeDemande() . " de " . $demande->getSalarie() . ".");
                    $notification->setValideurEncours($demande->getValideurNiveau2());
                    $notification->setMessageValideurEnCours("Demande " . $texte . "  " . $demande->getTypeDemande() . " de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie()->getNomprenom() . " à  valider.");
                    $notification->setDemande($demande);

                    // $this->envoiEmailValidationNiveau1($salarie, $demande);


                    $em->persist($notification);
                    $em->persist($demande);
                    $em->flush();


                    $user = $this->getUser();
                    if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateurs_demandes'));
                    }
                    if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateurs_demandes'));
                    }
                }
            }

            if ($valideurConnecte == $demande->getValideurNiveau2()) {
                //Si c'est la validation finale
                if ($valideurConnecte == $demande->getValideurFinal()) {
                    $demande->setEstValideNiveau2(1);
                    $demande->setDateValidation2(new \DateTime());
                    $demande->setValideurNiveau2($valideurConnecte);
                    $this->cloturerDemande($demande, $salarie, $notification, $valideurConnecte);

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->flush();
                    return $this->redirect($this->generateUrl('collaborateurs_demandes'));
                } else {
                    //On est au 2e niveau de validation
                    $demande->setEstValideNiveau2(1);
                    $demande->setDateValidation2(new \DateTime());
                    $demande->setValideurNiveau2($valideurConnecte);
                    $demande->setValideurEncours($demande->getValideurFinal());

                    if ($demande->getTypeDemande() != "Absence exceptionnelle") {
                        $texte = "de";
                    } else {
                        $texte = "d'";
                    }

                    $notification->setValideurEncours($demande->getValideurFinal());
                    $notification->setValideurPrecedent($valideurConnecte);
                    $notification->setMessageDemandeur("2e validation par le " . $valideurConnecte->getPoste() . " .");
                    $notification->setMessageValideurPrecedent("Vous avez validé la demande " . $texte . " " . $demande->getTypeDemande() . " de " . $demande->getSalarie() . ".");
                    $notification->setMessageValideurEncours("Demande " . $texte . " " . $demande->getTypeDemande() . " de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie()->getNomprenom() . " à  valider.");
                    $notification->setDemande($demande);

                    //$this->envoiEmailValidationNiveau2($salarie, $demande);


                    $em->persist($notification);
                    $em->persist($demande);
                    $em->flush();

                    $user = $this->getUser();
                    if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateurs_demandes'));
                    }
                    if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateurs_demandes'));
                    }
                }
            }
            if ($valideurConnecte == $demande->getValideurNiveau3()) {
                //Si c'est la validation finale
                if ($valideurConnecte == $demande->getValideurFinal()) {
                    $demande->setEstValideNiveau3(1);
                    $demande->setDateValidation3(new \DateTime());
                    $demande->setValideurNiveau3($valideurConnecte);
                    $this->cloturerDemande($demande, $salarie, $notification, $valideurConnecte);

                    $em->persist($notification);
                    $em->persist($demande);
                    $em->flush();
                    return $this->redirect($this->generateUrl('collaborateurs_demandes'));
                } else {
                    //On est au 3e niveau de validation
                    $demande->setEstValideNiveau3(1);
                    $demande->setDateValidation3(new \DateTime());
                    $demande->setValideurNiveau3($valideurConnecte);
                    $demande->setValideurEncours($demande->getValideurFinal());

                    if ($demande->getTypeDemande() != "Absence exceptionnelle") {
                        $texte = "de";
                    } else {
                        $texte = "d'";
                    }

                    $notification->setValideurEncours($demande->getValideurFinal());
                    $notification->setValideurPrecedent($valideurConnecte);
                    $notification->setMessageDemandeur("3e validation par le " . $valideurConnecte->getPoste() . " .");
                    $notification->setMessageValideurPrecedent("Vous avez validé la demande " . $texte . " " . $demande->getTypeDemande() . " de " . $demande->getSalarie() . ".");
                    $notification->setMessageValideurEncours("Demande " . $texte . " " . $demande->getTypeDemande() . " de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie()->getNomprenom() . " à  valider.");
                    $notification->setDemande($demande);

                    //$this->envoiEmailValidationNiveau3($salarie, $demande);


                    $em->persist($notification);
                    $em->persist($demande);
                    $em->flush();

                    $user = $this->getUser();
                    if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
                        return $this->redirect($this->generateUrl('collaborateurs_demandes'));
                    }
                    if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                        return $this->redirect($this->generateUrl('top_manager_collaborateurs_demandes'));
                    }
                }
            }

            //Si validation finale
            if ($valideurConnecte == $demande->getValideurFinal()) {
                //Clôture
                $notification->setDemande($demande);
                $demande->SetEstEncours(0);
                $demande->setEstValide(1);
                $demande->SetEstCloture(1);
                $demande->setValideurFinal($valideurConnecte);
                $demande->setDateValidation(new \DateTime());
                $demande->setDateCloture(new \DateTime());

                //notification finale
                $em = $this->getDoctrine()->getManager();
                $drh = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByPoste("Directeur des ressources humaines");
                $admin = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByPoste("Administrateur IMA");
                $notification->setObservateur($drh);
                $notification->setSuperieurN1($salarie->getSuperviseur());
                if ($demande->getTypeDemande() != "Absence exceptionnelle" || $demande->getTypeDemande() != "Arrêt maladie") {
                    $texte = "de";
                } else {
                    $texte = "d'";
                }

                if ($demande->getTypeDemande() == "Arrêt maladie") {
                    $notification->setAdmin($admin);
                    $notification->setMessageValideurSuivant("Demande d'" . $demande->getTypeDemande() . " de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " à justifier.");
                    $notification->setValideurPrecedent($valideurConnecte);
                    $notification->setMessageDemandeur("Validation finale par le " . $valideurConnecte->getPoste() . " de votre demande d'" . $demande->getTypeDemande() . ".");
                    $notification->setMessageValideurPrecedent("Vous avez validé la demande d'" . $demande->getTypeDemande() . " de " . $demande->getSalarie() . ".");
                    $notification->setMessageFinal("Demande d'" . $demande->getTypeDemande() . " validée le " . $demande->getDateValidation()->format('d-m-Y H:i') . " " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " est donc autorisé(e) à s'absenter pour la durée souhaitée.");

                    // Préparation de l'absence à justifier
                    $now = new \DateTime();
                    $delaiMax = (60 * 60 * 24) * 2; // deux jours
                    $now_tms = $now->getTimestamp(); // conversion en timestamp
                    //determination du delai maximal
                    $delai_tms = $now_tms + $delaiMax;
                    $dateMax = date('Y-m-d H:i:s', $delai_tms);

                    $absenceAjustifier = new AbsencesAjustifier();
                    $absenceAjustifier->setDemande($demande);
                    $absenceAjustifier->setDateCreation(new \DateTime());
//                  $absenceAjustifier->setDateMax($dateMax);
                    $absenceAjustifier->setStatut("En attente");
                    $absenceAjustifier->setDateMax($dateMax);
                    $absenceAjustifier->setJustifieAtemps(false);
                    $em->persist($absenceAjustifier);
                    $em->flush();
                } else {
                    $notification->setValideurPrecedent($valideurConnecte);
                    $notification->setMessageDemandeur("Validation finale par le " . $valideurConnecte->getPoste() . " de votre demande " . $texte . " " . $demande->getTypeDemande() . ".");
                    $notification->setMessageValideurPrecedent("Vous avez validé la demande " . $texte . " " . $demande->getTypeDemande() . " de " . $demande->getSalarie() . ".");
                    $notification->setMessageFinal("Demande " . $texte . " " . $demande->getTypeDemande() . " validée le " . $demande->getDateValidation()->format('d-m-Y H:i') . " " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " est donc autorisé(e) à s'absenter pour la durée souhaitée.");
                }

                $this->mettreAjourCompteurs($demande, $salarie, $notification);
                //$this->envoiEmailDecision($salarie, $demande);

                $em->persist($notification);
                $em->persist($demande);
                $em->flush();

                if ($demande->getTypeDemande() == "Arrêt maladie") {

                    // Préparation de l'absence à justifier
                    $now = new \DateTime();
                    $delaiMax = (60 * 60 * 24) * 2; // deux jours
                    $now_tms = $now->getTimestamp(); // conversion en timestamp
                    //determination du delai maximal
                    $delai_tms = $now_tms + $delaiMax;
                    $dateMax = date('Y-m-d H:i:s', $delai_tms);

                    $absenceAjustifier = new AbsencesAjustifier();
                    $absenceAjustifier->setDemande($demande);
                    $absenceAjustifier->setDateCreation(new \DateTime());
//                  $absenceAjustifier->setDateMax($dateMax->format());
                    $absenceAjustifier->setStatut("En attente");
                    $em->persist($absenceAjustifier);
                    $em->flush();
                }

                $user = $this->getUser();
                if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
                    return $this->redirect($this->generateUrl('collaborateurs_demandes'));
                }
                if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                    return $this->redirect($this->generateUrl('top_manager_collaborateurs_demandes'));
                }
            }
        }

        $valide_Form = $this->ValideDemandeForm($demande);
        $refus_Form = $this->RefusDemandeForm($demande);
        $droits = $salarie->getDroits();

        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Superviseur:detail-demande_2.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droit' => $droits,
                        'form1' => $valide_Form->createView(),
                        'form2' => $refus_Form->createView(),
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Demande\Top-manager:detail-demande_2.html.twig', array(
                        'entity' => $demande,
                        'salarie' => $salarie,
                        'droit' => $droits,
                        'form1' => $valide_Form->createView(),
                        'form2' => $refus_Form->createView(),
            ));
        }
    }

    /*     * ****************************************FORMULAIRES **************************************************************** */

    /**
     * Créé le formulaire de modification d'une demande d'absence.
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function RefusDemandeForm(Demande $entity) {
        $form = $this->createForm(new ValidationDemandeType(), $entity, array(
            'action' => $this->generateUrl('collaborateur_demande_absence_refus', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('raisonRefus', 'textarea', array('required' => 1));

        return $form;
    }

    /**
     * Créé le formulaire de modification d'une demande d'absence.
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function ValideDemandeForm(Demande $entity) {
        $form = $this->createForm(new ValidationDemandeType(), $entity, array(
            'action' => $this->generateUrl('collaborateur_demande_absence_valide', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));


        return $form;
    }

    /*     * ***********************NOUVEAUX FORMULAIRES @05052015********************** */

    /**
     * Créé le formulaire de de calcul de l'absence
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function calculAbsenceForm(Demande $entity) {
        $form = $this->createForm(new DemandeType(), $entity, array(
            'action' => $this->generateUrl('calcul_demande_absence'),
            'method' => 'POST',
        ));
        $form->add('motif', 'hidden', array('required' => true));
        return $form;
    }

    /**
     * Créé le formulaire de de calcul du congé
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function calculCongeForm(Demande $entity) {
        $form = $this->createForm(new DemandeType(), $entity, array(
            'action' => $this->generateUrl('calcul_demande_conge'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Créé le formulaire de de calcul du congé pour les demandes provenant des superviseurs
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function calculCongeForm2(Demande $entity) {
        $form = $this->createForm(new DemandeForSalarieType(), $entity, array(
            'action' => $this->generateUrl('calcul_demande_conge_for_salarie'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Créé le formulaire de de calcul du congé pour les demandes provenant des superviseurs
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function calculAbsenceForm2(Demande $entity) {
        $form = $this->createForm(new DemandeForSalarieType(), $entity, array(
            'action' => $this->generateUrl('calcul_demande_permission_for_salarie'),
            'method' => 'POST',
        ));
        $form->add('motif', 'hidden');
        return $form;
    }

    /**
     * Créé le formulaire de modification d'une demande d'absence.
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditAbsenceForm(Demande $entity) {
        $form = $this->createForm(new DemandeType(), $entity, array(
            'action' => $this->generateUrl('sa_demande_absence_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Créé le formulaire de demande d'absence
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateAbsenceForm(Demande $entity) {
        $form = $this->createForm(new DemandeType(), $entity, array(
            'action' => $this->generateUrl('sa_demande_absence_create'),
            'method' => 'POST',
        ));

        $form->add('motif', 'hidden', array('required' => true));

        return $form;
    }

    /**
     * Créé le formulaire de modification d'une demande de congé.
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditCongeForm(Demande $entity) {
        $form = $this->createForm(new DemandeType(), $entity, array(
            'action' => $this->generateUrl('sa_demande_conge_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Créé le formulaire de demande de congé
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateCongeForm(Demande $entity) {
        $form = $this->createForm(new DemandeType(), $entity, array(
            'action' => $this->generateUrl('sa_demande_conge_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Créé le formulaire de demande de congé
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCongeBySupForm(Demande $entity) {
        $form = $this->createForm(new DemandeBySuperviseurType(), $entity, array(
            'action' => $this->generateUrl('sup_demande_conge_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Créé le formulaire de demande de congé
     *
     * @param Demande $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAbsenceBySupForm(Demande $entity) {
        $form = $this->createForm(new DemandeBySuperviseurType(), $entity, array(
            'action' => $this->generateUrl('sup_demande_permission_create'),
            'method' => 'POST',
        ));
        $form->add('motif', 'hidden', array('required' => true));
        return $form;
    }

    //    ############################## BASE DE DONNEES #############################

    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function demandesListeAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Demande')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Demandes:demandes.html.twig', array(
                    'entities' => $entities,
        ));
    }

//    ############################## FONCTIONS ADDITIONNELLES #############################

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

    public function mettreAjourCompteurs($demande, $salarie, $notification) {
        $em = $this->getDoctrine()->getManager();
        if ($demande->getTypeDemande() == "Conge") {
            // Création du congé
            $conge = new Conge();
            $droits = $salarie->getDroits();

            $conge->setDateDebut($demande->getDateDebut());
            $conge->setDateFin($demande->getDateFin());
            $conge->setDateRetour($demande->getDateRetour());
            $conge->setNbJoursOuvrables($demande->getNbJoursOuvrables());
            $conge->setSalarie($salarie);
            $conge->setDemande($demande);

            //Droits
            $droits->setDroitsPris($demande->getNbJoursOuvrables());
            $droits->setTotalDroitsAprendre();
            //Ajout de la gestion de l'historique des droits. @BHKONAN le 30/04/2015
            $historiqueDroits = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findOneByDemande($demande);

            $historiqueDroits->setSoldeCongeNouveau($droits->getTotalDroitsAprendre());
            $historiqueDroits->setSoldePermissionNouveau($droits->getSoldePermissions());
            $historiqueDroits->setConge($conge->getId());
            $historiqueDroits->setDateModification(new \DateTime());

            $em->persist($notification);
            $em->persist($demande);
            $em->persist($conge);
            $em->persist($droits);
            $em->persist($historiqueDroits);
            $em->flush();
        }
        if ($demande->getTypeDemande() == "Permission") {
            // Création de l'absence
            $absence = new Absence();
            $droits = $salarie->getDroits();
            $duree = $demande->getNbJoursOuvrables();

            //Vérification de la durée de l'absence (si elle est en heure ou en jour)
            $heureDateRetour = $demande->getDateDebut()->format('H:i:s');
            if ($heureDateRetour != '00:00:00'){
                $newDuree = round($duree/24, 2);
                $duree = $newDuree;
            }

            $absence->setMotif($demande->getMotif());
            $absence->setDateDebut($demande->getDateDebut());
            $absence->setDateFin($demande->getDateFin());
            $absence->setDateRetour($demande->getDateRetour());
            $absence->setNbJoursOuvrables($duree);
            $absence->setSalarie($salarie);
            $absence->setDemande($demande);

            // CAS : Où le sode de permissions est  inferieur à 10 .
            if (($droits->getSoldePermissions() - $duree) >= 0) {
                //Droits
                $droits->setPermissionsPrises($duree);
                if ($droits->getPermissionsPrises() == 10) {
                    $droits->setSoldePermissions(0);
                }
                if ($droits->getPermissionsPrises() != 10) {
                    $droits->updateSoldePermissions();
                }
                //Ajout de la gestion de l'historique des droits. @BHKONAN le 30/04/2015
                $historiqueDroits = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findOneByDemande($demande);

                $historiqueDroits->setSoldeCongeNouveau($droits->getTotalDroitsAprendre());
                $historiqueDroits->setSoldePermissionNouveau($droits->getSoldePermissions());
                $historiqueDroits->setAbsence($absence->getId());
                $historiqueDroits->setDateModification(new \DateTime());
            }

            // CAS : Où le sode de permissions est  à 10 .
            if (($droits->getSoldePermissions()) - ($duree) < 0 and ( $droits->getSoldePermissions()) >= 0) {
                if (($droits->getSoldePermissions()) == 0) {
                    // Updates des droits du salarié
                    $droits->setDroitsPris($duree);
                    $droits->setTotalDroitsAprendre();


                    //Ajout de la gestion de l'historique des droits. @BHKONAN le 30/04/2015
                    $historiqueDroits = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findOneByDemande($demande);

                    $historiqueDroits->setSoldeCongeNouveau(($droits->getTotalDroitsAprendre()) - ($duree));
                    $historiqueDroits->setSoldePermissionNouveau(0);
                    $historiqueDroits->setAbsence($absence->getId());
                    $historiqueDroits->setDateModification(new \DateTime());
                }
                if (($droits->getSoldePermissions()) > 0) {
                    // Il faut trouver la valeur annulant le solde de permission
                    // Récupération du solde existant
                    $resultat = $droits->getSoldePermissions();

                    // il faut maintenant trouver la valeur à retrancher sur le solde de congé
                    $delta = $duree - $resultat;

                    // Updates des droits du salarié
                    $droits->setDroitsPris($delta);
                    $droits->setTotalDroitsAprendre();

                    //Droits
                    $droits->setPermissionsPrises(10);
                    $droits->setSoldePermissions(0);
                    //Ajout de la gestion de l'historique des droits. @BHKONAN le 30/04/2015
                    $historiqueDroits = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findOneByDemande($demande);

                    $historiqueDroits->setSoldeCongeNouveau(($droits->getTotalDroitsAprendre()));
                    $historiqueDroits->setSoldePermissionNouveau(0);
                    $historiqueDroits->setAbsence($absence->getId());
                    $historiqueDroits->setDateModification(new \DateTime());
                }
            }

            $em->persist($notification);
            $em->persist($demande);
            $em->persist($absence);
            $em->persist($droits);
            $em->persist($historiqueDroits);

            $em->flush();
        }

        if ($demande->getTypeDemande() == "Absence exceptionnelle") {

            //Initialisation de la valeur de la durée
            $duree = $demande->getNbJoursOuvrables();

            //Vérification de la durée de l'absence (si elle est en heure ou en jour)
            $heureDateRetour = $demande->getDateDebut()->format('H:i:s');
            if ($heureDateRetour != '00:00:00'){
                $newDuree = round($duree/24, 2);
                $duree = $newDuree;
            }
            if ($demande->getMotif() == "Fêtes réligieuses") {
                // Création de l'absence
                $absence = new Absence();
                $droits = $salarie->getDroits();

                $absence->setMotif("Jours férié");
                $absence->setDateDebut($demande->getDateDebut());
                $absence->setDateFin($demande->getDateFin());
                $absence->setDateRetour($demande->getDateRetour());
                $absence->setNbJoursOuvrables($duree);
                $absence->setSalarie($salarie);
                $absence->setDemande($demande);

                $historiqueDroits = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findOneByDemande($demande);

                $historiqueDroits->setSoldeCongeNouveau($droits->getTotalDroitsAprendre());
                $historiqueDroits->setSoldePermissionNouveau($droits->getSoldePermissions());
                $historiqueDroits->setAbsence($absence->getId());
                $historiqueDroits->setDateModification(new \DateTime());

                $em->persist($notification);
                $em->persist($demande);
                $em->persist($absence);
                $em->persist($droits);
                $em->persist($historiqueDroits);

                $em->flush();
            }
            else {

                // Création de l'absence
                $absence = new Absence();
                $droits = $salarie->getDroits();

                $absence->setMotif("Absence exceptionnelle");
                $absence->setDateDebut($demande->getDateDebut());
                $absence->setDateFin($demande->getDateFin());
                $absence->setDateRetour($demande->getDateRetour());
                $absence->setNbJoursOuvrables($duree);
                $absence->setSalarie($salarie);
                $absence->setDemande($demande);

                //Droits
                $droits->setDroitsPris($duree);
                $droits->setTotalDroitsAprendre();

                $historiqueDroits = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findOneByDemande($demande);

                $historiqueDroits->setSoldeCongeNouveau($droits->getTotalDroitsAprendre());
                $historiqueDroits->setSoldePermissionNouveau($droits->getSoldePermissions());
                $historiqueDroits->setAbsence($absence->getId());
                $historiqueDroits->setDateModification(new \DateTime());

                $em->persist($notification);
                $em->persist($demande);
                $em->persist($absence);
                $em->persist($droits);
                $em->persist($historiqueDroits);

                $em->flush();
            }
        }
        if ($demande->getTypeDemande() == "Arrêt maladie") {
            // Création de l'absence
            $absence = new Absence();
            $droits = $salarie->getDroits();

            $absence->setMotif("Rendez-vous médical");
            $absence->setDateDebut($demande->getDateDebut());
            $absence->setDateFin($demande->getDateFin());
            $absence->setDateRetour($demande->getDateRetour());
            $absence->setNbJoursOuvrables($demande->getNbJoursOuvrables());
            $absence->setSalarie($salarie);
            $absence->setDemande($demande);

            $historiqueDroits = $em->getRepository('KbhGestionCongesBundle:HistoriqueDroits')->findOneByDemande($demande);

            $historiqueDroits->setSoldeCongeNouveau($droits->getTotalDroitsAprendre());
            $historiqueDroits->setSoldePermissionNouveau($droits->getSoldePermissions());
            $historiqueDroits->setAbsence($absence->getId());
            $historiqueDroits->setDateModification(new \DateTime());

            $em->persist($notification);
            $em->persist($demande);
            $em->persist($absence);
            $em->persist($droits);
            $em->persist($historiqueDroits);

            $em->flush();
        }
    }

    public function cloturerDemande($demande, $salarie, $notification, $valideurConnecte) {

        //Clôture
        $notification->setDemande($demande);
        $demande->SetEstEncours(0);
        $demande->setEstValide(1);
        $demande->SetEstCloture(1);
        $demande->setValideurFinal($valideurConnecte);
        $demande->setDateValidation(new \DateTime());
        $demande->setDateCloture(new \DateTime());

        //notification finale
        $em = $this->getDoctrine()->getManager();
        $drh = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByPoste("Directeur des ressources humaines");
        $admin = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByPoste("Administrateur IMA");
        $notification->setObservateur($drh);
//        var_dump($salarie);
//        die();
        $notification->setSuperieurN1($salarie->getSuperviseur());
        if ($demande->getTypeDemande() != "Absence exceptionnelle" || $demande->getTypeDemande() != "Arrêt maladie") {
            $texte = "de";
        } else {
            $texte = "d'";
        }

        if ($demande->getTypeDemande() == "Férie(s)") {
            $notification->setAdmin($admin);
            $notification->setMessageValideurSuivant("Demande d'absence de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " à justifier.");
            $notification->setValideurPrecedent($valideurConnecte);
            $notification->setMessageDemandeur("Validation finale par le " . $valideurConnecte->getPoste() . " de votre demande d'absence.");
            $notification->setMessageValideurPrecedent("Vous avez validé la demande d'absence de " . $demande->getSalarie() . ".");
            $notification->setMessageFinal("Demande d'absence validée le " . $demande->getDateValidation()->format('d-m-Y H:i') . " " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " est donc autorisé(e) à s'absenter pour la durée souhaitée.");
        }
        if ($demande->getTypeDemande() == "Arrêt maladie") {
            $notification->setAdmin($admin);
            $notification->setMessageValideurSuivant("Demande d' " . $demande->getTypeDemande() . " de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " à justifier.");
            $notification->setValideurPrecedent($valideurConnecte);
            $notification->setMessageDemandeur("Validation finale par le " . $valideurConnecte->getPoste() . " de votre demande d'" . $demande->getTypeDemande() . ".");
            $notification->setMessageValideurPrecedent("Vous avez validé la demande d'" . $demande->getTypeDemande() . " de " . $demande->getSalarie() . ".");
            $notification->setMessageFinal("Demande d'" . $demande->getTypeDemande() . " validée le " . $demande->getDateValidation()->format('d-m-Y H:i') . " " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " est donc autorisé(e) à s'absenter pour la durée souhaitée.");
        }else {
            $notification->setValideurPrecedent($valideurConnecte);
            $notification->setMessageDemandeur("Validation finale par le " . $valideurConnecte->getPoste() . " de votre demande " . $texte . " " . $demande->getTypeDemande() . ".");
            $notification->setMessageValideurPrecedent("Vous avez validé la demande " . $texte . " " . $demande->getTypeDemande() . " de " . $demande->getSalarie() . ".");
            $notification->setMessageFinal("Demande " . $texte . " " . $demande->getTypeDemande() . " validée le " . $demande->getDateValidation()->format('d-m-Y H:i') . " " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie() . " est donc autorisé(e) à s'absenter pour la durée souhaitée.");
        }
        // Envoie Email final
        // $this->envoiEmailDecision($salarie,$demande,$drh);

        $this->mettreAjourCompteurs($demande, $salarie, $notification);
    }

    public function validationNiveau1($demande, $valideurConnecte, $notification) {
        $demande->setEstValideNiveau1(1);
        $demande->setDateValidation1(new \DateTime());
        $demande->setValideurNiveau1($valideurConnecte);
        $demande->setValideurEncours($demande->getValideurNiveau2());
        if ($demande->getTypeDemande() != "Absence exceptionnelle" || $demande->getTypeDemande() != "Arrêt maladie") {
            $texte = "de";
        } else {
            $texte = "d'";
        }
        $notification->setMessageDemandeur("Demande ".$texte." ". $demande->getTypeDemande() . " mise à jour (1e validation) par " . $valideurConnecte->getPoste() . ".");
        $notification->setMessageValideurEnCours("Vous avez validé la demande ".$texte." ". $demande->getTypeDemande() . " de " . $demande->getSalarie() . ".");
        $notification->setMessageValideurSuivant("Demande ".$texte." ". $demande->getTypeDemande() . " de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie()->getNomprenom() . " à valider.");
        $notification->setDemande($demande);
    }

    public function validationNiveau2($demande, $valideurConnecte, $notification) {
        $demande->setEstValideNiveau2(1);
        $demande->setDateValidation2(new \DateTime());
        $demande->setValideurNiveau2($valideurConnecte);
        if ($demande->getTypeDemande() != "Absence exceptionnelle" || $demande->getTypeDemande() != "Arrêt maladie") {
            $texte = "de";
        } else {
            $texte = "d'";
        }

        $notification->setMessageDemandeur("Demande ".$texte." ". $demande->getTypeDemande() . " mise à jour (2e validation) par " . $valideurConnecte->getPoste() . ".");
        $notification->setMessageValideurEnCours("Vous avez validé la demande ".$texte." ". $demande->getTypeDemande() . " de " . $demande->getSalarie() . ".");
        $notification->setMessageValideurSuivant("Demande ".$texte." ". $demande->getTypeDemande() . " n°" . $demande->getId() . " de " . $demande->getSalarie()->getCivilite() . " " . $demande->getSalarie()->getNomprenom() . " à valider.");
        $notification->setDemande($demande);
    }

    public function envoiEmailDemande($salarie, $demande) {
        $objet1 = "";
        $objet2 = "";
        if ($demande->getTypeDemande() == 'Absence exceptionnelle') {
            $objet1 = "Demande d'" . $demande->getTypeDemande() . " soumise à votre hiérarchie.";
            $objet2 = "Demande d'" . $demande->getTypeDemande() . " à valider.";
        } else {
            $objet1 = "Demande de " . $demande->getTypeDemande() . " soumise à votre hiérarchie.";
            $objet2 = "Demande de " . $demande->getTypeDemande() . " à valider.";
        }
        // Email au demandeur
        $sendto = $salarie->getEmail();
        $subject = $objet1;
        $message = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $salarie->getPrenom() . ',</p>' .
                '<p>Votre demande a bien été enregistrée et transmise à votre hiérarchie pour décision.</p>' .
                '<p>Récapitulatif de votre demande:</p>' .
                '<ul>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                '</ul>' .
                '<p>Pour suivre l\'avancement de votre demande, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                                    <tr>
                                        <td align="center">
                                            <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                        </td>
                                    </tr>
                               </table>' .
                '</div>' .
                '</body>';

        $header = "From: ima.cnps@polescompetences.com\n";
        $header .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto, $subject, $message, $header);

        // Email au manageur
        $sendto2 = $salarie->getSuperviseur()->getEmail();
        $subject2 = $objet2;
        $message2 = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $demande->getValideurNiveau1()->getPrenom() . ',</p>' .
                '<p>Votre attention est requise pour la validation de la demande d\'un collaborateur.</p>' .
                '<p>Récapitulatif de la demande:</p>' .
                '<ul>' .
                '<li>Collaborateur: ' . $salarie->getCivilite() . ' ' . $salarie->getNomprenom() . '</li>' .
                '<li>Unité: ' . $salarie->getUnite() . '</li>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                '</ul>' .
                '<p>Pour statuer sur cette demande, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                               <tr>
                                   <td align="center">
                                       <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                   </td>
                               </tr>
                          </table>' .
                '</div>' .
                '</body>';
        $header2 = "From: ima.cnps@polescompetences.com\n";
        $header2 .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto2, $subject2, $message2, $header2);
    }

    public function envoiEmailRefus($salarie, $demande) {
        $objet1 = "";
        $objet2 = "";
        if ($demande->getTypeDemande() == 'Absence exceptionnelle') {
            $objet1 = "Demande d'" . $demande->getTypeDemande() . " refusée.";
            $objet2 = "Vous avez refusé une demande d'" . $demande->getTypeDemande() . " d'un collaborateur.";
        } else {
            $objet1 = "Demande de " . $demande->getTypeDemande() . " refusée.";
            $objet2 = "Vous avez refusé une demande de " . $demande->getTypeDemande() . " d'un collaborateur.";
        }

        // Email au manageur
//        $sendto = $demande->getRefusePar()->getEmail();
//        $subject = $objet2;
//        $message = '<!DOCTYPE HTML>'. 
//                   '<head>'. 
//                       '<meta http-equiv="content-type" content="text/html">'. 
//                       '<title>Notification IMA</title>'. 
//                   '</head>'. 
//                   '<body bgcolor="#f6f6f6">'. 
//                       '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">'. 
//                       '</div>'. 
//
//                       '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">'.  
//                          '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">'. 
//                              '<p>Bonjour '.$demande->getRefusePar()->getPrenom().',</p>'.
//                                   '<p>Vous avez refusé la demande d\'un collaborateur.</p>'.
//                                   '<p>Récapitulatif de la demande:</p>'.
//                                   '<ul>'.
//                                       '<li>Collaborateur: '.$salarie->getCivilite().' '.$salarie->getNomprenom().'</li>'.
//                                       '<li>Unité: '.$salarie->getUnite().'</li>'.
//                                       '<li>Type d\'absence: '.$demande->getTypeDemande().'</li>'.
//                                       '<li>Date demande: '.$demande->getDateDemande()->format('d-m-Y H:i').'</li>'.
//                                       '<li>Date début: '.$demande->getDateDebut()->format('d-m-Y H:i').'</li>'.
//                                       '<li>Date fin: '.$demande->getDateFin().'</li>'.
//                                       '<li>Reprise du travail le '.$demande->getDateRetour().' matin.</li>'.
//                                   '</ul>'.
//                                   '<p>Pour consulter cette décision, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>'.
//
//                                    '<p>Merci et bonne journée.</p>'. 
//                          '</div>'.   
//                       '</div>'. 
//
//                       '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">'. 
//                          '<table>
//                               <tr>
//                                   <td align="center">
//                                       <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
//                                   </td>
//                               </tr>
//                          </table>'.
//                       '</div>'. 
//                   '</body>';
//        $header = "From: ima.cnps@polescompetences.com\n";
//        $header .= "Content-Type: text/html; charset=utf-8\n";
//
//	     // Send
//         mail($sendto,$subject,$message,$header);
        // Email au demandeur

        $sendto2 = $salarie->getEmail();
        $subject2 = $objet1;
        $message2 = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $salarie->getPrenom() . ',</p>' .
                '<p>Votre demande a été refusée par votre ' . $demande->getRefusePar()->getPoste() . ' le ' . $demande->getDateRefus()->format('d-m-Y H:i') . '.<br>
                                        Motif du refus :' . $demande->getRaisonRefus() . '</p>' .
                '<p>Récapitulatif de votre demande:</p>' .
                '<ul>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                '</ul>' .
                '<p>Pour consulter la décision de la hiérarchie, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                                    <tr>
                                        <td align="center">
                                            <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                        </td>
                                    </tr>
                               </table>' .
                '</div>' .
                '</body>';

        $header2 = "From: ima.cnps@polescompetences.com\n";
        $header2 .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto2, $subject2, $message2, $header2);
    }

    public function envoiEmailValidationNiveau1($salarie, $demande) {
        $objet1 = "";
        $objet2 = "";
        $objet3 = "";
        if ($demande->getTypeDemande() == 'Absence exceptionnelle') {
            $objet1 = "Avancement de votre demande d'" . $demande->getTypeDemande() . ".";
            $objet2 = "Vous avez validé une demande d'" . $demande->getTypeDemande() . " d'un collaborateur.";
            $objet3 = "Demande d'" . $demande->getTypeDemande() . " à valider.";
        } else {
            $objet1 = "Avancement de votre demande de " . $demande->getTypeDemande() . ".";
            $objet2 = "Vous avez validé une demande de " . $demande->getTypeDemande() . " d'un collaborateur.";
            $objet3 = "Demande de " . $demande->getTypeDemande() . " à valider.";
        }

        // Email au valideurNiveau2
        $sendto2 = $demande->getValideurNiveau2()->getEmail();
        $subject2 = $objet3;
        $message2 = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $demande->getValideurNiveau2()->getPrenom() . ',</p>' .
                '<p>Votre attention est requise pour la validation de la demande d\'un collaborateur.</p>' .
                '<p>Récapitulatif de la demande:</p>' .
                '<ul>' .
                '<li>Collaborateur: ' . $salarie->getCivilite() . ' ' . $salarie->getNomprenom() . '</li>' .
                '<li>Unité: ' . $salarie->getUnite() . '</li>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                'Niveau d\'avancement: 2 sur ' . $demande->getNbNiveauxValidation() . '.</p>' .
                '</ul>' .
                '<p>Pour statuer sur cette demande, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                                   <tr>
                                       <td align="center">
                                           <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                       </td>
                                   </tr>
                              </table>' .
                '</div>' .
                '</body>';
        $header2 = "From: ima.cnps@polescompetences.com\n";
        $header2 .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto2, $subject2, $message2, $header2);

        // Email au demandeur

        $sendto = $salarie->getEmail();
        $subject = $objet1;
        $message = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $salarie->getPrenom() . ',</p>' .
                '<p>Votre demande a été validée par votre ' . $demande->getValideurNiveau1()->getPoste() . ' le ' . $demande->getDateValidation1()->format('d-m-Y H:i') . '.<br>
                                           Niveau de validation: 1 sur ' . $demande->getNbNiveauxValidation() . '.</p>' .
                '<p>Récapitulatif de votre demande:</p>' .
                '<ul>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                '</ul>' .
                '<p>Pour suivre cette demande, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                                       <tr>
                                           <td align="center">
                                               <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                           </td>
                                       </tr>
                                  </table>' .
                '</div>' .
                '</body>';

        $header = "From: ima.cnps@polescompetences.com\n";
        $header .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto, $subject, $message, $header);
    }

    public function envoiEmailValidationNiveau2($salarie, $demande) {
        $objet1 = "";
        $objet2 = "";
        $objet3 = "";
        if ($demande->getTypeDemande() == 'Absence exceptionnelle') {
            $objet1 = "Avancement de votre demande d'" . $demande->getTypeDemande() . ".";
            $objet2 = "Vous avez validé une demande d'" . $demande->getTypeDemande() . " d'un collaborateur.";
            $objet3 = "Demande d'" . $demande->getTypeDemande() . " à valider.";
        } else {
            $objet1 = "Avancement de votre demande de " . $demande->getTypeDemande() . ".";
            $objet2 = "Vous avez validé une demande de " . $demande->getTypeDemande() . " d'un collaborateur.";
            $objet3 = "Demande de " . $demande->getTypeDemande() . " à valider.";
        }

        // Email au demandeur

        $sendto = $salarie->getEmail();
        $subject = $objet1;
        $message = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $salarie->getPrenom() . ',</p>' .
                '<p>Votre demande a été validée par votre ' . $demande->getValideurNiveau2()->getPoste() . ' le ' . $demande->getDateValidation2()->format('d-m-Y H:i') . '.<br>
                                           Niveau de validation: 2 sur ' . $demande->getNbNiveauxValidation() . '.</p>' .
                '<p>Récapitulatif de votre demande:</p>' .
                '<ul>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                '</ul>' .
                '<p>Pour suivre cette demande, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                                       <tr>
                                           <td align="center">
                                               <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                           </td>
                                       </tr> 
                                  </table>' .
                '</div>' .
                '</body>';

        $header = "From: ima.cnps@polescompetences.com\n";
        $header .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto, $subject, $message, $header);

        // Email au valideurNiveau3

        $sendto3 = $demande->getValideurFinal()->getEmail();
        $subject3 = $objet3;
        $message3 = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $demande->getValideurFinal()->getPrenom() . ',</p>' .
                '<p>Votre attention est requise pour la validation de la demande d\'un collaborateur.</p>' .
                '<p>Récapitulatif de la demande:</p>' .
                '<ul>' .
                '<li>Collaborateur: ' . $salarie->getCivilite() . ' ' . $salarie->getNomprenom() . '</li>' .
                '<li>Unité: ' . $salarie->getUnite() . '</li>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                'Niveau d\'avancement: 3 sur ' . $demande->getNbNiveauxValidation() . '.</p>' .
                '</ul>' .
                '<p>Pour statuer sur cette demande, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                                   <tr>
                                       <td align="center">
                                           <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                       </td>
                                   </tr>
                              </table>' .
                '</div>' .
                '</body>';
        $header3 = "From: ima.cnps@polescompetences.com\n";
        $header3 .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto3, $subject3, $message3, $header3);
    }

    public function envoiEmailValidationNiveau3($salarie, $demande) {

        $objet1 = "";
        $objet2 = "";
        $objet3 = "";
        if ($demande->getTypeDemande() == 'Absence exceptionnelle') {
            $objet1 = "Avancement de votre demande d'" . $demande->getTypeDemande() . ".";
            $objet2 = "Vous avez validé une demande d'" . $demande->getTypeDemande() . " d'un collaborateur.";
            $objet3 = "Demande d'" . $demande->getTypeDemande() . " à valider.";
        } else {
            $objet1 = "Avancement de votre demande de " . $demande->getTypeDemande() . ".";
            $objet2 = "Vous avez validé une demande de " . $demande->getTypeDemande() . " d'un collaborateur.";
            $objet3 = "Demande de " . $demande->getTypeDemande() . " à valider.";
        }

        // Email au demandeur
        $sendto = $salarie->getEmail();
        $subject = $objet1;
        $message = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $salarie->getPrenom() . ',</p>' .
                '<p>Votre demande a été validée par votre ' . $demande->getValideurNiveau2()->getPoste() . ' le ' . $demande->getDateValidation2()->format('d-m-Y H:i') . '.<br>
                                           Niveau de validation: 3 sur ' . $demande->getNbNiveauxValidation() . '.</p>' .
                '<p>Récapitulatif de votre demande:</p>' .
                '<ul>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                '</ul>' .
                '<p>Pour suivre cette demande, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                                       <tr>
                                           <td align="center">
                                               <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                           </td>
                                       </tr>
                                  </table>' .
                '</div>' .
                '</body>';
        $header = "From: ima.cnps@polescompetences.com\n";
        $header .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto, $subject, $message, $header);

        // Email au valideurFinal
        $sendto3 = $demande->getValideurFinal()->getEmail();
        $subject3 = $objet3;
        $message3 = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $demande->getValideurFinal()->getPrenom() . ',</p>' .
                '<p>Votre attention est requise pour la validation de la demande d\'un collaborateur.</p>' .
                '<p>Récapitulatif de la demande:</p>' .
                '<ul>' .
                '<li>Collaborateur: ' . $salarie->getCivilite() . ' ' . $salarie->getNomprenom() . '</li>' .
                '<li>Unité: ' . $salarie->getUnite() . '</li>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                'Niveau d\'avancement: 4 sur ' . $demande->getNbNiveauxValidation() . '.</p>' .
                '</ul>' .
                '<p>Pour statuer sur cette demande, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                              <tr>
                                  <td align="center">
                                      <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                  </td>
                              </tr>
                         </table>' .
                '</div>' .
                '</body>';
        $header3 = "From: ima.cnps@polescompetences.com\n";
        $header3 .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto3, $subject3, $message3, $header3);
    }

    public function envoiEmailDecision($salarie, $demande, $observateur) {

        $objet1 = "";
        $objet2 = "";
        $texte = "";
        if ($demande->getTypeDemande() == 'Conge') {
            $objet1 = "Décision de congé.";
            $objet2 = "Décision de congé de " . $salarie->getCivilite() . " " . $salarie->getNomprenom() . ".";
            $texte = "Excellent congé !";
        } else {
            $objet1 = "Décision d'autorisation d'absence.";
            $objet2 = "Décision d'autorisation d'absence de " . $salarie->getCivilite() . " " . $salarie->getNomprenom() . ".";
        }
        // Email au demandeur  
        $sendto = $salarie->getEmail();
        $subject = $objet1;
        $message = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $salarie->getPrenom() . ',</p>' .
                '<p>Votre demande a été validée par votre hiérarchie, le ' . $demande->getDateValidation()->format('d-m-Y H:i') . '.<br>
                                         <p>Vous êtes autorisé(e) à vous absenter pour la période et la durée souhaitée.</p>   
                                        <p>Récapitulatif de votre demande:</p>' .
                '<ul>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                '</ul>' .
                '<p>Pour consulter cette décision, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>' . $texte . '</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                                    <tr>
                                        <td align="center">
                                            <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                        </td>
                                    </tr>
                               </table>' .
                '</div>' .
                '</body>';
        $header = "From: ima.cnps@polescompetences.com\n";
        $header .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto, $subject, $message, $header);

        // Email au valideurNiveau1
        $sendto2 = $demande->getValideurNiveau1()->getEmail();
        $subject2 = $objet2;
        $message2 = '<!DOCTYPE HTML>' .
                '<head>' .
                '<meta http-equiv="content-type" content="text/html">' .
                '<title>Notification IMA</title>' .
                '</head>' .
                '<body bgcolor="#f6f6f6">' .
                '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">' .
                '</div>' .
                '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">' .
                '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">' .
                '<p>Bonjour ' . $demande->getValideurNiveau1()->getPrenom() . ',</p>' .
                '<p>La demande d\'absence de ' . $salarie->getCivilite() . ' ' . $salarie->getNomprenom() . ' a été validée.Le collaborateur est autorisé à s\'absenter pour la période demandée.</p>' .
                '<p>Récapitulatif de la demande:</p>' .
                '<ul>' .
                '<li>Type d\'absence: ' . $demande->getTypeDemande() . '</li>' .
                '<li>Date demande: ' . $demande->getDateDemande()->format('d-m-Y H:i') . '</li>' .
                '<li>Date début: ' . $demande->getDateDebut()->format('d-m-Y H:i') . '</li>' .
                '<li>Date fin: ' . $demande->getDateFin() . '</li>' .
                '<li>Reprise du travail le ' . $demande->getDateRetour() . ' matin.</li>' .
                '</ul>' .
                '<p>Pour consulter la décision, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>' .
                '<p>Merci et bonne journée.</p>' .
                '</div>' .
                '</div>' .
                '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">' .
                '<table>
                               <tr>
                                   <td align="center">
                                       <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
                                   </td>
                               </tr>
                          </table>' .
                '</div>' .
                '</body>';
        $header2 = "From: ima.cnps@polescompetences.com\n";
        $header2 .= "Content-Type: text/html; charset=utf-8\n";

        // Send
        mail($sendto2, $subject2, $message2, $header2);


        //Email à la DRH
//        $sendto3 = $observateur->getEmail();
//        $subject3 = $objet2;
//        $message3 = '<!DOCTYPE HTML>'. 
//                   '<head>'. 
//                       '<meta http-equiv="content-type" content="text/html">'. 
//                       '<title>Notification IMA</title>'. 
//                   '</head>'. 
//                   '<body bgcolor="#f6f6f6">'. 
//                       '<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">'. 
//                       '</div>'. 
//
//                       '<div id="outer" bgcolor="#f6f6f6" style="width: 80%;margin: 0 auto;margin-top: 10px; ">'.  
//                          '<div id="inner" bgcolor="#FFFFFF" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">'. 
//                              '<p>Bonjour,</p>'.
//                                   '<p>La demande d\'absence de '.$salarie->getCivilite().' '.$salarie->getNomprenom().' a été validée.Le collaborateur est autorisé à s\'absenter pour la période demandée.</p>'.
//                                   '<p>Récapitulatif de la demande:</p>'.
//                                   '<ul>'.
//                                       '<li>Unité: '.$salarie->getUnite().'</li>'.
//                                       '<li>Type d\'absence: '.$demande->getTypeDemande().'</li>'.
//                                       '<li>Date demande: '.$demande->getDateDemande()->format('d-m-Y H:i').'</li>'.
//                                       '<li>Date début: '.$demande->getDateDebut()->format('d-m-Y H:i').'</li>'.
//                                       '<li>Date fin: '.$demande->getDateFin().'</li>'.
//                                       '<li>Reprise du travail le '.$demande->getDateRetour().' matin.</li>'.
//                                   '</ul>'.
//                                   '<p>Pour consulter la décision, cliquez sur <a href="http://www.grhappdemo.ivolife.com/web/app_dev.php/login" class="btn-primary">Accès IMA</a></p>'.
//
//                                    '<p>Merci et bonne journée.</p>'. 
//                          '</div>'.   
//                       '</div>'. 
//
//                       '<div id="footer" style="width: 80%;height: 40px;margin: 0 auto;text-align: center;padding: 10px;font-family: Verdena;background-color: #E2E2E2;">'. 
//                          '<table>
//                               <tr>
//                                   <td align="center">
//                                       <p> Copyright IMA 2015.Pôles Compétences SARL - Tous droits réservés.</p>
//                                   </td>
//                               </tr>
//                          </table>'.
//                       '</div>'. 
//                   '</body>';
//        $header3 = "From: ima.cnps@polescompetences.com\n";
//        $header3 .= "Content-Type: text/html; charset=utf-8\n";
//
//	     // Send
//       mail($sendto3,$subject3,$message3,$header3);
    }

}
