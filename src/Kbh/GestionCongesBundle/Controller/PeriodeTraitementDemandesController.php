<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\PeriodeTraitementDemandes;
use Kbh\GestionCongesBundle\Form\PeriodeTraitementDemandesType;

/**
 * PeriodeTraitementDemandes controller.
 *
 */
class PeriodeTraitementDemandesController extends Controller
{

    /**
     * Lists all PeriodeTraitementDemandes entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->findAll();

        return $this->render('KbhGestionCongesBundle:PeriodeTraitementDemandes:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new PeriodeTraitementDemandes entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PeriodeTraitementDemandes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setStatut('En attente');
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ad_periodetraitementdemandes_new'));
        }
        $erreur="ERREUR lors de la saisie";
        
        return $this->render('KbhGestionCongesBundle:Admin/PeriodeTraitementDemandes:new.html.twig', array(
            'entity' => $entity,
             'erreur' => $erreur,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a PeriodeTraitementDemandes entity.
     *
     * @param PeriodeTraitementDemandes $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PeriodeTraitementDemandes $entity)
    {
        $form = $this->createForm(new PeriodeTraitementDemandesType(), $entity, array(
            'action' => $this->generateUrl('ad_periodetraitementdemandes_create'),
            'method' => 'POST',
        ));
        
        return $form;
    }

    /**
     * Displays a form to create a new PeriodeTraitementDemandes entity.
     *
     */
    public function newAction()
    {   
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);
        if(count($entity) == 0){
            $entity = new PeriodeTraitementDemandes();
            $form   = $this->createCreateForm($entity);
            $erreur="";

            return $this->render('KbhGestionCongesBundle:Admin/PeriodeTraitementDemandes:new.html.twig', array(
                'entity' => $entity,
                'erreur' => $erreur,
                'form'   => $form->createView(),
            ));
        } else {
            
            //calcul du nombre de jour 
            $debut = $entity->getDateDebut()->format('d-m-Y');
            $fin = $entity->getDateFin()->format('d-m-Y');
            
            //Conversion en timestamp
            $tms_debut = strtotime($debut);
            $tms_fin = strtotime($fin);
            $day = 60*60*24; // equivalent d'une journée en seconde
            
            $tms_delta = ($tms_fin - $tms_debut) ; //  rajouter +1 jr si la journée de la fin de période est inclus
            $nb_jour = round(($tms_delta/$day), 0);
            
            return $this->render('KbhGestionCongesBundle:Admin/PeriodeTraitementDemandes:show.html.twig', array(
                'entity' => $entity,
                'nb_jour' => $nb_jour,
            ));
        }
    }
    
           /**
     * Lists all PeriodeDepotDemandes entities.
     *
     */
    public function demarrageAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);
        $entity->setStatut('En cours');
        
         $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('ad_periodetraitementdemandes_new'));
    }
    
     /**
     * Lists all PeriodeDepotDemandes entities.
     *
     */
    public function clotureAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find(1);
        $entity->setStatut('Clôturé');
        
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('ad_periodetraitementdemandes_new'));
    }

    /**
     * Displays a form to edit an existing PeriodeTraitementDemandes entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find($id);


        $editForm = $this->createEditForm($entity);
         $erreur = "";
         
        return $this->render('KbhGestionCongesBundle:Admin/PeriodeTraitementDemandes:edit.html.twig', array(
            'entity'      => $entity,
             'erreur' => $erreur,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a PeriodeTraitementDemandes entity.
    *
    * @param PeriodeTraitementDemandes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PeriodeTraitementDemandes $entity)
    {
        $form = $this->createForm(new PeriodeTraitementDemandesType(), $entity, array(
            'action' => $this->generateUrl('ad_periodetraitementdemandes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing PeriodeTraitementDemandes entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find($id);

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
          $entity->setStatut('En attente');
            $em->flush();

            return $this->redirect($this->generateUrl('ad_periodetraitementdemandes_new'));
        }
        
        $erreur = "ERREUR lors de la saisie";
        
        return $this->render('KbhGestionCongesBundle:Admin/PeriodeTraitementDemandes:edit.html.twig', array(
            'entity'      => $entity,
            'erreur'   => $erreur,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a PeriodeTraitementDemandes entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:PeriodeTraitementDemandes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PeriodeTraitementDemandes entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('periodetraitementdemandes'));
    }

    /**
     * Creates a form to delete a PeriodeTraitementDemandes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('periodetraitementdemandes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
