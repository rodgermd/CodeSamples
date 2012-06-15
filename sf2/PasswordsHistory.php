<?php

namespace Arvo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Arvo\UserBundle\Entity\PasswordsHistory
 *
 * @ORM\Table(
 *  uniqueConstraints={@ORM\UniqueConstraint(name="user_password_unique_idx", columns={"user_id", "password"})}
 * )
 * @ORM\Entity(repositoryClass="Arvo\UserBundle\Entity\PasswordsHistoryRepository")
 */
class PasswordsHistory
{
  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var integer $user_id
   *
   * @ORM\Column(name="user_id", type="integer")
   */
  private $user_id;

  /**
   * @var string $password
   *
   * @ORM\Column(name="password", type="string", length=255)
   */
  private $password;

  /**
   * @var datetime $created_at
   *
   * @ORM\Column(name="created_at", type="datetime")
   * @Gedmo\Timestampable(on="create")
   */
  private $created_at;

  /**
   * @var User $user
   * @ORM\ManyToOne(targetEntity="User", inversedBy="passwords")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   */
  protected $user;


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
   * Set user_id
   *
   * @param integer $userId
   */
  public function setUserId($userId)
  {
    $this->user_id = $userId;
  }

  /**
   * Get user_id
   *
   * @return integer
   */
  public function getUserId()
  {
    return $this->user_id;
  }

  /**
   * Set password
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
   * Set created_at
   *
   * @param datetime $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->created_at = $createdAt;
  }

  /**
   * Get created_at
   *
   * @return datetime
   */
  public function getCreatedAt()
  {
    return $this->created_at;
  }

  /**
   * Gets related user
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * Sets related user
   * @param User $user
   * @return PasswordsHistory
   */
  public function setUser(User $user)
  {
    $this->setUserId($user->getId());
    $this->user = $user;

    return $this;
  }
}