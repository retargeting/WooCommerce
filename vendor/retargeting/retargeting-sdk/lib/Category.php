<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-02-19
 * Time: 08:02
 */

namespace RetargetingSDK;

use RetargetingSDK\Helpers\CategoryHelper;
use RetargetingSDK\Helpers\UrlHelper;

/**
 * Class Category
 * @package Retargeting
 */
class Category extends AbstractRetargetingSDK
{
    protected $id = '-1';
    protected $name = '';
    protected $url = '';
    protected $parent = false;
    protected $breadcrumb = [];

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
        $id = $this->getProperFormattedString($id);

        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $name = $this->getProperFormattedString($name);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     * @throws \Exception
     */
    public function setUrl($url)
    {
        $this->url = UrlHelper::validate($url);
    }

    /**
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param int $parent
     */
    public function setParent($parent)
    {
        $parent = (is_bool($parent) && !$parent) || $parent === '' ? false : $parent;

        $this->parent = $parent;
    }

    /**
     * @return array
     */
    public function getBreadcrumb()
    {
        return $this->breadcrumb;
    }

    /**
     * @param array $breadcrumb
     */
    public function setBreadcrumb(array $breadcrumb)
    {
        $breadcrumb = CategoryHelper::validateBreadcrumb($breadcrumb);

        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @param bool $encoded
     * @return array|string
     */
    public function getData($encoded = true)
    {
        $category = [
            'id'            => $this->getId(),
            'name'          => $this->getName(),
            'url'           => $this->getUrl(),
            'parent'        => $this->getParent(),
            'breadcrumb'    => $this->getBreadcrumb()
        ];

        return $encoded ? $this->toJSON($category) : $category;
    }
}