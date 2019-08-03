<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Form\SalarieType;
use Kbh\GestionCongesBundle\Entity\Droits;
use Kbh\GestionCongesBundle\Entity\OrganigrammeUnite;
use Kbh\GestionCongesBundle\Entity\BaseDroits;
use Kbh\GestionCongesBundle\Form\BaseDroitsType;
use Kbh\UserBundle\Entity\User;
use Kbh\GestionCongesBundle\Form\UserType;
use Kbh\GestionCongesBundle\Form\UserSupAdminType;
use Kbh\GestionCongesBundle\Form\UserEditType;
use Kbh\GestionCongesBundle\Form\UserEditPassType;

/**
 * Salarie controller.
 *
 */
class SalarieController extends Controller
{
/**************************** ADMINISTRATEUR *******************************/
    /**
     * Lists all Salarie entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();

        return $this->render('KbhGestionCongesBundle:Admin\Salarie:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    /**
     * Creates a new Salarie entity.
     *
     */
    public function createAction(Request $request)
    {
        $user_connected = $this->getUser();
        $entity = new Salarie();
        $droits = new Droits();
        
        $entity->setDroits($droits);
        $droits->setSalarie($entity);
        
        if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                         $form = $this->createCreateSupAdminForm($entity);
                         $form->handleRequest($request);
            }
        if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
                        $form = $this->createCreateForm($entity);
                        $form->handleRequest($request);
            }    

        if ($form->isValid()) {
            //Ajout du 1er mars 2015 - BhK
            $entity->setSuperviseur($entity->getUnite()->getManager());
            
            $user = new \Kbh\UserBundle\Entity\User();
            $entity->setNomprenom();
            
            $roles = $entity->getUser()->getRoles();
            foreach($roles as $role){
                if($role == "ROLE_SALARIE"){
                    $user->addRole('ROLE_SALARIE');
                }
                 if($role == "ROLE_SUPERVISEUR"){
                    $user->addRole('ROLE_SUPERVISEUR');
                }
                 if($role == "ROLE_ADMIN"){
                    $user->addRole('ROLE_ADMIN');
                }
                if($role == "ROLE_SUPER_ADMIN"){
                    $user->addRole('ROLE_SUPER_ADMIN');
                }
                if($role == "ROLE_TOP_MANAGER"){
                    $user->addRole('ROLE_TOP_MANAGER');
                }
            }
            
            $user->setUsername($entity->getUser()->getUsername());
            $user->setUsernameCanonical($entity->getUser()->getUsername());
            $user->setEmail($entity->getEmail());
            $user->setPlainPassword($entity->getMatricule());
            $user->setEnabled(true);
            $entity->setUser($user); 
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($droits);
            $em->persist($user);
            $em->persist($entity);
            $em->flush();
            
            //Log de l'action éffectuée
            $salarie = $this->getSalarieByUser();
            $cible = "salariés";
            $action = "Création d'un nouveau salarié";
            $msg = $salarie->getCivilite()." ".$salarie->getNomprenom()." à ajouté un nouveau salarié.";
            $this->logActivite($salarie, $cible, $action, $msg);
          
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
                 return $this->redirect($this->generateUrl('ad_salarie_show', array('id' => $entity->getId())));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->redirect($this->generateUrl('sup_ad_salarie_show', array('id' => $entity->getId())));
            }
        }
        
        $erreur = "Erreur de saisie";
        
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
                  return $this->render('KbhGestionCongesBundle:Admin\Salarie:new.html.twig', array(
                        'entity' => $entity,
                        'droits' => $droits,
                        'erreur'  => $erreur,
                        'user' => $user,
                        'form'   => $form->createView(),
                    ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:new.html.twig', array(
                        'entity' => $entity,
                        'droits' => $droits,
                        'erreur'  => $erreur,
                        'user' => $user,
                        'form'   => $form->createView(),
                    ));
            }
            
    }

    /**
     * Creates a form to create a Salarie entity.
     *
     * @param Salarie $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Salarie $entity)
    {
        $form = $this->createForm(new SalarieType(), $entity);
        $form->add('user', new UserType());
        return $form;
    }
    
        /**
     * Creates a form to create a Salarie entity.
     *
     * @param Salarie $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateSupAdminForm(Salarie $entity)
    {
        $form = $this->createForm(new SalarieType(), $entity);
        $form->add('user', new UserSupAdminType());
        return $form;
    }

    /**
     * Displays a form to create a new Salarie entity.
     *
     */
    public function newAction()
    {
        $entity = new Salarie();
        $erreur ="";
        $form   = $this->createCreateForm($entity);
        // form superAdmin
        $form2   = $this->createCreateSupAdminForm($entity);
        
         $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Admin\Salarie:new.html.twig', array(
                       'entity' => $entity,
                       'erreur'      => $erreur,
                       'form'   => $form->createView(),
                   ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:new.html.twig', array(
                       'entity' => $entity,
                       'erreur'      => $erreur,
                       'form'   => $form2->createView(),
                   ));
            }
            
    }  
    
    /**
     * Finds and displays a Salarie entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);
        $droits = $entity->getDroits();

        $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
               return $this->render('KbhGestionCongesBundle:Admin\Salarie:show.html.twig', array(
                    'entity'    => $entity,
                    'droits'    => $droits,
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
               return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:show.html.twig', array(
                    'entity'    => $entity,
                    'droits'    => $droits,
                ));
            }
            
    }
    
      /**
     * Finds and displays a Salarie entity.
     *
     */
    public function activeCompteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);
        $user = $entity->getUser();
        
        $entity->setStatutEmploi('Actif');
        $user->setEnabled(1);
        
        $em->persist($entity);
        $em->persist($user);
        $em->flush();
        
        //Log de l'action éffectuée
        $salarie = $this->getSalarieByUser();
        $cible = "salariés";
        $action = "Activation du compte";
        $msg = $salarie->getCivilite()." ".$salarie->getNomprenom()." à activé le compte du salarié ".$entity->getNomprenom();
        $this->logActivite($salarie, $cible, $action, $msg);
        
         $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('ad_salarie_show', array('id' => $id)));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
              return $this->redirect($this->generateUrl('sup_ad_salarie_show', array('id' => $id)));
            }
    }
    
      /**
     * Finds and displays a Salarie entity.
     *
     */
    public function desactiveCompteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);
        $user = $entity->getUser();
        
        $entity->setStatutEmploi('Inactif');
        $user->setEnabled(0);
        
        $em->persist($entity);
        $em->persist($user);
        $em->flush();
        
        //Log de l'action éffectuée
        $salarie = $this->getSalarieByUser();
        $cible = "salariés";
        $action = "Désactivation du compte";
        $msg = $salarie->getCivilite()." ".$salarie->getNomprenom()." à désactivé le compte du salarié ".$entity->getNomprenom();
        $this->logActivite($salarie, $cible, $action, $msg);
        
        $user_connected = $this->getUser();
          if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
            return $this->redirect($this->generateUrl('ad_salarie_show', array('id' => $id)));
          }
          if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
            return $this->redirect($this->generateUrl('sup_ad_salarie_show', array('id' => $id)));
          }
        
    }

    /**
     * Displays a form to edit an existing Salarie entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);
        $erreur = "";

        $editForm = $this->createEditForm($entity);
       
         $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Admin\Salarie:edit.html.twig', array(
                      'entity'      => $entity,
                      'erreur'      => $erreur,
                      'form'   => $editForm->createView(),
                  ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:edit.html.twig', array(
                      'entity'      => $entity,
                      'erreur'      => $erreur,
                      'form'   => $editForm->createView(),
                  ));
            }
           
    }

    /**
    * Creates a form to edit a Salarie entity.
    *
    * @param Salarie $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Salarie $entity)
    {
        $form = $this->createForm(new SalarieType(), $entity);
//        $form->add('user', new UserEditType());

        return $form;
    }
    
    /**
     * Edits an existing Salarie entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setNomprenom();
            $em->persist($entity);
            $em->flush();
            
            //Log de l'action éffectuée
            $salarie = $this->getSalarieByUser();
            $cible = "salariés";
            $action = "Modification du profil d'un salarié";
            $msg = $salarie->getCivilite()." ".$salarie->getNomprenom()." à modifié le profil du salarié ".$entity->getNomprenom();
            $this->logActivite($salarie, $cible, $action, $msg);
            
            $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
            return $this->redirect($this->generateUrl('ad_salarie_show', array('id' => $id)));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->redirect($this->generateUrl('sup_ad_salarie_show', array('id' => $id)));
            }
        }
        
        $erreur = "Erreur de saisie";
        
         $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Admin\Salarie:edit.html.twig', array(
                    'entity'      => $entity,
                    'erreur'      => $erreur,
                    'edit_form'   => $editForm->createView(),
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:edit.html.twig', array(
                        'entity'      => $entity,
                        'erreur'      => $erreur,
                        'edit_form'   => $editForm->createView(),
                    ));
            }
       
    }

/**************************** UPDATE USER ENTITY OF SALARIE **************************/
    /**
     * Displays a form to edit an existing Salarie entity.
     *
     */
    public function editUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhUserBundle:User')->find($id);
        $erreur = "";
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($id);
        
        $editForm = $this->createEditUserForm($entity);
       
        $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Admin\Salarie:edit-user.html.twig', array(
                    'entity'      => $entity,
                    'erreur'      => $erreur,
                    'salarie'  => $salarie,
                    'form'   => $editForm->createView(),
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:edit-user.html.twig', array(
                        'entity'      => $entity,
                        'erreur'      => $erreur,
                        'salarie'  => $salarie,
                        'form'   => $editForm->createView(),
                    ));
            }
    }
    
     /**
     * Displays a form to edit an existing Salarie entity.
     *
     */
    public function newRoleAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhUserBundle:User')->find($id);
        $erreur = "";
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($id);
        
        
       $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Admin\Salarie:add-role.html.twig', array(
                        'entity'      => $entity,
                        'erreur'      => $erreur,
                        'salarie'  => $salarie,
                    ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:add-role.html.twig', array(
                        'entity'      => $entity,
                        'erreur'      => $erreur,
                        'salarie'  => $salarie,
                    ));
            }
       
    }

        /**
     * Edits an existing Salarie entity.
     *
     */
    public function addRoleAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhUserBundle:User')->find($id);
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($id);
        
        //Récupération du contenu du formulaire
        $role = $_REQUEST['role'];
        
        //hydratation de l'entité
        $entity->addRole($role);
        $em->persist($entity);
        $em->flush();
        
        //Log de l'action éffectuée
        $salarieConnecte = $this->getSalarieByUser();
        $cible = "salariés";
        $action = "Ajout d'un nouveau rôle";
        $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à ajouté un nouveau rôle au salarié ".$salarie->getNomprenom();
        $this->logActivite($salarieConnecte, $cible, $action, $msg);
        
        $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
             return $this->redirect($this->generateUrl('ad_salarie_show', array('id' => $salarie->getId())));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->redirect($this->generateUrl('sup_ad_salarie_show', array('id' => $salarie->getId())));
            }

    }    
    
         /**
     * Displays a form to edit an existing Salarie entity.
     *
     */
    public function deleteRoleAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhUserBundle:User')->find($id);
        $roles = $entity->getRoles();
        $erreur = "";
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($id);
        
        $user_connected = $this->getUser();
        if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
           return $this->render('KbhGestionCongesBundle:Admin\Salarie:delete-role.html.twig', array(
                'entity'      => $entity,
                'roles'  => $roles,
                'erreur'      => $erreur,
                'salarie'  => $salarie,
            ));
        }
        if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:delete-role.html.twig', array(
                    'entity'      => $entity,
                    'roles'  => $roles,
                    'erreur'      => $erreur,
                    'salarie'  => $salarie,
                ));
        }
            
    }

        /**
     * Edits an existing Salarie entity.
     *
     */
    public function removeRoleAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhUserBundle:User')->find($id);
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($id);
        
        //Récupération du contenu du formulaire
        $role = $_REQUEST['role'];
        
        //hydratation de l'entité
        $entity->removeRole($role);
        $em->persist($entity);
        $em->flush();
        
        //Log de l'action éffectuée
        $salarieConnecte = $this->getSalarieByUser();
        $cible = "salariés";
        $action = "Suppression d'un rôle";
        $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à supprimé le rôle du salarié ".$salarie->getNomprenom();
        $this->logActivite($salarieConnecte, $cible, $action, $msg);
        
        $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
             return $this->redirect($this->generateUrl('ad_salarie_show', array('id' => $salarie->getId())));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->redirect($this->generateUrl('sup_ad_salarie_show', array('id' => $salarie->getId())));
            }

    }    
    
    /**
    * Creates a form to edit a Salarie entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditUserForm(User $entity)
    {
        $form = $this->createForm(new UserEditType(), $entity);
        return $form;
    }
    
    /**
     * Edits an existing Salarie entity.
     *
     */
    public function updateUserAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhUserBundle:User')->find($id);
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);

        
        $editForm = $this->createEditUserForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setEmailCanonical($entity->getEmail());
            $salarie->setEmail($entity->getEmail());

            $em->persist($entity);
            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "salariés";
            $action = "Modification paramètres d'accès";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifié les paramètres d'accès du salarié ".$salarie->getNomprenom();
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
             return $this->render('KbhGestionCongesBundle:Admin\Salarie:edit-user.html.twig', array(
                        'entity'      => $entity,
                        'erreur'      => $erreur,
                        'salarie'  => $salarie,
                        'form'   => $editForm->createView(),
                    ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:edit-user.html.twig', array(
                        'entity'      => $entity,
                        'erreur'      => $erreur,
                        'salarie'  => $salarie,
                        'form'   => $editForm->createView(),
                    ));
            }
        
    }
    


/**************************** UPDATE USER-PASS OF SALARIE **************************/
    /**
     * Displays a form to edit an existing Salarie entity.
     *
     */
    public function editUserPassAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhUserBundle:User')->find($id);
        $erreur = "";
        
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($id);

        $editForm = $this->createEditUserPassForm($entity);
       
        $user_connected = $this->getUser();
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Admin\Salarie:edit-user-pass.html.twig', array(
                    'entity'      => $entity,
                    'erreur'      => $erreur,
                    'salarie'  => $salarie,
                    'form'   => $editForm->createView(),
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:edit-user-pass.html.twig', array(
                        'entity'      => $entity,
                        'erreur'      => $erreur,
                        'salarie'  => $salarie,
                        'form'   => $editForm->createView(),
                    ));
            }
        
    }

    /**
    * Creates a form to edit a Salarie entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditUserPassForm(User $entity)
    {
        $form = $this->createForm(new UserEditPassType(), $entity);
        return $form;
    }
    
    /**
     * Edits an existing Salarie entity.
     *
     */
    public function updateUserPassAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhUserBundle:User')->find($id);
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($id);

        $editForm = $this->createEditUserPassForm($entity);
        $editForm->handleRequest($request);
        
        //Récupération du nouveau mot de passe
        $password = $entity->getPlainPassword();
        if ($editForm->isValid()) {
            
            $entity->setPlainPassword($password);
            $entity->getSalt();
//            var_dump(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
//            var_dump($entity->unserialize($entity->serialize()));
//            die();
//            $em->persist($entity);
//            $em->flush();
            
            //Log de l'action éffectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "salariés";
            $action = "Modification du mot de passe";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifié le mot de passe du salarié ".$salarie->getNomprenom();
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
              return $this->render('KbhGestionCongesBundle:Admin\Salarie:edit-user-pass.html.twig', array(
                    'entity'      => $entity,
                    'erreur'      => $erreur,
                     'salarie'  => $salarie,
                    'form'   => $editForm->createView(),
                ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:edit-user-pass.html.twig', array(
                    'entity'      => $entity,
                    'erreur'      => $erreur,
                     'salarie'  => $salarie,
                    'form'   => $editForm->createView(),
                ));
            }
            
    }

    
    
    
    /******************************** SUPERVISEUR *******************************/
    /**
     * Liste de tous les supervisés.
     *
     */
    public function collabsListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $superviseur = $this->getSalarieByUser();
        $droits_superviseur = $superviseur->getDroits();

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
            
            //Cas spéciaux
            $listeSalaries = "";
            $retire = array(); // les compte admin et super admin
            $e = 0;
            if (in_array("ROLE_TOP_MANAGER", $superviseur->getUser()->getRoles())) {
            
            $listeSalaries = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();  
            
            // Récupération des administrateurs système et des salariés désactivés
                foreach ($listeSalaries as $salarie){
                   if ( in_array("ROLE_SUPER_ADMIN", $salarie->getUser()->getRoles()) == true || in_array("ROLE_ADMIN", $salarie->getUser()->getRoles()) == true ) {
                         $retire[$e] = $salarie;
                         $e += 1;
                     }
                 }
            }            
            
            $user = $this->getUser();
            if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
               return $this->render('KbhGestionCongesBundle:Superviseur\Supervises:collaborateurs.html.twig', array(
                       'collabs' => $salaries,
                       'collabs_liste' => $listeSalaries,
                       'salarie_connect' => $superviseur,
                       'droits'  => $droits_superviseur,
                       'retire'  => $retire,
                     ));
               }
             if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
                   return $this->render('KbhGestionCongesBundle:Top-manager\Supervises:collaborateurs.html.twig', array(
                       'collabs' => $salaries,
                       'collabs_liste' => $listeSalaries,
                       'salarie_connect' => $superviseur,
                       'droits'  => $droits_superviseur,
                       'retire'  => $retire,
                     ));
               }
            
//        }

    }
    
    /**
     * Liste de tous les employés.
     *
     */
    public function collabsAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $superviseur = $this->getSalarieByUser();
        $droits_superviseur = $this->getSalarieByUser()->getDroits();

        $entities = $em->getRepository('KbhGestionCongesBundle:Salarie')->findAll();

        return $this->render('KbhGestionCongesBundle:ValideurFinal\Supervises:collaborateurs.html.twig', array(
            'entities' => $entities,
            'salarie' => $superviseur,
            'droits'  => $droits_superviseur,
                
        ));
    }
    
    /**
     * Fiche d'un supervisé.
     *
     */
    public function collabShowAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $superviseur = $this->getSalarieByUser();
        $droits_superviseur = $superviseur->getDroits();
        
        $entity = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Salarie entity.');
        }
        $droits_collaborateur = $entity->getDroits();
        
          $user = $this->getUser();
         if (in_array("ROLE_SUPERVISEUR", $user->getRoles())) {
            return $this->render('KbhGestionCongesBundle:Superviseur\Supervises:collaborateur-fiche.html.twig', array(
               'collaborateur'  => $entity,
               'droitsCollab' => $droits_collaborateur,
               'salarie' => $superviseur,
               'droits'  => $droits_superviseur,
           ));
        }
        if (in_array("ROLE_TOP_MANAGER", $user->getRoles())) {
             return $this->render('KbhGestionCongesBundle:Top-manager\Supervises:collaborateur-fiche.html.twig', array(
                'collaborateur'  => $entity,
                'droitsCollab' => $droits_collaborateur,
                'salarie' => $superviseur,
                'droits'  => $droits_superviseur,
            ));
        }
        
    }
    
     /**
     * Finds and displays a Salarie entity.
     *
     */
    public function superviseurShowAction()
    {
        $salarie = $this->getSalarieByUser();
        
        return $this->render('KbhGestionCongesBundle:Superviseur\Salarie:show.html.twig', array(
            'salarie' => $salarie,
        
        ));
    }
    
    /******************************* BASE DE CALCUL DES DROITS ********************************************/
    
    /**
     * Displays a form to edit an existing Salarie entity.
     *
     */
    public function editBaseCalculDroitsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);
        $erreur = "";
        $user_connected = $this->getUser();
        $baseDroits = $em->getRepository('KbhGestionCongesBundle:BaseDroits')->findOneBySalarie($id);

        if(count($baseDroits) == 0){
            $baseDroits = new \Kbh\GestionCongesBundle\Entity\BaseDroits();
            $newForm = $this->baseCalculDroitsForm($baseDroits);
            
             if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Admin\Salarie:new-base-droits.html.twig', array(
                      'entity'      => $baseDroits,
                      'salarie'      => $salarie,
                      'erreur'      => $erreur,
                      'form'   => $newForm->createView(),
                  ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:new-base-droits.html.twig', array(
                      'entity'      => $baseDroits,
                      'salarie'      => $salarie,
                      'erreur'      => $erreur,
                      'form'   => $newForm->createView(),
                  ));
            }
            
        }
        else {
             $editForm = $this->baseCalculDroitsForm($baseDroits);
             
              if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
              return $this->render('KbhGestionCongesBundle:Admin\Salarie:edit-base-droits.html.twig', array(
                      'entity'      => $baseDroits,
                      'salarie'      => $salarie,
                      'erreur'      => $erreur,
                      'form'   => $editForm->createView(),
                  ));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                 return $this->render('KbhGestionCongesBundle:Super-Admin\Salarie:edit-base-droits.html.twig', array(
                      'entity'      => $baseDroits,
                      'salarie'      => $salarie,
                      'erreur'      => $erreur,
                      'form'   => $editForm->createView(),
                  ));
            }
        }
       
    }

    /**
    * Creates a form to edit a Salarie entity.
    *
    * @param BaseDroits $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function baseCalculDroitsForm(BaseDroits $entity)
    {
        $form = $this->createForm(new BaseDroitsType(), $entity);
        return $form;
    }
    
     /**
     * Creates a new Salarie entity.
     *
     */
    public function baseCalculDroitsCreateAction(Request $request, $id)
    {
        $entity = new BaseDroits();
        $em = $this->getDoctrine()->getManager();
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);
        $paramCalcul = $em->getRepository('KbhGestionCongesBundle:Paramcalculsdroits')->find(1);
        
        $form = $this->baseCalculDroitsForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
           // 1- Vérifions si c'est une femme
            if($salarie->getCivilite() != "Mr" ){
                //vérifions son âge
                    $today = time();
                    $annif = $salarie->getDateNaissance()->getTimestamp();
                    $delta = abs($today - $annif);
                    $age = round(($delta/(60*60*24))/365.5);
                    
                      // cas inferieur à 21 ans
                        if($age < 21){
                            $entity->setEstFemmedeMoinsDe21ans(true);
                            $enfants = $entity->getNbEnfantsMineursAcharge();
                            
                            // Enfants à charges      
                            for($i = 0; $i < $enfants; $i++){
                                $entity->setJoursSupAnnuel($paramCalcul->getJoursSupFemmeParEnfantMineur());
                            }
                        }
                      // cas supérieur à 21 ans
                       if($age > 21){
                            $entity->setEstFemmedeMoinsDe21ans(true);
                            $enfants = $entity->getNbEnfantsMineursAcharge();
                            // Enfants à charges      
                            if ($enfants >= 4){
                                for($i = 4; $i <= $enfants; $i++){
                                  $entity->setJoursSupAnnuel($paramCalcul->getJoursSupFemmeParEnfantMineur());
                                }
                            }
                        }
                               
                  }
              
          // 2- Vérifions l'anciènneté du salarié
              $anciennete = date('Y') - $salarie->getDateEmbauche()->format('Y');
              $entity->setAnciennete($anciennete); 
                      
                if($anciennete == 5){
                    $entity->setAPlusDe5ansAnciennete(true);
                      $entity->setJoursSupAnnuel($paramCalcul->getJourssupanciennete5ans());
                    }
                if($anciennete == 10){
                    $entity->setAPlusDe10ansAnciennete(true);
                     $entity->setJoursSupAnnuel($paramCalcul->getJourssupanciennete10ans());
                 } 
                if($anciennete == 15){
                    $entity->setAPlusDe15ansAnciennete(true);
                     $entity->setJoursSupAnnuel($paramCalcul->getJourssupanciennete15ans());
                 } 
                if($anciennete == 20){
                    $entity->setAPlusDe20ansAnciennete(true);
                     $entity->setJoursSupAnnuel($paramCalcul->getJourssupanciennete20ans());
                 } 
                 if($anciennete == 25){
                    $entity->setAPlusDe25ansAnciennete(true);
                     $entity->setJoursSupAnnuel($paramCalcul->getJourssupanciennete25ans());
                 } 
                 
             // 3- Médaille d'honneur
                 if($entity->getAMedailleHonneurTravail() == true){
                     $entity->setJoursSupAnnuel($paramCalcul->getJourssupmedaillehonneur());
                 }
             
           // 4- Logé dans l'entreprise
                 if($entity->getEstLogeDansEntreprise() == true){
                     $entity->setJoursSupAnnuel($paramCalcul->getJoursSupAstreinte());
                 }      
                 
          // 5- Expatrié séjour 1
                 if($entity->getEstExpatrieSejour1() == true){
                     $entity->setJoursSupAnnuel($paramCalcul->getDroitsExpatSejour1());
                 }    
         
          // 6- Expatrié séjour base
                 if($entity->getEstExpatrieSejourSuivant() == true){
                     $entity->setJoursSupAnnuel($paramCalcul->getDroitsExpatBase());
                 }
                 
         // 7- Expatrié séjour 1
                 if($entity->getTravailleAuMoins50hsemaine() == true){
                     $entity->setJoursSupAnnuel($paramCalcul->getJoursSupPr200hmois());
                 }        
                 
            $entity->setSalarie($salarie);
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "salariés";
            $action = "Création des bases de calcul des droits";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à crée une nouvelle base de calcul pour les droits supplémentaire du salarié ".$salarie->getNomprenom();
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            return $this->redirect($this->generateUrl('ad_salarie_show',array('id'=>$id)));
        }
    return $this->redirect($this->generateUrl('ad_baseDroitsSalarie_edit',array('id'=>$id)));    
    }    
    
    /**
     * Edits an existing Salarie entity.
     *
     */
    public function updateBaseCalculDroitsAction(Request $request, $id, $bd)
    {
        $em = $this->getDoctrine()->getManager();
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->find($id);
        $paramCalcul = $em->getRepository('KbhGestionCongesBundle:Paramcalculsdroits')->find(1);

        $entity = $em->getRepository('KbhGestionCongesBundle:BaseDroits')->find($bd);

        $editForm = $this->baseCalculDroitsForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
          
            //initialisation
             $jours = $entity->getJoursSupAnnuel();
             $entity->setJoursSupAnnuel(-$jours);
             
            // 1- Vérifions si c'est une femme
            if($salarie->getCivilite() != "Mr" ){
                //vérifions son âge
                    $today = time();
                    $annif = $salarie->getDateNaissance()->getTimestamp();
                    $delta = abs($today - $annif);
                    $age = round(($delta/(60*60*24))/365.5);
                    
                      // cas inferieur à 21 ans
                        if($age < 21){
                            $entity->setEstFemmedeMoinsDe21ans(true);
                            $enfants = $entity->getNbEnfantsMineursAcharge();
                            
                            // Enfants à charges      
                            for($i = 0; $i < $enfants; $i++){
                                $entity->setJoursSupAnnuel($paramCalcul->getJoursSupFemmeParEnfantMineur());
                            }
                        }
                      // cas supérieur à 21 ans
                       if($age > 21){
                            $entity->setEstFemmedeMoinsDe21ans(true);
                            $enfants = $entity->getNbEnfantsMineursAcharge();
                            // Enfants à charges      
                            if ($enfants >= 4){
                                for($i = 4; $i <= $enfants; $i++){
                                  $entity->setJoursSupAnnuel($paramCalcul->getJoursSupFemmeParEnfantMineur());
                                }
                            }
                        }
                               
                  }
              
          // 2- Vérifions l'anciènneté du salarié
              $anciennete = date('Y') - $salarie->getDateEmbauche()->format('Y');
              $entity->setAnciennete($anciennete); 
                      
                if($anciennete == 5){
                    $entity->setAPlusDe5ansAnciennete(1);
                      $entity->setJoursSupAnnuel($paramCalcul->getAPlusDe5ansAnciennete());
                    }
                if($anciennete == 10){
                    $entity->setAPlusDe10ansAnciennete(1);
                     $entity->setJoursSupAnnuel($paramCalcul->getAPlusDe10ansAnciennete());
                 }
                if($anciennete == 15){
                    $entity->setAPlusDe15ansAnciennete(1);
                     $entity->setJoursSupAnnuel($paramCalcul->getAPlusDe15ansAnciennete());
                 }  
                if($anciennete == 20){
                    $entity->setAPlusDe20ansAnciennete(1);
                     $entity->setJoursSupAnnuel($paramCalcul->getAPlusDe20ansAnciennete());
                 }
                 if($anciennete == 25){
                    $entity->setAPlusDe25ansAnciennete(1);
                     $entity->setJoursSupAnnuel($paramCalcul->getAPlusDe25ansAnciennete());
                 }
                 
             // 3- Médaille d'honneur
                 if($entity->getAMedailleHonneurTravail() == 1){
                     $entity->setJoursSupAnnuel($paramCalcul->getJoursSupMedailleHonneur());
                 }
             
           // 4- Logé dans l'entreprise
                 if($entity->getEstLogeDansEntreprise() == 1){
                     $entity->setJoursSupAnnuel($paramCalcul->getJoursSupAstreinte());
                 }
                 
          // 5- Expatrié séjour 1
                 if($entity->getEstExpatrieSejour1() == 1){
                     $entity->setJoursSupAnnuel($paramCalcul->getDroitsExpatSejour1());
                 }   
         
          // 6- Expatrié séjour base
                 if($entity->getEstExpatrieSejourSuivant() == 1){
                     $entity->setJoursSupAnnuel($paramCalcul->getDroitsExpatBase());
                 }
                 
         // 7- Expatrié séjour 1
                 if($entity->getTravailleAuMoins50hsemaine() == 1){
                     $entity->setJoursSupAnnuel($paramCalcul->getJoursSupPr200hmois());
                 }       
          
                 
            $entity->setSalarie($salarie);
            $em->persist($entity);
            $em->flush();
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "salariés";
            $action = "Modification des bases de calcul des droits";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifié la base de calcul pour les droits supplémentaire du salarié ".$salarie->getNomprenom();
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            return $this->redirect($this->generateUrl('ad_salarie_show',array('id'=>$id)));
        }   
       
    }
    
    /**************************** SALARIE *******************************/
     /**
     * Finds and displays a Salarie entity.
     *
     */
    public function salarieShowAction()
    {
        $salarie = $this->getSalarieByUser();
        
        return $this->render('KbhGestionCongesBundle:Salarie\Salarie:show.html.twig', array(
            'salarie' => $salarie,
        
        ));
    }

     public function getSalarieByUser(){
        $user = $this->container->get('security.context')->getToken()->getUser();
        if(!$user){
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





