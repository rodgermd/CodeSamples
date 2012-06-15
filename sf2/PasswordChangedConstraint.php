<?php
namespace Arvo\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordChangedConstraint extends Constraint
{
  public $message = 'The entered password was found in your passwords history! Please enter new password.';
  public $passwordProperty;
  public $userProperty;
  public $newPasswordProperty;

  public function getRequiredOptions()
  {
    return array('passwordProperty', 'userProperty', 'newPasswordProperty');
  }

  public function validatedBy()
  {
    return 'fos_user.validator.password_changed';
  }

  /**
   * {@inheritDoc}
   */
  public function getTargets()
  {
    return self::CLASS_CONSTRAINT;
  }
}