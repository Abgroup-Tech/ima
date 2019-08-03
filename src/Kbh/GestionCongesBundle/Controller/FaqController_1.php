<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\Faq;
use Kbh\GestionCongesBundle\Form\FaqType;

/**
 * Faq controller.
 *
 */
class FaqController extends Controller
{

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

            //Calcul du nombre de jours total du congé sans les jours féries
            $nombreJoursTotal = $nombreJours ;

            //Conversion du nombre de jours total en timestamp
            $tms_nbjTotal = $nombreJoursTotal * $nbSecondes;

            //Addition du nombre de jours total et de la date de debut
            $tms_add = $tms_nbjTotal + $tms_dateDebut;
            
            //Addition du nombre de jours total et du nombre de secondes pour obtenir la date de retour
            $tms_add_retour = $tms_add + $nbSecondes;
            
            //Conversion de la variable $tms_add en date afin d'obtenir la date de fin
            $datetFinale = date('Y-m-d', $tms_add);

            //Conversion de la variable $tms_add pour obtenir la date finale
            $dateRetour = date('Y/m/d H:i:s', $tms_add_retour);

            //on renseigne les résultats dans la demande
            $demande->setDateFin($datetFinale);
            $demande->setDateRetour($dateRetour);

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

              /**********************************************
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


            /** ********************************************
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
            if($joursSup2 > $joursSup1){
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
            $tms_add = ($tms_nbjTotal + $nbSecondes )  + $tms_dateDebut;

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
    
    
    
}
