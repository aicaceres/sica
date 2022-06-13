<?php

namespace GN\ConfigBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use GN\ConfigBundle\Entity\Estado;
use GN\ConfigBundle\Form\EstadoType;

/**
 * Estado controller.
 *
 */
class EstadoController extends Controller
{

    /**
     * Lists all entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ConfigBundle:Estado')->findAll();
        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
        }
        return $this->render('ConfigBundle:Estado:index.html.twig', array(
            'entities' => $entities,
            'deleteForms' => $deleteForms,
        ));
    }
    /**
     * Creates a new entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Estado();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $datos = $request->get('gn_configbundle_estado');
            $new = $datos['andnew'];

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            if($new=='true'){
                $this->get('session')->getFlashBag()->add('info', 'El estado se ha creado correctamente. Ya puede agregar un nuevo.');
                return $this->redirect($this->generateUrl('parametro_estado_new'));
            }else{
                $this->get('session')->getFlashBag()->add('success', 'El estado se ha creado correctamente.');
                return $this->redirect($this->generateUrl('parametro_estado'));
            }
        }

        return $this->render('ConfigBundle:Estado:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create an entity.
     *
     * @param Estado $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Estado $entity)
    {
        $form = $this->createForm(new EstadoType(), $entity, array(
            'action' => $this->generateUrl('parametro_estado_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new entity.
     *
     */
    public function newAction()
    {
        $entity = new Estado();
        $em = $this->getDoctrine()->getManager();
        $count = count( $em->getRepository('ConfigBundle:Estado')->findAll() );
        $entity->setOrden($count+1);

        $form   = $this->createCreateForm($entity);

        return $this->render('ConfigBundle:Estado:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays an entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConfigBundle:Estado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Estado entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConfigBundle:Estado:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConfigBundle:Estado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConfigBundle:Estado:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit an entity.
    *
    * @param Estado $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Estado $entity)
    {
        $form = $this->createForm(new EstadoType(), $entity, array(
            'action' => $this->generateUrl('parametro_estado_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $form;
    }
    /**
     * Edits an existing entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConfigBundle:Estado')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $datos = $request->get('gn_configbundle_estado');
            $new = $datos['andnew'];

            $em->flush();

            if($new=='true'){
                $this->get('session')->getFlashBag()->add('info', 'Los datos se han modificado correctamente. Ya puede agregar un nuevo estado.');
                return $this->redirect($this->generateUrl('parametro_estado_new'));
            }else{
                $this->get('session')->getFlashBag()->add('success', 'Los datos se han modificado correctamente.');
                return $this->redirect($this->generateUrl('parametro_estado'));
            }
        }
        return $this->render('ConfigBundle:Estado:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes an entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ConfigBundle:Estado')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Sector entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success','Eliminado con éxito!' );
        }

        return $this->redirect($this->generateUrl('parametro_estado'));
    }

    /**
     * Creates a form to delete an entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parametro_estado_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
