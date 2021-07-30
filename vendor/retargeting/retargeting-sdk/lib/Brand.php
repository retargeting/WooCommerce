<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-02-19
 * Time: 08:03
 */

namespace RetargetingSDK;

use RetargetingSDK\Helpers\BrandHelper;

class Brand extends AbstractRetargetingSDK
{
    protected $id;
    protected $name = '';

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param bool $encoded
     * @return array|bool|\stdClass|string|null
     */
    public function getData($encoded = true)
    {
        $brand = BrandHelper::validate([
            'id' => $this->getId(),
            'name' => $this->getProperFormattedString($this->getName())
        ]);

        if(!empty($brand))
        {
            return $encoded ? $this->toJSON($brand) : $brand;
        }
        else
        {
            return null;
        }
    }
}