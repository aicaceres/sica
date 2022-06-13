<?php
namespace GN\ConfigBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * GN\ConfigBundle\Entity\Usuario
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="GN\ConfigBundle\Entity\UsuarioRepository")
 */
class Usuario implements UserInterface
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $username
     * @ORM\Column(name="username", type="string",unique=true)
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @var string $nombre
     * @ORM\Column(name="nombre", type="string")
     * @Assert\NotBlank()
     */
    protected $nombre;

    /**
     * @var string $dni
     * @ORM\Column(name="dni", type="string", length=8, nullable=true)
     *
     */
    protected $dni;

    /**
     * @var string $email
     * @ORM\Column(name="email", type="string")
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @var string $password
     * @ORM\Column(name="password", type="string")
     */
    protected $password;

    /**
     * @ORM\Column(name="activo", type="boolean")
     */
    protected $activo = true;

    /**
     * @ORM\Column(name="fecha_alta", type="datetime")
     */
    protected $fechaAlta;

     /**
     * @ORM\ManyToMany(targetEntity="GN\ConfigBundle\Entity\Sector")
     * @ORM\JoinTable(name="sectores_x_usuario",
     *      joinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id",onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sector_id", referencedColumnName="id",onDelete="cascade")}
     * )
     * 
     */
    private $sectores;

    /**
     * @var string $roles
     * @ORM\Column(name="roles", type="string")
     * @Assert\NotBlank()
     */
    protected $roles='ROLE_SUPERVISOR';

    public function __toString() {
        return $this->nombre;
    }

    public function getTitle(){
        return '" '.$this->username.' - '.$this->nombre.' "';
    }

    public function __construct()
    {
        $this->fechaAlta = new \DateTime();        
    }
   /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get username
     * @return string
     */
    public function getUsername()
    {
        return strtoupper($this->username);
    }

    /**
     * Set nombre
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
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
     * Set dni
     *
     * @param string $dni
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *  password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param string $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = strtoupper($username);
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
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return Usuario
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

        // IMPLEMENTACION DE USERINTERFACE
    public function getRoles()
    {
        $datos = explode(',',$this->roles);
        return $datos;
    }
    public function getSalt(){
        return false;
    }

    public function eraseCredentials(){
        return false;
    }

    public function equals(UserInterface $user){
        return $this->getUsername() == $user->getUsername();
    }

    /**
     * Add sectores
     *
     * @param \GN\ConfigBundle\Entity\Sector $sectores
     * @return Usuario
     */
    public function addSectore(\GN\ConfigBundle\Entity\Sector $sectores)
    {
        $this->sectores[] = $sectores;

        return $this;
    }

    /**
     * Remove sectores
     *
     * @param \GN\ConfigBundle\Entity\Sector $sectores
     */
    public function removeSectore(\GN\ConfigBundle\Entity\Sector $sectores)
    {
        $this->sectores->removeElement($sectores);
    }

    /**
     * Get sectores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSectores()
    {
        return $this->sectores;
    }

}
