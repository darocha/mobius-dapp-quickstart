<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A user in the Mobius DApp store associated with this DApp
 *
 * @ORM\Entity
 * @ORM\Table(name="mobius_users")
 */
class MobiusUser
{
    /**
     * Internal application ID for this user
     * 
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;

    /**
     * Email address that the user used when signing up for the DApp store
     *
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    protected $mobiusEmail;

    /**
     * Total number of credits the user has in the app
     *
     * @var string
     *
     * @ORM\Column(name="balance", type="integer", nullable=true)
     */
    protected $balance;

    /**
     * @param $mobiusEmail string Email address that the user used when signing up for the DApp store
     */
    public function __construct($mobiusEmail)
    {
        $this->mobiusEmail = $mobiusEmail;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMobiusEmail()
    {
        return $this->mobiusEmail;
    }

    /**
     * @param string $mobiusEmail
     */
    public function setMobiusEmail($mobiusEmail)
    {
        $this->mobiusEmail = $mobiusEmail;
    }

    /**
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param string $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }
}