<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\CalculAllocationConge;
use Kbh\GestionCongesBundle\Form\CalculAllocationCongeType;
use Kbh\GestionCongesBundle\Entity\SalaireMoyenJournalier;
use Kbh\GestionCongesBundle\Entity\Salarie;
use Kbh\GestionCongesBundle\Entity\Droits;

/**
 * CalculAllocationConge controller.
 *
 */
class CalculAllocationCongeController extends Controller
{

    /**
     * Lists all CalculAllocationConge entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        //Récupérer la liste des salariés et leur salaires journaliers
        $salairesmj = $em->getRepository('KbhGestionCongesBundle:SalaireMoyenJournalier')->findAll();
            
        $entities = $em->getRepository('KbhGestionCongesBundle:CalculAllocationConge')->findAll();

        return $this->render('KbhGestionCongesBundle:CalculAllocationConge:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new CalculAllocationConge entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CalculAllocationConge();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('calculallocationconge_show', array('id' => $entity->getId())));
        }

        return $this->render('KbhGestionCongesBundle:CalculAllocationConge:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CalculAllocationConge entity.
     *
     * @param CalculAllocationConge $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CalculAllocationConge $entity)
    {
        $form = $this->createForm(new CalculAllocationCongeType(), $entity, array(
            'action' => $this->generateUrl('calculallocationconge_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CalculAllocationConge entity.
     *
     */
    public function newAction()
    {
        //Récupérer la liste des salariés et leur salaires journaliers
            $em = $this->getDoctrine()->getManager();
            $dateEffet = new \DateTime();
            $compteurSMJ = 0;
            $compteurDL = 0;
            $compteurDR = 0;
            $compteurAC = 0;
            $effectif=0;
            
            $salariesmjs = $em->getRepository('KbhGestionCongesBundle:SalaireMoyenJournalier')->findAll();
            foreach ($salariesmjs as $salariemj){
                $calculAllocationConge = new CalculAllocationConge();
                
                $calculAllocationConge->setSalarie($salariemj->getSalarie());
                $calculAllocationConge->setMatricule($salariemj->getMatricule());
                $effectif +=1;
                
                $calculAllocationConge->setDateEffet(new \DateTime());
                $calculAllocationConge->setDateCalcul(new \DateTime());
                
                $calculAllocationConge->setSalaireMoyenJournalier($salariemj->getSalaireMoyenJournalier());
                $compteurSMJ +=$salariemj->getSalaireMoyenJournalier();
                
                //DroitsLégaux = totalDroitsAprendre
                $calculAllocationConge->setDroitsLegaux($salariemj->getSalarie()->getDroits()->getTotalDroitsAprendre());
                $compteurDL +=$calculAllocationConge->getDroitsLegaux();
                
                //DroitsRéels = DroitsLégaux + dimanches => choix arbitraire de 4 jours
                $calculAllocationConge->setDroitsReels($calculAllocationConge->getDroitsLegaux()+4);
                $compteurDR += $calculAllocationConge->getDroitsReels();
                
                $calculAllocationConge->setAllocationConge();
                $compteurAC += $calculAllocationConge->getAllocationConge();
                
                //$em->persist($calculAllocationConge);
                //$em->flush();
            }
            $em = $this->getDoctrine()->getManager();
            $calculsAllocationConges = $em->getRepository('KbhGestionCongesBundle:CalculAllocationConge')->findAll();

        return $this->render('KbhGestionCongesBundle:CalculAllocationConge:index.html.twig', array(
            'entities' => $calculsAllocationConges,
            'dateEffet'=>$dateEffet,
            'compteurSMJ'=>$compteurSMJ,
            'compteurDL'=>$compteurDL,
            'compteurDR'=>$compteurDR,
            'compteurAC'=>$compteurAC,
            'effectif'=>$effectif
            
        ));
    }

    /**
     * Finds and displays a CalculAllocationConge entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:CalculAllocationConge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CalculAllocationConge entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:CalculAllocationConge:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CalculAllocationConge entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:CalculAllocationConge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CalculAllocationConge entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:CalculAllocationConge:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CalculAllocationConge entity.
    *
    * @param CalculAllocationConge $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CalculAllocationConge $entity)
    {
        $form = $this->createForm(new CalculAllocationCongeType(), $entity, array(
            'action' => $this->generateUrl('calculallocationconge_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing CalculAllocationConge entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:CalculAllocationConge')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CalculAllocationConge entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('calculallocationconge_edit', array('id' => $id)));
        }

        return $this->render('KbhGestionCongesBundle:CalculAllocationConge:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CalculAllocationConge entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:CalculAllocationConge')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CalculAllocationConge entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('calculallocationconge'));
    }

    /**
     * Creates a form to delete a CalculAllocationConge entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('calculallocationconge_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
