<?php

require_once RTG_TRACKER_DIR . '/vendor/autoload.php';

/**
 * Class WooCommerceRTGFeed
 */
class WooCommerceRTGFeed
{
    /**
     * @var int
     */
    private $currentPage = 1;

    /**
     * @var int
     */
    private $perPage = 100;

    /**
     * @var int
     */
    private $maxPerPage = 500;

    /**
     * @var int
     */
    private $lastPage = 1;

    /**
     * @var null|string
     */
    private $token = null;

    /**
     * @var \RetargetingSDK\CustomersFeed|\RetargetingSDK\ProductFeed
     */
    private $feed;

    private $fileName;
    private $filePath;
    private $fileRule;

    private $marginMeta = null;
    private $wpSeo = null;
    private $check = '';

    /**
     * WooCommerceRTGFeed constructor.
     */
    public function __construct()
    {
        set_time_limit(1800);
        ini_set('max_execution_time',1800);
        
        $this->token = isset( $_GET['token'] ) ? $_GET['token'] : null;

        $this->fileName = 'retargeting.csv';

        $this->filePath = [
            'doStatic' => RTG_TRACKER_DIR . '/' . $this->fileName,
            'doCron' => RTG_TRACKER_DIR . '/' . $this->fileName . '.tmp',
            'doLive' => 'php://output'
        ];

        $this->fileRule = [
            'doStatic' => 'rb',
            'doCron' => 'w+',
            'doLive' => 'w'
        ];
    }

    /**
     * @throws Exception
     */
    public function getCustomers()
    {
        if(!empty($this->token))
        {
            $this->feed = new \RetargetingSDK\CustomersFeed($this->token);

            $query = $this->queryCustomers();

            foreach ($query->get_results() AS $customer)
            {
                $RTGCustomer = new \RetargetingSDK\Customer();

                $customerName = explode(' ', $customer->data->display_name);

                if(!empty($customerName[0]))
                {
                    $RTGCustomer->setFirstName($customerName[0]);

                    unset($customerName[0]);
                }

                if(!empty($customerName))
                {
                    $RTGCustomer->setLastName(implode(' ', $customerName));
                }

                $RTGCustomer->setEmail($customer->data->user_email);
                $RTGCustomer->setStatus($customer->data->user_status > 0);

                $this->feed->addCustomer($RTGCustomer->getData());
            }

            $this->outputCustomers($query->get_total());
        }
        else
        {
            echo 'Token arg is missing or is empty!';
        }
    }

    /**
     * @return WP_User_Query
     */
    private function queryCustomers()
    {
        // Set base query arguments
        $queryArgs = array(
            'role'    => 'customer',
            'orderby' => 'registered',
            'number'  => $this->perPage,
            'limit'   => $this->perPage,
            'offset'  => $this->perPage * ( $this->currentPage - 1 )
        );

        return new WP_User_Query( $queryArgs );
    }

    /**
     * @throws Exception
     */
    private function getProducts()
    {
        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-category-model.php';
        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-product-model.php';

        $this->feed = new \RetargetingSDK\ProductFeed();

        $products = $this->queryProducts()->get_products();

        foreach ($products->products AS $product)
        {
            $RTGProduct = new WooCommerceRTGProductModel($product);

            $productData = $RTGProduct->getData(false);
            $productData['is_in_stock'] = $product->is_in_stock();
            $productData['visibility'] = $product->get_status();

            $this->feed->addProduct(json_encode($productData, JSON_UNESCAPED_UNICODE));
        }
        return json_decode($this->feed->getData());
    }

    /**
     * @return WC_Product_Query
     */
    private function queryProducts()
    {
        // Set base query arguments
        $queryArgs = array(
            'order'   => 'ASC',
            'orderby' => 'ID',
            'limit'   => $this->perPage,
            'offset'  => $this->perPage * ( $this->currentPage - 1 ),
            'paginate' => true,
        );

        return new WC_Product_Query( $queryArgs );
    }

    /**
     * @param $totalItems
     */
    private function outputCustomers($totalItems)
    {
        // Feed URL
        $feedURL = get_site_url() . '?rtg-feed=customers&per_page=' . $this->perPage;

        // Last page
        $this->lastPage = $totalItems > 0 ? ceil($totalItems / $this->perPage) : 1;

        // Previous page
        $prevPage = $this->currentPage - 1;

        if($prevPage < 1)
        {
            $prevPage = $this->currentPage;
        }

        // Next page
        $nextPage = $this->currentPage + 1;

        if($nextPage > $this->lastPage)
        {
            $nextPage = $this->lastPage;
        }

        $this->feed->setCurrentPage($this->currentPage);
        $this->feed->setPrevPage($feedURL . '&page=' . $prevPage);
        $this->feed->setNextPage($feedURL . '&page=' . $nextPage);
        $this->feed->setLastPage($this->lastPage);

        echo $this->feed->getData();
    }

    function getMainCategory($prod){
        $cat = end($prod->category);
        $taxonomy = 'product_cat';
        
        if ( $this->wpSeo === null && class_exists('WPSEO_Primary_Term') ) {
            $this->check = '_yoast_wpseo_primary_' . $taxonomy;
            $this->wpSeo = true;
            
        }else if ( $this->wpSeo === null && class_exists('RankMath')) {
            $this->check = 'rank_math_primary_category';
            $this->wpSeo = true;
        }

        if ($this->wpSeo !== null)
        {
            $primary_cat_id = get_post_meta($prod->id, $this->check, true);
            if(!empty($primary_cat_id)){
                $primary_cat = get_term($primary_cat_id, $taxonomy);
                
				if(!($primary_cat instanceof WP_Error) && !empty($primary_cat->name)){
                    $cat = $primary_cat;
					$cat->id = $primary_cat->term_id;
                }   
            }
        }
        
        return $cat;
    }

    protected function getCost($product){
        if ($this->marginMeta === null) {
            $margin = get_post_meta( $product, '_wc_cog_cost' );
            
            $this->marginMeta = '_wc_cog_cost';

            if (empty($margin)) {
                $margin = get_post_meta( $product, '_alg_wc_cog_cost' );
                $this->marginMeta = '_alg_wc_cog_cost';
            }

            if(empty($margin) ){
                $this->marginMeta = false;
            }
        } else if ($this->marginMeta !== null && $this->marginMeta !== false) {
            $margin = get_post_meta( $product, $this->marginMeta );
        }

        if(empty($margin) ){
            $margin = null;
        }else {
            $margin = is_array($margin) ? $margin[0] : $margin;
            $margin = (int) $margin;

        }

        return $margin;
    }
    
    public function fixURL($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL) && !strpos($url, "%20")) {
            $new_URL = explode("?", $url, 2);
            $newURL = explode("/",$new_URL[0]);
            $m = ['http:','https:'];

            foreach ($newURL as $k=>$v ){
                if ($k === 0 && !in_array($v, $m) || $k > 2) {
                    $newURL[$k] = rawurlencode($v);
                }
            }
    
            if (isset($new_URL[1])) {
                $new_URL[0] = implode("/",$newURL);
                $new_URL[1] = str_replace("&amp;","&",$new_URL[1]);
                return implode("?", $new_URL);
            } else {
                return implode("/",$newURL);
            }
        }
        return $url;
    }

    /**
     * @param $hasProductsInPage
     * @return Generator
     */
    private $isFirst = null;
    protected function getProductData($hasProductsInPage)
    {
        wp_cache_flush();
        while($hasProductsInPage)
        {
            $productsBatch = $this->getProducts()->data;

            if(count($productsBatch) <= 0)
            {
                $hasProductsInPage = false;
            }
            else {
                $this->currentPage++;

                foreach ($productsBatch as $product)
                {
                    
                    $product = json_decode($product);

                    if ($this->isFirst === null ) { 
                        $this->isFirst = $product->id;
                    }else if ($this->isFirst === $product->id){
                        $hasProductsInPage = false;
                        continue;
                    }
                    
                    // Check product stock, url and categories
                    if ( !$product->url ||
                        empty($product->name) ||
                        empty($product->price)||
                        $product->visibility === 'private' ||
						$product->visibility === 'trash'
                    ) {
                        continue;
                    }

                    // Check if product has image
                    $productImg = $this->getProductImage($product);

                    if (empty($productImg)) {
                        continue;
                    }
					$brand = wp_get_post_terms( $product->id, 'pa_brand', array('orderby'=>'name', 'fields' => 'names'));

                    if($brand instanceof WP_Error){
                        $brand = '';
                    }else {
                        $brand = $brand[0];
                    }

                    // Check if product sale is 0
                    $promo = $this->checkPromoPrice($product);
                    $price = $product->price;
                    
                    // Get product images
                    $images = $this->getProductImages($product);

                    // Get product variations
                    $productVariations = $this->getProductVariations($product);
                    
                    $stock = $product->visibility === 'publish' && $product->is_in_stock ?
                        1 : 0;

                    $acp = $this->getCost($product->id);

                    $category = $this->getMainCategory($product);
                    // Get product categories
                    
					$categoryNames = [ ];
					
					$categoryN = wc_get_product_term_ids( $product->id, 'product_cat' );

					foreach ($categoryN as $k => $v) {
						$c = get_term($v, 'product_cat');
						$categoryNames[$v] = $c->name;
					}
					
                    foreach($product->category as $key=>$value){
                        $categoryNames[$value->id] = $value->name;
                    }

					unset($categoryNames[$category->id]);
                    
                    $categoryNames[$category->id] = $category->name;

                    yield [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_url' => $this->fixURL($product->url),
                        'price' => number_format((float) $price, 2, '.', ''),
                        'sale_price' => number_format((float) $promo, 2, '.', ''),
						'brand' => $brand ?? '',
                        'category' => $category->name ?? "Root",
                        'productImg' => $productImg,
                        'productStock' => $stock,
                        'images' => $images,
                        'acq_price' => $acp,
                        'categoryNames' => $categoryNames,
                        'productVariations' => $productVariations
                    ];
                }
            }
        }
    }

    /**
     * @param $productId
     * @return array
     */
    protected function getProductVariations($productId)
    {
        $variable = (new WC_Product_Variable($productId->id))->get_children();

        $productVariations = [];

        foreach ($variable as $value) {
            $single_variation = new WC_Product_Variation($value);

            if ($single_variation->get_stock_quantity() === null ||
                // $single_variation->get_price() === 0 ||
                $single_variation->get_stock_quantity() < 0) {
                continue;
            }

            $acp = $this->getCost($single_variation->get_id());

            $price = empty($single_variation->get_price()) ?
				$productId->price : $single_variation->get_price();

            $price = $this->priceConvert($price);

            $sp = $this->priceConvert($single_variation->get_sale_price());
			
            $sp = empty($sp) || (float) $sp > (float) $price ?
                $this->checkPromoPrice($productId) : $sp;

            $stock = $single_variation->get_stock_quantity();
            $stock = $stock > 0 ? $stock : 0;
                
            $productVariations[] = [
                'code' => $single_variation->get_id(),
                'price' => $price,
                'sale_price' => $sp,
                'stock' => $single_variation->get_stock_quantity(),
                'acq_price' => $acp,
                'in_supplier_stock' => null
            ];
        }
        
        return $productVariations;
    }
    
    public function priceConvert($p)
    {
        return number_format((float) $p, 2, '.', '');
    }

    /**
     * @param $product
     * @return mixed
     */
    protected function getProductImage($product)
    {
        $productImg = $product->img;

        if (!$product->img) {
            $custom_logo_id = get_theme_mod('custom_logo');

            if($custom_logo_id)
            {
                $image = wp_get_attachment_image_src($custom_logo_id, 'full');
                $productImg = $image[0];
            }
            else
            {
                $productImg = false;
            }
        }
        return $this->fixURL($productImg);
    }

    /**
     * @param $product
     */
    protected function checkPromoPrice($product)
    {
        if ( empty($product->promo) ) {
            return $product->price;
        }
        return $product->promo;
    }

    /**
     * @param $product
     * @return mixed
     */
    protected function getProductImages($product)
    {
        $images = [];
        if (is_array($product->images)) {
            foreach ($product->images as $k => $v) {
                $images[] = $this->fixURL($v);
            }
        } else {
            $images[] = $this->fixURL($product->images);
        }

        if (is_array($product->img)) {
            foreach ($product->img as $k => $v) {
                $images[] = $this->fixURL($v);
            }
        } else {
            $images[] = $this->fixURL($product->img);
        }

        return $images;
    }

    /**
     * @param $file
     * @param array $data
     */
    protected function writeCSVData($file, array $data)
    {
        fputcsv($file, array(
            'product id' => $data['product_id'],
            'product name' => $data['product_name'],
            'product url' => $data['product_url'],
            'image url' => $data['productImg'],
            'stock' => $data['productStock'],
            'price' => $data['price'],
            'sale price' => $data['sale_price'],
            'brand' => $data['brand'],
            'category' => $data['category'],
            'extra data' => json_encode([
                'acq_price' => $data['acq_price'],
                'categories' => $data['categoryNames'],
                'margin' => null,
                'media_gallery' => $data['images'],
                'variations' => $data['productVariations'],
                'in_supplier_stock' => null
            ], JSON_UNESCAPED_UNICODE)
        ), ',', '"');
    }

    public function productsCSV($type = 'doLive') {
        
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '8G');
        set_time_limit(0);
        
        if ($type === 'doStatic' && !file_exists($this->filePath[$type])) {
            $type = 'doCron';
        }

        if ( $type !== 'doCron' ) {
            header('Content-Disposition: attachment; filename=' . $this->fileName);
            header("Content-type: text/csv; charset=utf-8");
        }

        $upstream = fopen($this->filePath[$type], $this->fileRule[$type]);

        if (FALSE === $upstream) {
            exit("Failed to open stream to URL, Check File Permission of " . RTG_TRACKER_DIR);
        }

        if ($type !== 'doStatic') {
            fputcsv($upstream, array(
                'product id',
                'product name',
                'product url',
                'image url',
                'stock',
                'price',
                'sale price',
                'brand',
                'category',
                'extra data'
            ), ',', '"');

            foreach ( $this->getProductData( true ) as $data ) {
                $this->writeCSVData( $upstream, $data );
            }
        } else {
            while (!feof($upstream)) {
                echo fread($upstream, filesize($this->filePath[$type]));
            }
        }

        fclose($upstream);

        $type === 'doCron' && copy( $this->filePath[$type], $this->filePath['doStatic'] );

        if ( $type === 'doCron' && !isset($_GET['isCronInternal']) ) {
            header( 'Content-Type: text/json' );
            echo json_encode( ['status' => 'success'] );
        }

        return true ;
    }
}