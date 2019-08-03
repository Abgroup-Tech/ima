<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\SalaireMoyenJournalier;
use Kbh\GestionCongesBundle\Form\SalaireMoyenJournalierType;

/**
 * SalaireMoyenJournalier controller.
 *
 */
class SalaireMoyenJournalierController extends Controller
{

    /**
     * Lists all SalaireMoyenJournalier entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:SalaireMoyenJournalier')->findAll();

        return $this->render('KbhGestionCongesBundle:SalaireMoyenJournalier:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new SalaireMoyenJournalier entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new SalaireMoyenJournalier();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $entity->setMatricule($entity->getSalarie()->getMatricule());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('salairemoyenjournalier_show', array('id' => $entity->getId())));
        }

        return $this->render('KbhGestionCongesBundle:SalaireMoyenJournalier:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a SalaireMoyenJournalier entity.
     *
     * @param SalaireMoyenJournalier $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SalaireMoyenJournalier $entity)
    {
        $form = $this->createForm(new SalaireMoyenJournalierType(), $entity, array(
            'action' => $this->generateUrl('salairemoyenjournalier_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new SalaireMoyenJournalier entity.
     *
     */
    public function newAction()
    {
        $entity = new SalaireMoyenJournalier();
        $form   = $this->createCreateForm($entity);

        return $this->render('KbhGestionCongesBundle:SalaireMoyenJournalier:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SalaireMoyenJournalier entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:SalaireMoyenJournalier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalaireMoyenJournalier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:SalaireMoyenJournalier:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SalaireMoyenJournalier entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:SalaireMoyenJournalier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalaireMoyenJournalier entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:SalaireMoyenJournalier:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a SalaireMoyenJournalier entity.
    *
    * @param SalaireMoyenJournalier $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SalaireMoyenJournalier $entity)
    {
        $form = $this->createForm(new SalaireMoyenJournalierType(), $entity, array(
            'action' => $this->generateUrl('salairemoyenjournalier_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing SalaireMoyenJournalier entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:SalaireMoyenJournalier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SalaireMoyenJournalier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('salairemoyenjournalier_edit', array('id' => $id)));
        }

        return $this->render('KbhGestionCongesBundle:SalaireMoyenJournalier:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a SalaireMoyenJournalier entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:SalaireMoyenJournalier')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SalaireMoyenJournalier entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('salairemoyenjournalier'));
    }

    /**
     * Creates a form to delete a SalaireMoyenJournalier entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('salairemoyenjournalier_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
