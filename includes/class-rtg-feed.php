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

    private $fName = 'retargeting.csv';

    private $doStatic;
    private $doCron;
    private $doLive;

    /**
     * WooCommerceRTGFeed constructor.
     */
    public function __construct()
    {
        $this->token = $_GET['token'] ?? null;

        $this->doStatic = [
            RTG_TRACKER_DIR . '/' . $this->fName,
            'rb'
        ];

        $this->doCron = [
            RTG_TRACKER_DIR . '/' . $this->fName . '.tmp',
            'w+'
        ];

        $this->doLive = [
            'php://output',
            'w'
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

            $this->feed->addProduct(json_encode($productData, JSON_PRETTY_PRINT));
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

    /**
     * @param $hasProductsInPage
     * @return Generator
     */
    protected function getProductData($hasProductsInPage)
    {
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
                    wp_cache_flush();

                    $product = json_decode($product);

                    // Check product stock, url and categories
                    if ( !$product->url ||
                        $product->price == 0 ||
                        !wc_get_product_cat_ids($product->id)
                    ) {
                        continue;
                    }

                    // Check if product has image
                    $productImg = $this->getProductImage($product);
                    if (!$productImg) {
                        continue;
                    }

                    // Check if product sale is 0
                    $this->checkPromoPrice($product);

                    // Get product categories
                    $categoryNames = str_replace(',', ' |', strip_tags(wc_get_product_category_list($product->id)));

                    // Get product images
                    $images = $this->getProductImages($product);

                    // Get product variations
                    $productVariations = $this->getProductVariations($product->id);
                    
                    $stock = $product->visibility === 'publish' && $product->is_in_stock ?
                        1 : 0;

                    yield [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_url' => $product->url,
                        'price' => number_format((float)$product->price, 2, '.', ''),
                        'sale_price' => number_format((float)$product->promo, 2, '.', ''),
                        'category' => $product->category[0]->name,
                        'productImg' => $productImg,
                        'productStock' => $stock,
                        'images' => $images,
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
        $variable = (new WC_Product_Variable($productId))->get_children();
        $productVariations = [];

        foreach ($variable as $value) {
            $single_variation = new WC_Product_Variation($value);

            if ($single_variation->get_stock_quantity() === null) {
                continue;
            }

            $productVariations[] = [
                'id' => $single_variation->get_id(),
                'price' => $single_variation->get_price(),
                'sale price' => $single_variation->get_sale_price(),
                'stock' => $single_variation->get_stock_quantity(),
                'margin' => null,
                'in_supplier_stock' => null
            ];
        }
        return $productVariations;
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
        return $productImg;
    }

    /**
     * @param $product
     */
    protected function checkPromoPrice($product)
    {
        if ($product->promo == 0 || empty($product->promo)) {
            $product->promo = $product->price;
        }
    }

    /**
     * @param $product
     * @return mixed
     */
    protected function getProductImages($product)
    {
        return [
            $product->images,
            $product->img
        ];
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
            'brand' => '',
            'category' => $data['category'],
            'extra data' => json_encode([
                'margin' => null,
                'media_gallery' => $data['images'],
                'categories' => $data['categoryNames'],
                'variations' => $data['productVariations'],
                'in_supplier_stock' => null
            ])
        ), ',', '"');
    }

    public function productsCSV($type = 'doLive') {
        header( 'Content-Disposition: attachment; filename=' . $this->fName );
        header('Content-Type: text/csv');
        $outstream = fopen($this->$type, "w");

        fputcsv($outstream, array(
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

        $hasProductsInPage = true;

        foreach($this->getProductData($hasProductsInPage) as $data)
        {
            $this->writeCSVData($outstream, $data);
        }

        fclose($outstream);
    }








    public function staticProductsCSV()
    {

        if ($type === 'doStatic') {
            if (!file_exists($this->doStatic)) {
                $this->productsCSVCron();
            }
        }

        header('Content-Disposition: attachment; filename=' . $this->fName);
        header('Content-Type: text/csv');

        $filename = RTG_TRACKER_DIR . '/' . $this->fName;

        if (!file_exists($filename)) {
            $this->productsCSVCron(false);
        }

        $outstream = fopen($filename, "rb");

        if (FALSE === $outstream) {
            exit("Failed to open stream to URL");
        }

        $contents = '';
        while (!feof($outstream)) {
            $contents .= fread($outstream, filesize($filename));
        }

        fclose($outstream);

        echo $contents;
    }

    public function productsCSVCron()
    {
        $filename = RTG_TRACKER_DIR . '/' . $this->fName;

        $outstream = fopen($filename.'.tmp', 'w+');

        ob_start();

        fputcsv($outstream, array(
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

        $hasProductsInPage = true;

        foreach($this->getProductData($hasProductsInPage) as $data)
        {
            $this->writeCSVData($outstream, $data);
        }
        
        ob_get_clean();

        fclose($outstream);

        rename ($filename.'.tmp', $filename);

        if(!isset($_GET['isCronInternal'])) {
            header('Content-Type: text/json');
            echo json_encode(['status'=>'succes']);
            return ;
        }
        return ;
    }



    public function outputProductsCSV()
    {

        header('Content-Disposition: attachment; filename=retargeting.csv');
        header('Content-Type: text/csv');
        $outstream = fopen("php://output", "w");

        fputcsv($outstream, array(
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

        $hasProductsInPage = true;

        foreach($this->getProductData($hasProductsInPage) as $data)
        {
            $this->writeCSVData($outstream, $data);
        }

        fclose($outstream);
    }
}