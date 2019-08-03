<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
/**
 * run a command on the controller.
 *
 */
class RunCommandeController extends Controller
{

    public function runCommand($command, $arguments = array())
    {
        $kernel = $this->container->get('kernel');
        $app = new Application($kernel);

        $args = array_merge(array('command' => $command), $arguments);

        $input = new ArrayInput($args);
        $output = new NullOutput();

        return $app->doRun($input, $output);
    }

    public function cacheClearAction()
    {
        $this->runCommand('cache:clear');
        return $this->redirect($this->generateUrl('ad_historique_activities'));

    }
    
    public function changePasswordAction($id)
    {

        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhUserBundle:User')->find($id);
        $salarie = $em->getRepository('KbhGestionCongesBundle:Salarie')->findOneByUser($id); 
        $user_connected = $this->getUser();
        
        //Récupération des données du formulaire
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        $confirmation = $_REQUEST['confirmation'];
       
        if($password == $confirmation){
            $argument = array();
            $argument[0] = $username;
            $argument[1] = $password;
            $this->runCommand('fos:user:change-password', $arguments = array('username'=>$argument[0], 'password'=>$argument[1]));
            
            //Log action effectuée
            $salarieConnecte = $this->getSalarieByUser();
            $cible = "password";
            $action = "Modification d'un mot de passe";
            $msg = $salarieConnecte->getCivilite()." ".$salarieConnecte->getNomprenom()." à modifié le mot de passe du salarié ".$salarie->getNomprenom();
            $this->logActivite($salarieConnecte, $cible, $action, $msg);
            
            //Redirection au cas où le password est validé
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
               return $this->redirect($this->generateUrl('ad_salarie_show', array('id' => $salarie->getId())));
           }
           if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
               return $this->redirect($this->generateUrl('sup_ad_salarie_show', array('id' => $salarie->getId())));
           }
            
        }else{
            
            //Redirection au cas où le password n'est pas le même  
            if (in_array("ROLE_ADMIN", $user_connected->getRoles())) {
                return $this->redirect($this->generateUrl('ad_salarie_edit_user_pass', array('id' => $entity->getId())));
            }
            if (in_array("ROLE_SUPER_ADMIN", $user_connected->getRoles())) {
                return $this->redirect($this->generateUrl('sup_ad_salarie_edit_user_pass', array('id' => $entity->getId())));
            }
            
        }
        
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
        //7ème cas : cible concernant les documents importés
        if($cible == "password"){
            $logActivite->setIcon("icon-lock-open");
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