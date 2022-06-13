<?php
namespace GN\AdminBundle\Entity;
use Doctrine\ORM\EntityRepository;
use GN\ConfigBundle\Controller\UtilsController;
/**
* ControlRepository
*/
class ControlRepository extends EntityRepository {

    public function deleteControlData($rango){
        $query = $this->_em->createQueryBuilder();
        $query->delete('GN\AdminBundle\Entity\Control', 'c')
                ->where("c.fecha>='".$rango['desde']."'")
                ->andWhere("c.fecha<'".$rango['hasta']."'");
        return $query->getQuery()->execute();
    }

    public function getDiariasPorControl($id){
        $query = $this->_em->createQueryBuilder();
        $query->select('COUNT(DISTINCT t.id)')
              ->from('GN\AdminBundle\Entity\ControlTarea', 'ct')
              ->innerJoin('ct.control', 'c')
              ->innerJoin('ct.tarea', 't')
              ->where("t.tipo='D'")
              ->andWhere('c.id='.$id);
        return $query->getQuery()->getSingleScalarResult();
    }

    public function findControlByFecha($fecha,$sectorId=NULL) {
        $cadenaFecha = " c.fecha= '" . $fecha . "'";
        $query = $this->_em->createQueryBuilder();
        $query->select('c')
              ->from('GN\AdminBundle\Entity\Control', 'c')
              ->innerJoin('c.sector', 's')
              ->where($cadenaFecha);
        if($sectorId){
            $query->andWhere('s.id='.$sectorId);
        }

        return $query->getQuery()->getOneOrNullResult();
    }

    public function ultimaFechaVerificacionTarea($tareaId){
        $query = $this->_em->createQueryBuilder();
        $query->select('MAX(ct.fechaHora)')
              ->from('GN\AdminBundle\Entity\ControlTarea', 'ct')
              ->innerJoin('ct.control', 'c')
              ->innerJoin('ct.tarea', 't')
              ->where('t.id='.$tareaId) ;
        return $query->getQuery()->getOneOrNullResult();
    }
    public function tareaVerificadaxFecha($tareaId,$rango){
        $query = $this->_em->createQueryBuilder();
        $query->select('ct')
              ->from('GN\AdminBundle\Entity\ControlTarea', 'ct')
              ->innerJoin('ct.control', 'c')
              ->innerJoin('ct.tarea', 't')
              ->where('t.id='.$tareaId)
              ->andWhere("c.fecha BETWEEN '".$rango['desde']."' AND '".$rango['hasta']."'")
              ->orderBy('ct.fechaHora')  ;
        return $query->getQuery()->getResult();
    }

    public function getCountDistinctTareas($ssId,$tipo,$ctrl=NULL,$rango=NULL){
        $query = $this->_em->createQueryBuilder();
        $query->select('COUNT(DISTINCT t.id)')
              ->from('GN\AdminBundle\Entity\ControlTarea', 'ct')
              ->innerJoin('ct.control', 'c')
              ->innerJoin('ct.tarea', 't')
              ->innerJoin('t.subsector', 'ss')
              ->where('ss.id='.$ssId)
              ->andWhere("t.tipo='".$tipo."'");
        if($ctrl)
            $query->andWhere('c.id='.$ctrl);
        if($rango)
            $query->andWhere("c.fecha BETWEEN '".$rango['desde']."' AND '".$rango['hasta']."'");

        return $query->getQuery()->getOneOrNullResult();
    }

    public function getDistinctTareasSemanales($rango,$ssId){
        $query = $this->_em->createQueryBuilder();
        $query->select('COUNT(DISTINCT t.id)')
              ->from('GN\AdminBundle\Entity\ControlTarea', 'ct')
              ->innerJoin('ct.control', 'c')
              ->innerJoin('ct.tarea', 't')
              ->innerJoin('t.subsector', 'ss')
              ->where('ss.id='.$ssId)
              ->andWhere("c.fecha BETWEEN '".$rango['desde']."' AND '".$rango['hasta']."'")
              ->andWhere("t.tipo='S'");
        return $query->getQuery()->getOneOrNullResult();
    }

    public function getPorcentajeDiarias($controlId){
        $query = $this->_em->createQueryBuilder();
        $query->select('c')
              ->from('GN\AdminBundle\Entity\Control', 'c')
              ->innerJoin('c.controlTareas', 'ct')
              ->innerJoin('ct.tarea', 't')
              ->where('c.id='.$controlId)
              ->andWhere("t.tipo='D'");

        return $query->getQuery()->getResult();

    }

    public function findByCriteria($filtro){
        $query = $this->_em->createQueryBuilder();
        $query->select('c')
              ->from('GN\AdminBundle\Entity\Control', 'c')
              ->innerJoin('c.sector', 's')
              ->orderBy('c.fecha')  ;
        if($filtro['sectorId']){
            $query->andWhere('s.id='.$filtro['sectorId']);
        }
        if ($filtro['desde']) {
            $cadena = " c.fecha >= '" . UtilsController::toAnsiDate($filtro['desde']) . "'";
            $query->andWhere($cadena);
        }
        if ($filtro['hasta']) {
            $cadena = " c.fecha <= '" . UtilsController::toAnsiDate($filtro['hasta']) . "'";
            $query->andWhere($cadena);
        }

        return $query->getQuery()->getResult();

    }

    public function findCtrlTareasCheckAll($id,$ssId,$tipoTarea){
        $query = $this->_em->createQueryBuilder();
        $query->select('ct')
              ->from('GN\AdminBundle\Entity\ControlTarea', 'ct')
              ->innerJoin('ct.control', 'c')
              ->innerJoin('ct.tarea', 't')
              ->innerJoin('t.subsector', 'ss')
              ->where('c.id='.$id)
              ->andWhere("t.tipo='".$tipoTarea."'")
              ->andWhere("ss.id=".$ssId);
        return $query->getQuery()->getResult();
    }


    /*
     * PARA BORRAR
     */

    public function getEstadoSectorByFechas($sector,$fecha){
        $query = $this->_em->createQueryBuilder();
        $query->select("SUM( case when t.tipo='D' then 1 else 0 end as totDiaria")
              ->addSelect("SUM( case when t.tipo='S' then 1 else 0 end as totSemanal")
              ->addSelect("SUM( case when t.tipo='M' then 1 else 0 end as totMensual")
              ->addSelect("SUM( case when (ct.fechaHora is not null and t.tipo='D') then 1 else 0 end as verifDiaria")
              ->addSelect("SUM( case when (ct.fechaHora is not null and t.tipo='S') then 1 else 0 end as verifSemanal")
              ->addSelect("SUM( case when (ct.fechaHora is not null and t.tipo='M') then 1 else 0 end as verifMensual")
              ->from('GN\AdminBundle\Entity\ControlTarea', 'ct')
              ->innerJoin('ct.control', 'c')
              ->innerJoin('ct.tarea', 't')
              ->innerJoin('c.sector', 's')
              ->where('s.id='.$sector)
              ->andWhere("c.fecha BETWEEN '".$fecha['desde']."' AND '".$fecha['hasta']."'");

        return $query->getQuery()->getOneOrNullResult();
    }

    public function getEstadoSubSectorByFechas($subsector,$fecha){
        $query = $this->_em->createQueryBuilder();
        $query->select("SUM( case when t.tipo='D' then 1 else 0 end as totDiaria")
              ->addSelect("SUM( case when t.tipo='S' then 1 else 0 end as totSemanal")
              ->addSelect("SUM( case when t.tipo='M' then 1 else 0 end as totMensual")
              ->addSelect("SUM( case when (ct.fechaHora is not null and t.tipo='D') then 1 else 0 end as verifDiaria")
              ->addSelect("SUM( case when (ct.fechaHora is not null and t.tipo='S') then 1 else 0 end as verifSemanal")
              ->addSelect("SUM( case when (ct.fechaHora is not null and t.tipo='M') then 1 else 0 end as verifMensual")
              ->from('GN\AdminBundle\Entity\ControlTarea', 'ct')
              ->innerJoin('ct.control', 'c')
              ->innerJoin('ct.tarea', 't')
              ->innerJoin('t.subsector', 'ss')
              ->where('ss.id='.$subsector)
              ->andWhere("c.fecha BETWEEN '".$fecha['desde']."' AND '".$fecha['hasta']."'");
        return $query->getQuery()->getOneOrNullResult();
    }

}