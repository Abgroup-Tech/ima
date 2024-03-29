<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\Organigramme;
use Kbh\GestionCongesBundle\Form\OrganigrammeType;

/**
 * Organigramme controller.
 *
 */
class OrganigrammeController extends Controller
{

    /**
     * Lists all Organigramme entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:Organigramme')->findAll();

        return $this->render('KbhGestionCongesBundle:Admin\Organigramme:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Organigramme entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Organigramme();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('organigramme_show', array('id' => $entity->getId())));
        }

        return $this->render('KbhGestionCongesBundle:Admin\Organigramme:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Organigramme entity.
     *
     * @param Organigramme $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Organigramme $entity)
    {
        $form = $this->createForm(new OrganigrammeType(), $entity, array(
            'action' => $this->generateUrl('organigramme_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Organigramme entity.
     *
     */
    public function newAction()
    {
        $entity = new Organigramme();
        $form   = $this->createCreateForm($entity);

        return $this->render('KbhGestionCongesBundle:Admin\Organigramme:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Organigramme entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Organigramme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organigramme entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:Admin\Organigramme:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Organigramme entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Organigramme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organigramme entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:Admin\Organigramme:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Organigramme entity.
    *
    * @param Organigramme $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Organigramme $entity)
    {
        $form = $this->createForm(new OrganigrammeType(), $entity, array(
            'action' => $this->generateUrl('organigramme_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Organigramme entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Organigramme')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organigramme entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('organigramme_edit', array('id' => $id)));
        }

        return $this->render('KbhGestionCongesBundle:Admin\Organigramme:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Organigramme entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:Organigramme')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Organigramme entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('organigramme'));
    }

    /**
     * Creates a form to delete a Organigramme entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('organigramme_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
