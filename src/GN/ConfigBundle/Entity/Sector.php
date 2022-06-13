<?php
namespace GN\ConfigBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

use GN\AdminBundle\Entity\Control; 

/**
 * GN\ConfigBundle\Entity\Sector
 * @ORM\Table(name="sector")
 * @ORM\Entity()
 */
class Sector
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
     * @ORM\Column(name="dias_laborables", type="string")
     */
    protected $diasLaborables = true;

    /**
     * @ORM\OneToMany(targetEntity="GN\ConfigBundle\Entity\SubSector", mappedBy="sector")
     */
    protected $subsectores;

    /**
     * @ORM\OneToMany(targetEntity="GN\AdminBundle\Entity\Control", mappedBy="sector")
     */
    protected $controles;


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
     * Constructor
     */
    public function __construct()
    {
        $this->subsectores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add subsectores
     *
     * @param \GN\ConfigBundle\Entity\SubSector $subsectores
     * @return Sector
     */
    public function addSubsector(\GN\ConfigBundle\Entity\SubSector $subsectores)
    {
        $this->subsectores[] = $subsectores;

        return $this;
    }

    /**
     * Remove subsectores
     *
     * @param \GN\ConfigBundle\Entity\SubSector $subsectores
     */
    public function removeSubsector(\GN\ConfigBundle\Entity\SubSector $subsectores)
    {
        $this->subsectores->removeElement($subsectores);
    }

    /**
     * Get subsectores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubsectores()
    {
        return $this->subsectores;
    }

    /**
     * Add controles
     *
     * @param \GN\AdminBundle\Entity\Control $controles
     * @return Sector
     */
    public function addControl(\GN\AdminBundle\Entity\Control $controles)
    {
        $this->controles[] = $controles;

        return $this;
    }

    /**
     * Remove controles
     *
     * @param \GN\AdminBundle\Entity\Control $controles
     */
    public function removeControl(\GN\AdminBundle\Entity\Control $controles)
    {
        $this->controles->removeElement($controles);
    }

    /**
     * Get controles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getControles()
    {
        return $this->controles;
    }
    
    public function getTareasDiarias(){
        $cant=0;
        foreach ($this->subsectores as $ss) {
            if($ss->getActivo())
                $cant += $ss->getTareasDiarias();             
        }
        return $cant;
    }
    
    /**
     * Set diasLaborables
     *
     * @param string $diasLaborables
     * @return Sector
     */
    public function setDiasLaborables($diasLaborables)
    {
        $this->diasLaborables = $diasLaborables;

        return $this;
    }

    /**
     * Get diasLaborables
     *
     * @return string 
     */
    public function getDiasLaborables()
    {
        return $this->diasLaborables;
    }

}
