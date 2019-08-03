<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\OrganigrammeUnite;
use Kbh\GestionCongesBundle\Form\OrganigrammeUniteType;
use Kbh\GestionCongesBundle\Entity\Document;
use Kbh\GestionCongesBundle\Form\DocumentType;

/**
 * OrganigrammeUnite controller.
 *
 */
class OrganigrammeUniteController extends Controller
{

    /**
     * Lists all OrganigrammeUnite entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();
       
        
        //Formulaire d'import de documents
        $entity = new Document();
        $form   = $this->createDocumentForm($entity);
        
        return $this->render('KbhGestionCongesBundle:Admin\OrganigrammeUnite:index.html.twig', array(
            'entities' => $entities,
            'form' => $form->createView(),
        ));
    }
    
    /**
     * Lists all OrganigrammeUnite entities.
     *
     */
    public function indexSupAdminAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->findAll();
       
        
        //Formulaire d'import de documents
        $entity = new Document();
        $form   = $this->createDocumentForm($entity);
        
        return $this->render('KbhGestionCongesBundle:Super-Admin\OrganigrammeUnite:index.html.twig', array(
            'entities' => $entities,
            'form' => $form->createView(),
        ));
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
                                )
                ));
        return $form;
    }
    
    
    /**
     * Creates a new OrganigrammeUnite entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new OrganigrammeUnite();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $user = $this->getUser();
            if (in_array("ROLE_ADMIN", $user->getRoles())) {
                return $this->redirect($this->generateUrl('ad_organigrammeunite'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
                return $this->redirect($this->generateUrl('sup_ad_organigrammeunite'));
            }
        }
        $erreur = "Erreur de saisie";
        
        $user = $this->getUser();
            if (in_array("ROLE_ADMIN", $user->getRoles())) {
               return $this->render('KbhGestionCongesBundle:Admin\OrganigrammeUnite:new.html.twig', array(
                    'entity' => $entity,
                    'erreur'  => $erreur,
                    'form'   => $form->createView(),
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
               return $this->render('KbhGestionCongesBundle:Super-Admin\OrganigrammeUnite:new.html.twig', array(
                    'entity' => $entity,
                    'erreur'  => $erreur,
                    'form'   => $form->createView(),
                ));
            }

        
    }

    
    /**
     * Creates a form to create a OrganigrammeUnite entity.
     *
     * @param OrganigrammeUnite $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(OrganigrammeUnite $entity)
    {
        $form = $this->createForm(new OrganigrammeUniteType(), $entity, array(
            'action' => $this->generateUrl('ad_organigrammeunite_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new OrganigrammeUnite entity.
     *
     */
    public function newAction()
    {
        $entity = new OrganigrammeUnite();
        $erreur = "";
        $form   = $this->createCreateForm($entity);

        return $this->render('KbhGestionCongesBundle:Admin\OrganigrammeUnite:new.html.twig', array(
            'entity' => $entity,
            'erreur'  => $erreur,
            'form'   => $form->createView(),
        ));
    }
    
        /**
     * Displays a form to create a new OrganigrammeUnite entity.
     *
     */
    public function newSupAdminAction()
    {
        $entity = new OrganigrammeUnite();
        $erreur = "";
        $form   = $this->createCreateForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin\OrganigrammeUnite:new.html.twig', array(
            'entity' => $entity,
            'erreur'  => $erreur,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a OrganigrammeUnite entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrganigrammeUnite entity.');
        }

        return $this->render('KbhGestionCongesBundle:Admin\OrganigrammeUnite:show.html.twig', array(
            'entity'      => $entity,

        ));
    }

    /**
     * Displays a form to edit an existing OrganigrammeUnite entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->find($id);
        $erreur = "";

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrganigrammeUnite entity.');
        }

        $editForm = $this->createEditForm($entity);
    
        return $this->render('KbhGestionCongesBundle:Admin\OrganigrammeUnite:edit.html.twig', array(
            'entity'      => $entity,
            'erreur'  => $erreur,
            'form'   => $editForm->createView(),
    
        ));
    }
    
        /**
     * Displays a form to edit an existing OrganigrammeUnite entity.
     *
     */
    public function editSupAdminAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->find($id);
        $erreur = "";

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrganigrammeUnite entity.');
        }

        $editForm = $this->createEditForm($entity);
    
        return $this->render('KbhGestionCongesBundle:Super-Admin\OrganigrammeUnite:edit.html.twig', array(
            'entity'      => $entity,
            'erreur'  => $erreur,
            'form'   => $editForm->createView(),
    
        ));
    }

    /**
    * Creates a form to edit a OrganigrammeUnite entity.
    *
    * @param OrganigrammeUnite $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(OrganigrammeUnite $entity)
    {
        $form = $this->createForm(new OrganigrammeUniteType(), $entity, array(
            'action' => $this->generateUrl('ad_organigrammeunite_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing OrganigrammeUnite entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:OrganigrammeUnite')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OrganigrammeUnite entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "unités";
            $action = "Modification d'une unité";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifié les champs de l'unité ".$entity->getNom();
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            $user = $this->getUser();
            if (in_array("ROLE_ADMIN", $user->getRoles())) {
                return $this->redirect($this->generateUrl('ad_organigrammeunite'));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
                return $this->redirect($this->generateUrl('sup_ad_organigrammeunite'));
            }
        }
        
        $erreur = "Erreur de saisie";
        
         $user = $this->getUser();
            if (in_array("ROLE_ADMIN", $user->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Admin\OrganigrammeUnite:edit.html.twig', array(
                    'entity'      => $entity,
                    'erreur'  => $erreur,
                    'edit_form'   => $editForm->createView(),

                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Super-Admin\OrganigrammeUnite:edit.html.twig', array(
                    'entity'      => $entity,
                    'erreur'  => $erreur,
                    'edit_form'   => $editForm->createView(),
                ));
            }
        

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
            $logActivite->setIcon("icon-wrench");
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
