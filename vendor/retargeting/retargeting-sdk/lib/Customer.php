<?php

namespace RetargetingSDK;

use RetargetingSDK\Helpers\EmailHelper;

/**
 * Class Customer
 * @package RetargetingSDK
 */
class Customer extends AbstractRetargetingSDK
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var boolean
     */
    protected $status;

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $this->getProperFormattedString($firstName);
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $this->getProperFormattedString($lastName);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @throws \Exception
     */
    public function setEmail($email)
    {
        $this->email = EmailHelper::validate($email);
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $this->getProperFormattedString($phone);
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = is_bool($status) ? $status : false;
    }

    /**
     * @param bool $encoded
     * @return array|string
     */
    public function getData($encoded = true)
    {
        $data = [
            'firstname' => $this->getFirstName(),
            'lastname'  => $this->getLastName(),
            'email'     => $this->getEmail(),
            'phone'     => $this->getPhone(),
            'status'    => $this->getStatus()
        ];

        return $encoded ? $this->toJSON($data) : $data;
    }
}