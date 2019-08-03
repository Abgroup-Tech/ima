<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\Entreprise;
use Kbh\GestionCongesBundle\Form\EntrepriseType;
use Kbh\GestionCongesBundle\Form\ParamCalculsDroitsType;
use Kbh\GestionCongesBundle\Entity\Paramcalculsdroits;
use Kbh\GestionCongesBundle\Entity\Feries;
use Kbh\GestionCongesBundle\Form\FeriesType;
use Kbh\GestionCongesBundle\Entity\Parampermissions;
use Kbh\GestionCongesBundle\Form\ParamPermissionsType;

/**
 * Entreprise controller.
 *
 */
class EntrepriseController extends Controller
{

    /**
     * Lists all Entreprise entities.
     * 
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Entreprise')->findAll();

        return $this->render('KbhGestionCongesBundle:Admin\Entreprise:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    /**
     * Creates a new Entreprise entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Entreprise();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ad_systeme_show'));
        }

        return $this->render('KbhGestionCongesBundle:Admin\Entreprise:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    
        /**
     * Creates a new Feries entity.
     *
     */
    public function createFeriesAction(Request $request)
    {
        $entity = new Feries();
        $form = $this->createFeriesForm($entity);
        $form->handleRequest($request);
        $user_connected = $this->getUser();

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "système";
            $action = "Création d'un nouveau férié";
            $msg = $salarieConnecte->getNomprenom()." à ajouté un nouveau férié.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

           if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
            }
        }

           if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('ad_systeme_show'));
               }
           if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
           }
    }
    
    
        /**
     * Creates a new Permissions entity.
     *
     */
    public function createPermissionsAction(Request $request)
    {
        $entity = new Parampermissions();
        $form = $this->createPermissionForm($entity);
        $form->handleRequest($request);
        $user_connected = $this->getUser();
        

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "système";
            $action = "Création d'une nouvelle permission exceptionnelle";
            $msg = $salarieConnecte->getNomprenom()." à créé une nouvelle permission exceptionnelle.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);

            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
            }
        }

           if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
            }
    }

    /**
     * Creates a form to create a Entreprise entity.
     *
     * @param Entreprise $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Entreprise $entity)
    {
        $form = $this->createForm(new EntrepriseType(), $entity, array(
            'action' => $this->generateUrl('ad_entreprise_create'),
            'method' => 'POST',
        ));

        return $form;
    }
    
        /**
     * Creates a form to create a Entreprise entity.
     *
     * @param Feries $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createFeriesForm(Feries $entity)
    {
        $form = $this->createForm(new FeriesType(), $entity, array(
            'action' => $this->generateUrl('ad_feries_create'),
            'method' => 'POST',
        ));

        return $form;
    }
    
        /**
     * Creates a form to create a Entreprise entity.
     *
     * @param Parampermissions $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createPermissionForm(Parampermissions $entity)
    {
        $form = $this->createForm(new ParamPermissionsType(), $entity, array(
            'action' => $this->generateUrl('ad_permissions_create'),
            'method' => 'POST',
        ));

        return $form;
    }    

    /**
     * Displays a form to create a new Entreprise entity.
     *
     */
    public function newAction()
    {
        $entity = new Entreprise();
        $form   = $this->createCreateForm($entity);
        $erreur = "";

        return $this->render('KbhGestionCongesBundle:Admin\Entreprise:new.html.twig', array(
            'entity' => $entity,
            'erreur' => $erreur,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Entreprise entity.
     *
     */
    public function showAction()
    {
        
        //Vérification de l'existance des données de l'entreprise
        
        $em = $this->getDoctrine()->getManager();
        $user_connected = $this->getUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:Entreprise')->findAll();
        $cp= count($entity);
        
        if($cp != 0){
        $id=1;
        
        // Information de l'entreprise et formulaire
        $entity = $em->getRepository('KbhGestionCongesBundle:Entreprise')->find($id);
        $editForm = $this->createEditForm($entity);
        
        //Fériés et formulaire
        $feries = $em->getRepository('KbhGestionCongesBundle:Feries')->findAll();
        $new_feries = new Feries();
        $feriesForm = $this->createFeriesForm($new_feries);
        
        //ParamPermissions et formulaire
        $Permission = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->findAll();
        $new_permission = new Parampermissions();
        $PermissionForm = $this->createPermissionForm($new_permission);
        
        //ParamCalculDroit
        $calculDroit = $em->getRepository('KbhGestionCongesBundle:Paramcalculsdroits')->find($id);
        $CalculDroitForm = $this->EditParamCalDroitForm($calculDroit);
        
        }
        
         if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Admin\Entreprise:show.html.twig', array(
                    'entity'                          => $entity,
                    'edit_form'                    => $editForm->createView(),
                    'feriesForm'                  => $feriesForm->createView(),
                    'permissionForm'          => $PermissionForm->createView(),
                    'edit_calculDroitForm'  => $CalculDroitForm->createView(),
                    'feries'                          => $feries,
                    'param_permission'      => $Permission,
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Entreprise:show.html.twig', array(
                    'entity'                          => $entity,
                    'edit_form'                    => $editForm->createView(),
                    'feriesForm'                  => $feriesForm->createView(),
                    'permissionForm'          => $PermissionForm->createView(),
                    'edit_calculDroitForm'  => $CalculDroitForm->createView(),
                    'feries'                          => $feries,
                    'param_permission'      => $Permission,
                ));
            }
    }

    public function feriesAction()
    {
        
        //Vérification de l'existance des données de l'entreprise
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:Entreprise')->findAll();
        $cp= count($entity);
        
        if($cp == 0){
            return $this->redirect($this->generateUrl('ad_entreprise_new'));
        }
        if($cp != 0){
        
        //Fériés et formulaire
        $feries = $em->getRepository('KbhGestionCongesBundle:Feries')->findAll();
        $new_feries = new Feries();
        $feriesForm = $this->createFeriesForm($new_feries);

        }
        return $this->render('KbhGestionCongesBundle:Admin\Entreprise:feries.html.twig', array(
            'entity'      => $entity,
            'feriesForm'   => $feriesForm->createView(),
            'feries'      => $feries,
        ));
    }    
    
    /**
     * Displays a form to edit an existing Feries entity.
     *
     */
    public function editFeriesAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user_connected = $this->getUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:Feries')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feries entity.');
        }

        $editForm = $this->createEditFeriesForm($entity);
        $deleteForm = $this->createDeleteFeriesForm($id);

         if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Admin\Entreprise:edit-feries.html.twig', array(
                    'entity'      => $entity,
                    'form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Entreprise:edit-feries.html.twig', array(
                    'entity'      => $entity,
                    'form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
    }
    
        /**
     * Displays a form to edit an existing Permissions entity.
     *
     */
    public function editPermissionsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user_connected = $this->getUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Permissions entity.');
        }

        $editForm = $this->createEditPermissionsForm($entity);
        $deleteForm = $this->createDeletePermissionsForm($id);

        if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Admin\Entreprise:edit-permissions.html.twig', array(
                    'entity'      => $entity,
                    'form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Entreprise:edit-permissions.html.twig', array(
                    'entity'      => $entity,
                    'form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }
    }

    /**
    * Creates a form to edit a Entreprise entity.
    *
    * @param Entreprise $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Entreprise $entity)
    {
        $form = $this->createForm(new EntrepriseType(), $entity, array(
            'action' => $this->generateUrl('ad_entreprise_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    
    /**
    * Creates a form to edit a Entreprise entity.
    *
    * @param Paramcalculsdroits $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function EditParamCalDroitForm(Paramcalculsdroits $entity)
    {
        $form = $this->createForm(new ParamCalculsDroitsType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_paramcalculsdroits_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    
        /**
    * Creates a form to edit a Entreprise entity.
    *
    * @param Feries $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditFeriesForm(Feries $entity)
    {
        $form = $this->createForm(new FeriesType(), $entity, array(
            'action' => $this->generateUrl('ad_feries_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    
        /**
    * Creates a form to edit a Entreprise entity.
    *
    * @param Parampermissions $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditPermissionsForm(Parampermissions $entity)
    {
        $form = $this->createForm(new ParamPermissionsType(), $entity, array(
            'action' => $this->generateUrl('ad_permissions_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    
    /**
     * Edits an existing Entreprise entity.
     *
     */
    public function updateEntrepriseAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Entreprise')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Entreprise entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "entreprise";
            $action = "Modification des données de l'entreprise";
            $msg = $salarieConnecte->getNomprenom()." à modifié les données de l'entreprise.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
             $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
            }
        }

             if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
            }
    }
    
        /**
     * Edits an existing Entreprise entity.
     *
     */
    public function updateFeriesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user_connected = $this->getUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:Feries')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feries entity.');
        }
        $titre = $entity->getTitreFeries();
        $editForm = $this->createEditFeriesForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "système";
            $action = "Modification d'un férié";
            $msg = $salarieConnecte->getNomprenom()." à modifié un férié <<".$titre.">>.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
            }
        }

            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
            }
    }
    
        /**
     * Edits an existing Entreprise entity.
     *
     */
    public function updatePermissionsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user_connected = $this->getUser();
        $entity = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Permissions entity.');
        }
        $motif = $entity->getMotif();
        $editForm = $this->createEditPermissionsForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "système";
            $action = "Modification d'une permission exceptionnelle";
            $msg = $salarieConnecte->getNomprenom()." à modifié une permission exceptionnelle <<".$motif.">>.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
            }
        }

            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
            }
    }
    
    /**
     * Deletes a Entreprise entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:Entreprise')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Entreprise entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('entreprise'));
    }

        /**
     * Deletes a Entreprise entity.
     *
     */
    public function deleteFeriesAction(Request $request, $id)
    {
        $form = $this->createDeleteFeriesForm($id);
        $form->handleRequest($request);
        $user_connected = $this->getUser();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:Feries')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Feries entity.');
            }

            $em->remove($entity);
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "système";
            $action = "Suppression d'un férié du système";
            $msg = $salarieConnecte->getNomprenom()." à supprimé le férié <<".$entity->getTitreFeries().">>.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
        }

        if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
        if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
             return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
        }
    }

    
        /**
     * Deletes a Entreprise entity.
     *
     */
    public function deletePermissionsAction(Request $request, $id)
    {
        $form = $this->createDeletePermissionsForm($id);
        $form->handleRequest($request);
        $user_connected = $this->getUser();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:Parampermissions')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Permissions entity.');
            }

            $em->remove($entity);
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "système";
            $action = "Suppression d'une permission exceptionnelle du système";
            $msg = $salarieConnecte->getNomprenom()." à supprimé la permission exceptionnelle <<".$entity->getMotif().">>.";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
        }

        if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_systeme_show'));
            }
        if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
             return $this->redirect($this->generateUrl('sup_ad_systeme_show'));
        }
    }

    
    /**
     * Creates a form to delete a Entreprise entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('entreprise_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
        /**
     * Creates a form to delete a Entreprise entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteFeriesForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ad_feries_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
        /**
     * Creates a form to delete a Entreprise entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeletePermissionsForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ad_permissions_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
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
        //8ème cas : cible concernant les modifications système
        if($cible == "système"){
            $logActivite->setIcon("icon-settings");
        }
        //9ème cas : cible concernant les modifications de l'entreprise
        if($cible == "entreprise"){
            $logActivite->setIcon("icon-home");
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
