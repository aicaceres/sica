<?php
namespace GN\ConfigBundle\Entity;
use Doctrine\ORM\EntityRepository;

/**
* SubSectorRepository
*/
class SubSectorRepository extends EntityRepository {

    public function findBySectorId($sector_id) {
        $query = $this->getEntityManager()->createQuery("
        SELECT SubSector
        FROM ConfigBundle:SubSector SubSector
        LEFT JOIN SubSector.sector sector
        WHERE SubSector.activo = 1
        AND sector.id = :sector_id
        ORDER BY SubSector.nombre
        ")->setParameter('sector_id', $sector_id);

        return $query->getArrayResult();
    }

}