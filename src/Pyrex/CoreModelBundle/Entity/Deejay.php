<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

/**
 * @author: Pyrex-Fwi
 */
namespace Pyrex\CoreModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Deejay
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pyrex\CoreModelBundle\Repository\DeejayRepository")
 * @UniqueEntity("email")
 * @UniqueEntity("name")
 */
class Deejay implements \Symfony\Component\Security\Core\User\AdvancedUserInterface, \Serializable
{
    const ROLE_USER     = 'ROLE_USER';
    const ROLE_ADMIN    = 'ROLE_ADMIN';

    use TimestampableEntity;
    //use SoftDeleteableEntity;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=128, unique=true, nullable=true, options={"default":null})
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var array
     * @Assert\Callback({"Pyrex\CoreModelBundle\Entity\Deejay", "validRoles"})
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $plainPassword = '';
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", options={"default"=null}, nullable=true)
     */
    private $expirationDate;
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled = false;

    /**
     * @var string
     * @Gedmo\Slug(fields={"name"}, unique=true)
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string", options={"default"=null}, nullable=true)
     */
    private $facebookId;
    /**
     * @var string
     *
     * @ORM\Column(type="string", options={"default"=null}, nullable=true)
     */
    private $googleId;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=40, unique=true, nullable=true)
     * @var
     */
    private $activationToken;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $activationTokenDate;

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
     * @return Album
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
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param sstring $role
     * @return $this
     */
    public function addRole($role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->name;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = '';
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Deejay
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     * @return Deejay
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->name,
            $this->password,
            $this->enabled,
            // $this->salt,
        ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->name,
            $this->password,
            $this->enabled,
            ) = unserialize($serialized);
    }


    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        if ($this->getExpirationDate()) {
            return $this->getExpirationDate() < new \DateTime('now') ?  false : true;
        }

        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $expirationDate
     * @return Deejay
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return array
     */
    public static function getAllowedRoles()
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_USER,
        ];
    }

    public static function validRoles($object, ExecutionContextInterface $executionContext, $payload)
    {
        foreach ($object as $role) {
            if (!in_array($role, self::getAllowedRoles())) {
                $executionContext->buildViolation('Le role de l\'utilisateurs est incorrect')
                    ->atPath('roles')
                    ->addViolation();
            }
        }
    }

    public function setSalt($string)
    {
        $this->salt = $string;
    }

    /**
     * @return mixed
     */
    public function getActivationToken()
    {
        return $this->activationToken;
    }

    /**
     * @param mixed $activationToken
     * @return Deejay
     */
    public function setActivationToken($activationToken)
    {
        $this->activationToken = $activationToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getActivationTokenDate()
    {
        return $this->activationTokenDate;
    }

    /**
     * @param \DateTime $activationTokenDate
     * @return Deejay
     */
    public function setActivationTokenDate($activationTokenDate)
    {
        $this->activationTokenDate = $activationTokenDate;

        return $this;
    }

}
