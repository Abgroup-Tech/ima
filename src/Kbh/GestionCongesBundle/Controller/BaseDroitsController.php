<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\BaseDroits;
use Kbh\GestionCongesBundle\Form\BaseDroitsType;

/**
 * BaseDroits controller.
 *
 */
class BaseDroitsController extends Controller
{

    /**
     * Lists all BaseDroits entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:BaseDroits')->findAll();

        return $this->render('KbhGestionCongesBundle:BaseDroits:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new BaseDroits entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BaseDroits();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('basedroits_show', array('id' => $entity->getId())));
        }

        return $this->render('KbhGestionCongesBundle:BaseDroits:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BaseDroits entity.
     *
     * @param BaseDroits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BaseDroits $entity)
    {
        $form = $this->createForm(new BaseDroitsType(), $entity, array(
            'action' => $this->generateUrl('basedroits_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BaseDroits entity.
     *
     */
    public function newAction()
    {
        $entity = new BaseDroits();
        $form   = $this->createCreateForm($entity);

        return $this->render('KbhGestionCongesBundle:BaseDroits:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BaseDroits entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:BaseDroits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BaseDroits entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:BaseDroits:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BaseDroits entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:BaseDroits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BaseDroits entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:BaseDroits:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BaseDroits entity.
    *
    * @param BaseDroits $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BaseDroits $entity)
    {
        $form = $this->createForm(new BaseDroitsType(), $entity, array(
            'action' => $this->generateUrl('basedroits_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BaseDroits entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:BaseDroits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BaseDroits entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('basedroits_edit', array('id' => $id)));
        }

        return $this->render('KbhGestionCongesBundle:BaseDroits:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BaseDroits entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:BaseDroits')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BaseDroits entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('basedroits'));
    }

    /**
     * Creates a form to delete a BaseDroits entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('basedroits_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
