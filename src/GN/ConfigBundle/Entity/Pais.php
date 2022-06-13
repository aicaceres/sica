<?php
namespace GN\ConfigBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * GN\ConfigBundle\Entity\Pais
 * @ORM\Table(name="pais")
 * @ORM\Entity()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Pais
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
     * @ORM\OneToMany(targetEntity="GN\ConfigBundle\Entity\Provincia", mappedBy="pais")
     */
    protected $provincias;
    
    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->provincias = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Pais
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
     * Add provincias
     *
     * @param \GN\ConfigBundle\Entity\Provincia $provincias
     * @return Pais
     */
    public function addProvincia(\GN\ConfigBundle\Entity\Provincia $provincias)
    {
        $this->provincias[] = $provincias;
    
        return $this;
    }

    /**
     * Remove provincias
     *
     * @param \GN\ConfigBundle\Entity\Provincia $provincias
     */
    public function removeProvincia(\GN\ConfigBundle\Entity\Provincia $provincias)
    {
        $this->provincias->removeElement($provincias);
    }

    /**
     * Get provincias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProvincias()
    {
        return $this->provincias;
    }

    /**
     * Set byDefault
     *
     * @param boolean $byDefault
     * @return Pais
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
     * @return Pais
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
