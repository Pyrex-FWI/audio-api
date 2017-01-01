<?php

/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace AppBundle\Security\Core\User;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Pyrex\CoreModelBundle\Entity\Deejay;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * User provider for the ORM that loads users given a mapping between resource
 * owner names and the properties of the entities.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class OAuthDeejayUserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $properties = array(
        'identifier' => 'id',
    );

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry    manager registry
     * @param array           $properties  Mapping of resource owners to properties
     * @param string          $class       user entity class to load
     * @param string          $managerName Optional name of the entity manager to use
     */
    public function __construct(ManagerRegistry $registry, array $properties = ['facebook' => 'facebookId'], $class = Deejay::class, $managerName = null)
    {
        $this->em = $registry->getManager($managerName);
        $this->class = $class;
        $this->properties = array_merge($this->properties, $properties);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        /** @var Deejay $user */
        $user = $this->findUser(array('username' => $username));
        if (!$user) {
            throw new UsernameNotFoundException(sprintf("User '%s' not found.", $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        $oAuthData = $response->getUsername();

        $path = $this->properties[$resourceOwnerName];
        if (null === $user = $this->findUser(array($path => $oAuthData))) {
            $user = new Deejay();
            $user->setRoles(['ROLE_USER']);
            $user->setName($response->getRealName());
            $user->setEmail($response->getEmail());
            $user->{'set'.ucfirst($path)}($oAuthData);
            $user->setEnabled(true);
        }
        $user->{'set'.ucfirst($resourceOwnerName).'AccessToken'}($response->getAccessToken());

        $this->em->persist($user);
        $this->em->flush($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $identifier = $this->properties['identifier'];
        if (!$this->supportsClass(get_class($user)) || !$accessor->isReadable($user, $identifier)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $userId = $accessor->getValue($user, $identifier);
        if (null === $user = $this->findUser(array($identifier => $userId))) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $userId));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->class || is_subclass_of($class, $this->class);
    }

    /**
     * @param array $criteria
     *
     * @return Deejay
     */
    protected function findUser(array $criteria)
    {
        if (null === $this->repository) {
            $this->repository = $this->em->getRepository($this->class);
        }

        return $this->repository->findOneBy($criteria);
    }
}
