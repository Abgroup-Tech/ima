<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ddeboer\DataImportBundle\DataImport\Workflow;
use Ddeboer\DataImportBundle\DataImport\Reader\ExcelReader;
use Ddeboer\DataImportBundle\DataImport\Writer\DoctrineWriter;
use Kbh\GestionCongesBundle\Entity\Document;
use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Entity\Feries;
use Kbh\GestionCongesBundle\Entity\OrganigrammeUnite;
use Kbh\GestionCongesBundle\Entity\Droits;
use Kbh\UserBundle\Entity\User;
use Kbh\GestionCongesBundle\Entity\LogCalculAllocationConge;
use Kbh\GestionCongesBundle\Entity\CalculAllocationConge;
use Kbh\GestionCongesBundle\Form\DocumentType;
use Kbh\GestionCongesBundle\Entity\ImportSalarie;
use Kbh\GestionCongesBundle\Entity\ImportUnite;
use Kbh\GestionCongesBundle\Form\ImportSalarieType;
use Kbh\GestionCongesBundle\Form\CalculAllocationCongeType;
use Kbh\GestionCongesBundle\Form\ImportUniteType;

/**
 * Document controller.
 *
 */
class DocumentController extends Controller {

    /**
     * Creates a new Document entity.
     *
     */
    public function importSalarieAction($id) {
        //Recherche du document    
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);

        $file = new \SplFileObject($entity->getAbsolutePath());
        $excelReader = new ExcelReader($file);

        // Tell the reader that the first row in the Excel file contains column headers
        $excelReader->setHeaderRowNumber(0);

        // Create the workflow from the reader
        $workflow = new Workflow($excelReader);

        // Create a writer: you need Doctrine’s EntityManager.
        $doctrineWriter = new DoctrineWriter($em, 'KbhGestionCongesBundle:ImportSalarie');

        $workflow->addWriter($doctrineWriter);

        // Process the workflow
        $workflow->process();


        return $this->redirect($this->generateUrl('sup_ad_import_show_sa', array('id' => $id)));
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function importFeriesAction() {
        //Recherche du document    
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Document')->findOneByCible("Feries");

        $file = new \SplFileObject($entity->getAbsolutePath());
        $excelReader = new ExcelReader($file);

        // Tell the reader that the first row in the Excel file contains column headers
        $excelReader->setHeaderRowNumber(0);

        // Create the workflow from the reader
        $workflow = new Workflow($excelReader);

        // Create a writer: you need Doctrine’s EntityManager.
        $doctrineWriter = new DoctrineWriter($em, 'KbhGestionCongesBundle:ImportFeries');

        $workflow->addWriter($doctrineWriter);

        // Process the workflow
        $workflow->process();


        return $this->redirect($this->generateUrl('installation_traitement_import_feries'));
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function importPermissionsAction() {
        //Recherche du document    
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Document')->findOneByCible("Permissions");

        $file = new \SplFileObject($entity->getAbsolutePath());
        $excelReader = new ExcelReader($file);

        // Tell the reader that the first row in the Excel file contains column headers
        $excelReader->setHeaderRowNumber(0);

        // Create the workflow from the reader
        $workflow = new Workflow($excelReader);

        // Create a writer: you need Doctrine’s EntityManager.
        $doctrineWriter = new DoctrineWriter($em, 'KbhGestionCongesBundle:Parampermissions');

        $workflow->addWriter($doctrineWriter);

        // Process the workflow
        $workflow->process();

        $entity->setStatut('Traité');
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('installation_paramcalculdroits_new'));
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function importAllocationAction($id) {
        //Recherche du document    
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);

        $file = new \SplFileObject($entity->getAbsolutePath());
        $excelReader = new ExcelReader($file);

        // Tell the reader that the first row in the Excel file contains column headers
        $excelReader->setHeaderRowNumber(0);

        // Create the workflow from the reader
        $workflow = new Workflow($excelReader);

        // Create a writer: you need Doctrine’s EntityManager.
        $doctrineWriter = new DoctrineWriter($em, 'KbhGestionCongesBundle:LogCalculAllocationConge');

        $workflow->addWriter($doctrineWriter);

        // Process the workflow
        $workflow->process();

        $user = $this->getUser();
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->redirect($this->generateUrl('top_manager_import_show_allocation', array('id' => $id)));
        }
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->redirect($this->generateUrl('sup_import_show_allocation', array('id' => $id)));
        }
    }

    /**
     * Creates a new Document entity.
     *
     */
    public function importUniteAction($id) {
        //Recherche du document    
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);

        $file = new \SplFileObject($entity->getAbsolutePath());
        $excelReader = new ExcelReader($file);

        // Tell the reader that the first row in the Excel file contains column headers
        $excelReader->setHeaderRowNumber(0);

        // Create the workflow from the reader
        $workflow = new Workflow($excelReader);

        // Create a writer: you need Doctrine’s EntityManager.
        $doctrineWriter = new DoctrineWriter($em, 'KbhGestionCongesBundle:ImportUnite');

        $workflow->addWriter($doctrineWriter);

        // Process the workflow
        $workflow->process();


        return $this->redirect($this->generateUrl('sup_ad_import_show_unit', array('id' => $id)));
    }

    /**
     * Finds and displays a Document entity.
     *
     */
    public function importShowSalariesAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:ImportSalarie')->findAll();
        $count = count($entities);

        $document = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);

        return $this->render('KbhGestionCongesBundle:Super-Admin\Document:show-salarie-import.html.twig', array(
                    'entities' => $entities,
                    'doc' => $document,
                    'number' => $count,
        ));
    }

    /**
     * Finds and displays a Document entity.
     *
     */
    public function importShowAllocationAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:LogCalculAllocationConge')->findAll();
        $count = count($entities);

        $document = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);

        $user = $this->getUser();
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:top-manager\CalculAllocation:show-fichier-import.html.twig', array(
                        'entities' => $entities,
                        'doc' => $document,
                        'number' => $count,
            ));
        }
        if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Superviseur\CalculAllocation:show-fichier-import.html.twig', array(
                        'entities' => $entities,
                        'doc' => $document,
                        'number' => $count,
            ));
        }
    }

    /**
     * Finds and displays a Document entity.
     *
     */
    public function importShowUnitAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:ImportUnite')->findAll();
        $count = count($entities);

        $document = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);

        return $this->render('KbhGestionCongesBundle:Super-Admin\Document:show-unit-import.html.twig', array(
                    'entities' => $entities,
                    'doc' => $document,
                    'number' => $count,
        ));
    }

    /**
     * Finds and displays the liste of Document entity.
     *
     */
    public function documentsListeAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Document')->findAll();
        $count = count($entities);

        $user = $this->getUser();
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Admin\Document:documents-list.html.twig', array(
                        'entities' => $entities,
                        'number' => $count,
            ));
        }
        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Super-Admin\Document:documents-list.html.twig', array(
                        'entities' => $entities,
                        'number' => $count,
            ));
        }
    }

    /**
     * Finds and displays the liste of Document entity.
     *
     */
    public function PiecesJointesListeAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:PiecesJointes')->findAll();
        $count = count($entities);

        return $this->render('KbhGestionCongesBundle:Admin\Document:piecesJointes-list.html.twig', array(
                    'entities' => $entities,
                    'number' => $count,
        ));
    }

    /**
     * Deletes a Entreprise entity.
     *
     */
    public function deleteDocumentAction($id) {
//        $form = $this->createDeleteDocumentForm($id);
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Document entity.');
        }

        $em->remove($entity);
        $em->flush();
//        }

        return $this->redirect($this->generateUrl('sup_ad_documents_show'));
    }

    /**
     * Creates a form to delete a Entreprise entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteDocumentForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('sup_ad_document_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * Displays a form to edit an existing data of salarie entity import.
     *
     */
    public function importSalarieEditAction($id, $id_salarie) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:ImportSalarie')->find($id_salarie);

        $doc = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);
        $erreur = "";

        $editForm = $this->createEditSalarieForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin\Document:edit-salarie.html.twig', array(
                    'entity' => $entity,
                    'erreur' => $erreur,
                    'doc' => $doc,
                    'form' => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing data of salarie entity import.
     *
     */
    public function importAllocationEditAction($id, $id_donnee) {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();

        $entity = $em->getRepository('KbhGestionCongesBundle:LogCalculAllocationConge')->find($id_donnee);

        $doc = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);
        $erreur = "";

        $editForm = $this->createEditAllocationForm($entity);

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:top-manager\CalculAllocation:edit-donnee.html.twig', array(
                        'entity' => $entity,
                        'erreur' => $erreur,
                        'doc' => $doc,
                        'form' => $editForm->createView(),
            ));
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Superviseur\CalculAllocation:edit-donnee.html.twig', array(
                        'entity' => $entity,
                        'erreur' => $erreur,
                        'doc' => $doc,
                        'form' => $editForm->createView(),
            ));
        }
    }

    /**
     * Displays a form to edit an existing Data of units entity import.
     *
     */
    public function importUniteEditAction($id, $id_unit) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:ImportUnite')->find($id_unit);

        $doc = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);
        $erreur = "";

        $editForm = $this->createEditUnitForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin\Document:edit-unit.html.twig', array(
                    'entity' => $entity,
                    'erreur' => $erreur,
                    'doc' => $doc,
                    'form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to get a Document entity.
     *
     * @param ImportSalarie $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditSalarieForm(ImportSalarie $entity) {
        $form = $this->createForm(new ImportSalarieType(), $entity);
        return $form;
    }

    /**
     * Creates a form to get a Document entity.
     *
     * @param LogCalculAllocationConge $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditAllocationForm(LogCalculAllocationConge $entity) {
        $form = $this->createForm(new CalculAllocationCongeType(), $entity);
        return $form;
    }

    /**
     * Creates a form to get a Document entity.
     *
     * @param ImportUnite $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditUnitForm(ImportUnite $entity) {
        $form = $this->createForm(new ImportUniteType(), $entity);
        return $form;
    }

    /**
     * Edits an existing Droits entity.
     *
     */
    public function updateSalarieAction(Request $request, $id, $doc) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:ImportSalarie')->find($id);

        $editForm = $this->createEditSalarieForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "import document";
            $action = "Modification d'une donnée importée";
            $msg = $salarieConnecte->getCivilite() . " " . $salarieConnecte->getNomprenom() . " à modifié une donnée importé concernant la liste des salariés.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_import_show_sa', array(
                                'id' => $doc,
            )));
        }

        $erreur = "Erreur de saisie";

        return $this->render('KbhGestionCongesBundle:Super-Admin\Document:edit-salarie.html.twig', array(
                    'entity' => $entity,
                    'erreur' => $erreur,
                    'form' => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Droits entity.
     *
     */
    public function updateUniteAction(Request $request, $id, $doc) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:ImportUnite')->find($id);

        $editForm = $this->createEditUnitForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "import document";
            $action = "Modification d'une donnée importée";
            $msg = $salarieConnecte->getCivilite() . " " . $salarieConnecte->getNomprenom() . " à modifié une donnée importé concernant l'organigramme unité.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('sup_ad_import_show_unit', array(
                                'id' => $doc,
            )));
        }

        $erreur = "Erreur de saisie";

        return $this->render('KbhGestionCongesBundle:Super-Admin\Document:edit-unit.html.twig', array(
                    'entity' => $entity,
                    'erreur' => $erreur,
                    'form' => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Droits entity.
     *
     */
    public function updateAllocationAction(Request $request, $id, $id_doc) {
        $em = $this->getDoctrine()->getManager();
        $salarie = $this->getSalarieByUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:LogCalculAllocationConge')->find($id);

        $doc = $em->getRepository('KbhGestionCongesBundle:Document')->find($id_doc);
        $editForm = $this->createEditAllocationForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "import document";
            $action = "Modification d'une donnée importée";
            $msg = $salarieConnecte->getCivilite() . " " . $salarieConnecte->getNomprenom() . " à modifié une donnée importé concernant le calcul de l'allocation.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->redirect($this->generateUrl('top_manager_import_show_allocation', array(
                                    'id' => $id_doc,
                )));
            }
            if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
                //Redirection vers la page de confirmation
                return $this->redirect($this->generateUrl('sup_import_show_allocation', array(
                                    'id' => $id_doc,
                )));
            }
        }

        $erreur = "Erreur de saisie";

        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:top-manager\CalculAllocation:edit-donnee.html.twig', array(
                        'entity' => $entity,
                        'erreur' => $erreur,
                        'doc' => $doc,
                        'form' => $editForm->createView(),
            ));
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Superviseur\CalculAllocation:edit-donnee.html.twig', array(
                        'entity' => $entity,
                        'erreur' => $erreur,
                        'doc' => $doc,
                        'form' => $editForm->createView(),
            ));
        }
    }

    /**
     * Displays a form to edit an existing Data of units entity import.
     *
     */
    public function reinitialisationAction() {
        $em = $this->getDoctrine()->getManager();
        $donnees = $em->getRepository('KbhGestionCongesBundle:CalculAllocationConge')->findAll();
        $nb_donnees = count($donnees);
        if ($nb_donnees > 0) {
            foreach ($donnees as $donnee) {
                $em->remove($donnee);
                $em->flush();
            }
        }
        if ($nb_donnees == 0) {
            return $this->redirect($this->generateUrl('welcome'));
        }

        return $this->redirect($this->generateUrl('welcome'));
    }

//#######################################################################
//#######################################################################
    //#################### MIGRATION DES DONNEES IMPORTEES #######################

    /**
     * Migrations des salariés imortés
     *
     */
    public function migrationSalarieAction($id) {
        $em = $this->getDoctrine()->getManager();

        //Récupération des salariés importés
        $sa_import = $em->getRepository('KbhGestionCongesBundle:ImportSalarie')->findAll();

        //Récupération du document traité et mise à jour de son statut
        $document = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);
        $document->setStatut('Traité');

        //Récupération des salariés existants
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();

        //Initialisation des compteurs
        $erreurs = array();
        $nb = 0;
        $compteur_erreur = 0;
        $success = 0;
        $exist = false;

        foreach ($sa_import as $new) {
            //Vérification de l'existance du salarié
            foreach ($salaries as $old) {
                if ($new->getMatricule() == $old->getMatricule()) {
                    //Message d'erreur
                    $erreurs[$nb] = "Erreur lors de la migration du salarié. Le salarié ayant le matricule <<" . $old->getMatricule() . ">> existe déjà en base, son nom est : " . $old->getNomPrenom();
                    $compteur_erreur += 1;
                    $nb+=1;
                    $exist = true;
                }
            }
            if ($exist == false) {
                /* Cas de la non existance du salarié en base!
                  Migration des nouvelles données du salarié */

                // 1ère etape : (insertions des données personnelles du salarié)
                $new_salarie = new Salarie();

                $new_salarie->setCivilite($new->getCivilite());
                $new_salarie->setNom($new->getNom());
                $new_salarie->setPrenom($new->getPrenom());
                $new_salarie->setNomprenom();
                $new_salarie->setMatricule($new->getMatricule());
                $new_salarie->setNumeroCnps($new->getNumeroCnps());
                $new_salarie->setDateNaissance(new \DateTime($new->getDateNaissance()));
                $new_salarie->setStatutMarital($new->getStatutMarital());
                $new_salarie->setPoste($new->getPoste());
                $new_salarie->setEmail($new->getEmail());
                $new_salarie->setTelephone($new->getTelephone());
                $new_salarie->setDateEmbauche(new \DateTime($new->getDateEmbauche()));
                $new_salarie->setTypeContratTravail($new->getTypeContrat());
                $new_salarie->setStatutEmploi($new->getStatutEmploi());

                // 2ème étape : (Mise en place des droits du salarié)
                $new_droit = new Droits();

                $new_droit->setSalarie($new_salarie);
                $new_droit->setDroitsAcquisAnneeEnCours($new->getDroitsAcquis());
                $new_droit->setReliquatDroitsAnterieur($new->getDroitsAnterieur());
                $new_droit->setCumulDroitsAcquis($new->getCumulDroits());
                $new_droit->setDroitsPris($new->getDroitsPris());
                $new_droit->setSoldePermissions($new->getSoldePermission());
                $new_droit->setPermissionsPrises($new->getPermissionsPrises());
                $new_droit->setTotalDroitsAprendre($new->getSoldeConges());

                //Ajout du droit du salarié
                $new_salarie->setDroits($new_droit);

                // 3ème étape : (Création des accès du salarié)
                $user = new User();

                $user->addRole($new->getRoleUtilisateur());
                $user->setUsername($new_salarie->generateUsername());
                $user->setUsernameCanonical($new_salarie->generateUsername());
                $user->setEmail($new_salarie->getEmail());
                $user->setPlainPassword($new_salarie->getMatricule());
                $user->setEnabled(true);

                $new_salarie->setUser($user);
                $em->persist($document);
                $em->persist($new_droit);
                $em->persist($user);
                $em->persist($new_salarie);
                $em->flush();

                $success += 1;
            }
            //Réinitialisation du boolen  
            $exist = false;
        }

        //Log action effectuée
        $salarieConnecte = $this->getSalarieByUser();
        $cible = "import document";
        $action = "Import de document pour la création des salariés";
        $msg = $salarieConnecte->getCivilite() . " " . $salarieConnecte->getNomprenom() . " à importé un document pour la création des salariés.";
        $this->logActivite($salarieConnecte, $cible, $action, $msg);

        return $this->render('KbhGestionCongesBundle:Super-Admin\Document:resultat-migration.html.twig', array(
                    'erreurs' => $erreurs,
                    'compteur_erreur' => $compteur_erreur,
                    'success' => $success,
        ));
    }

    /**
     * Migrations des salariés imortés
     *
     */
    public function migrationFeriesAction() {
        $em = $this->getDoctrine()->getManager();

        //Récupération des salariés importés
        $feries_import = $em->getRepository('KbhGestionCongesBundle:ImportFeries')->findAll();

        //Récupération du document traité et mise à jour de son statut
        $document = $em->getRepository('KbhGestionCongesBundle:Document')->findOneByCible("Feries");
        $document->setStatut('Traité');

        //Récupération des salariés existants
        $feries = $em->getRepository('KbhGestionCongesBundle:Feries')->findAll();

        //Initialisation des compteurs
        $erreurs = array();
        $nb = 0;
        $compteur_erreur = 0;
        $success = 0;
        $exist = false;

        foreach ($feries_import as $new) {
            //Vérification de l'existance de féries
            foreach ($feries as $old) {
                if ($new->getTitreFeries() == $old->getTitreFeries()) {
                    //Message d'erreur
                    $erreurs[$nb] = "Erreur lors de la migration du férié. Le férié ayant le nom <<" . $old->getTitreFeries() . ">> existe déjà en base.";
                    $compteur_erreur += 1;
                    $nb+=1;
                    $exist = true;
                }
            }
            if ($exist == false) {
                /* Cas de la non existance du salarié en base!
                  Migration des nouvelles données du salarié */

                //Préparation des dates 
                $dateDebutTms = strtotime($new->getDateDebutFerie());
                $dateFinTms = strtotime($new->getDateFinFerie());

                $dateDebut = date('Y/m/d H:i:s', $dateDebutTms);
                $dateFin = date('Y/m/d H:i:s', $dateFinTms);

                // 1ère etape : (insertions des données )
                $new_ferie = new Feries();

                $new_ferie->setTitreFeries($new->getTitreFeries());
                $new_ferie->setDateDebutFerie(new \DateTime($dateDebut));
                $new_ferie->setDateFinFerie(new \DateTime($dateFin));
                $new_ferie->setNbJoursFerie($new->getNbJoursFerie());

                $em->persist($document);
                $em->persist($new_ferie);
                $em->flush();

                $success += 1;
            }
            //Réinitialisation du boolen  
            $exist = false;
        }

        return $this->redirect($this->generateUrl('installation_import_permissions'));
    }

    /**
     * Migrations des unités imortés
     *
     */
    public function migrationUniteAction($id) {
        $em = $this->getDoctrine()->getManager();

        //Récupération des unités importés
        $unit_import = $em->getRepository('KbhGestionCongesBundle:ImportUnite')->findAll();

        //Récupération du document traité et mise à jour de son statut
        $document = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);
        $document->setStatut('Traité');

        //Récupération des unités existantes
        $unites = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();

        //Initialisation des compteurs
        $erreurs = array();
        $nb = 0;
        $compteur_erreur = 0;
        $success = 0;
        $exist = false;

        foreach ($unit_import as $new) {
            //Vérification de l'existance de l'unité
            foreach ($unites as $old) {
                if ($new->getNomUnite() == $old->getNom() || $new->getSigle() == $old->getSigle()) {
                    //Message d'erreur
                    $erreurs[$nb] = "Erreur lors de la migration de l'unité. Il existe déja une unité du nom de <<" . $old->getNom() . ">> ayant pour sigle " . $old->getSigle();
                    $compteur_erreur += 1;
                    $nb+=1;
                    $exist = true;
                }
            }
            if ($exist == false) {
                /* Cas de la non existance de l'unite en base!
                  Migration des nouvelles données de l'organigramme */

                // 1ère etape : (insertions des données)
                $new_unit = new OrganigrammeUnite();

                $new_unit->setNom($new->getNomUnite());
                $new_unit->setSigle($new->getSigle());

                //Recherche du manager de l'unite
                $manager = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByMatricule($new->getMatriculeManager());
                $new_unit->setManager($manager);

                if ($new->getTypeUnite() == "SER") {
                    $new_unit->setEstService(1);
                }
                if ($new->getTypeUnite() == "CEL") {
                    $new_unit->setEstCellule(1);
                }
                if ($new->getTypeUnite() == "DEP") {
                    $new_unit->setEstDepartement(1);
                }
                if ($new->getTypeUnite() == "DIR") {
                    $new_unit->setEstDirection(1);
                }
                if ($new->getTypeUnite() == "DGA") {
                    $new_unit->setEstDga(1);
                }
                if ($new->getTypeUnite() == "DG") {
                    $new_unit->setEstDg(1);
                }
                if ($new->getTypeUnite() == "DRH") {
                    $new_unit->setEstDrh(1);
                }

                $em->persist($document);
                $em->persist($new_unit);
                $em->flush();

                $success += 1;
            }
            //Réinitialisation du boolen  
            $exist = false;
        }
        //Log action effectuée
        $salarieConnecte = $this->getSalarieByUser();
        $cible = "import document";
        $action = "Import de document pour la création des unités";
        $msg = $salarieConnecte->getCivilite() . " " . $salarieConnecte->getNomprenom() . " à importé un document pour la création des documents.";
        $this->logActivite($salarieConnecte, $cible, $action, $msg);

        return $this->render('KbhGestionCongesBundle:Super-Admin\Document:resultat-migration.html.twig', array(
                    'erreurs' => $erreurs,
                    'compteur_erreur' => $compteur_erreur,
                    'success' => $success,
        ));
    }

//#######################################################################
//#######################################################################
    //#################### CALCUL DE L'ALLOCATION #######################

    /**
     * Migrations des salariés imortés
     *
     */
    public function calculAllocationAction($id) {
        $em = $this->getDoctrine()->getManager();

        //Récupération des salariés importés
        $sa_import = $em->getRepository('KbhGestionCongesBundle:LogCalculAllocationConge')->findAll();

        //Récupération du document traité et mise à jour de son statut
        $document = $em->getRepository('KbhGestionCongesBundle:Document')->find($id);
        $document->setStatut('Traité');

        //Récupération des salariés existants
        $salaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();

        //Initialisation des compteurs
        $erreurs = array();
        $nb = 0;
        $compteur_erreur = 0;
        $success = 0;
        $somme_allocation = 0;

        foreach ($sa_import as $import) {
            //Vérification de l'existance du salarié
            $okey = false;
            foreach ($salaries as $old) {
                if ($import->getMatricule() == $old->getMatricule()) {

                    //1ere étape : Récupération des droits légaux du salarié
                    $droit_legal = $old->getDroits()->getTotalDroitsAprendre();

                    //Calcul des données de fin et retour.
                    // Recuperation du contenu des champs nombre de jours et date de debut
                    $nombreJours = $droit_legal - 1;
                    $dateDebut = new \Datetime($import->getDateEffet());

                    //Conversion du nombre de jours et de la date de debut en secondes (timestamp)
                    $nbSecondes = 60 * 60 * 24;
                    //$tms_dateDebut = strtotime($dateDebut);        
                    $tms_dateDebut = $dateDebut->getTimestamp();

                    //Recuperation du jour de la date de debut
                    $jour = getdate($tms_dateDebut); // On obtient ainsi le jour de la semaine 
                    $jourDateDebut = $jour['wday'];

                    /*                     * ********************************************
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


                    /*                     * ********************************************
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
                            $nb_ferie = 0;
                            $nb_ferie += $ferie->getNbJoursFerie();
                            $titreFerie = $ferie->getTitreFeries();
                            $joursFeries[$cp] = $titreFerie;
                            $cp++;
                        }
                        if ($tms_dateFin == $tms_DDF && $tms_dateFin < $tms_DFF) {
                            $nb_ferie = 0;
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
                    $tms_add = ($tms_nbjTotal + $nbSecondes) + $tms_dateDebut;

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

                    // $datetFinale = date('Y/m/d H:i:s', $tms_add_nbjF);
                                
                                
                    //***************************************************************************//
                    //***************************************************************************//  
                    //***********************  DETERMINATION DE L'ALLOCATION ************************// 
                    //Déterminons le droits réél du salarié
                    $droit_reel = round((($tms_add_nbjF - $tms_dateDebut) / $nbSecondes), 0);


                    //3ème étape calcul du salaire moyen
                    $salaireMoyen = round((($import->getSalaireMensuel() * 12) + $import->getGratification()) / 360, 0);

                    //4ème étatpe Calcul de l'allocation
                    $calculAllocation = $salaireMoyen * $droit_reel;

                    $new_calcul_allocation = new CalculAllocationConge();
                    $new_calcul_allocation->setSalarie($old);
                    $new_calcul_allocation->setMatricule($old->getMatricule());
                    $new_calcul_allocation->setSalaireMoyenJournalier($salaireMoyen);
                    $new_calcul_allocation->setDroitsLegaux($droit_legal);
                    $new_calcul_allocation->setDroitsReels($droit_reel);
                    $new_calcul_allocation->setAllocationConge($calculAllocation);
                    $new_calcul_allocation->setUnite($old->getUnite());
                    $new_calcul_allocation->setDateCalcul(new \Datetime());
                    $new_calcul_allocation->setDateEffet(new \Datetime($import->getDateEffet()));

                    $em->persist($new_calcul_allocation);
                    $em->flush();

                    $somme_allocation += $calculAllocation;
                    $success += 1;
                    $okey = true;
                }
            }
            if ($okey == false) {

                //Message d'erreur
                $erreurs[$nb] = "Erreur lors de l'operation. Il n'existe aucun salarié du nom de <<" . $import->getSalarie() . ">> ayant pour matricule " . $import->getMatricule();
                $compteur_erreur += 1;
                $nb+=1;
            }
        }

        //Log action effectuée
        $salarieConnecte = $this->getSalarieByUser();
        $cible = "import document";
        $action = "Import de document pour les calculs d'allocations";
        $msg = $salarieConnecte->getCivilite() . " " . $salarieConnecte->getNomprenom() . " à importé un document pour le calcul de l'allocation.";
        $this->logActivite($salarieConnecte, $cible, $action, $msg);

        $resultat = $em->getRepository('KbhGestionCongesBundle:CalculAllocationConge')->findAll();

        $salarie = $this->getSalarieByUser();
        if (in_array("ROLE_TOP_MANAGER", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:top-manager\CalculAllocation:resultat-operation.html.twig', array(
                        'erreurs' => $erreurs,
                        'somme_allocation' => $somme_allocation,
                        'entities' => $resultat,
                        'compteur_erreur' => $compteur_erreur,
                        'success' => $success,
            ));
        }
        if (in_array("ROLE_SUPERVISEUR", $salarie->getUser()->getRoles())) {
            //Redirection vers la page de confirmation
            return $this->render('KbhGestionCongesBundle:Superviseur\CalculAllocation:resultat-operation.html.twig', array(
                        'erreurs' => $erreurs,
                        'somme_allocation' => $somme_allocation,
                        'entities' => $resultat,
                        'compteur_erreur' => $compteur_erreur,
                        'success' => $success,
            ));
        }
    }

//    ############################## BASE DE DONNEES #############################

    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function documentsSysListeAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Document')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/Documents:documents.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function PiecesJointesSysListeAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:PiecesJointes')->findAll();

        return $this->render('KbhGestionCongesBundle:Super-Admin/DonneesSysteme/PiecesJointes:Pieces-jointes.html.twig', array(
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

    /**
     * Fonction de log des activités des salariés (timeline)
     *
     */
    public function logActivite($salarie, $cible, $action, $message) {
        $em = $this->getDoctrine()->getManager();
        $logActivite = new \Kbh\GestionCongesBundle\Entity\LogActivites;

        //1er cas : cible concernant les salariés
        if ($cible == "salariés") {
            $logActivite->setIcon("icon-user");
        }
        //2ème cas : cible concernant les notifications
        if ($cible == "notifications") {
            $logActivite->setIcon("icon-bell");
        }
        //3ème cas : cible concernant les arrêt de travail
        if ($cible == "arrêt de travail") {
            $logActivite->setIcon("icon-close");
        }
        //4ème cas : cible concernant les unités
        if ($cible == "unités") {
            $logActivite->setIcon("icon-note");
        }
        //5ème cas : cible concernant les justifications des absences
        if ($cible == "justifications des absences") {
            $logActivite->setIcon("icon-layers");
        }
        //6ème cas : cible concernant les mises à jours
        if ($cible == "mises à jours") {
            $logActivite->setIcon("icon-refresh");
        }
        //7ème cas : cible concernant les documents importés
        if ($cible == "documents") {
            $logActivite->setIcon("icon-doc");
        }
        //7ème cas : cible concernant les documents importés
        if ($cible == "import document") {
            $logActivite->setIcon("icon-arrow-down");
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
