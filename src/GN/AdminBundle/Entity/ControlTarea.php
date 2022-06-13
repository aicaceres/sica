<?php
namespace GN\AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * GN\AdminBundle\Entity\ConttrolTarea
 * @ORM\Table(name="control_tarea")
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ControlTarea
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="GN\AdminBundle\Entity\Control", inversedBy="controlTareas")
     * @ORM\JoinColumn(name="control_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $control;

    /**
     * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Tarea", inversedBy="controlTareas")
     * @ORM\JoinColumn(name="tarea_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $tarea;

    /**
     * @ORM\OneToMany(targetEntity="GN\AdminBundle\Entity\ControlTareaPersonal", mappedBy="controlTarea", cascade={"persist", "remove"})
     */
    private $personales;

    /**
    * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Estado")
    * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
    */
    protected $estado;

    /**
     * @ORM\Column(name="fecha_hora", type="datetime", nullable=true)
     */
    protected $fechaHora;

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
     * @var datetime $updated
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var User $updatedBy
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

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
     * Set fechaHora
     *
     * @param \DateTime $fechaHora
     * @return ControlTarea
     */
    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    /**
     * Get fechaHora
     *
     * @return \DateTime
     */
    public function getFechaHora()
    {
        return $this->fechaHora;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ControlTarea
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return ControlTarea
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return ControlTarea
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set control
     *
     * @param \GN\AdminBundle\Entity\Control $control
     * @return ControlTarea
     */
    public function setControl(\GN\AdminBundle\Entity\Control $control = null)
    {
        $this->control = $control;

        return $this;
    }

    /**
     * Get control
     *
     * @return \GN\AdminBundle\Entity\Control
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * Set tarea
     *
     * @param \GN\ConfigBundle\Entity\Tarea $tarea
     * @return ControlTarea
     */
    public function setTarea(\GN\ConfigBundle\Entity\Tarea $tarea = null)
    {
        $this->tarea = $tarea;

        return $this;
    }

    /**
     * Get tarea
     *
     * @return \GN\ConfigBundle\Entity\Tarea
     */
    public function getTarea()
    {
        return $this->tarea;
    }

    /**
     * Set createdBy
     *
     * @param \GN\ConfigBundle\Entity\Usuario $createdBy
     * @return ControlTarea
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

    /**
     * Set updatedBy
     *
     * @param \GN\ConfigBundle\Entity\Usuario $updatedBy
     * @return ControlTarea
     */
    public function setUpdatedBy(\GN\ConfigBundle\Entity\Usuario $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \GN\ConfigBundle\Entity\Usuario
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set estado
     *
     * @param \GN\ConfigBundle\Entity\Estado $estado
     * @return ControlTarea
     */
    public function setEstado(\GN\ConfigBundle\Entity\Estado $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \GN\ConfigBundle\Entity\Estado
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /*
     * FUNCIONES PERSONALIZADAS
     */
    // indica que la tarea se verifico en el control. Se usa para distinguir las tareas semanales o mensuales realizadas
    protected $esPropia = TRUE;
    public function setEsPropia($value)
    {
        $this->esPropia = $value;
        return $this;
    }
    public function getEsPropia(){
        return $this->esPropia ;
    }
    // indica que la tarea debería haberse realizado.
    protected $alerta = FALSE;
    public function setAlerta($value)
    {
        $this->alerta = $value;
        return $this;
    }
    public function getAlerta(){
        return $this->alerta ;
    }
    // para cargar la fecha de ultima modificacion si sin verificar.
    protected $ultimaVerificacion = NULL;
    public function setUltimaVerificacion($value)
    {
        $this->ultimaVerificacion = $value;
        return $this;
    }
    public function getUltimaVerificacion(){
        return $this->ultimaVerificacion ;
    }

    public function getTipoTarea(){
        return $this->tarea->getTipo();
    }
    public function getFecha(){
        return $this->fechaHora->format('Y-m-d');
    }
    public function getHora(){
        return $this->fechaHora->format('H:i');
    }
    public function getSubsector() {
        return $this->getTarea()->getSubsector();
    }
    public function getVerificada(){
        return $this->fechaHora ? true : false;
    }

    public function getVencida(){
        $today = strtotime(date("Y-m-d"));
        $vto = strtotime($this->getFechaVencimiento());
        if (!$this->fechaHora && ( $today > $vto ) ) {
            return true;
        }
        return false;
    }

    public function getVerificionAtrasada() {
        /* La verificacion de la tarea se realizó fuera del plazo establecido para el tipo de tarea */
        $atrasada = false;
        if ($this->fechaHora) {
            $fecha_verificado = $this->getFecha();
            $atrasada = ($fecha_verificado > $this->getFechaVencimiento() );
        }
        return $atrasada;
    }
    public function getVerificionAtrasadaReal() {
        /* La verificacion de la tarea se realizó fuera del plazo establecido para el tipo de tarea */
        $atrasada = false;
        if ($this->fechaHora) {
            $fecha_verificado = $this->getCreated()->format('Y-m-d');
            $atrasada = ($fecha_verificado > $this->getFechaVencimiento() );
        }
        return $atrasada;
    }

    public function getFechaVencimiento() {
        $fecha_control = $this->getControl()->getFecha();
        switch ($this->getTipoTarea()) {
            case 'D':
                $fecha_vencimiento = $fecha_control->format("Y-m-d");
                break;
            case 'S':
                // Si es sabado es el mismo dia, sino es el siguiente sabado.
                $aux = clone $fecha_control;
                $fecha_vencimiento = ( $fecha_control->format("w") == 6 ) ? $fecha_control->format("Y-m-d") : $aux->modify('next Saturday')->format("Y-m-d");
                break;
            case 'M':
                // Ultimo dia del mes menos 6 dias. El plazo es hasta 7 días antes de fin del mes.
                $ultimo_dia_mes = mktime(0, 0, 0, $fecha_control->format("n") + 1, 1, $fecha_control->format("Y")) - 1;
                $fecha_vencimiento = date("Y-m-d", (strtotime('-6 day', $ultimo_dia_mes)));
                break;
        }
        return $fecha_vencimiento;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->personales = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add personales
     *
     * @param \GN\AdminBundle\Entity\ControlTareaPersonal $personales
     * @return ControlTarea
     */
    public function addPersonal(\GN\AdminBundle\Entity\ControlTareaPersonal $personales)
    {
        $personales->setControlTarea($this);
        $this->personales[] = $personales;

        return $this;
    }

    /**
     * Remove personales
     *
     * @param \GN\AdminBundle\Entity\ControlTareaPersonal $personales
     */
    public function removePersonal(\GN\AdminBundle\Entity\ControlTareaPersonal $personales)
    {
        $this->personales->removeElement($personales);
    }

    /**
     * Get personales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersonales()
    {
        return $this->personales;
    }
}
