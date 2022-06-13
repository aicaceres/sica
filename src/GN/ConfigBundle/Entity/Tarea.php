<?php
namespace GN\ConfigBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GN\ConfigBundle\Entity\Tarea
 * @ORM\Table(name="tarea")
 * @ORM\Entity()
 */
class Tarea
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
     * @var string $tipo
     * @ORM\Column(name="tipo", type="string",length=1, nullable=false)
     */
    protected $tipo="D";

    /**
     * @ORM\Column(name="orden", type="integer")
     */
    protected $orden = 1;

    /**
     * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\SubSector", inversedBy="tareas")
     * @ORM\JoinColumn(name="subsector_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $subsector;

    /**
     * @ORM\OneToMany(targetEntity="GN\AdminBundle\Entity\ControlTarea", mappedBy="tarea", cascade={"persist"})
     */
    private $controlTareas;


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
     * @return Tarea
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
     * Set tipo
     *
     * @param string $tipo
     * @return Tarea
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set orden
     *
     * @param boolean $orden
     * @return Tarea
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return boolean
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set subsector
     *
     * @param \GN\ConfigBundle\Entity\SubSector $subsector
     * @return Tarea
     */
    public function setSubsector(\GN\ConfigBundle\Entity\SubSector $subsector = null)
    {
        $this->subsector = $subsector;

        return $this;
    }

    /**
     * Get subsector
     *
     * @return \GN\ConfigBundle\Entity\SubSector
     */
    public function getSubsector()
    {
        return $this->subsector;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->controlTareas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add controlTareas
     *
     * @param \GN\AdminBundle\Entity\ControlTarea $controlTareas
     * @return Tarea
     */
    public function addControlTarea(\GN\AdminBundle\Entity\ControlTarea $controlTareas)
    {
        $this->controlTareas[] = $controlTareas;

        return $this;
    }

    /**
     * Remove controlTareas
     *
     * @param \GN\AdminBundle\Entity\ControlTarea $controlTareas
     */
    public function removeControlTarea(\GN\AdminBundle\Entity\ControlTarea $controlTareas)
    {
        $this->controlTareas->removeElement($controlTareas);
    }

    /**
     * Get controlTareas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getControlTareas()
    {
        return $this->controlTareas;
    }
}
