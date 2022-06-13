<?php
namespace GN\ConfigBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use GN\ConfigBundle\Entity\Pais;
use GN\ConfigBundle\Entity\Provincia;
use GN\ConfigBundle\Entity\Localidad;
use GN\ConfigBundle\Form\RegionType;

class RegionController extends Controller
{

    public function indexAction($table)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ConfigBundle:'.$table)->findAll();
        $html = $this->renderView('ConfigBundle:Region:list.html.twig',
                array('entities' => $entities ,'table'=>$table )
        );
        return $this->render('ConfigBundle:Region:index.html.twig', array(
            'title' => $table, 'html' => json_encode($html)
        ));
    }

    public function listAction($table){
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ConfigBundle:'.$table)->findAll();
        $html = $this->renderView('ConfigBundle:Region:list.html.twig',
                array('entities' => $entities )
        );
        return new Response($html);
    }

    public function newAction($table)
    {
        $entity = $this->getNewObject($table);
        $urlCreate = $this->generateUrl('parametro_region_create', array('table' => $table));
        $form = $this->createForm(new RegionType(), $entity, array(
                        'action' => $urlCreate, 'method' => 'POST',
                    ));

        $html = $this->renderView('ConfigBundle:Region:edit.html.twig',
                array('entity' => $entity, 'form' => $form->createView(), 'table' => $table  )
        );
        return $this->render('ConfigBundle:Region:index.html.twig', array(
            'title' => $table, 'html' => json_encode($html)
        ));
    }

    public function createAction(Request $request, $table){
        $entity = $this->getNewObject($table);
        $urlCreate = $this->generateUrl('parametro_region_create', array('table' => $table));
        $form = $this->createForm(new RegionType(), $entity, array(
                        'action' => $urlCreate, 'method' => 'POST',
                    ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            try{
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info','Creado con éxito!' );
                 return $this->redirect($this->generateUrl('parametro_region',array('table'=>$table)));
            }catch (\Exception $ex) {
                $this->get('session')->getFlashBag()->add('danger',$ex->getTraceAsString());
            }
        }
        $html = $this->renderView('ConfigBundle:Region:edit.html.twig',
                array('entity' => $entity, 'form' => $form->createView(), 'table' => $table  )
        );
        return $this->render('ConfigBundle:Retion:index.html.twig', array(
            'title' => $table, 'html' => json_encode($html)
        ));
    }

    public function editAction($table,$id){
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ConfigBundle:'.$table)->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe');
        }
        $urlUpdate = $this->generateUrl('parametro_region_update',
                array('table' => $table,'id' => $entity->getId()) );

        $form = $this->createForm(new RegionType(), $entity, array(
            'action' => $urlUpdate,
            'method' => 'POST',
        ));
        $html = $this->renderView('ConfigBundle:Region:edit.html.twig',
                array('entity' => $entity, 'form' => $form->createView(), 'table' => $table  )
        );
        return $this->render('ConfigBundle:Region:index.html.twig', array(
            'title' => $table, 'html' => json_encode($html)
        ));
    }

    public function updateAction(Request $request, $table, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ConfigBundle:' . $table)->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No existe.');
        }
        $urlUpdate = $this->generateUrl('parametro_region_update', array('table' => $table, 'id' => $entity->getId()));

        $form = $this->createForm(new RegionType(), $entity, array(
            'action' => $urlUpdate,
            'method' => 'POST',
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            try{
                $em->persist($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info','Modificado con éxito!' );
                return $this->redirect($this->generateUrl('parametro_region',array('table'=>$table)));

            }catch (\Exception $ex) {
                $this->get('session')->getFlashBag()->add('danger',$ex->getTraceAsString());
            }
        }
        $html = $this->renderView('ConfigBundle:Region:edit.html.twig',
                array('entity' => $entity, 'form' => $form->createView(), 'table' => $table  )
        );
        return $this->render('ConfigBundle:Region:index.html.twig', array(
            'title' => $table, 'html' => json_encode($html)
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
        return $this->redirect($this->generateUrl('parametro_region',array('table'=>$table)));
    }

    /*
     * Functions
     */
    private function getNewObject($obj) {
        switch ($obj) {
            case 'Localidad':
                $entity = new Localidad();
                break;
            case 'Provincia':
                $entity = new Provincia();
                break;
            case 'Pais':
                $entity = new Pais();
                break;
        }
        return $entity;
    }

}