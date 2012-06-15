<?php

namespace Arvo\UserBundle\Validator;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use \Arvo\UserBundle\Entity\UserManager;

class PasswordChangedValidator extends ConstraintValidator
{
  protected $encoderFactory;
  protected $userManager;

  public function setEncoderFactory(EncoderFactoryInterface $factory)
  {
    $this->encoderFactory = $factory;
  }

  public function setUserManager(UserManager $userManager)
  {
    $this->userManager = $userManager;
  }

  /**
   * Checks if the passed value is valid.
   * The new encoded value must differ from previous
   *
   * @param mixed      $object     The object that should be validated
   * @param Constraint $constraint The constrain for the validation
   *
   * @return Boolean Whether or not the value is valid
   *
   * @throws UnexpectedTypeException if $object is not an object
   */

  public function isValid($object, Constraint $constraint)
  {
    if (!is_object($object)) {
      throw new UnexpectedTypeException($object, 'object');
    }

    $old_password = $object->{$constraint->passwordProperty};
    $new_password = $object->{$constraint->newPasswordProperty};
    $user         = null === $constraint->userProperty ? $object : $object->{$constraint->userProperty};
    $encoder      = $this->encoderFactory->getEncoder($user);

    // if old password is not valid - do not validate
    if (!$encoder->isPasswordValid($user->getPassword(), $old_password, $user->getSalt())) return true;

    // check if new password equals to currently stored
    if ($new_password == $old_password) {
      $this->setMessage($constraint->message);
      return false;
    }

    // check if new password exists in history
    $new_password_hash = $encoder->encodePassword($new_password, $user->getSalt());
    if ($this->userManager->isPasswordRepeated($user, $new_password_hash))
    {
      $this->setMessage($constraint->message);
      return false;
    }

    return true;
  }
}