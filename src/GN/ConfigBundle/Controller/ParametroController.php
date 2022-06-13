<?php
namespace GN\ConfigBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use GN\ConfigBundle\Entity\Nacionalidad;
use GN\ConfigBundle\Entity\TipoDocumento;
use GN\ConfigBundle\Form\ParametroType;

class ParametroController extends Controller
{
    private $tableDesc = array('Nacionalidad' => 'Nacionalidad','TipoDocumento' => 'Tipos de Documento');


    public function parametroAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ConfigBundle:Parametro')->find(1);

        return $this->render('ConfigBundle:Parametro:edit.html.twig', array(
            'entity' => $entity
        ));
    }

    public function saveAction($id) {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $request = $this->get('request');
            $fecha = $request->get('fechaInicio');
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ConfigBundle:Parametro')->find($id);
            $desde = $entity->getFechaInicio()->format('Y-m-d');
            $entity->setFechaInicio(new \DateTime($fecha) );
            $rango = array('desde'=>$desde,
                    'hasta'=> $entity->getFechaInicio()->format('Y-m-d'));
            $res = $em->getRepository('AdminBundle:Control')->deleteControlData($rango);

            $em->persist($entity);
            $em->flush();
            // actualizo el dato de fecha en sesion
            $session = $this->get('session');
            $session->set('initialDate', $entity->getFechaInicio()->format("Y-m-d"));

            return $this->redirect($this->generateUrl('admin_homepage', array()));
        }
    }





    public function indexAction($table)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ConfigBundle:'.$table)->findAll();
        $html = $this->renderView('ConfigBundle:Parametro:list.html.twig',
                array('entities' => $entities ,'table'=>$table )
        );
        return $this->render('ConfigBundle:Parametro:index.html.twig', array(
            'title' => $this->tableDesc[$table], 'html' => json_encode($html)
        ));
    }

    public function listAction($table){
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ConfigBundle:'.$table)->findAll();
        $html = $this->renderView('ConfigBundle:Parametro:list.html.twig',
                array('entities' => $entities )
        );
        return new Response($html);
    }

    public function newAction($table)
    {
        $entity = $this->getNewObject($table);
        $urlCreate = $this->generateUrl('parametro_general_create', array('table' => $table));
        $form = $this->createForm(new ParametroType(), $entity, array(
                        'action' => $urlCreate, 'method' => 'POST',
                    ));

        $html = $this->renderView('ConfigBundle:Parametro:edit.html.twig',
                array('entity' => $entity, 'form' => $form->createView(), 'table' => $table  )
        );
        return $this->render('ConfigBundle:Parametro:index.html.twig', array(
            'title' => $this->tableDesc[$table], 'html' => json_encode($html)
        ));
    }

    public function createAction(Request $request, $table){
        $entity = $this->getNewObject($table);
        $urlCreate = $this->generateUrl('parametro_general_create', array('table' => $table));
        $form = $this->createForm(new ParametroType(), $entity, array(
                        'action' => $urlCreate, 'method' => 'POST',
                    ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try{
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info','Creado con éxito!' );
                 return $this->redirect($this->generateUrl('parametro_general',array('table'=>$table)));
            }catch (\Exception $ex) {
                $this->get('session')->getFlashBag()->add('danger',$ex->getTraceAsString());
            }
        }
        $html = $this->renderView('ConfigBundle:Parametro:edit.html.twig',
                array('entity' => $entity, 'form' => $form->createView(), 'table' => $table  )
        );
        return $this->render('ConfigBundle:Parametro:index.html.twig', array(
            'title' => $this->tableDesc[$table], 'html' => json_encode($html)
        ));
    }
    public function editAction($table,$id){
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ConfigBundle:'.$table)->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe');
        }
        $urlUpdate = $this->generateUrl('parametro_general_update',
                array('table' => $table,'id' => $entity->getId()) );

        $form = $this->createForm(new ParametroType(), $entity, array(
            'action' => $urlUpdate,
            'method' => 'POST',
        ));
        $html = $this->renderView('ConfigBundle:Parametro:edit.html.twig',
                array('entity' => $entity, 'form' => $form->createView(), 'table' => $table  )
        );
        return $this->render('ConfigBundle:Parametro:index.html.twig', array(
            'title' => $this->tableDesc[$table], 'html' => json_encode($html)
        ));
    }

    public function updateAction(Request $request, $table, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ConfigBundle:' . $table)->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe.');
        }
        $urlUpdate = $this->generateUrl('parametro_general_update', array('table' => $table, 'id' => $entity->getId()));

        $form = $this->createForm(new ParametroType(), $entity, array(
            'action' => $urlUpdate,
            'method' => 'POST',
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            try{
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info','Modificado con éxito!' );
                return $this->redirect($this->generateUrl('parametro_general',array('table'=>$table)));

            }catch (\Exception $ex) {
                $this->get('session')->getFlashBag()->add('danger',$ex->getTraceAsString());
            }
        }
        $html = $this->renderView('ConfigBundle:Parametro:edit.html.twig',
                array('entity' => $entity, 'form' => $form->createView(), 'table' => $table  )
        );
        return $this->render('ConfigBundle:Parametro:index.html.twig', array(
            'title' => $this->tableDesc[$table], 'html' => json_encode($html)
        ));
    }

    /**
     * Deletes a Parametro entity.
     */
    public function deleteAction($table, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ConfigBundle:'.$table)->find($id);
        try{
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('info','Se ha eliminado con éxito!' );
        } catch (\Exception $ex) {
            $this->get('session')->getFlashBag()->add('danger',$ex->getTraceAsString() );
        }
        return $this->redirect($this->generateUrl('parametro_general',array('table'=>$table)));
    }

    /*
     * Functions
     */
    private function getNewObject($obj) {
        switch ($obj) {
            case 'Nacionalidad':
                $entity = new Nacionalidad();
                break;
            case 'TipoDocumento':
                $entity = new TipoDocumento();
                break;
        }
        return $entity;
    }
}