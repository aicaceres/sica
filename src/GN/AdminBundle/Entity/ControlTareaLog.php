<?php
namespace GN\AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * GN\AdminBundle\Entity\ConttrolTareaLog
 * @ORM\Table(name="control_tarea_log")
 * @ORM\Entity()
 */
class ControlTareaLog
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
     * @ORM\JoinColumn(name="control_tarea_id", referencedColumnName="id")
     */
    protected $controlTarea;

    /**
     * @ORM\Column(name="nombre_personal_old", type="string", nullable=true)
     */
    protected $nombrePersonalOld;    
    
    /**
    * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Personal")
    * @ORM\JoinColumn(name="personal_id_old", referencedColumnName="id")
    */
    protected $personalOld;

    /**
    * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Estado")
    * @ORM\JoinColumn(name="estado_id_old", referencedColumnName="id")
    */
    protected $estadoOld;
    
    /**
     * @ORM\Column(name="fecha_hora_old", type="datetime", nullable=true)
     */
    protected $fechaHoraOld;
    
    /**
     * @ORM\Column(name="nombre_personal_new", type="string", nullable=true)
     */
    protected $nombrePersonalNew;    
    
    /**
    * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Personal")
    * @ORM\JoinColumn(name="personal_id_new", referencedColumnName="id")
    */
    protected $personalNew;

    /**
    * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Estado")
    * @ORM\JoinColumn(name="estado_id_new", referencedColumnName="id")
    */
    protected $estadoNew;
    
    /**
     * @ORM\Column(name="fecha_hora_new", type="datetime", nullable=true)
     */
    protected $fechaHoraNew;

    /**
     * @var datetime $created
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var User $createdBy
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;


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
     * Set nombrePersonalOld
     *
     * @param string $nombrePersonalOld
     * @return ControlTareaLog
     */
    public function setNombrePersonalOld($nombrePersonalOld)
    {
        $this->nombrePersonalOld = $nombrePersonalOld;

        return $this;
    }

    /**
     * Get nombrePersonalOld
     *
     * @return string 
     */
    public function getNombrePersonalOld()
    {
        return $this->nombrePersonalOld;
    }

    /**
     * Set fechaHoraOld
     *
     * @param \DateTime $fechaHoraOld
     * @return ControlTareaLog
     */
    public function setFechaHoraOld($fechaHoraOld)
    {
        $this->fechaHoraOld = $fechaHoraOld;

        return $this;
    }

    /**
     * Get fechaHoraOld
     *
     * @return \DateTime 
     */
    public function getFechaHoraOld()
    {
        return $this->fechaHoraOld;
    }

    /**
     * Set nombrePersonalNew
     *
     * @param string $nombrePersonalNew
     * @return ControlTareaLog
     */
    public function setNombrePersonalNew($nombrePersonalNew)
    {
        $this->nombrePersonalNew = $nombrePersonalNew;

        return $this;
    }

    /**
     * Get nombrePersonalNew
     *
     * @return string 
     */
    public function getNombrePersonalNew()
    {
        return $this->nombrePersonalNew;
    }

    /**
     * Set fechaHoraNew
     *
     * @param \DateTime $fechaHoraNew
     * @return ControlTareaLog
     */
    public function setFechaHoraNew($fechaHoraNew)
    {
        $this->fechaHoraNew = $fechaHoraNew;

        return $this;
    }

    /**
     * Get fechaHoraNew
     *
     * @return \DateTime 
     */
    public function getFechaHoraNew()
    {
        return $this->fechaHoraNew;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ControlTareaLog
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set controlTarea
     *
     * @param \GN\AdminBundle\Entity\ControlTarea $controlTarea
     * @return ControlTareaLog
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
     * Set personalOld
     *
     * @param \GN\ConfigBundle\Entity\Personal $personalOld
     * @return ControlTareaLog
     */
    public function setPersonalOld(\GN\ConfigBundle\Entity\Personal $personalOld = null)
    {
        $this->personalOld = $personalOld;

        return $this;
    }

    /**
     * Get personalOld
     *
     * @return \GN\ConfigBundle\Entity\Personal 
     */
    public function getPersonalOld()
    {
        return $this->personalOld;
    }

    /**
     * Set estadoOld
     *
     * @param \GN\ConfigBundle\Entity\Estado $estadoOld
     * @return ControlTareaLog
     */
    public function setEstadoOld(\GN\ConfigBundle\Entity\Estado $estadoOld = null)
    {
        $this->estadoOld = $estadoOld;

        return $this;
    }

    /**
     * Get estadoOld
     *
     * @return \GN\ConfigBundle\Entity\Estado 
     */
    public function getEstadoOld()
    {
        return $this->estadoOld;
    }

    /**
     * Set personalNew
     *
     * @param \GN\ConfigBundle\Entity\Personal $personalNew
     * @return ControlTareaLog
     */
    public function setPersonalNew(\GN\ConfigBundle\Entity\Personal $personalNew = null)
    {
        $this->personalNew = $personalNew;

        return $this;
    }

    /**
     * Get personalNew
     *
     * @return \GN\ConfigBundle\Entity\Personal 
     */
    public function getPersonalNew()
    {
        return $this->personalNew;
    }

    /**
     * Set estadoNew
     *
     * @param \GN\ConfigBundle\Entity\Estado $estadoNew
     * @return ControlTareaLog
     */
    public function setEstadoNew(\GN\ConfigBundle\Entity\Estado $estadoNew = null)
    {
        $this->estadoNew = $estadoNew;

        return $this;
    }

    /**
     * Get estadoNew
     *
     * @return \GN\ConfigBundle\Entity\Estado 
     */
    public function getEstadoNew()
    {
        return $this->estadoNew;
    }

    /**
     * Set createdBy
     *
     * @param \GN\ConfigBundle\Entity\Usuario $createdBy
     * @return ControlTareaLog
     */
    public function setCreatedBy(\GN\ConfigBundle\Entity\Usuario $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \GN\ConfigBundle\Entity\Usuario 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
