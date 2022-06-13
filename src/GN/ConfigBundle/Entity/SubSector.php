<?php
namespace GN\ConfigBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GN\ConfigBundle\Entity\SubSector
 * @ORM\Table(name="subsector")
 * @ORM\Entity(repositoryClass="GN\ConfigBundle\Entity\SubSectorRepository")
 */
class SubSector
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @var string $nombre
     * @ORM\Column(name="nombre", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    protected $nombre;

    /**
     * @ORM\Column(name="activo", type="boolean")
     */
    protected $activo = true;

    /**
     * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Sector", inversedBy="subsectores")
     * @ORM\JoinColumn(name="sector_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $sector;

    /**
     * @ORM\OneToMany(targetEntity="GN\ConfigBundle\Entity\Tarea", mappedBy="subsector",cascade={"persist", "remove"})
     * @ORM\OrderBy({"orden"="ASC"})
     */
    protected $tareas;

     

    public function __toString() {
        return $this->nombre;
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Sector
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set activo
     * @param boolean $activo
     * @return Usuario
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set sector
     *
     * @param \GN\ConfigBundle\Entity\Sector $sector
     * @return SubSector
     */
    public function setSector(\GN\ConfigBundle\Entity\Sector $sector = null)
    {
        $this->sector = $sector;

        return $this;
    }

    /**
     * Get sector
     *
     * @return \GN\ConfigBundle\Entity\Sector
     */
    public function getSector()
    {
        return $this->sector;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tareas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tareas
     *
     * @param \GN\ConfigBundle\Entity\Tarea $tareas
     * @return SubSector
     */
    public function addTarea(\GN\ConfigBundle\Entity\Tarea $tareas)
    {
        $tareas->setSubsector($this);
        $this->tareas[] = $tareas;
        return $this;
    }

    /**
     * Remove tareas
     *
     * @param \GN\ConfigBundle\Entity\Tarea $tareas
     */
    public function removeTarea(\GN\ConfigBundle\Entity\Tarea $tareas)
    {
        $this->tareas->removeElement($tareas);
    }

    /**
     * Get tareas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTareas()
    {
        return $this->tareas;
    }
    
    public function getTareasDiarias(){
        $cant = 0;
        foreach ($this->tareas as $tarea) {
            $cant += ($tarea->getTipo()=='D')? 1 : 0;
        }
        return $cant;
    }
    
    public function getCantidadTareasPorTipo(){
        $diaria = $semanal = $mensual = 0;
        foreach ($this->tareas as $tarea) {
            switch ($tarea->getTipo()) {
                case 'D':
                    $diaria += 1;
                    break;
                case 'S':
                    $semanal += 1;
                    break;
                case 'M':
                    $mensual += 1;
                    break;
            }
        }
        return array('diaria'=>$diaria,'semanal'=>$semanal,'mensual'=>$mensual);
    }
}
