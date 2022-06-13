<?php
namespace GN\AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * GN\AdminBundle\Entity\ConttrolTareaPersonal
 * @ORM\Table(name="control_tarea_personal")
 * @ORM\Entity()
 */
class ControlTareaPersonal
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="GN\AdminBundle\Entity\ControlTarea")
     * @ORM\JoinColumn(name="control_tarea_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $controlTarea;

    /**
     * @ORM\Column(name="nombre_personal", type="string", nullable=true)
     */
    protected $nombrePersonal;

    /**
    * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Personal")
    * @ORM\JoinColumn(name="personal_id", referencedColumnName="id", onDelete="CASCADE")
    */
    protected $personal;

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
     * Set nombrePersonal
     *
     * @param string $nombrePersonal
     * @return ControlTareaPersonal
     */
    public function setNombrePersonal($nombrePersonal)
    {
        $this->nombrePersonal = $nombrePersonal;

        return $this;
    }

    /**
     * Get nombrePersonal
     *
     * @return string
     */
    public function getNombrePersonal()
    {
        return $this->nombrePersonal;
    }

    /**
     * Set controlTarea
     *
     * @param \GN\AdminBundle\Entity\ControlTarea $controlTarea
     * @return ControlTareaPersonal
     */
    public function setControlTarea(\GN\AdminBundle\Entity\ControlTarea $controlTarea = null)
    {
        $this->controlTarea = $controlTarea;

        return $this;
    }

    /**
     * Get controlTarea
     *
     * @return \GN\AdminBundle\Entity\ControlTarea
     */
    public function getControlTarea()
    {
        return $this->controlTarea;
    }

    /**
     * Set personal
     *
     * @param \GN\ConfigBundle\Entity\Personal $personal
     * @return ControlTareaPersonal
     */
    public function setPersonal(\GN\ConfigBundle\Entity\Personal $personal = null)
    {
        $this->personal = $personal;

        return $this;
    }

    /**
     * Get personal
     *
     * @return \GN\ConfigBundle\Entity\Personal
     */
    public function getPersonal()
    {
        return $this->personal;
    }
    
}
