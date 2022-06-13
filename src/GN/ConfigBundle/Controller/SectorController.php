<?php
namespace GN\ConfigBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use GN\ConfigBundle\Entity\Sector;
use GN\ConfigBundle\Form\SectorType;
/**
 * Sector controller.
 */
class SectorController extends Controller
{
    private $dias = array('1'=>'Lunes', '2'=>'Martes', '3'=> 'Miercoles',
        '4'=>'Jueves','5'=>'Viernes','6'=>'Sábado','0'=>'Domingo');

    /**
     * Lists all entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ConfigBundle:Sector')->findAll();
        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
        }
        return $this->render('ConfigBundle:Sector:index.html.twig', array(
            'entities' => $entities,
            'deleteForms' => $deleteForms,
        ));
    }
    /**
     * Creates a new entity.
     */
    public function createAction(Request $request)
    {
        $entity = new Sector();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $datos = $request->get('gn_configbundle_sector');
            $new = $datos['andnew'];
            $diaslab = $request->get('dias');
            $entity->setDiasLaborables(json_encode($diaslab));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            if($new=='true'){
                $this->get('session')->getFlashBag()->add('info', 'El sector se ha creado correctamente. Ya puede agregar un nuevo.');
                return $this->redirect($this->generateUrl('parametro_sector_new'));
            }else{
                $this->get('session')->getFlashBag()->add('success', 'El sector se ha creado correctamente.');
                return $this->redirect($this->generateUrl('parametro_sector'));
            }
        }

        return $this->render('ConfigBundle:Sector:edit.html.twig', array(
            'entity' => $entity,
            'dias' => $this->dias,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create entity.
     * @param Sector $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Sector $entity)
    {
        $form = $this->createForm(new SectorType(), $entity, array(
            'action' => $this->generateUrl('parametro_sector_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new entity.
     */
    public function newAction()
    {
        $entity = new Sector();
        $diaslab = Array('1','2','3','4','5','6','0');
        $entity->setDiasLaborables(json_encode($diaslab) );
        $form   = $this->createCreateForm($entity);

        return $this->render('ConfigBundle:Sector:edit.html.twig', array(
            'dias' => $this->dias,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays entity.
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
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ConfigBundle:Sector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ConfigBundle:Sector:edit.html.twig', array(
            'entity'      => $entity,
            'dias' => $this->dias,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit entity.
    *
    * @param Sector $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Sector $entity)
    {
        $form = $this->createForm(new SectorType(), $entity, array(
            'action' => $this->generateUrl('parametro_sector_update', array('id' => $entity->getId())),
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

        $entity = $em->getRepository('ConfigBundle:Sector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }
        $diaslab = $request->get('dias');
        $entity->setDiasLaborables(json_encode($diaslab));

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $datos = $request->get('gn_configbundle_sector');
            $new = $datos['andnew'];

            $em->flush();

            if($new=='true'){
                $this->get('session')->getFlashBag()->add('info', 'Los datos se han modificado correctamente. Ya puede agregar un nuevo sector.');
                return $this->redirect($this->generateUrl('parametro_sector_new'));
            }else{
                $this->get('session')->getFlashBag()->add('success', 'Los datos se han modificado correctamente.');
                return $this->redirect($this->generateUrl('parametro_sector'));
            }
        }
        return $this->render('ConfigBundle:Sector:edit.html.twig', array(
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
            $entity = $em->getRepository('ConfigBundle:Sector')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Sector entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success','Eliminado con éxito!' );
        }

        return $this->redirect($this->generateUrl('parametro_sector'));
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
            ->setAction($this->generateUrl('parametro_sector_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
