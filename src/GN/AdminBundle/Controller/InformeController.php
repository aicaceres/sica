<?php
namespace GN\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;

use GN\AdminBundle\Entity\Control;
use GN\AdminBundle\Entity\ControlTarea;

class InformeController extends Controller
{
    /**
     * INFORME GRAFICO
     */

    public function informeGraficoAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ConfigBundle:Usuario')->find($this->get('security.context')->getToken()->getUser()->getId());
        $sectores = $user->getSectores();
        $filtro = $this->setVariablesInformeGrafico();

        $firstDay = strtotime($filtro['desde']);
        $lastDay = strtotime($filtro['hasta']);
        $infoSectores = $infoDonut = array();
        foreach ($sectores as $sector) {
            $totDiaria = $verifDiaria = 0;
            $totSemanal = $verifSemanal = 0;
            $totMensual = $verifMensual = 0;

            $dvt = $dva = $dpt = $dpv = 0;
            $diaslab = json_decode($sector->getDiasLaborables());

            for ($i = $firstDay; $i <= $lastDay; $i += 86400) {
              if (in_array(date("w", $i), $diaslab)) {
                $control = $em->getRepository('AdminBundle:Control')->findControlByFecha(date("Y-m-d", $i), $sector->getId());
                if (!$control) {
                    $control = new Control();
                    $control->setSector($sector);
                    $control->setFecha(new \DateTime(date("Y-m-d", $i)));
                }
                $this->completeControlTareas($control);

                foreach ($control->getControlTareas() as $ctrlTarea) {
                    if ($ctrlTarea->getVencida()) {
                        $alerta = true;
                    }
                    switch ($ctrlTarea->getTipoTarea()) {
                        case 'D':
                            $totDiaria += 1;
                            if( $ctrlTarea->getVerificada() ){
                                $verifDiaria += 1;
                                if( $ctrlTarea->getVerificionAtrasada() ){
                                    $dva += 1;
                                }else{
                                    $dvt += 1;
                                }
                            }else{
                                if( $ctrlTarea->getVencida() ){
                                    $dpv += 1;
                                }else{
                                    $dpt += 1;
                                }
                            }
                            break;
                        case 'S':
                            $totSemanal += 1;
                            $verifSemanal += ($ctrlTarea->getVerificada()) ? 1 : 0;
                            break;
                        case 'M':
                            $totMensual += 1;
                            $verifMensual += ($ctrlTarea->getVerificada()) ? 1 : 0;
                            break;
                    }
                }
              }
            }
            //verificadas en termino
            $pdvt = ($totDiaria) ? round(($dvt * 100) / $totDiaria, 2): 0;
            //verificacion atrasada
            $pdva = ($totDiaria) ? round(($dva * 100) / $totDiaria, 2): 0;
            //pendiente en termino
            $pdpt = ($totDiaria) ? round(($dpt * 100) / $totDiaria, 2): 0;
            //pendiente en vencida
            $pdpv = ($totDiaria) ? round(($dpv * 100) / $totDiaria, 2): 0;


            $porcDiaria = ($totDiaria) ? round(($verifDiaria * 100) / $totDiaria, 2): 0;
            $porcSemanal = ($totSemanal) ? round(($verifSemanal * 100) / $totSemanal, 2): 0;
            $porcMensual = ($totMensual) ? round(($verifMensual * 100) / $totMensual, 2): 0;

            $infoDonut[] = array(
                'id' => $sector->getId(), 'nombre' => $sector->getNombre(),
                'dvt' => $pdvt,
                'dva' => $pdva,
                'dpt' => $pdpt,
                'dpv' => $pdpv
            );

            $infoSectores[] = array(
                'id' => $sector->getId(), 'nombre' => $sector->getNombre(),
                'diario' => $porcDiaria,
                'semanal' => $porcSemanal,
                'mensual' => $porcMensual
                    );
        }
        $infoBar = array();
        foreach ($infoSectores as $info){
            $infoBar[] = array('sector' => $info['nombre'],'id' => $info['id'], 'diaria'=>$info['diario'],'semanal'=>$info['semanal'],'mensual'=>$info['mensual'] );
        }
        $ord = usort($infoBar, function($a1, $a2) {
                $value1 = $a1['diaria'];
                $value2 = $a2['diaria'];
                return $value2 - $value1;
            });

        return $this->render('AdminBundle:Control:informe_grafico.html.twig', array(
              'sectores'=>$sectores, 'tareas' => json_encode($infoBar)
            ));

    }

    private function setVariablesInformeGrafico() {
        // SETEO DE VARIABLES DE SESION PARA EL FILTRO
        $request = $this->get('request');
        $session = $this->get('session');
        $sessionFiltro = $session->get('informeGrafico');
        $firstMonthDay =  (new \DateTime('first day of this month'))->format('d-m-Y') ;
        switch ($request->get('_opFiltro')) {
            case 'limpiar':
                $filtro = array('sectorId' => '0','desde' => date('d-m-Y'), 'hasta' => date('d-m-Y'));
                break;
            case 'buscar':
                $filtro = array(
                    'sectorId' => $request->get('_sectorId'),
                    'desde' => ($request->get('_desde'))?$request->get('_desde'):$firstMonthDay,
                    'hasta' => ($request->get('_hasta'))?$request->get('_hasta'):date('d-m-Y'));
                break;
            default:
                //usar session
                $filtro = array(
                    'sectorId' => ($sessionFiltro['sectorId'])?$sessionFiltro['sectorId']:'0',
                    'desde' => ($sessionFiltro['desde'])?$sessionFiltro['desde']:$firstMonthDay,
                    'hasta' => ($sessionFiltro['hasta'])?$sessionFiltro['hasta']:date('d-m-Y') );
                break;
        }
        $initialDate = strtotime($session->get('initialDate'));
        $firstDay = strtotime( (new \DateTime($filtro['desde']))->format('Y-m-d') );
        // tomar siempre como primer dia el dia de inicio de actividades
        if($firstDay < $initialDate){
            $filtro['desde'] = (new \DateTime( $session->get('initialDate') ))->format('d-m-Y');
            $session->getFlashBag()->clear();
            $session->getFlashBag()->add('info', 'La fecha Desde no puede ser menor al Inicio de Actividades ('.$filtro['desde'].')');
            if( $filtro['desde'] > $filtro['hasta'])
                $filtro['hasta'] = $filtro['desde'];
        }
        $session->set('informeGrafico', $filtro);
        return $filtro;
    }

    public function datosGraficoSubsectorAction() {
        $request = $this->get('request');
        $session = $this->get('session');
        $sessionFiltro = $session->get('informeGrafico');
        $firstMonthDay = (new \DateTime('first day of this month'))->format('d-m-Y');
        $filtro = array(
            'sectorId' => $request->get('_sectorId'),
            'desde' => ($sessionFiltro['desde']) ? $sessionFiltro['desde'] : $firstMonthDay,
            'hasta' => ($sessionFiltro['hasta']) ? $sessionFiltro['hasta'] : date('d-m-Y'));
        $session->set('informeGrafico', $filtro);

        $firstDay = strtotime($filtro['desde']);
        $lastDay = strtotime($filtro['hasta']);
        $em = $this->getDoctrine()->getManager();
        $sector = $em->getRepository('ConfigBundle:Sector')->find($filtro['sectorId']);
        $diaslab = json_decode($sector->getDiasLaborables());

        $controles = new ArrayCollection();
        for ($i = $firstDay; $i <= $lastDay; $i += 86400) {
          if (in_array(date("w", $i), $diaslab)) {
            $control = $em->getRepository('AdminBundle:Control')->findControlByFecha(date("Y-m-d", $i), $sector->getId());
            if (!$control) {
                $control = new Control();
                $control->setSector($sector);
                $control->setFecha(new \DateTime(date("Y-m-d", $i)));
            }
            $this->completeControlTareas($control);
            $controles[] = $control;
          }
        }

        $infoss = array();

        foreach ($sector->getSubsectores() as $ss) {
            $td = $vd = $ts = $vs = $tm = $vm = 0;
            foreach ($controles as $ctrl) {
                foreach ($ctrl->getControlTareas() as $ctrlTarea) {
                    if ($ctrlTarea->getTarea()->getSubsector()->getId() == $ss->getId()) {
                        switch ($ctrlTarea->getTipoTarea()) {
                            case 'D':
                                $td += 1;
                                $vd += ($ctrlTarea->getVerificada()) ? 1 : 0;
                                break;
                            case 'S':
                                $ts += 1;
                                $vs += ($ctrlTarea->getVerificada()) ? 1 : 0;
                                break;
                            case 'M':
                                $tm += 1;
                                $vm += ($ctrlTarea->getVerificada()) ? 1 : 0;
                                break;
                        }
                    }
                }
            }
            //estadisticas para subsector
            $pd = ($td) ? round(($vd * 100) / $td, 2) : 0;
            $sort = ($td) ? round(($vd * 100) / $td, 2) : 0;
            $ps = ($ts) ? round(($vs * 100) / $ts, 2) : 0;
            $pm = ($tm) ? round(($vm * 100) / $tm, 2) : 0;
            $infoss[] = array(
                'id' => $ss->getId(), 'subsector' => $ss->getNombre(),
                'diaria' => $pd,
                'semanal' => $ps,
                'mensual' => $pm);
        }
        $ord = usort($infoss, function($a1, $a2) {
            $value1 = $a1['diaria'];
            $value2 = $a2['diaria'];
            return $value2 - $value1;
        });


        return new Response(json_encode($infoss));
    }

    /**
     * INFORME DE TAREAS
     */

    public function informeTareasAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ConfigBundle:Usuario')->find($this->get('security.context')->getToken()->getUser()->getId());
        $sectores = $user->getSectores();
        $filtroSectores = array();

        $filtro = $this->setVariablesInformeTareas();
        $filtroTxt = $this->setTxtVariablesInformeTareas();

        $subSectores = $em->getRepository('ConfigBundle:SubSector')->findBy(array('sector' => $filtro['sectorId'], 'activo' => 1), array('nombre' => 'ASC'));
        $usuarios = $em->getRepository('ConfigBundle:Usuario')->findBy(array('activo' => 1), array('nombre' => 'ASC'));
        $personal = $em->getRepository('ConfigBundle:Personal')->findBy(array('activo' => 1), array('nombre' => 'ASC'));
        $estados = $em->getRepository('ConfigBundle:Estado')->findBy(array(), array('orden' => 'ASC'));
        $firstDay = strtotime($filtro['desde']);
        $lastDay = strtotime($filtro['hasta']);

        foreach ($sectores as $sector) {
            $arrayControles = new ArrayCollection();
            if ($sector->getActivo()) {
                if ($filtro['sectorId']) {
                    if ($sector->getId() != $filtro['sectorId'])
                        continue;
                }
                $diaslab = json_decode($sector->getDiasLaborables());
                for ($i = $firstDay; $i <= $lastDay; $i += 86400) {
                  if (in_array(date("w", $i), $diaslab)) {
                    $control = $em->getRepository('AdminBundle:Control')->findControlByFecha(date("Y-m-d", $i), $sector->getId());
                    if (!$control) {
                        $control = new Control();
                        $control->setSector($sector);
                        $control->setPlantelCompleto(null);
                        $control->setFecha(new \DateTime(date("Y-m-d", $i)));
                    }
                    // completa el control con las tareas que deberia tener.
                    $this->completeControlTareas($control);
                    $ctrlFiltrado = $this->filtrarControlTareas($control, $filtro);

                    $arrayControles[] = $ctrlFiltrado;
                  }
                }
                $filtroSectores[] = array(
                    'id' => $sector->getId(), 'nombre' => $sector->getNombre(), 'controles' => $arrayControles);
            }
        }

        $infoSector = $this->getDataInforme($filtroSectores);

        if ($this->get('request')->get('salida') == 'print') {
            // emision del pdf
            $facade = $this->get('ps_pdf.facade');
            $response = new Response();
            $this->render('AdminBundle:Control:informe_tareas.pdf.twig', array('filtroTxt' => $filtroTxt, 'resultado' => $infoSector), $response);

            $xml = $response->getContent();
            $content = $facade->render($xml);
            return new Response($content, 200, array('content-type' => 'application/pdf',
                'Content-Disposition' => 'inline;filename=informe-tareas-' . date('YmdHi') . '.pdf'));
        } else {
            // muestra el informe en pantalla
            return $this->render('AdminBundle:Control:informe_tareas.html.twig', array(
                        'sectores' => $sectores, 'subSectores' => $subSectores, 'estados' => $estados, 'usuarios' => $usuarios,
                        'personal' => $personal, 'resultado' => $infoSector
            ));
        }
    }

    private function setVariablesInformeTareas() {
        // SETEO DE VARIABLES DE SESION PARA EL FILTRO
        $request = $this->get('request');
        $session = $this->get('session');
        $sessionFiltro = $session->get('informeTareas');

        switch ($request->get('_opFiltro')) {
            case 'limpiar':
                $filtro = array('sectorId' => '0', 'subSectorId' => '0', 'usuarioId' => '0', 'personalId' => '0', 'tipoTarea' => '0',
                    'estadoId' => '0', 'verificada' => '0', 'vencida' => '0', 'tarea'=>'' , 'desde' => date('d-m-Y'), 'hasta' => date('d-m-Y'));
                break;
            case 'buscar':
                $filtro = array(
                    'sectorId' => $request->get('_sectorId'),
                    'subSectorId' => $request->get('_subSectorId'),
                    'usuarioId' => $request->get('_usuarioId'),
                    'personalId' => $request->get('_personalId'),
                    'estadoId' => $request->get('_estadoId'),
                    'tipoTarea' => $request->get('_tipoTarea'),
                    'verificada' => $request->get('_verificada'),
                    'vencida' => $request->get('_vencida'),
                    'tarea' => $request->get('_tarea'),
                    'desde' => ($request->get('_desde'))?$request->get('_desde'):date('d-m-Y'),
                    'hasta' => ($request->get('_hasta'))?$request->get('_hasta'):date('d-m-Y'));
                break;
            default:
                //usar session
                $filtro = array(
                    'sectorId' => ($sessionFiltro['sectorId'])?$sessionFiltro['sectorId']:'0',
                    'subSectorId' => ($sessionFiltro['subSectorId'])?$sessionFiltro['subSectorId']:'0',
                    'usuarioId' => ($sessionFiltro['usuarioId'])?$sessionFiltro['usuarioId']:'0',
                    'personalId' => ($sessionFiltro['personalId'])?$sessionFiltro['personalId']:'0',
                    'estadoId' => ($sessionFiltro['estadoId'])?$sessionFiltro['estadoId']:'0',
                    'tipoTarea' => ($sessionFiltro['tipoTarea'])?$sessionFiltro['tipoTarea']:'0',
                    'verificada' => ($sessionFiltro['verificada'])?$sessionFiltro['verificada']:'0',
                    'vencida' => ($sessionFiltro['vencida'])?$sessionFiltro['vencida']:'0',
                    'tarea' => ($sessionFiltro['tarea'])?$sessionFiltro['tarea']:'',
                    'desde' => ($sessionFiltro['desde'])?$sessionFiltro['desde']:date('d-m-Y'),
                    'hasta' => ($sessionFiltro['hasta'])?$sessionFiltro['hasta']:date('d-m-Y') );
                break;
        }
        $initialDate = strtotime($session->get('initialDate'));
        $firstMonthDay = strtotime( (new \DateTime($filtro['desde']))->format('Y-m-d') );
        // tomar siempre como primer dia el dia de inicio de actividades
        if($firstMonthDay < $initialDate){
            $filtro['desde'] = (new \DateTime( $session->get('initialDate') ))->format('d-m-Y');
            $session->getFlashBag()->clear();
            $session->getFlashBag()->add('info', 'La fecha Desde no puede ser menor al Inicio de Actividades ('.$filtro['desde'].')');
            if( $filtro['desde'] > $filtro['hasta'])
                $filtro['hasta'] = $filtro['desde'];
        }
        $session->set('informeTareas', $filtro);

        return $filtro;
    }

    private function setTxtVariablesInformeTareas() {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $sessionFiltro = $session->get('informeTareas');

        $sector = $em->getRepository('ConfigBundle:Sector')->find( $sessionFiltro['sectorId'] );
        $sectorTxt = ($sector) ? $sector->getNombre() : '';

        $subSector = $em->getRepository('ConfigBundle:SubSector')->find( $sessionFiltro['subSectorId'] );
        $subsectorTxt = ($subSector) ? $subSector->getNombre() : '';

        $usuario = $em->getRepository('ConfigBundle:Usuario')->find( $sessionFiltro['usuarioId'] );
        $usuarioTxt = ($usuario) ? $usuario->getNombre() : '';

        $personal = $em->getRepository('ConfigBundle:Personal')->find( $sessionFiltro['personalId'] );
        $personalTxt = ($personal) ? $personal->getNombre() : '';

        $estado = $em->getRepository('ConfigBundle:Estado')->find( $sessionFiltro['estadoId'] );
        $estadoTxt = ($estado) ? $estado->getNombre() : '';


        $data = array('desde'=>$sessionFiltro['desde'],'hasta'=>$sessionFiltro['hasta'], 'sectorTxt'=>$sectorTxt, 'subsectorTxt'=> $subsectorTxt,
           'usuarioTxt'=>$usuarioTxt, 'personalTxt'=>$personalTxt, 'estadoTxt'=>$estadoTxt );
        return $data;

    }

    private function completeControlTareas(&$control) {
        /* Cargo en el control una sola vez las tareas semanales o mensuales   */
        // auxiliar para tarea semanal
        $aux_ini = clone($control->getFecha());
        $aux_fin = clone($control->getFecha());
        $week = array('desde' => $aux_ini->modify('Monday this week')->format('Y-m-d'),
                      'hasta' => $aux_fin->modify('Sunday this week')->format('Y-m-d') );
        // auxiliar para tarea mensual
        $aux_ini = clone($control->getFecha());
        $aux_fin = clone($control->getFecha());
        $month = array('desde' => $aux_ini->modify('first day of this month')->format('Y-m-d'),
                       'hasta' => $aux_fin->modify('last day of this month')->format('Y-m-d') );

        $em = $this->getDoctrine()->getManager();
        $sector = $control->getSector();
        foreach ($sector->getSubSectores() as $subsector) {
            if ($subsector->getActivo()) {
                foreach ($subsector->getTareas() as $tarea) {
                    //$agregar = TRUE;
                    if ($control->getTareas()->contains($tarea)) {
                        continue;
                    } elseif ($tarea->getTipo() == 'S') {
                        //verificar que no se haya hecho en esta semana.
                        $verif = $em->getRepository('AdminBundle:Control')->tareaVerificadaxFecha($tarea->getId(),$week);
                        if($verif) continue;
                    }elseif ($tarea->getTipo() == 'M') {
                        $verif = $em->getRepository('AdminBundle:Control')->tareaVerificadaxFecha($tarea->getId(),$month);
                        if($verif) continue;
                    }
                    $controlTarea = new ControlTarea();
                    $controlTarea->setTarea($tarea);
                    $controlTarea->setControl($control);
                    $control->addControlTarea($controlTarea);
                }
            }
        }
    }

    private function filtrarControlTareas($control,$filtro){
        // genero un control auxiliar para el resultado del filtro
        $ctrl = new Control();
        $ctrl->setSector($control->getSector());
        $ctrl->setFecha($control->getFecha());
        $ctrl->setPlantelCompleto($control->getPlantelCompleto());

        foreach ($control->getControlTareas() as $ctrltarea){
            if ($filtro['subSectorId']) {
                // filtro por subsector
                if ($ctrltarea->getTarea()->getSubSector()->getId() != $filtro['subSectorId']) {
                    continue;
                }
            }
            /* TIPO TAREA */
            if ($filtro['tipoTarea']) {
                if ($ctrltarea->getTipoTarea() != $filtro['tipoTarea']) {
                    continue;
                }
            }
            /* TAREA */
            if ($filtro['tarea']) {
                $posicion_coincidencia = strpos( strtoupper(trim($ctrltarea->getTarea()->getNombre())) , strtoupper(trim($filtro['tarea'])) );
                if ($posicion_coincidencia === false) {
                    continue;
                }
            }
            /* VERIFICADA */
            if ($filtro['verificada']) {
                if ($filtro['verificada'] == 'S' && !$ctrltarea->getVerificada()) {
                    continue;
                } elseif ($filtro['verificada'] == 'N' && $ctrltarea->getVerificada()) {
                    continue;
                }
            }
            /* VENCIDA */
            if ($filtro['vencida']) {
                if ($filtro['vencida'] == 'S' && !$ctrltarea->getVencida()) {
                    continue;
                } elseif ($filtro['vencida'] == 'N' && $ctrltarea->getVencida()) {
                    continue;
                }
            }
            /* ESTADO */
            if ($filtro['estadoId']) {
                if ($ctrltarea->getEstado()->getId() != $filtro['estadoId']) {
                    continue;
                }
            }
            /* USUARIO */
            if ($filtro['usuarioId']) {
                if ($ctrltarea->getUpdatedBy()->getId() != $filtro['usuarioId']) {
                    continue;
                }
            }
            /* PERSONAL*/
            if ($filtro['personalId']) {
                $incluye = false;
                foreach ($ctrltarea->getPersonales() as $pers){
                    // Otro
                    if ($filtro['personalId'] == '999') {
                        if (!$pers->getNombrePersonal()) {
                            continue;
                        }
                    }else {
                        if ($pers->getPersonal()) {
                            if ($pers->getPersonal()->getId() != $filtro['personalId']) {
                                continue;
                            }
                        }else{
                            continue;
                        }
                    }
                    $incluye = true;
                }
                if(!$incluye){
                    continue;
                }
            }

            $ctrl->addControlTarea($ctrltarea);
        }
        return $ctrl;
    }

    private function getDataInforme($filtroSectores){
        foreach ($filtroSectores as $fsector){
                $totDiaria = $verifDiaria = 0;
                $totSemanal = $verifSemanal = 0;
                $totMensual = $verifMensual = 0;
                $dias = array();
                foreach ($fsector['controles'] as $ctrl){
                    $infoss = array();
                    foreach ($ctrl->getSubsectoresControl() as $ss){
                        $tareas = array();
                        $td = $vd = $ts = $vs = $tm = $vm = 0;
                        $nombreSubsector = '';
                        foreach($ctrl->getControlTareas() as $ctrlTarea){
                            if( $ctrlTarea->getTarea()->getSubsector()->getId()==$ss){
                                $nombreSubsector =  $ctrlTarea->getTarea()->getSubsector()->getNombre();
                                if($ctrlTarea->getPersonales()){
                                    $nombre = '';
                                    foreach ($ctrlTarea->getPersonales() as $pers){
                                        if($pers->getPersonal())
                                            $aux = $pers->getPersonal()->getNombre();
                                        else
                                            $aux = $pers->getNombrePersonal();
                                        $nombre = (empty($nombre)) ? $aux : $nombre.', '.$aux;
                                    }
                                }
                                switch ($ctrlTarea->getTipoTarea()) {
                                    case 'D':
                                        $sort3 = 1;
                                        $td += 1;
                                        $vd += ($ctrlTarea->getVerificada()) ? 1 : 0;
                                        break;
                                    case 'S':
                                        $sort3 = 2;
                                        $ts += 1;
                                        $vs += ($ctrlTarea->getVerificada()) ? 1 : 0;
                                        break;
                                    case 'M':
                                        $sort3 = 3;
                                        $tm += 1;
                                        $vm += ($ctrlTarea->getVerificada()) ? 1 : 0;
                                        break;
                                }
                                $tareas[] = array('tipoTarea'=>$ctrlTarea->getTipoTarea(),'sort3' => $sort3,
                                        'nombreTarea'=>$ctrlTarea->getTarea()->getNombre(), 'fechaHora'=>$ctrlTarea->getFechaHora(),
                                        'estado'=>($ctrlTarea->getEstado())?$ctrlTarea->getEstado()->getNombre():null,
                                        'personal'=>$nombre);
                            }
                        }
                        $tar = usort($tareas, function($a1, $a2) {
                            $value1 = $a1['sort3'];
                            $value2 = $a2['sort3'];
                            return $value1 - $value2;
                        });

                        //estadisticas para subsector
                        $pd = ($td) ? round( ($vd*100)/$td ,2).' %' : '--';
                        $sort = ($td) ? round( ($vd*100)/$td ,2) : 0;
                        $ps = ($ts) ? round( ($vs*100)/$ts ,2).' %' : '--';
                        $pm = ($tm) ? round( ($vm*100)/$tm ,2).' %' : '--';

                        $infoss[] =  array(
                            'sort1' => $sort,
                            'id'=>$ss ,'nombre'=>$nombreSubsector,
                            'diario'=> $pd,
                            'semanal'=> $ps,
                            'mensual'=> $pm,
                            'tareas'=>$tareas) ;

                        $totDiaria += $td;
                        $verifDiaria += $vd;
                        $totSemanal += $ts;
                        $verifSemanal += $vs;
                        $totMensual += $tm;
                        $verifMensual += $vm;
                    }
                    $ord = usort($infoss, function($a1, $a2) {
                        $value1 = $a1['sort1'];
                        $value2 = $a2['sort1'];
                        return $value2 - $value1;
                    });
                    $dias[] = array('fecha'=>$ctrl->getFecha()->format('d-m-Y'),
                        'plantelCompleto' => $ctrl->getPlantelCompleto(),
                        'subsectores'=>$infoss);
                }

                $porcDiaria = ($totDiaria) ? round(($verifDiaria * 100) / $totDiaria, 2) . ' %' : '--';
                $sort = ($totDiaria) ? round( ($verifDiaria*100)/$totDiaria ,2) : 0;
                $porcSemanal = ($totSemanal) ? round(($verifSemanal * 100) / $totSemanal, 2) . ' %' : '--';
                $porcMensual = ($totMensual) ? round(($verifMensual * 100) / $totMensual, 2) . ' %' : '--';

                $infoSector[] =  array(
                            'sort2' => $sort,
                            'id'=>$fsector['id'] ,'nombre'=>$fsector['nombre'],
                            'diario'=> $porcDiaria,
                            'semanal'=> $porcSemanal,
                            'mensual'=> $porcMensual,
                            'dias'=>$dias) ;
            }
        $ord1 = usort($infoSector, function($a1, $a2) {
            $value1 = $a1['sort2'];
            $value2 = $a2['sort2'];
            return $value2 - $value1;
        });

        return $infoSector;
    }

    public function informeTareasXXAction() {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ConfigBundle:Usuario')->find($this->get('security.context')->getToken()->getUser()->getId());
        $sectores = $user->getSectores();

        $filtro = $this->setVariablesInformeTareas();

        $subSectores = $em->getRepository('ConfigBundle:SubSector')->findBy(array('sector' => $filtro['sectorId'], 'activo' => 1), array('nombre' => 'ASC'));
        $usuarios = $em->getRepository('ConfigBundle:Usuario')->findBy(array('activo' => 1), array('nombre' => 'ASC'));
        $personal = $em->getRepository('ConfigBundle:Personal')->findBy(array('activo' => 1), array('nombre' => 'ASC'));
        $estados = $em->getRepository('ConfigBundle:Estado')->findBy(array(), array('orden' => 'ASC'));
        $firstDay = strtotime($filtro['desde']) ;
        $lastDay = strtotime($filtro['hasta']) ;

        $result = array();
        foreach ($sectores as $sector) {
                $totDiaria = $verifDiaria = 0;
                $totSemanal = $verifSemanal = 0;
                $totMensual = $verifMensual = 0;

                if ($sector->getActivo()) {
                    if ($filtro['sectorId']) {
                        if ($sector->getId() != $filtro['sectorId'])
                            continue;
                    }
                    $dias = array();
                    for ($i = $firstDay; $i <= $lastDay; $i+=86400) {
                        // dias
                        $control = $em->getRepository('AdminBundle:Control')->findControlByFecha(date("Y-m-d", $i),$sector->getId());
                        if (!$control) {
                            $control = new Control();
                            $control->setSector($sector);
                            $control->setFecha(new \DateTime(date("Y-m-d", $i)));
                        }
                        $control = $this->completeControlTareas($control);
                        $ssArray = array();
                        //subsectores
                        foreach ($control->getSubsectoresActivos() as $subsector) {
                            $tareas = array();
                            foreach ($control->getControlTareas() as $ctrltarea) {
                                if( $ctrltarea->getTarea()->getSubsector()->getId() == $subsector->getId() ){
                                    /* SUBSECTOR */
                                    if ($filtro['subSectorId']) {
                                        if ($ctrltarea->getTarea()->getSubSector()->getId() != $filtro['subSectorId']) {
                                            continue;
                                        }
                                    }
                                    /* TIPO TAREA */
                                    if ($filtro['tipoTarea']) {
                                        if ($ctrltarea->getTipoTarea() != $filtro['tipoTarea']) {
                                            continue;
                                        }
                                    }
                                    /* TAREA */
                                    if ($filtro['tarea']) {
                                        $posicion_coincidencia = strpos( strtoupper(trim($ctrltarea->getTarea()->getNombre())) , strtoupper(trim($filtro['tarea'])) );
                                        if ($posicion_coincidencia === false) {
                                            continue;
                                        }
                                    }
                                    /* VERIFICADA */
                                    if ($filtro['verificada']) {
                                        if ($filtro['verificada'] == 'S' && !$ctrltarea->getVerificada()) {
                                            continue;
                                        } elseif ($filtro['verificada'] == 'N' && $ctrltarea->getVerificada()) {
                                            continue;
                                        }
                                    }
                                    /* ESTADO */
                                    if ($filtro['estadoId']) {
                                        if ($ctrltarea->getEstado()->getId() != $filtro['estadoId']) {
                                            continue;
                                        }
                                    }
                                    /* USUARIO */
                                    if ($filtro['usuarioId']) {
                                        if ($ctrltarea->getUpdatedBy()->getId() != $filtro['usuarioId']) {
                                            continue;
                                        }
                                    }

                                    // APLICAR FILTRO PERSONAL

                                    $tareas[] = array('id'=>$ctrltarea->getId(),'tipo'=>$ctrltarea->getTipoTarea(),
                                        'tarea'=>$ctrltarea->getTarea()->getNombre(), 'fechahora'=>$ctrltarea->getFechaHora(),
                                        'estado'=>($ctrltarea->getEstado())?$ctrltarea->getEstado()->getNombre():null);
                                }
                            }
                            $ssArray[] = array('id'=>$subsector->getId(),'nombre'=>$subsector->getNombre(),'tareas'=>$tareas);

                        }

                        $dias[] = array('fecha'=>date("Y-m-d", $i),'ssInfo'=>$ssArray);
                        $estado = $this->getEstadoControl($control,$filtro['subSectorId']);

                        // Suma tareas diarias
                        $verifDiaria += $estado['verifDiaria'];
                        $totDiaria += $estado['totDiaria'];
                        // Suma tareas semanales
                        $verifSemanal += $estado['verifSemanal'];
                        $totSemanal += $estado['totSemanal'];
                        // Suma tareas mensuales
                        $verifMensual += $estado['verifMensual'];
                        $totMensual += $estado['totMensual'];
                        $subsectorInfo[date("Y-m-d", $i)] = $estado['subsectores'];
                    }
                    $result[] = array('id'=>$sector->getId(),'nombre'=>$sector->getNombre(),'dias'=>$dias);
                    $porcDiaria = ($totDiaria) ? round( ($verifDiaria*100)/$totDiaria ,2).' %' : '--';
                    $sort = ($totDiaria) ? round( ($verifDiaria*100)/$totDiaria ,2) : 0;
                    $porcSemanal = ($totSemanal) ? round( ($verifSemanal*100)/$totSemanal ,2).' %' : '--';
                    $porcMensual = ($totMensual) ? round( ($verifMensual*100)/$totMensual ,2).' %' : '--';

                    $filtroSectores[] =  array(
                        'id'=>$sector->getId() ,'nombre'=>$sector->getNombre(),
                        'diario'=> $porcDiaria,
                        'sort' => $sort,
                        'semanal'=> $porcSemanal,
                        'mensual'=> $porcMensual,
                        'subsectoresInfo'=>$subsectorInfo) ;
                }

            }

        $ord = usort($filtroSectores, function($a1, $a2) {
            $value1 = $a1['sort'];
            $value2 = $a2['sort'];
            return $value2 - $value1;
        });

        $controles = $this->getControlesByFiltro($filtro, $filtroSectores);
        return $this->render('AdminBundle:Control:informe_tareas.html.twig', array(
                    'sectores' => $sectores, 'subSectores' => $subSectores, 'estados' => $estados, 'usuarios' => $usuarios,
                    'personal' => $personal, 'filtroSectores' => $filtroSectores, 'resultado' => null
        ));
    }

}