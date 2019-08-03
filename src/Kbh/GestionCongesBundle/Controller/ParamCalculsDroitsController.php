<?php

namespace Kbh\GestionCongesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Kbh\GestionCongesBundle\Entity\ParamCalculsDroits;
use Kbh\GestionCongesBundle\Form\ParamCalculsDroitsType;

/**
 * ParamCalculsDroits controller.
 *
 */
class ParamCalculsDroitsController extends Controller
{

    /**
     * Lists all ParamCalculsDroits entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KbhGestionCongesBundle:ParamCalculsDroits')->findAll();

        return $this->render('KbhGestionCongesBundle:Admin\ParamCalculsDroits:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ParamCalculsDroits entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ParamCalculsDroits();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('paramcalculsdroits_show', array('id' => $entity->getId())));
        }

        return $this->render('KbhGestionCongesBundle:Admin\ParamCalculsDroits:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ParamCalculsDroits entity.
     *
     * @param ParamCalculsDroits $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ParamCalculsDroits $entity)
    {
        $form = $this->createForm(new ParamCalculsDroitsType(), $entity, array(
            'action' => $this->generateUrl('paramcalculsdroits_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ParamCalculsDroits entity.
     *
     */
    public function newAction()
    {
        $entity = new Paramcalculsdroits();
        $form   = $this->createCreateForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin\ParamCalculsDroits:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ParamCalculsDroits entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:ParamCalculsDroits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ParamCalculsDroits entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('KbhGestionCongesBundle:Admin\ParamCalculsDroits:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ParamCalculsDroits entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Paramcalculsdroits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ParamCalculsDroits entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('KbhGestionCongesBundle:Super-Admin\ParamCalculsDroits:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ParamCalculsDroits entity.
    *
    * @param ParamCalculsDroits $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ParamCalculsDroits $entity)
    {
        $form = $this->createForm(new ParamCalculsDroitsType(), $entity, array(
            'action' => $this->generateUrl('sup_ad_paramcalculsdroits_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing ParamCalculsDroits entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KbhGestionCongesBundle:Paramcalculsdroits')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ParamCalculsDroits entity.');
        }
        
        die('test');
        
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('sup_ad_paramcalculsdroits_edit', array('id' => $id)));
        }

        return $this->render('KbhGestionCongesBundle:Super-Admin\ParamCalculsDroits:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a ParamCalculsDroits entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KbhGestionCongesBundle:ParamCalculsDroits')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ParamCalculsDroits entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('paramcalculsdroits'));
    }

    /**
     * Creates a form to delete a ParamCalculsDroits entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('paramcalculsdroits_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
