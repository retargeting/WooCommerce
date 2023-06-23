<?php
/**
 * 2014-2020 Retargeting BIZ SRL
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@retargeting.biz so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Retargeting SRL <info@retargeting.biz>
 * @copyright 2014-2020 Retargeting SRL
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Class WooCommerceRTGCategoryModel
 */
class WooCommerceRTGProductModel extends \RetargetingSDK\Product
{
    /**
     * WooCommerceRTGProductModel constructor.
     * @param null $product
     * @throws Exception
     */
    public function __construct($product = null)
    {
        if (!$product instanceof WC_Product)
        {
            $productId = get_the_ID();

            if(!empty($productId))
            {
                $product = wc_get_product($productId);
            }
        }

        if ($product instanceof WC_Product && !empty($product->get_id()))
        {
            $this->_setProductData($product);
        }
    }

    /**
     * @param WC_Product $product
     * @throws Exception
     */
    private function _setProductData($product)
    {
        $this->setId($product->get_id());
        $this->setName($product->get_name());
        $this->setUrl($product->get_permalink());
        $this->setWeight($product->get_weight());
        $this->_setProductPrices($product);
        $this->_setProductImages($product->get_image_id(), $product->get_gallery_image_ids());
        $this->_setProductCategories($product->get_category_ids());

        $this->setInventory([
            'variations' => false,
            'stock'      => $product->is_in_stock()
        ]);
    }

    /**
     * @param WC_Product $product
     * @throws Exception
     */
    private function _setProductPrices($product)
    {
        if ($product->is_type([ 'simple', 'external' ]))
        {
            if(wc_tax_enabled()){
                $regularPrice = (float) wc_get_price_including_tax( $product, array('price' => $product->get_regular_price() ) );
                $salePrice    = (float) wc_get_price_including_tax( $product, array('price' => $product->get_sale_price() ) );
            } else {
                $regularPrice = (float) $product->get_regular_price();
                $salePrice    = (float) $product->get_sale_price();
            }

            $this->setPrice((float) number_format($regularPrice, 2, '.', ''));

            if($regularPrice != $salePrice)
            {
                $this->setPromo((float) number_format($salePrice, 2, '.', ''));
            }
        }
        elseif ($product->is_type('variable'))
        {
            if(wc_tax_enabled()){
                $regularPrice = (float) wc_get_price_including_tax( $product, array('price' => $product->get_price()) );
                $salePrice    = 0;
            } else {
                $regularPrice = (float) $product->get_price();
                $salePrice    = 0;
            }

            $product    = new WC_Product_Variable($product->get_id());
            $variations = $product->get_available_variations();

            if (is_array($variations) && !empty($variations[0]['display_price']) && !empty($variations[0]['display_regular_price']))
            {
                $variationRegularPrice = (float)$variations[0]['display_regular_price'];
                $variationSalePrice    = (float)$variations[0]['display_price'];

                if (!empty($variationRegularPrice) && !empty($variationSalePrice))
                {
                    $regularPrice = $variationRegularPrice;
                    $salePrice    = $variationSalePrice;
                }
            }

            $this->setPrice(number_format($regularPrice, 2, '.', ''));

            if ($salePrice > 0)
            {
                $this->setPromo(number_format((float) $salePrice, 2, '.', ''));
            }
        }
        else
        {
            $regularPrice = (float) $product->get_price();

            $this->setPrice(number_format($regularPrice, 2, '.', ''));
        }
    }

    /**
     * @param $imageId
     * @param $galleryImageIds
     * @throws Exception
     */
    private function _setProductImages($imageId, $galleryImageIds)
    {
        $featureImageUrl = $this->_getProductImageURL($imageId);

        if (!empty($featureImageUrl))
        {
            $this->setImg($featureImageUrl);
        }

        if (is_array($galleryImageIds) && !empty($galleryImageIds))
        {
            $galleryImages = [];

            foreach ($galleryImageIds AS $galleryImageId)
            {
                $galleryImageUrl = $this->_getProductImageURL($galleryImageId);

                if (!empty($galleryImageUrl))
                {
                    $galleryImages[] = $galleryImageUrl;
                }
            }

            if (!empty($galleryImages))
            {
                $this->setAdditionalImages($galleryImages);
            }
        }
    }

    /**
     * @param $imageId
     * @return mixed|null
     */
    private function _getProductImageURL($imageId)
    {
        if (!empty($imageId))
        {
            $image = wp_get_attachment_image_src($imageId, 'medium_large');

            if (is_array($image) && !empty($image['0']))
            {
                return $image[0];
            }
        }

        return null;
    }

    /**
     * @param $categoryIds
     * @throws Exception
     */
    private function _setProductCategories($categoryIds)
    {
        if (is_array($categoryIds) && !empty($categoryIds))
        {
            $categories = [];

            foreach ($categoryIds AS $categoryId)
            {
                $RTGCategory = new WooCommerceRTGCategoryModel($categoryId);

                if (!empty($RTGCategory->getId()))
                {
                    $categories[] = $RTGCategory->getData(false);
                }
            }

            if (!empty($categories))
            {
                $this->setCategory($categories);
            }
        }
    }
}