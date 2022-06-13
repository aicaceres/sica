<?php
namespace GN\ConfigBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UtilsController extends Controller
{
    public function subSectoresAction(Request $request)
    {
        $sector_id = $request->request->get('sector_id');
        $em = $this->getDoctrine()->getManager();
        $subSectores = $em->getRepository('ConfigBundle:SubSector')->findBySectorId($sector_id);
        return new JsonResponse($subSectores);
    }

    ##Location
    public function provinciasAction(Request $request)
    {
        $pais_id = $request->request->get('pais_id');
        $em = $this->getDoctrine()->getManager();
        $provincias = $em->getRepository('ConfigBundle:Provincia')->findByPaisId($pais_id);
        return new JsonResponse($provincias);
    }
    public function localidadesAction(Request $request)
    {
        $provincia_id = $request->request->get('provincia_id');
        $em = $this->getDoctrine()->getManager();
        $localidades = $em->getRepository('ConfigBundle:Localidad')->findByProvinciaId($provincia_id);
        return new JsonResponse($localidades);
    }
    public function codPostalAction(Request $request)
    {
        $localidad_id = $request->request->get('localidad_id');
        $em = $this->getDoctrine()->getManager();
        $localidad = $em->getRepository('ConfigBundle:Localidad')->find($localidad_id);
        return new JsonResponse($localidad->getCodPostal());
    }

    /// PRIMER Y ULTIMO DIA DEL MES
    public static function first_month_day($month,$year) {
      return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    }
    public static function last_month_day($month,$year) {
      $day = date("d", mktime(0,0,0, $month+1, 0, $year));
      return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    }

    /// PRIMER Y ULTIMO DIA DE LA SEMANA
    public static function first_week_day($month,$year) {
      return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    }

    public static function week_first_and_last_day($date) {
      // auxiliar para tarea semanal
      $aux_ini = clone($date);
      $aux_fin = clone($date);
      $week = array('desde' => $aux_ini->modify('Monday this week')->format('Y-m-d'),
                    'hasta' => $aux_fin->modify('Sunday this week')->format('Y-m-d') );
      return $week;
    }
    public static function month_first_and_last_day($date) {
      // auxiliar para tarea mensual
        $aux_ini = clone($date);
        $aux_fin = clone($date);
        $month = array('desde' => $aux_ini->modify('first day of this month')->format('Y-m-d'),
                       'hasta' => $aux_fin->modify('last day of this month')->format('Y-m-d') );
      return $month;
    }



    /// PARA FECHAS
    public static function toAnsiDate($value) {
        if (is_array($value))
            $value = isset($value['text']) ? $value['text'] : null;

        if (strpos($value, '-') === false)
            return $value;

        $date = UtilsController::toArray($value);

        $ansi = $date['Y'] . '-' . $date['m'] . '-' . $date['d'];
        return $ansi;
    }

    public static function toArray($value) {
        if (strpos($value, '-') === false)
            return array('Y' => '1969', 'm' => '01', 'd' => '01');

        $parts = explode('-', $value);
        $years = explode(' ', $parts[2]);

        $date = array('d' => $parts[0], 'm' => $parts[1], 'Y' => $years[0]);

        return $date;
    }

    //// TRUNCADO DE CADENAS
    public static function myTruncate($string, $limit, $break=" ", $pad="…") {
        // return with no change if string is shorter than $limit
        if(strlen($string) <= $limit) return $string;
        // is $break present between $limit and the end of the string?
        if(false !== ($breakpoint = strpos($string, $break, $limit))) {
            if( $breakpoint < strlen($string)-1 ) {
                $string = substr($string, 0, $breakpoint) . $pad;
            }
        } return $string;
    }

}
