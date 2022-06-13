<?php

namespace GN\ConfigBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;

use GN\ConfigBundle\Entity\SubSector;
use GN\ConfigBundle\Form\SubSectorType;

/**
 * SubSector controller.
 *
 */
class SubSectorController extends Controller
{

    /**
     * Lists all entities.
     *
     */
    public function indexAction(Request $request)
    {
        $session = $this->get('session');
        $sectorId =  $request->get('sector');
        if( empty($sectorId) ){
            $sessionSector = $session->get('sectorId');
            $sectorId = empty($sessionSector) ? 1 : $sessionSector;
        }
        $session->set('sectorId',$sectorId);

        $em = $this->getDoctrine()->getManager();
        $sectores = $em->getRepository('ConfigBundle:Sector')->findAll();
        $entities = $em->getRepository('ConfigBundle:SubSector')->findBySector( $sectorId );
        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
        }
        return $this->render('ConfigBundle:SubSector:index.html.twig', array(
            'entities' => $entities,
            'sectores' => $sectores,
            'sectorId' =>  $sectorId,
            'deleteForms' => $deleteForms,
        ));
    }
    /**
     * Creates a new entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new SubSector();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $datos = $request->get('gn_configbundle_subsector');
            $new = $datos['andnew'];

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            if($new=='true'){
                $this->get('session')->getFlashBag()->add('info', 'El sub-sector se ha creado correctamente. Ya puede agregar un nuevo.');
                return $this->redirect($this->generateUrl('parametro_subsector_new'));
            }else{
                $this->get('session')->getFlashBag()->add('success', 'El sub-sector se ha creado correctamente.');
                return $this->redirect($this->generateUrl('parametro_subsector'));
            }
        }

        return $this->render('ConfigBundle:SubSector:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create entity.
     *
     * @param SubSector $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SubSector $entity)
    {
        $form = $this->createForm(new SubSectorType(), $entity, array(
            'action' => $this->generateUrl('parametro_subsector_create'),
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
        $entity = new SubSector();
        $em = $this->getDoctrine()->getManager();
        $sector = $em->getRepository('ConfigBundle:Sector')->find( $this->get('session')->get('sectorId') );
        $entity->setSector($sector);
        $form   = $this->createCreateForm($entity);

        return $this->render('ConfigBundle:SubSector:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConfigBundle:Sector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConfigBundle:Sector:show.html.twig', array(
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

        $entity = $em->getRepository('ConfigBundle:SubSector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConfigBundle:SubSector:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit entity.
    *
    * @param SubSector $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SubSector $entity)
    {
        $form = $this->createForm(new SubSectorType(), $entity, array(
            'action' => $this->generateUrl('parametro_subsector_update', array('id' => $entity->getId())),
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

        $entity = $em->getRepository('ConfigBundle:SubSector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $originalTareas = new ArrayCollection();
        foreach ($entity->getTareas() as $item) {
            $originalTareas->add($item);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            foreach ($originalTareas as $item) {
                if (false === $entity->getTareas()->contains($item)) {
                     $em->remove($item);
                }
            }

            $datos = $request->get('gn_configbundle_subsector');
            $new = $datos['andnew'];

            $em->flush();

            if($new=='true'){
                $this->get('session')->getFlashBag()->add('info', 'Los datos se han modificado correctamente. Ya puede agregar un nuevo sub-sector.');
                return $this->redirect($this->generateUrl('parametro_subsector_new'));
            }else{
                $this->get('session')->getFlashBag()->add('success', 'Los datos se han modificado correctamente.');
                return $this->redirect($this->generateUrl('parametro_subsector'));
            }
        }
        return $this->render('ConfigBundle:SubSector:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ConfigBundle:SubSector')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Sector entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success','Eliminado con éxito!' );
        }

        return $this->redirect($this->generateUrl('parametro_subsector'));
    }

    /**
     * Creates a form to delete entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parametro_subsector_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
