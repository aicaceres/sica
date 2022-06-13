<?php
namespace GN\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GN\AdminBundle\Entity\Control;
use GN\AdminBundle\Entity\ControlTarea;

class HomeController extends Controller
{
    public function indexAction() {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $em = $this->getDoctrine()->getManager();
            $session = $this->get('session');
            $user = $em->getRepository('ConfigBundle:Usuario')->find($this->get('security.context')->getToken()->getUser()->getId());
            $userSectores = $user->getSectores();
            $color = 'green';
            $sectores = array();

            $parametro = $em->getRepository('ConfigBundle:Parametro')->find(1);
            // guardo en sesion la fecha de inicio
            $session->set('initialDate', $parametro->getFechaInicio()->format("Y-m-d"));

            $initialDate = strtotime($parametro->getFechaInicio()->format("Y-m-d"));
            $firstMonthDay = strtotime( (new \DateTime('first day of this month'))->format('Y-m-d') );
            $firstDay = ($firstMonthDay > $initialDate) ? $firstMonthDay : $initialDate;
            $today = strtotime(date("Y-m-d"));

            foreach ($userSectores as $sector) {
                $totDiaria = $verifDiaria = 0;
                $totSemanal = $verifSemanal = 0;
                $totMensual = $verifMensual = 0;
                $alerta = FALSE;
                $diaslab = json_decode($sector->getDiasLaborables());
                for ($i = $firstDay; $i <= $today; $i += 86400) {

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
                                    $verifDiaria += ($ctrlTarea->getVerificada()) ? 1 : 0;
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
                $color = ($alerta) ? 'red' : 'green';
                $porcDiaria = ($totDiaria) ? round( ($verifDiaria*100)/$totDiaria ,2).' %' : '--';
                $porcSemanal = ($totSemanal) ? round( ($verifSemanal*100)/$totSemanal ,2).' %' : '--';
                $porcMensual = ($totMensual) ? round( ($verifMensual*100)/$totMensual ,2).' %' : '--';

                $sectores[] =  array(
                    'id'=>$sector->getId() ,'nombre'=>$sector->getNombre(),
                    'diario'=> $porcDiaria,
                    'semanal'=> $porcSemanal,
                    'mensual'=> $porcMensual,
                    'color'=>$color ) ;

            }

            return $this->render('AdminBundle:Home:index.html.twig', array(
                        'sectores' => $sectores
                    ));

        } else {
            return $this->redirect($this->generateUrl('login', array()));
        }
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

}