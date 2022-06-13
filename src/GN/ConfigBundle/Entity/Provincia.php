<?php
namespace GN\ConfigBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GN\ConfigBundle\Entity\Provincia
 * @ORM\Table(name="provincia")
 * @ORM\Entity(repositoryClass="GN\ConfigBundle\Entity\ProvinciaRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false) 
 */
class Provincia
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="by_default", type="boolean", nullable=true)
     */
    protected $byDefault = false;

    /**
     * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Pais", inversedBy="provincias")
     * @ORM\JoinColumn(name="pais_id", referencedColumnName="id")
     */
    protected $pais;

    /**
     * @ORM\OneToMany(targetEntity="GN\ConfigBundle\Entity\Localidad", mappedBy="provincia")
     */
    protected $localidades;
    
    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->localidades = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function __toString() {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     * @return Provincia
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set pais
     *
     * @param \GN\ConfigBundle\Entity\Pais $pais
     * @return Provincia
     */
    public function setPais(\GN\ConfigBundle\Entity\Pais $pais = null)
    {
        $this->pais = $pais;
    
        return $this;
    }

    /**
     * Get pais
     *
     * @return \GN\ConfigBundle\Entity\Pais 
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Add localidades
     *
     * @param \GN\ConfigBundle\Entity\Localidad $localidades
     * @return Provincia
     */
    public function addLocalidade(\GN\ConfigBundle\Entity\Localidad $localidades)
    {
        $this->localidades[] = $localidades;
    
        return $this;
    }

    /**
     * Remove localidades
     *
     * @param \GN\ConfigBundle\Entity\Localidad $localidades
     */
    public function removeLocalidade(\GN\ConfigBundle\Entity\Localidad $localidades)
    {
        $this->localidades->removeElement($localidades);
    }

    /**
     * Get localidades
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLocalidades()
    {
        return $this->localidades;
    }

    /**
     * Set byDefault
     *
     * @param boolean $byDefault
     * @return Provincia
     */
    public function setByDefault($byDefault)
    {
        $this->byDefault = $byDefault;

        return $this;
    }

    /**
     * Get byDefault
     *
     * @return boolean 
     */
    public function getByDefault()
    {
        return $this->byDefault;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Provincia
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
}
