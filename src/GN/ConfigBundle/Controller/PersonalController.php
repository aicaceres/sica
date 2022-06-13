<?php

namespace GN\ConfigBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use GN\ConfigBundle\Entity\Personal;
use GN\ConfigBundle\Form\PersonalType;

/**
 * Personal controller.
 *
 */
class PersonalController extends Controller
{

    /**
     * Lists all entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ConfigBundle:Personal')->findAll();
        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
        }
        return $this->render('ConfigBundle:Personal:index.html.twig', array(
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
        $entity = new Personal();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $datos = $request->get('gn_configbundle_personal');
            $new = $datos['andnew'];

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            if($new=='true'){
                $this->get('session')->getFlashBag()->add('info', 'El personal se ha creado correctamente. Ya puede agregar un nuevo.');
                return $this->redirect($this->generateUrl('parametro_personal_new'));
            }else{
                $this->get('session')->getFlashBag()->add('success', 'El personal se ha creado correctamente.');
                return $this->redirect($this->generateUrl('parametro_personal'));
            }
        }

        return $this->render('ConfigBundle:Personal:edit.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create an entity.
     *
     * @param Personal $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Personal $entity)
    {
        $form = $this->createForm(new PersonalType(), $entity, array(
            'action' => $this->generateUrl('parametro_personal_create'),
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
        $entity = new Personal();
        $form   = $this->createCreateForm($entity);

        return $this->render('ConfigBundle:Personal:edit.html.twig', array(
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

        $entity = $em->getRepository('ConfigBundle:Personal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Personal entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConfigBundle:Personal:show.html.twig', array(
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

        $entity = $em->getRepository('ConfigBundle:Personal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConfigBundle:Personal:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit an entity.
    *
    * @param Personal $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Personal $entity)
    {
        $form = $this->createForm(new PersonalType(), $entity, array(
            'action' => $this->generateUrl('parametro_personal_update', array('id' => $entity->getId())),
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

        $entity = $em->getRepository('ConfigBundle:Personal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $datos = $request->get('gn_configbundle_personal');
            $new = $datos['andnew'];

            $em->flush();

            if($new=='true'){
                $this->get('session')->getFlashBag()->add('info', 'Los datos se han modificado correctamente. Ya puede agregar un nuevo personal.');
                return $this->redirect($this->generateUrl('parametro_personal_new'));
            }else{
                $this->get('session')->getFlashBag()->add('success', 'Los datos se han modificado correctamente.');
                return $this->redirect($this->generateUrl('parametro_personal'));
            }
        }
        return $this->render('ConfigBundle:Personal:edit.html.twig', array(
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
            $entity = $em->getRepository('ConfigBundle:Personal')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Sector entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success','Eliminado con éxito!' );
        }

        return $this->redirect($this->generateUrl('parametro_personal'));
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
            ->setAction($this->generateUrl('parametro_personal_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


/* Suggest */
    public function getPersonalSuggestAction(Request $request){
        $term = $request->get('term');
        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository('ConfigBundle:Personal')->findAllByTerm($term);
        $array = array();
        if($items){
            foreach($items as $item){
                array_push($array,
                        array('id'=>$item['id'],'label'=> $item['nombre'], 'value'=>$item['nombre'] ));
            }
        }
        return new Response(json_encode($array));
    }
}
