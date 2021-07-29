<?php

namespace RetargetingSDK;

/**
 * Class RecommendationEngine
 * @package RetargetingSDK
 */
class RecommendationEngine
{
    /**
     * @var array
     */
    private $tags = [];

    /**
     * Mark home page
     */
    public function markHomePage()
    {
        $this->addTag(['id' => 'retargeting-recommeng-home-page']);
    }

    /**
     * Mark category page
     */
    public function markCategoryPage()
    {
        $this->addTag(['id' => 'retargeting-recommeng-category-page']);
    }

    /**
     * Mark product page
     */
    public function markProductPage()
    {
        $this->addTag(['id' => 'retargeting-recommeng-product-page']);
    }

    /**
     * Mark checkout page
     */
    public function markCheckoutPage()
    {
        $this->addTag(['id' => 'retargeting-recommeng-checkout-page']);
    }

    /**
     * Mark thank you page
     */
    public function markThankYouPage()
    {
        $this->addTag(['id' => 'retargeting-recommeng-thank-you-page']);
    }

    /**
     * Mark out of stock page
     */
    public function markOutOfStockPage()
    {
        $this->addTag(['id' => 'retargeting-recommeng-out-of-stock-page']);
    }

    /**
     * Mark search page
     */
    public function markSearchPage()
    {
        $this->addTag(['id' => 'retargeting-recommeng-search-page']);
    }

    /**
     * Mark not found page
     */
    public function markNotFoundPage()
    {
        $this->addTag(['id' => 'retargeting-recommeng-not-found-page']);
    }

    /**
     * Generate tags
     *
     * @return string
     */
    public function generateTags()
    {
        return implode('', $this->tags);
    }

    /**
     * Add tag
     *
     * @param array $attrs
     * @param string $type
     */
    private function addTag($attrs = [], $type = 'div')
    {
        if(!empty($attrs))
        {
            $tag = '<' . $type;

            foreach ($attrs AS $attrKey => $attrVal)
            {
                $tag .= (' ' . $attrKey . '="' . $attrVal . '"');
            }

            $tag .= ('></' . $type . '>');

            $this->tags[] = $tag;
        }
    }
}