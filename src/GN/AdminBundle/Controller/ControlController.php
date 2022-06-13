<?php
namespace GN\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use GN\ConfigBundle\Controller\UtilsController;
use GN\AdminBundle\Entity\Control;
use GN\AdminBundle\Entity\ControlTarea;
use GN\AdminBundle\Entity\ControlTareaPersonal;
use GN\AdminBundle\Form\ControlType;

class ControlController extends Controller
{
    public function calendarAction($sectorId) {
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('ConfigBundle:Usuario')->find($this->get('security.context')->getToken()->getUser()->getId());
            $sectores = $user->getSectores();

            $sector = $em->getRepository('ConfigBundle:Sector')->find($sectorId);
            $parametro = $em->getRepository('ConfigBundle:Parametro')->find(1);
            if( $sectores->contains($sector) ){
                return $this->render('AdminBundle:Control:calendar.html.twig', array(
                    'sector' => $sector, 'fechaInicio' => $parametro->getFechaInicio()->format('Y-m-d')
                ));
            }else{
                $this->get('session')->getFlashBag()->add('danger', 'No posee permiso para ver este sector.');
                return $this->redirect($this->generateUrl('admin_homepage'));
            }
        }else{
            return $this->redirect($this->generateUrl('login', array()));
        }
    }

    public function getCalendarDataAction() {
        $request = $this->get('request');
        $session = $this->get('session');
        $sectorId = $request->get('sectorId');
        $month = $request->get('mes') + 1;
        $year = $request->get('anio');

        //datos
        $salida = array();
        $em = $this->getDoctrine()->getManager();
        $initialDate = strtotime($session->get('initialDate'));
        $firstMonthDay = strtotime(date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)));
        $lastMonthDay = strtotime(UtilsController::last_month_day($month, $year));
        $today = strtotime(date("Y-m-d"));
        $lastDay = ($today > $lastMonthDay) ? $lastMonthDay : $today;
        $firstDay = ($firstMonthDay > $initialDate) ? $firstMonthDay : $initialDate;

        $sector = $em->getRepository('ConfigBundle:Sector')->find($sectorId);
        $diaslab = json_decode($sector->getDiasLaborables());

        for ($i = $firstDay; $i <= $lastDay; $i += 86400) {
            if (in_array(date("w", $i), $diaslab)) {
                $diaria = array(
                    'start' => date("Y-m-d", $i) . ' 00:01',
                    'color' => '#f56954'
                );
                $control = $em->getRepository('AdminBundle:Control')->findControlByFecha(date("Y-m-d", $i), $sectorId);
                if (!$control) {
                    array_push($salida, $diaria);
                } else {
                    // controlar alerta solo por diarias
                    $sector = $em->getRepository('ConfigBundle:Sector')->find($sectorId);
                    $cantControl = $em->getRepository('AdminBundle:Control')->getDiariasPorControl($control->getId());
                    if ($sector->getTareasDiarias() > $cantControl) {
                        array_push($salida, $diaria);
                    }
                }
            }
        }

        return new Response(json_encode(array('datos' => $salida)));
    }

    public function editAction() {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $request = $this->get('request');
            $sectorId = $request->get('sId');
            $fecha = $request->get('day');

            $em = $this->getDoctrine()->getManager();
            $sector = $em->getRepository('ConfigBundle:Sector')->find($sectorId);

            $control = $em->getRepository('AdminBundle:Control')->findControlByFecha($fecha,$sectorId);
            if (!$control) {

                try {
                    $em->getConnection()->beginTransaction();
                    // nuevo control
                    $control = new Control();
                    $control->setSector($sector);
                    $control->setFecha(new \DateTime($fecha));
                    $em->persist($control);
                    $em->flush();
                    $em->getConnection()->commit();

                } catch (\Exception $ex) {
                    $this->get('session')->getFlashBag()->add('error', $ex->getMessage());
                    $em->getConnection()->rollback();
                    return $this->render('AdminBundle:Control:calendar.html.twig', array(
                                'sector' => $sector
                    ));
                }
            }
            $form = $this->createEditForm($control);

            return $this->render('AdminBundle:Control:edit.html.twig', array(
                'control' => $control,
                'estados' => $this->getEstadoControl($control),
                'permiso' => $this->checkPermiso($fecha),
                'form' => $form->createView()
            ));
        } else {
            return $this->redirect($this->generateUrl('login', array()));
        }
    }

    private function createEditForm(Control $entity)
    {
        $form = $this->createForm(new ControlType(), $entity, array(
            'action' => $this->generateUrl('control_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));
        return $form;
    }

    private function getEstadoControl($control){
        $id = $control->getId();
        $array = array();
        $em = $this->getDoctrine()->getManager();
        foreach ($control->getSubsectoresActivos() as $subsector){
            $alerta = FALSE;
            $ssId = $subsector->getId();
            $totalTareas = $subsector->getCantidadTareasPorTipo();

            $ctrlDiaria = $em->getRepository('AdminBundle:Control')->getCountDistinctTareas($ssId,'D',$id);
            $porcDiaria = ($totalTareas['diaria']) ? round( ( $ctrlDiaria[1] *100)/$totalTareas['diaria'] ,2).' %' : '--';
            $relDiaria = ($totalTareas['diaria']) ? $ctrlDiaria[1].'/'.$totalTareas['diaria'] : '0/0';
            if( $totalTareas['diaria'] != $ctrlDiaria[1] )
                $alerta = TRUE;

            $week = UtilsController::week_first_and_last_day($control->getFecha());
            $ctrlSemanal = $em->getRepository('AdminBundle:Control')->getCountDistinctTareas($ssId,'S',NULL,$week);
            $porcSemanal = ($totalTareas['semanal']) ? round( ( $ctrlSemanal[1] *100)/$totalTareas['semanal'] ,2).' %' : '--';
            $relSemanal = ($totalTareas['semanal']) ? $ctrlSemanal[1].'/'.$totalTareas['semanal'] : '0/0';
            if( $totalTareas['semanal'] != $ctrlSemanal[1] && date('w')<=6 )
                $alerta = TRUE;

            $month = UtilsController::month_first_and_last_day($control->getFecha());
            $ctrlMensual = $em->getRepository('AdminBundle:Control')->getCountDistinctTareas($ssId,'M',NULL,$month);
            $porcMensual = ($totalTareas['mensual']) ? round( ( $ctrlMensual[1] *100)/$totalTareas['mensual'] ,2).' %' : '--';
            $relMensual = ($totalTareas['mensual']) ? $ctrlMensual[1].'/'.$totalTareas['mensual'] : '0/0';
            if( $totalTareas['mensual'] != $ctrlMensual[1] && date('w')<=6 )
                $alerta = TRUE;

            $array[$subsector->getId()] = array('alerta'=>$alerta,
                'porcDiaria'=>$porcDiaria,'porcSemanal'=>$porcSemanal,'porcMensual'=>$porcMensual,
                'relDiaria'=>$relDiaria,'relSemanal'=>$relSemanal,'relMensual'=>$relMensual,
                'totDiaria'=>$totalTareas['diaria'],'verifDiaria'=>$ctrlDiaria[1],
                'totSemanal'=>$totalTareas['semanal'],'verifSemanal'=>$ctrlSemanal[1],
                'totMensual'=>$totalTareas['mensual'],'verifMensual'=>$ctrlMensual[1]
                );
        }
        return $array;
    }


    public function getControlTareaTabAction(){
        $request = $this->get('request');
        $ssId = $request->get('ssId');
        $ctrlId = $request->get('ctrlId');

        $em = $this->getDoctrine()->getManager();
        $ctrl = $em->getRepository('AdminBundle:Control')->find( $ctrlId );
        $subsector = $em->getRepository('ConfigBundle:SubSector')->find( $ssId );

        $control = $this->completeControlTareasParaTab($ctrl);
        $ctrlAux = new Control();
        $ctrlAux->setFecha($control->getFecha());
        $ctrlAux->setPlantelCompleto( $control->getPlantelCompleto() );
        $ctrlAux->setSector( $control->getSector() );
        foreach ($control->getControlTareas() as $ctrlTarea) {
            $ultVerif = null;
            if($ctrlTarea->getTarea()->getSubsector()->getId() == $ssId){
               if( !$ctrlTarea->getVerificada() ){
                    $ultVerif = $em->getRepository('AdminBundle:Control')->ultimaFechaVerificacionTarea( $ctrlTarea->getTarea()->getId() );
                    if($ultVerif[1])
                        $ctrlTarea->setUltimaVerificacion(new \DateTime($ultVerif[1]));
                }
                $ctrlAux->addControlTarea ($ctrlTarea);
            }
        }
        $permiso = $this->checkPermiso( $ctrl->getFecha()->format('Y-m-d') );

        $partial = $this->renderView('AdminBundle:Control:_partial_controlTareas.html.twig',
                array('ss'=>$subsector, 'permiso' => $permiso, 'tareas' => $ctrlAux->getControlTareas(),
                    'estados' => $this->getEstadoControl($control) )
            );
        return new Response(json_encode( $partial ));
    }

    private function completeControlTareasParaTab($ctrl) {
        /* Funcion para completar los items del tab semanal y mensual con las tareas ya realizadas.*/
        $em = $this->getDoctrine()->getManager();
        $sector = $ctrl->getSector();
        foreach ($sector->getSubSectores() as $subsector) {
            if ($subsector->getActivo()) {
                foreach ($subsector->getTareas() as $tarea) {
                    $lastVerif = null;
                    if ($tarea->getTipo() == 'S') {
                        $week = UtilsController::week_first_and_last_day($ctrl->getFecha());
                        $lastVerif = $em->getRepository('AdminBundle:Control')->tareaVerificadaxFecha($tarea->getId(),$week);
                    }elseif ($tarea->getTipo() == 'M') {
                        $month = UtilsController::month_first_and_last_day($ctrl->getFecha());
                        $lastVerif = $em->getRepository('AdminBundle:Control')->tareaVerificadaxFecha($tarea->getId(),$month);
                    }elseif ($ctrl->getTareas()->contains($tarea)){
                        continue;
                    }
                    if( $lastVerif ){
                        foreach ($lastVerif as $item) {
                            if($ctrl->getControlTareas()->contains($item)) continue;
                           $item->setEsPropia(FALSE);
                           $ctrl->addControlTarea($item);
                        }
                        continue;
                    }else{
                        $controlTarea = new ControlTarea();
                        $controlTarea->setTarea($tarea);
                        $controlTarea->setControl($ctrl);
                        if( strtotime(date('Y-m-d')) >= strtotime($controlTarea->getFechaVencimiento()) )
                            $controlTarea->setAlerta (TRUE);
                        $ctrl->addControlTarea($controlTarea);
                    }
                }
            }
        }
        return $ctrl;
    }

    public function setPlantelCompletoAction() {
        $request = $this->get('request');
        $controlId = $request->get('ctrlId');
        $value = $request->get('val');
        $em = $this->getDoctrine()->getManager();
        $control = $em->getRepository('AdminBundle:Control')->find( $controlId );

        if( $control && $this->checkPermiso( $control->getFecha()->format('Y-m-d') ) ){
            $control->setPlantelCompleto( $value );
            $em->persist($control);
            $em->flush();
            $salida = 'OK';
        }else{
            $salida = 'ERROR';
        }
        return new Response(json_encode( $salida ));
    }


    public function getModalTareaAction(){
        $request = $this->get('request');
        $action = $request->get('action');
        $ctrlId = $request->get('ctrlId');
        $idItem = $request->get('idItem');

        $em = $this->getDoctrine()->getManager();
        if($action=='edit'){
            $controlTarea = $em->getRepository('AdminBundle:ControlTarea')->find( $idItem );
        }else{
           $control = $em->getRepository('AdminBundle:Control')->find( $ctrlId );
           $controlTarea = new ControlTarea();
           $controlTarea->setControl($control);
           $fechahora = new \DateTime($control->getFecha()->format('Y-m-d').' '.date('H:i'));
           $controlTarea->setFechaHora($fechahora);
           $controlTarea->addPersonal( new ControlTareaPersonal() );
        }

        $estados = $em->getRepository('ConfigBundle:Estado')->findBy(array(),array('orden' => 'ASC'));
        $personal = $em->getRepository('ConfigBundle:Personal')->findBy(array('activo'=>1),array('nombre' => 'ASC'));

        $partial = $this->renderView('AdminBundle:Control:_modal_item_tarea.html.twig',
                array('action'=>$action, 'idItem' =>$idItem, 'ctrlId'=> $ctrlId,
                    'estados' => $estados, 'personal' => $personal, 'controlTarea'=>$controlTarea )
            );
        return new Response( $partial);
    }

    public function setControlTareaAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $idItem = $request->get('idItem');
        $tareaPersonalId = $request->get('tareaPersonalId');
        $personalId = $request->get('personalId');
        $personalNombre = $request->get('personalNombre');

        if($request->get('action')=='delete'){
            $fechahora = null;
            $estado = null;
            $tareaPersonalId = null;
            $personalId = null;
            $personalNombre = null;
        }else{
            $fecha = $request->get('fecha-registro');
            $hora = $request->get('hora-registro');
            $fechahora = new \DateTime($fecha.' '.$hora);
            $estado = $em->getRepository('ConfigBundle:Estado')->find( $request->get('estado') );
        }

        $action = ($request->get('action')=='add') ? 'check' : $request->get('action');

        switch ($action) {
            case 'check_all':
                $param = explode("_", $idItem);
                $tipoTarea = substr($param[0], 0, 1);
                $ssId = $param[1];
                $subsector = $em->getRepository('ConfigBundle:SubSector')->find( $ssId );
                $control = $em->getRepository('AdminBundle:Control')->find($request->get('ctrlId'));

                foreach ($subsector->getTareas() as $tarea) {
                    if($tarea->getTipo()==$tipoTarea){
                        $controlTarea = new ControlTarea();
                        $controlTarea->setControl($control);
                        $controlTarea->setTarea($tarea);
                        $controlTarea->setFechaHora($fechahora);
                        $controlTarea->setEstado($estado);
                        foreach ($personalId as $key => $pId) {
                            if (is_null($pId) && is_null($personalNombre[$key]))
                                    continue;
                            $tareaPersonal = new ControlTareaPersonal();
                            $tareaPersonal->setNombrePersonal($personalNombre[$key]);
                            if ($pId) {
                                $personal = $em->getRepository('ConfigBundle:Personal')->find($pId);
                                $tareaPersonal->setPersonal($personal);
                            } else {
                                $tareaPersonal->setPersonal(NULL);
                            }
                            $controlTarea->addPersonal($tareaPersonal);
                        }
                        $em->persist($controlTarea);
                        $em->flush();
                        $relaciones = $this->getEstadoControl( $controlTarea->getControl() );
                    }
                }

                $controlTareas = $em->getRepository('AdminBundle:Control')->findCtrlTareasCheckAll(  $control->getId(), $ssId, $tipoTarea );
                $partial = $this->renderView('AdminBundle:Control:_partial_tab_controlTareas.html.twig',
                    array('controlTareas' => $controlTareas, 'permiso' => $this->checkPermiso( $fecha ) )
                );
                $salida = array('html'=>$partial,'checked'=>1, 'relaciones' => $relaciones[$ssId] );
                return new Response(json_encode( $salida ));
            case 'delete':
                $controlTarea = $this->persistControlTarea($idItem,$fechahora,$estado,$tareaPersonalId,$personalId,$personalNombre);
                $partial = $this->renderView('AdminBundle:Control:_partial_item.html.twig',
                    array('item' => $controlTarea, 'permiso' => $this->checkPermiso( $controlTarea->getControl()->getFecha()->format('Y-m-d') ) )
                );
                // borrar
                $tarea = $controlTarea->getTarea();
                $control = $controlTarea->getControl();
                $em->remove($controlTarea);
                $em->flush();

                // verificar si es duplicada.
                if( $control->getTareas()->contains($tarea) ){
                    $partial = '';
                }
                $relaciones = $this->getEstadoControl( $controlTarea->getControl() );
                $salida = array('html'=>$partial,'checked'=>false, 'relaciones' => $relaciones[$tarea->getSubsector()->getId()] );
                return new Response(json_encode( $salida ));

            case 'edit':
                $controlTarea = $this->persistControlTarea($idItem,$fechahora,$estado,$tareaPersonalId,$personalId,$personalNombre);
                $partial = $this->renderView('AdminBundle:Control:_partial_item.html.twig',
                    array('item' => $controlTarea, 'permiso' => $this->checkPermiso( $controlTarea->getControl()->getFecha()->format('Y-m-d') ) )
                );
                $relaciones = $this->getEstadoControl( $controlTarea->getControl() );
                $salida = array('html'=>$partial,'checked'=>$controlTarea->getVerificada(), 'relaciones' => $relaciones[$controlTarea->getTarea()->getSubsector()->getId()] );
                return new Response(json_encode( $salida ));
            case 'check':
                $tarea = $em->getRepository('ConfigBundle:Tarea')->find($idItem);
                $control = $em->getRepository('AdminBundle:Control')->find($request->get('ctrlId'));
                $controlTarea = new ControlTarea();
                $controlTarea->setControl($control);
                $controlTarea->setTarea($tarea);
                $controlTarea->setFechaHora($fechahora);
                $controlTarea->setEstado($estado);
                foreach ($personalId as $key => $pId) {
                    if (is_null($pId) && is_null($personalNombre[$key]))
                            continue;
                    $tareaPersonal = new ControlTareaPersonal();
                    $tareaPersonal->setNombrePersonal($personalNombre[$key]);
                    if ($pId) {
                        $personal = $em->getRepository('ConfigBundle:Personal')->find($pId);
                        $tareaPersonal->setPersonal($personal);
                    } else {
                        $tareaPersonal->setPersonal(NULL);
                    }
                    $controlTarea->addPersonal($tareaPersonal);
                }
                $em->persist($controlTarea);
                $em->flush();
                $partial = $this->renderView('AdminBundle:Control:_partial_item.html.twig',
                    array('item' => $controlTarea, 'permiso' => $this->checkPermiso( $controlTarea->getControl()->getFecha()->format('Y-m-d') ) )
                );
                $relaciones = $this->getEstadoControl( $controlTarea->getControl() );
                $salida = array('html'=>$partial,'checked'=>$controlTarea->getVerificada(), 'relaciones' => $relaciones[$tarea->getSubsector()->getId()] );
                return new Response(json_encode( $salida ));
        }

    }

    private function persistControlTarea($idItem,$fechahora,$estado,$tareaPersonalId,$personalId,$personalNombre){
        $em = $this->getDoctrine()->getManager();
        $controlTarea = $em->getRepository('AdminBundle:ControlTarea')->find( $idItem );
        if( $controlTarea ){
            $controlTarea->setFechaHora( $fechahora );
            $controlTarea->setEstado( $estado );
            if( is_null($tareaPersonalId) && is_null($personalId) && is_null($personalNombre) ){
                foreach($controlTarea->getPersonales() as $personal ){
                    $controlTarea->removePersonal($personal);
                    $em->remove($personal);
                }
            }else{
                foreach ($tareaPersonalId as $key => $tpId) {
                    if($tpId){
                        $tareaPersonal = $em->getRepository('AdminBundle:ControlTareaPersonal')->find( $tpId );

                        if( empty($personalId[$key]) && empty($personalNombre[$key]) ){
                            $controlTarea->removePersonal($tareaPersonal);
                            continue;
                        }
                    }else{
                        if( empty($personalId[$key]) && empty($personalNombre[$key]) )
                            continue;
                        $tareaPersonal = new ControlTareaPersonal();
                    }
                    $tareaPersonal->setNombrePersonal($personalNombre[$key]);
                    if ($personalId[$key]) {
                        $personal = $em->getRepository('ConfigBundle:Personal')->find($personalId[$key]);
                        $tareaPersonal->setPersonal($personal);
                    } else {
                        $tareaPersonal->setPersonal(NULL);
                    }
                    if(!$tpId){
                        $tareaPersonal->setControlTarea($controlTarea);
                    }
                    $em->persist($tareaPersonal);
                }
            }
            $em->persist($controlTarea);
            $em->flush();
        }
        return $controlTarea;
    }

    private function checkPermiso($fecha) {
        $permiso = TRUE;
        if ($this->get('security.context')->isGranted('ROLE_SUPERVISOR') && (strtotime($fecha) < strtotime(date("Y-m-d")))) {
            $permiso = FALSE;
        } elseif ($this->get('security.context')->isGranted('ROLE_ENCARGADO') && (strtotime($fecha) < strtotime('-2 day', strtotime(date("Y-m-d"))) )) {
            $permiso = FALSE;
        }
        return $permiso;
    }
}
