<?php

namespace Arvo\UserBundle\Security\Authentication;

use \Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\LockedException;

class AuthenticationProvider extends UserAuthenticationProvider
{

  private $encoderFactory;
  private $userProvider;

  /**
   * Constructor.
   *
   * @param UserProviderInterface   $userProvider               An UserProviderInterface instance
   * @param UserCheckerInterface    $userChecker                An UserCheckerInterface instance
   * @param string                  $providerKey                The provider key
   * @param EncoderFactoryInterface $encoderFactory             An EncoderFactoryInterface instance
   * @param Boolean                 $hideUserNotFoundExceptions Whether to hide user not found exception or not
   */
  public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $providerKey, EncoderFactoryInterface $encoderFactory, $hideUserNotFoundExceptions = true)
  {
    parent::__construct($userChecker, $providerKey, $hideUserNotFoundExceptions);

    $this->encoderFactory = $encoderFactory;
    $this->userProvider = $userProvider;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
  {
    $currentUser = $token->getUser();
    if ($currentUser instanceof UserInterface) {
      if ($currentUser->getPassword() !== $user->getPassword()) {
        throw new BadCredentialsException('The credentials were changed from another session.');
      }
    } else {
      if (!$presentedPassword = $token->getCredentials()) {
        throw new BadCredentialsException('The presented password cannot be empty.');
      }

      if (!$this->encoderFactory->getEncoder($user)->isPasswordValid($user->getPassword(), $presentedPassword, $user->getSalt())) {
        $this->userProvider->handleWrongPassword($user);
        throw new BadCredentialsException('The presented password is invalid.');
      }
      else {
        $this->userProvider->handleGoodPassword($user);
      }

      if (!$user->isAccountNonLocked()) {
        throw new LockedException(strtr('User account is locked%until%.', array('%until%' => $user->getLockedUntil() ? sprintf(' until %s', $user->getLockedUntil()->format('Y-m-d H:i:s')) : '')), $user);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function retrieveUser($username, UsernamePasswordToken $token)
  {
    $user = $token->getUser();
    if ($user instanceof UserInterface) {
      return $user;
    }

    try {
      $user = $this->userProvider->loadUserByUsername($username);

      if (!$user instanceof UserInterface) {
        throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
      }

      return $user;
    } catch (UsernameNotFoundException $notFound) {
      throw $notFound;
    } catch (\Exception $repositoryProblem) {
      throw new AuthenticationServiceException($repositoryProblem->getMessage(), $token, 0, $repositoryProblem);
    }
  }

}