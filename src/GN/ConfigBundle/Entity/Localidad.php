<?php
namespace GN\ConfigBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GN\ConfigBundle\Entity\Localidad
 * @ORM\Table(name="localidad")
 * @ORM\Entity(repositoryClass="GN\ConfigBundle\Entity\LocalidadRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Localidad
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
     * @var string $codpostal
     * @ORM\Column(name="codpostal", type="string", nullable=true)
     */
    protected $codpostal;
    
    /**
     * @ORM\Column(name="by_default", type="boolean", nullable=true)
     */
    protected $byDefault = false;
    
    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="GN\ConfigBundle\Entity\Provincia", inversedBy="localidades")
     * @ORM\JoinColumn(name="provincia_id", referencedColumnName="id")
     */
    protected $provincia;

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
     * @return Localidad
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
     * Set provincia
     *
     * @param \GN\ConfigBundle\Entity\Provincia $provincia
     * @return Localidad
     */
    public function setProvincia(\GN\ConfigBundle\Entity\Provincia $provincia = null)
    {
        $this->provincia = $provincia;
    
        return $this;
    }

    /**
     * Get provincia
     *
     * @return \GN\ConfigBundle\Entity\Provincia 
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set codpostal
     *
     * @param string $codpostal
     * @return Localidad
     */
    public function setCodpostal($codpostal)
    {
        $this->codpostal = $codpostal;
    
        return $this;
    }

    /**
     * Get codpostal
     *
     * @return string 
     */
    public function getCodpostal()
    {
        return $this->codpostal;
    }

    /**
     * Set byDefault
     *
     * @param boolean $byDefault
     * @return Localidad
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
     * @return Localidad
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
