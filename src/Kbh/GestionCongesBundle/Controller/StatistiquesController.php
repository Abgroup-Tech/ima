<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kbh\GestionCongesBundle\Controller\DemandeController;
use Kbh\GestionCongesBundle\Entity\Droits;
use Kbh\GestionCongesBundle\Form\DroitsType;
use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Entity\Conge;
use Kbh\GestionCongesBundle\Entity\Absence;
use Kbh\GestionCongesBundle\Entity\Demande;
use Kbh\GestionCongesBundle\Entity\Etats;
use Kbh\GestionCongesBundle\Entity\Statistiques;

/**
 * Statistiques controller.
 *
 */
class StatistiquesController extends Controller {
    /*     * ********************* TRANSFERT DE TOUTES LES STATISTIQUES ******************************* */

    /**
     * Finds and displays statistics.
     *
     */
    public function showStatistiquesSupAction() {

        //DemandeController::verifConnexion();
        $em = $this->getDoctrine()->getManager();

        //Vérification de l'existance de statistiques
        $etats = $em->getRepository('KbhGestionCongesBundle:Etats')->findAll();
        $cp = count($etats);

//        var_dump($cp);
//        die();
        //Cr&ation des données de la table
        if ($cp == 0) {
            //1ere étape : Récuperer les unités de la BD
            $unites = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();

            //Ensuite récupérer les salariés appartenant à chaque unité
            // faire les traitement et sauvegarder en base.
            foreach ($unites as $unite) {
                //1-A initialisation des variables
                $congesAcquis = 0;
                $congesAnterieur = 0;
                $totalDroitsAcquis = 0;
                $totalCongesConsomme = 0;
                $permissions = 0;
                $absencesEx = 0;
                $totalAbsences = 0;
                $stockConges = 0;
                $congesPris = 0;

                //1-B Récupération des salarié de l'unité
                $salaries = $unite->getSalaries();

                //1-C Sommation des droits des salariés
                foreach ($salaries as $salarie) {
                    $congesAcquis = $salarie->getDroits()->getDroitsAcquisAnneeEnCours();
                    $congesAnterieur = $salarie->getDroits()->getReliquatDroitsAnterieur();
                    $totalDroitsAcquis = $salarie->getDroits()->getCumulDroitsAcquis();
                    $totalCongesConsomme += $salarie->getDroits()->getDroitspris(); // congé pris + absences ex

                    $stockConges = $salarie->getDroits()->getTotalDroitsAprendre();

                    //Récupération des absences et des congés du salarié
                    $absences = $em->getRepository('KbhGestionCongesBundle:Absence')->findBySalarie($salarie);
                    $conges = $em->getRepository('KbhGestionCongesBundle:Conge')->findBySalarie($salarie);

                    //Récupération du nombre de jours correspondant aux absences ex et aux permissions
                    foreach ($absences as $absence) {
                        if ($absence->getMotif() == "Absence exceptionnelle") {
                            $absencesEx += $absence->getNbJoursOuvrables();
                        }
                        if ($absence->getMotif() != "Absence exceptionnelle" && $absence->getMotif() != "Férie(s)") {
                            $permissions += $absence->getNbJoursOuvrables();
                        }
                    }

                    //Récupération du nombre de jours correspondant aux conges
                    foreach ($conges as $conge) {
                        $congesPris += $conge->getNbJoursOuvrables();
                    }
                    //Sommation du total des absences
                    $totalAbsences += $absencesEx + $permissions + $congesPris;
                }

                //1-D Mise en base des données collectées
                $etat = new Etats();

                $etat->setUnite($unite);
                $etat->setCongesAcquis($congesAcquis);
                $etat->setCongesAnterieur($congesAnterieur);
                $etat->setTotalDroitsAcquis($totalDroitsAcquis);
                $etat->setTotalCongesConsomme($totalCongesConsomme);
                $etat->setCongesPris($congesPris);
                $etat->setPermissions($permissions);
                $etat->setAbsencesEx($absencesEx);
                $etat->setStockConge($stockConges);
                $etat->setTotalAbsences($totalAbsences);

                $em->persist($etat);
                $em->flush();
            }
        }

        //Mise à jour des données de l'unité
        if ($cp != 0) {
            //1ere étape : Récuperer les unités de la BD
            $unites = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();
            $nb_etats = count($etats);

            for ($i = 1; $i <= $nb_etats; $i++) {
                $unite = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->find($i);

                //1-A initialisation des variables
                $congesAcquis = 0;
                $congesAnterieur = 0;
                $totalDroitsAcquis = 0;
                $totalCongesConsomme = 0;
                $permissions = 0;
                $absencesEx = 0;
                $totalAbsences = 0;
                $stockConges = 0;
                $congesPris = 0;

                //1-B Récupération des salarié de l'unité
                $salaries = $unite->getSalaries();

                //1-C Sommation des droits des salariés
                foreach ($salaries as $salarie) {
                    $congesAcquis += $salarie->getDroits()->getDroitsAcquisAnneeEnCours();

                    $congesAnterieur += $salarie->getDroits()->getReliquatDroitsAnterieur();
                    $totalDroitsAcquis += $salarie->getDroits()->getCumulDroitsAcquis();
                    $totalCongesConsomme += $salarie->getDroits()->getDroitspris(); // congé pris + absences ex

                    $stockConges += $salarie->getDroits()->getTotalDroitsAprendre();

                    //Récupération des absences et des congés du salarié
                    $absences = $em->getRepository('KbhGestionCongesBundle:Absence')->findBySalarie($salarie);
                    $conges = $em->getRepository('KbhGestionCongesBundle:Conge')->findBySalarie($salarie);

                    //Récupération du nombre de jours correspondant aux absences ex et aux permissions
                    foreach ($absences as $absence) {

                        if ($absence->getMotif() == "Absence exceptionnelle") {
                            $absencesEx += $absence->getNbJoursOuvrables();
                        }
                        if ($absence->getMotif() != "Absence exceptionnelle" && $absence->getMotif() != "Férie(s)") {
                            $permissions += $absence->getNbJoursOuvrables();
                        }
                    }

                    //Récupération du nombre de jours correspondant aux conges
                    foreach ($conges as $conge) {
                        $congesPris += $conge->getNbJoursOuvrables();
                    }

                    //Sommation du total des absences
                    $totalAbsences = $absencesEx + $permissions + $congesPris;
                }

                //1-D Mise en base des données collectées
                $etat = $em->getRepository('KbhGestionCongesBundle:Etats')->find($i);
                $etat->setUnite($unite);
                $etat->setCongesAcquis($congesAcquis);
                $etat->setCongesAnterieur($congesAnterieur);
                $etat->setTotalDroitsAcquis($totalDroitsAcquis);
                $etat->setTotalCongesConsomme($totalCongesConsomme);
                $etat->setCongesPris($congesPris);
                $etat->setPermissions($permissions);
                $etat->setAbsencesEx($absencesEx);
                $etat->setStockConge($stockConges);
                $etat->setTotalAbsences($totalAbsences);


                $em->persist($etat);
                $em->flush();
            }
        }

        //Calculs des totaux 
        //1-A initialisation des variables
        $totalCongesAcquis = 0;
        $totalCongesAnterieur = 0;
        $sommeTotalDroitsAcquis = 0;
        $sommeTotalCongesConsomme = 0;
        $totalPermissions = 0;
        $totalAbsencesEx = 0;
        $totalCongesPris = 0;
        $sommeTotalAbsences = 0;
        $totalStockConges = 0;

        foreach ($etats as $etat) {
            $totalCongesAcquis += $etat->getCongesAcquis();
            $totalCongesAnterieur += $etat->getCongesAnterieur();
            $sommeTotalDroitsAcquis += $etat->getTotalDroitsAcquis();
            $sommeTotalCongesConsomme += $etat->getTotalCongesConsomme();
            $totalPermissions += $etat->getPermissions();
            $totalAbsencesEx += $etat->getAbsencesEx();
            $totalCongesPris += $etat->getCongesPris();
            $sommeTotalAbsences += $etat->getTotalAbsences();
            $totalStockConges += $etat->getStockConge();
        }
        
//        var_dump($totalPermissions);
//        die();

        //###################################################
        //############ MAXIMUM, MINIMUM ET MOYENNES ##############
        //Recherche de l'unité ayant le plus grand stock de conges
        if ($cp != 0) {
            $max_conge = array();
            $i = 0;
            foreach ($etats as $etat) {
                $max_conge[$i] = $etat->getStockConge();
                $i += 1;
            }
            $max = max($max_conge);

            //Récupérons l'unité ayant le stock maximal
            $unite_conge_max = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByStockConge($max);

            //Recherche de l'unité ayant le plus petit stock de conges
            $min_conge = array();
            $i = 0;
            foreach ($etats as $etat) {
                $min_conge[$i] = $etat->getStockConge();
                $i += 1;
            }
            $min = min($min_conge);
            //Récupérons l'unité ayant le stock minimal
            $unite_conge_min = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByStockConge($min);

            //######################################################################
            //Recherche de l'unité ayant la plus grande consommation de permissions
            $max_permission = array();
            $i = 0;
            foreach ($etats as $etat) {
                $max_permission[$i] = $etat->getPermissions();
                $i += 1;
            }
            $max2 = max($max_permission);
            //Récupérons l'unité ayant la consommation maximale
            $unite_permissions_max = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByPermissions($max2);

            //Recherche de l'unité ayant la consommation minimale
            $min_permission = array();
            $i = 0;
            foreach ($etats as $etat) {
                $min_permission[$i] = $etat->getPermissions();
                $i += 1;
            }
            $min2 = min($min_permission);
            //Récupérons l'unité ayant la consommation minimale
            $unite_permissions_min = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByPermissions($min2);

            //######################################################################        
            //Recherche de l'unité ayant la plus grande consommation d'absences ex
            $max_absence = array();
            $i = 0;
            foreach ($etats as $etat) {
                $max_absence[$i] = $etat->getAbsencesEx();
                $i += 1;
            }
            $max3 = max($max_absence);
            //Récupérons l'unité ayant la consommation maximale
            $unite_absences_max = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByAbsencesEx($max3);

            //Recherche de l'unité ayant la consommation minimale
            $min_absence = array();
            $i = 0;
            foreach ($etats as $etat) {
                $min_absence[$i] = $etat->getAbsencesEx();
                $i += 1;
            }
            $min3 = min($min_absence);
            //Récupérons l'unité ayant la consommation minimale
            $unite_absences_min = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByAbsencesEx($min3);

            //Calcul des moyennes
            $solde_moy_conge = round(($totalStockConges / $cp), 2);
            $consom_moy_permission = round(($totalPermissions / $cp), 2);
            $consom_moy_absence = round(($totalAbsencesEx / $cp), 2);
        }
//         if($cp == 0){
//            //redirection pour la création des données de base
//            return $this->redirect($this->generateUrl('top_manager_graph_statistiques'));
//        }
//##########################################################################

        if ($cp == 0) {
            $solde_moy_conge = 0;
            $consom_moy_permission = 0;
            $consom_moy_absence = 0;
            $unite_conge_max = 0;
            $unite_conge_min = 0;
            $unite_permissions_max = 0;
            $unite_permissions_min = 0;
            $unite_absences_max = 0;
            $unite_absences_min = 0;
//                        var_dump($unite_conge_max);
//            die("ok");
        }

        $user = $this->getUser();
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Superviseur\Statistiques:etats.html.twig', array(
                        'etats' => $etats,
                        'totalCongesAcquis' => $totalCongesAcquis,
                        'totalCongesAnterieur' => $totalCongesAnterieur,
                        'sommeTotalDroitsAcquis' => $sommeTotalDroitsAcquis,
                        'sommeTotalCongesConsomme' => $sommeTotalCongesConsomme,
                        'totalPermissions' => $totalPermissions,
                        'totalAbsencesEx' => $totalAbsencesEx,
                        'totalCongesPris' => $totalCongesPris,
                        'sommeTotalAbsences' => $sommeTotalAbsences,
                        'totalStockConges' => $totalStockConges,
                        'unite_conge_max' => $unite_conge_max,
                        'unite_conge_min' => $unite_conge_min,
                        'unite_permissions_max' => $unite_permissions_max,
                        'unite_permissions_min' => $unite_permissions_min,
                        'unite_absences_max' => $unite_absences_max,
                        'unite_absences_min' => $unite_absences_min,
                        'solde_moy_conge' => $solde_moy_conge,
                        'consom_moy_permission' => $consom_moy_permission,
                        'consom_moy_absence' => $consom_moy_absence,
            ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Top-manager\Statistiques:etats.html.twig', array(
                        'etats' => $etats,
                        'totalCongesAcquis' => $totalCongesAcquis,
                        'totalCongesAnterieur' => $totalCongesAnterieur,
                        'sommeTotalDroitsAcquis' => $sommeTotalDroitsAcquis,
                        'sommeTotalCongesConsomme' => $sommeTotalCongesConsomme,
                        'totalPermissions' => $totalPermissions,
                        'totalAbsencesEx' => $totalAbsencesEx,
                        'totalCongesPris' => $totalCongesPris,
                        'sommeTotalAbsences' => $sommeTotalAbsences,
                        'totalStockConges' => $totalStockConges,
                        'unite_conge_max' => $unite_conge_max,
                        'unite_conge_min' => $unite_conge_min,
                        'unite_permissions_max' => $unite_permissions_max,
                        'unite_permissions_min' => $unite_permissions_min,
                        'unite_absences_max' => $unite_absences_max,
                        'unite_absences_min' => $unite_absences_min,
                        'solde_moy_conge' => $solde_moy_conge,
                        'consom_moy_permission' => $consom_moy_permission,
                        'consom_moy_absence' => $consom_moy_absence,
            ));
        }
    }

    //############### STATISTIQUES COLLABORATEURS ####################
    /**
     * Finds and displays statistics.
     *
     */
    public function showGraphStatsAction() {
        //Cas d'un responsable
        $salarie = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();
        $stats_unites = $em->getRepository('KbhGestionCongesBundle:Etats')->findAll();
        $list_unites = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();
        $stats_absences_at = $em->getRepository('KbhGestionCongesBundle:AbsencesAT')->findAll();

        $nb_unites = count($stats_unites);
        $nb_absences = count($stats_absences_at);

        //###### GRAPHIQUES ABSENCES ARRET DE TRAVAIL #########

        $nb_accident_travail = 0;
        $nb_maladie_prof = 0;
        $nb_maladie_ordinaires = 0;
        $nb_congés_maternites = 0;

        foreach ($stats_absences_at as $absencesAT) {
            if ($absencesAT->getMotif() == "Congé maternité") {
                $nb_congés_maternites += 1;
            }
            if ($absencesAT->getMotif() == "Accident de travail") {
                $nb_accident_travail += 1;
            }
            if ($absencesAT->getMotif() == "Maladie") {
                $nb_maladie_ordinaires += 1;
            }
            if ($absencesAT->getMotif() == "Maladie professionnelle") {
                $nb_maladie_prof += 1;
            }
        }

        //###### GRAPHIQUES SOMMES TOTAL CONGES #########
        $sommeTotalCongesConsommes = 0;
        foreach ($stats_unites as $unites) {
            $sommeTotalCongesConsommes += $unites->getTotalCongesConsomme();
        }

        //###### GRAPHIQUES STOCK DE CONGES #########
        $sommeStockConges = 0;
        foreach ($stats_unites as $unites) {
            $sommeStockConges += $unites->getStockConge();
        }

        //FONCTION DE CALCUL DU TAUX D'ABSENCE POUR MALADIES ORDINAIRES
        $typeAbsence[0] = "Maladie";
        $statMO = $this->calculTaux($typeAbsence[0], $stats_absences_at, $list_unites);

        //FONCTION DE CALCUL DU TAUX D'ABSENCE POUR MALADIES PROFESSIONNELLES
        $typeAbsence[1] = "Maladie professionnelle";
        $statMP = $this->calculTaux($typeAbsence[1], $stats_absences_at, $list_unites);

        //FONCTION DE CALCUL DU TAUX D'ABSENCE POUR ACCIDENT DE TRAVAIL
        $typeAbsence[2] = "Accident de travail";
        $statAcT = $this->calculTaux($typeAbsence[2], $stats_absences_at, $list_unites);

        //FONCTION DE CALCUL DU TAUX D'ABSENCE POUR CONGE MATERNITE
        $typeAbsence[3] = "Congé maternité";
        $statCM = $this->calculTaux($typeAbsence[3], $stats_absences_at, $list_unites);


        //################ TOTAUX DES DROITS #####################
        ////Calculs des totaux 
        //1-A initialisation des variables
        //Vérification de l'existance de statistiques
        $etats = $stats_unites;
        $cp = count($etats);

        if ($cp == 0) {
            //redirection pour la création des données de base
            return $this->redirect($this->generateUrl('top_manager_statistiques'));
        }

        $totalCongesAcquis = 0;
        $totalCongesAnterieur = 0;
        $sommeTotalDroitsAcquis = 0;
        $sommeTotalCongesConsomme = 0;
        $totalPermissions = 0;
        $totalAbsencesEx = 0;
        $totalCongesPris = 0;
        $sommeTotalAbsences = 0;
        $totalStockConges = 0;

        foreach ($etats as $etat) {
            $totalCongesAcquis += $etat->getCongesAcquis();
            $totalCongesAnterieur += $etat->getCongesAnterieur();
            $sommeTotalDroitsAcquis += $etat->getTotalDroitsAcquis();
            $sommeTotalCongesConsomme += $etat->getTotalCongesConsomme();
            $totalPermissions += $etat->getPermissions();
            $totalAbsencesEx += $etat->getAbsencesEx();
            $totalCongesPris += $etat->getCongesPris();
            $sommeTotalAbsences += $etat->getTotalAbsences();
            $totalStockConges += $etat->getStockConge();
        }

        if ($totalCongesAcquis == 0 && $totalCongesAnterieur == 0 && $sommeTotalDroitsAcquis == 0 && $sommeTotalCongesConsomme == 0) {
            if ($totalPermissions == 0 && $totalAbsencesEx == 0 && $totalCongesPris == 0 && $sommeTotalAbsences == 0 && $totalStockConges == 0) {
                //redirection pour la création des données de base
                return $this->redirect($this->generateUrl('top_manager_statistiques'));
            }
        }

        //############ MAXIMUM, MINIMUM ET MOYENNES ##############
        //Recherche de l'unité ayant le plus grand stock de conges
        if ($cp != 0) {
            $max_conge = array();
            $i = 0;
            foreach ($etats as $etat) {
                $max_conge[$i] = $etat->getStockConge();
                $i += 1;
            }
            $max = max($max_conge);
            //Récupérons l'unité ayant le stock maximal
            $unite_conge_max = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByStockConge($max);

            //Recherche de l'unité ayant le plus petit stock de conges
            $min_conge = array();
            $i = 0;
            foreach ($etats as $etat) {
                $min_conge[$i] = $etat->getStockConge();
                $i += 1;
            }
            $min = min($min_conge);
            //Récupérons l'unité ayant le stock minimal
            $unite_conge_min = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByStockConge($min);

            //######################################################################
            //Recherche de l'unité ayant la plus grande consommation de permissions
            $max_permission = array();
            $i = 0;
            foreach ($etats as $etat) {
                $max_permission[$i] = $etat->getPermissions();
                $i += 1;
            }
            $max2 = max($max_permission);
            //Récupérons l'unité ayant la consommation maximale
            $unite_permissions_max = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByPermissions($max2);

            //Recherche de l'unité ayant la consommation minimale
            $min_permission = array();
            $i = 0;
            foreach ($etats as $etat) {
                $min_permission[$i] = $etat->getPermissions();
                $i += 1;
            }
            $min2 = min($min_permission);
            //Récupérons l'unité ayant la consommation minimale
            $unite_permissions_min = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByPermissions($min2);

            //######################################################################        
            //Recherche de l'unité ayant la plus grande consommation d'absences ex
            $max_absence = array();
            $i = 0;
            foreach ($etats as $etat) {
                $max_absence[$i] = $etat->getAbsencesEx();
                $i += 1;
            }
            $max3 = max($max_absence);
            //Récupérons l'unité ayant la consommation maximale
            $unite_absences_max = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByAbsencesEx($max3);

            //Recherche de l'unité ayant la consommation minimale
            $min_absence = array();
            $i = 0;
            foreach ($etats as $etat) {
                $min_absence[$i] = $etat->getAbsencesEx();
                $i += 1;
            }
            $min3 = min($min_absence);
            //Récupérons l'unité ayant la consommation minimale
            $unite_absences_min = $em->getRepository('KbhGestionCongesBundle:Etats')->findOneByAbsencesEx($min3);
        }
        //##########################################################################
        //Calcul des moyennes
        if ($cp != 0) {
            $solde_moy_conge = round(($totalStockConges / $cp), 2);
            $consom_moy_permission = round(($totalPermissions / $cp), 2);
            $consom_moy_absence = round(($totalAbsencesEx / $cp), 2);
        }
        if ($cp == 0) {
            $solde_moy_conge = 0;
            $consom_moy_permission = 0;
            $consom_moy_absence = 0;
        }

        return $this->render('KbhGestionCongesBundle:Top-manager\Statistiques:stats-graphiques.html.twig', array(
                    'statsUnites' => $stats_unites,
                    'nb_unites' => $nb_unites,
                    'nb_absences' => $nb_absences,
                    'statsAbsencesAT' => $stats_absences_at,
                    'CongeMaternite' => $nb_congés_maternites,
                    'AccidentDeTravail' => $nb_accident_travail,
                    'Maladie' => $nb_maladie_ordinaires,
                    'MaladieProfessionnelle' => $nb_maladie_prof,
                    'SomTCC' => $sommeTotalCongesConsommes,
                    'SomSC' => $sommeStockConges,
                    'statsMaladieOrdinaires' => $statMO,
                    'statsMaladiePro' => $statMP,
                    'statsAcT' => $statAcT,
                    'statsCM' => $statCM,
                    'list_unites' => $list_unites,
                    'salarie' => $salarie,
                    'unite_conge_max' => $unite_conge_max,
                    'unite_conge_min' => $unite_conge_min,
                    'unite_permissions_max' => $unite_permissions_max,
                    'unite_permissions_min' => $unite_permissions_min,
                    'unite_absences_max' => $unite_absences_max,
                    'unite_absences_min' => $unite_absences_min,
                    'solde_moy_conge' => $solde_moy_conge,
                    'consom_moy_permission' => $consom_moy_permission,
                    'consom_moy_absence' => $consom_moy_absence,
        ));
    }

    //############### STATISTIQUES COLLABORATEUR ####################
    /**
     * Finds and displays statistics.
     *
     */
    public function showCollabStatsAction($id) {
        //DemandeController::verifConnexion();
        //Cas d'un superviseur quelconque
        $superviseur = $this->getSalarieByUser();
        $em = $this->getDoctrine()->getManager();

        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);
        $stat_collaborateur = $em->getRepository('KbhGestionCongesBundle:Statistiques')->findBySalarie($id);


        return $this->render('KbhGestionCongesBundle:Superviseur\Statistiques:stats-collaborateur.html.twig', array(
                    'stats' => $stat_collaborateur,
                    'superviseur' => $superviseur,
                    'salarie' => $salarie,
        ));
    }

//################### FONCTIONS ADDITIONNELLES ####################

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
     * Finds and displays statistics.
     *
     */
    public function calculTaux($typeAbsence, $listeAbsence, $listeUnite) {
        //FONCTION DE CALCUL DU TAUX D'ABSENCE 
        $list_salaries = array();
        $a = 0;
        foreach ($listeAbsence as $absencesAT) {
            //Récupération de la liste des salariés qui ont eu des absences pour maladie professionnelles
            if ($absencesAT->getMotif() == $typeAbsence) {
                $list_salaries[$a] = $absencesAT->getSalarie();
                $a++;
            }
        }
        //Récupération des unités 
        $uniteSalaries = array();
        $q = 0;
        foreach ($list_salaries as $sal) {
            //vérification de l'unité des salariés
            foreach ($listeUnite as $unite) {
                if ($sal->getUnite() == $unite) {
                    $uniteSalaries[$q] = $unite->getId();
                    $q++;
                }
            }
        }

        //Récupération du nombre d'unités liés aux salariés & initialisation du tableau de données statistiques
        $nbUnitesSalaries = count($uniteSalaries);
        $tauxAbsence = array();

        $i = 0;
        do {
            foreach ($list_salaries as $sal) {
                //vérification de l'unité des salariés
                if ($uniteSalaries[$i] == $sal->getUnite()->getId()) {
                    //Vérifions si la stat exist déja
                    if (!isset($statUnite[$sal->getUnite()->getId()])) {
                        $tauxAbsence[$sal->getUnite()->getId()] = 1;
                    } else {
                        $tauxAbsence[$sal->getUnite()->getId()] += 1;
                    }
                }$i++;
            }
        } while ($i == $nbUnitesSalaries - 1);

        return $tauxAbsence;
    }

}
