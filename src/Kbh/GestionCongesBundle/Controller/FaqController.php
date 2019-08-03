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
     * Lists all Faq entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $em->getRepository('KbhGestionCongesBundle:Faq')->findAll();
        
        $entity = new Faq();
        $form   = $this->createCreateForm($entity);
        
         if (in_array("ROLE_ADMIN",$user->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Faq:index-admin.html.twig', array(
                'entities' => $entities,
                'form'   => $form->createView(),
            ));
            }

        if (in_array("ROLE_SUPER_ADMIN", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Faq:index-super-admin.html.twig', array(
                'entities' => $entities,
                'form'   => $form->createView(),
            ));
            }
        
    }
    
    /**
     * Lists all Faq entities for salarie.
     *
     */
    public function faqSalarieAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Faq')->findAll();
        
        return $this->render('KbhGestionCongesBundle:Faq:index-salarie.html.twig', array(
            'entities' => $entities,
        ));
    }  
    
    /**
     * Lists all Faq entities for superviseur.
     *
     */
    public function faqSuperviseurAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $em->getRepository('KbhGestionCongesBundle:Faq')->findAll();
        
        if (in_array("ROLE_SUPERVISEUR",$user->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Superviseur\Faq:index-superviseur.html.twig', array(
               'entities' => $entities,
              ));
            }

        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Top-manager\Faq:index-manager.html.twig', array(
               'entities' => $entities,
              ));
            }
        
    }      
    
    /**
     * Creates a new Faq entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Faq();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "FAQ";
            $action = "Création d'une nouvelle question dans la FAQ";
            $msg = $salarieConnecte->getNomprenom()." à ajouté des informations dans la foire aux questions (FAQ).";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            return $this->redirect($this->generateUrl('ad_faq'));
        }

        return $this->render('KbhGestionCongesBundle:Faq:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Faq entity.
     *
     * @param Faq $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Faq $entity)
    {
        $form = $this->createForm(new FaqType(), $entity, array(
            'action' => $this->generateUrl('faq_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Faq entity.
     *
     */
//    public function newAction()
//    {
//        $entity = new Faq();
//        $form   = $this->createCreateForm($entity);
//
//        return $this->render('KbhGestionCongesBundle:Faq:new.html.twig', array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//        ));
//    }

    /**
     * Finds and displays a Faq entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Faq')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:Faq:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Faq entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Faq')->find($id);
        $erreur = "";
        
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:Faq:edit.html.twig', array(
            'entity'      => $entity,
            'erreur'      => $erreur,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Faq entity.
    *
    * @param Faq $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Faq $entity)
    {
        $form = $this->createForm(new FaqType(), $entity, array(
            'action' => $this->generateUrl('faq_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Faq entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Faq')->find($id);

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "FAQ";
            $action = "Modification d'une information dans la FAQ";
            $msg = $salarieConnecte->getNomprenom()." à modifié des informations dans la foire aux questions (FAQ).";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            return $this->redirect($this->generateUrl('ad_faq'));
        }
        $erreur = "Erreur de saisie";
        return $this->render('KbhGestionCongesBundle:Faq:edit.html.twig', array(
            'entity'      => $entity,
            'erreur'      => $erreur,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Faq entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:Faq')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Faq entity.');
            }

            $em->remove($entity);
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "FAQ";
            $action = "Suppression d'une question dans la FAQ";
            $msg = $salarieConnecte->getNomprenom()." à supprimé des informations dans la foire aux questions (FAQ).";
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
        }

        return $this->redirect($this->generateUrl('ad_faq'));
    }

    /**
     * Creates a form to delete a Faq entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('faq_delete', array('id' => $id)))
            ->setMethod('DELETE')
//            ->add('submit', 'submit', array('label' => 'Delete'))
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
         //8ème cas : cible concernant la FAQ
        if($cible == "FAQ"){
            $logActivite->setIcon("icon-info");
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
