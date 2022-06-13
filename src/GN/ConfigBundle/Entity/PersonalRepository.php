<?php
namespace GN\ConfigBundle\Entity;
use Doctrine\ORM\EntityRepository;

class PersonalRepository extends EntityRepository
{
  public function findAllByTerm($term){
        
        $query = $this->_em->createQuery("Select p.id,p.nombre
            from GN\ConfigBundle\Entity\Personal p 
            where p.nombre like '%".$term."%'
            order by p.nombre")->setMaxResults(20);            

      return $query->getScalarResult();
  }
}
?>