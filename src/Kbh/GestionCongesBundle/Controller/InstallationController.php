<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kbh\GestionCongesBundle\Entity\Document;
use Kbh\GestionCongesBundle\Form\ImportDocumentType;
use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Entity\Droits;
use Kbh\GestionCongesBundle\Form\SalarieType;
use Kbh\GestionCongesBundle\Form\UserSupAdminType;
use Kbh\GestionCongesBundle\Entity\Entreprise;
use Kbh\GestionCongesBundle\Form\EntrepriseType;
use Kbh\GestionCongesBundle\Entity\Paramcalculsdroits;
use Kbh\GestionCongesBundle\Form\ParamCalculsDroitsType;

/**
 * run a command on the controller.
 *
 */
class InstallationController extends Controller
{
    //########################## PARAMETRES ENTREPRISE #############################
    
    /**
     * Displays a form to create a new Salarie entity.
     *
     */
    public function bienvenueAction()
    {
        return $this->render('KbhGestionCongesBundle:Installation:welcome-new.html.twig');
    }
    
    /**
     * Displays a form to create a new Salarie entity.
     *
     */
    public function felicitationAction()
    {
        return $this->render('KbhGestionCongesBundle:Installation:felicitation.html.twig');
    }
    
        /**
     * Creates a form to create a Entreprise entity.
     *
     * @param Entreprise $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEntrepriseForm(Entreprise $entity)
    {
        $form = $this->createForm(new EntrepriseType(), $entity, array(
            'action' => $this->generateUrl('installation_entreprise_create'),
            'method' => 'POST',
        ));
        $form->add('docFeries', new ImportDocumentType());
        $form->add('docPermissions', new ImportDocumentType());
        return $form;
    }
    
    /**
     * Creates a form to create a Document entity.
     *
     * @param Document $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function importDocumentForm(Document $entity)
    {
        $form = $this->createForm(new ImportDocumentType(), $entity, array(
            'action' => $this->generateUrl('installation_entreprise_create'),
            'method' => 'POST',
        ));

        return $form;
    }
    
        /**
     * Displays a form to create a new Entreprise entity.
     *
     */
    public function newEntrepriseAction()
    {
        $entity = new Entreprise();
        $form   = $this->createEntrepriseForm($entity);

        return $this->render('KbhGestionCongesBundle:Installation:new-entreprise.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Entreprise entity.
     *
     */
    public function createEntrepriseAction(Request $request)
    {
        $entity = new Entreprise();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createEntrepriseForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('installation_import_feries'));
        }

        return $this->redirect($this->generateUrl('installation_entreprise_new'));
    }
    
    //########################## CONFIGURATION SYSTEME ####################################
    
    /**
     * Finds and displays a Entreprise entity.
     *
     */
    public function newParamCalculDroitsAction()
    {
        //ParamPermissions et formulaire
        $new_config = new Paramcalculsdroits();
        $CalculDroitForm = $this->createNewConfigForm($new_config);

        return $this->render('KbhGestionCongesBundle:Installation:new-param-calcul-droits.html.twig', array(
           'entity'                          => $new_config,
           'form'  => $CalculDroitForm->createView(),
       ));
            
    }
    
    /**
     * Creates a form to create a ParamCalculsDroits entity.
     *
     * @param Paramcalculsdroits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createNewConfigForm(Paramcalculsdroits $entity)
    {
        $form = $this->createForm(new ParamCalculsDroitsType(), $entity, array(
            'action' => $this->generateUrl('installation_paramcalculdroits_create'),
            'method' => 'POST',
        ));

//        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
    
    
    /**
     * Creates a new ParamCalculs entity.
     *
     */
    public function createParamCalculDroitsAction(Request $request)
    {
        $entity = new Paramcalculsdroits();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createNewConfigForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('installation_new_user'));
        }

        return $this->redirect($this->generateUrl('installation_paramcalculdroits_new'));
    }
    
    //########################### CREATION DU COMPTE ##############################
    
    /**
     * Displays a form to create a new Salarie entity.
     *
     */
    public function newUserCompteAction()
    {
        $entity = new Salarie();
        $form   = $this->createUserForm($entity);
        $erreur = "";

        return $this->render('KbhGestionCongesBundle:Installation:new-salarie.html.twig', array(
            'entity' => $entity,
            'erreur'      => $erreur,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Salarie entity.
     *
     * @param Salarie $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createUserForm(Salarie $entity)
    {
        $form = $this->createForm(new SalarieType(), $entity, array(
            'action' => $this->generateUrl('installation_user_create'),
            'method' => 'POST',
        ));
        
        $form->add('user', new UserSupAdminType());
        return $form;
    }
    
     /**
     * Creates a new Salarie entity.
     *
     */
    public function createNewUserAction(Request $request)
    {
        $entity = new Salarie();
        $droits = new Droits();
        
        $entity->setDroits($droits);
        $droits->setSalarie($entity);
        
        $form = $this->createUserForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            //Ajout du 1er mars 2015 - BhK            
//            $entity->setSuperviseur($entity->getUnite()->getManager());
            
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
//            $salarie = $this->getSalarieByUser();
            $cible = "installation";
            $action = "Installation et configuration de IMA";
            $msg = "Les configurations et le compte super admin à bien été crées.";
            $this->logActivite($entity, $cible, $action, $msg);

            return $this->redirect($this->generateUrl('installation_felicitaion'));
        }
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
            $logActivite->setIcon("icon-ban");
        }
        //3ème cas : cible concernant les arrêt de travail
        if($cible == "arrêt maladie"){
            $logActivite->setIcon("icon-heart");
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
        //8ème cas : cible concernant les absences 
        if($cible == "absence"){
            $logActivite->setIcon("icon-logout");
        }
        //9ème cas : cible concernant les congés 
        if($cible == "congé"){
            $logActivite->setIcon("icon-plane");
        }
        //10ème cas : cible concernant les demandes
        if($cible == "demande"){
            $logActivite->setIcon("icon-envelope-letter");
        }
        //10ème cas : cible concernant les pièces jointes
        if($cible == "pieces-jointes"){
            $logActivite->setIcon("icon-paper-clip");
        }
        //11ème cas : cible concernant les états
        if($cible == "etats"){
            $logActivite->setIcon("icon-graph");
        }
        //12ème cas : cible concernant les historiques de droits
        if($cible == "historique droits"){
            $logActivite->setIcon("icon-list");
        }
        //13ème cas : cible concernant les historiques de droits
        if($cible == "log activités"){
            $logActivite->setIcon("icon-equalizer");
        }
        //14ème cas : cible concernant les reports
        if($cible == "reports"){
            $logActivite->setIcon("icon-reload");
        }
        //15ème cas : cible concernant les reinitialisation
        if($cible == "reinitialisation"){
            $logActivite->setIcon("icon-power");
        }
        //16ème cas : cible concernant l'installation de ima
        if($cible == "installation"){
            $logActivite->setIcon("icon-social-dropbox");
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