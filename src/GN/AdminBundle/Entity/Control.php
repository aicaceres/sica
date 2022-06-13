<?php
namespace GN\AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use GN\AdminBundle\Entity\ControlTarea;
/**
 * GN\AdminBundle\Entity\Conttrol
 * @ORM\Table(name="control")
 * @ORM\Entity(repositoryClass="GN\AdminBundle\Entity\ControlRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Control
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="fecha", type="date")
     */
    protected $fecha;

    /**
     * @ORM\Column(name="plantel_completo", type="boolean")
     */
    protected $plantelCompleto = true;

    /**
     * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Sector", inversedBy="controles")
     * @ORM\JoinColumn(name="sector_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $sector;

    /**
     * @ORM\OneToMany(targetEntity="GN\AdminBundle\Entity\ControlTarea", mappedBy="control", cascade={"persist", "remove"})
     */
    private $controlTareas;


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
     * Constructor
     */
    public function __construct()
    {
        $this->controlTareas = new \Doctrine\Common\Collections\ArrayCollection();
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
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Control
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Control
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
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Control
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
     * Set sector
     *
     * @param \GN\ConfigBundle\Entity\Sector $sector
     * @return Control
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
     * Add controlTareas
     *
     * @param \GN\AdminBundle\Entity\ControlTarea $controlTareas
     * @return Control
     */
    public function addControlTarea(\GN\AdminBundle\Entity\ControlTarea $controlTareas)
    {
        $controlTareas->setControl($this);
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

    /**
     * Set createdBy
     *
     * @param \GN\ConfigBundle\Entity\Usuario $createdBy
     * @return Control
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
     * Set plantelCompleto
     *
     * @param boolean $plantelCompleto
     * @return Control
     */
    public function setPlantelCompleto($plantelCompleto)
    {
        $this->plantelCompleto = $plantelCompleto;

        return $this;
    }

    /**
     * Get plantelCompleto
     *
     * @return boolean
     */
    public function getPlantelCompleto()
    {
        return $this->plantelCompleto;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Control
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
     * Set updatedBy
     *
     * @param \GN\ConfigBundle\Entity\Usuario $updatedBy
     * @return Control
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

    /*
     * FUNCIONES PERSONALIZADAS
     */

    public function getTareas(){
        $tareas = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($this->controlTareas as $controlTarea) {
            $tareas->add($controlTarea->getTarea()) ;
        }
        return $tareas;
    }

    public function getTareasDiarias($ssId=NULL){
        $cant = 0;
        foreach ($this->controlTareas as $controlTarea) {
            if($ssId){
                if( $ssId != $controlTarea->getTarea()->getSubsector()->getId() )
                    continue;
            }
            if($controlTarea->getTarea()->getTipo()=='D')
                $cant += 1;
        }
        return $cant;
    }

    public function getSubsectoresActivos(){
        $this->subSectores = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($this->sector->getSubSectores() as $subsector) {
            if($subsector->getActivo()){
                $this->subSectores[] = $subsector;
            }
        }
        return $this->subSectores;
    }

    public function getSubsectoresControl(){
        $array = array();
        foreach ($this->controlTareas as $ctrlTarea) {
            $array[] = $ctrlTarea->getTarea()->getSubsector()->getId() ;
        }
        return array_unique($array);
    }

    public function getAlertas(){
        foreach ($this->controlTareas as $item) {
           if($item->getAlerta())
               return true;
        }
        return false;
    }
}