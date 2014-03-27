<?php
/**
 * User: leeparsons
 * Date: 26/12/2013
 * Time: 20:21
 */
 
class TppStoreModelCart extends TppStoreAbstractModelBase {

    private $_cart = array();

    private $exchange_rates = array();

    public function getSeoTitle()
    {
        return 'Cart';
    }

    public function getSeoDescription()
    {
        return 'Your Cart';
    }

    public function __construct()
    {
        //get the currency exchange rates!
        TppCacher::getInstance()->setCacheName('currency-exchange');
        TppCacher::getInstance()->setCachePath('cart/usd');

        if (false !== ($currency_exchange_rates = TppCacher::getInstance()->readCache('3600'))) {
            $this->exchange_rates = $currency_exchange_rates;
        } else {
            if (!class_exists('TppStoreAdapterPaypal')) {
                include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';
            }
            $p = new TppStoreAdapterPaypal();
            $p->generateExchangeRates();
            $this->exchange_rates = TppCacher::getInstance()->readCache('3600');
        }
    }


    public function add(TppStoreModelProduct $product, $quantity = 1)
    {

        $this->load();

        $quantity = intval($quantity);

        $option_id = filter_input(INPUT_POST, 'product_option', FILTER_SANITIZE_NUMBER_INT);


        if (!isset($this->_cart['stores'][$product->store_id]['products'][$product->product_id])) {

            $this->_cart['stores'][$product->store_id]['products'][$product->product_id] = new TppStoreModelCartItem();
            $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->setData($product);

            $image = $product->getMainImage('main');

            $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->setData(array(
                'product_image'     =>  isset($image['main'])?$image['main']:null,
                'store_email'       =>  $product->getStoreEmail()
            ));

            $this->_cart['stores'][$product->store_id]['currency'] = $product->currency;

            //determine if options are being added?

            if (intval($option_id) < 1) {
                if (intval($product->unlimited) == 0 && $quantity > $product->quantity_available) {
                    $quantity = $product->quantity_available;
                    TppStoreMessages::getInstance()->addMessage('message', array(
                        'cart'   =>  'We are sorry, the product: ' . $product->product_title . ' is extremely popular and the quantity you ordered is no longer available. Your cart has been updated accordingly'
                    ));
                } else {
                    $quantity += $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->order_quantity;
                }

                $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->setData(array(
                    'order_quantity'        =>  $quantity,
                    'tax_rate'              =>  $product->tax_rate,
                    'price'                 =>  $product->price,
                    'price_includes_tax'    =>  $product->price_includes_tax,
                    'quantity_available'    =>  $product->quantity_available,
                    'unlimited'             =>  $product->unlimited
                ));



            } else {

                //a product option is being added, so determine if it can be added...
//                $product_option = new TppStoreModelProductOptions();
//                $product_option->setData(array(
//                    'option_id' =>  $option_id
//                ))->getOptionById();

                /*
                 * product options
                 */
                $this->addOption($product, $option_id, $quantity);
            }


        } else {

            //determine if the quantity available is still correct
            $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->setData(array(
                'order_quantity'        =>  $quantity,
                'tax_rate'              =>  $product->tax_rate,
                'price'                 =>  $product->price,
                'price_includes_tax'    =>  $product->price_includes_tax,
                'quantity_available'    =>  $product->quantity_available,
                'unlimited'             =>  $product->unlimited
            ));

            /*
             * apply the product options if set
             */


            //$option_id = $product->getProductOptionsModel()->option_id;

            if ($option_id > 0) {

                /*
                 * product options
                 */
                $this->addOption($product, $option_id, $quantity);

            } else {
                if (intval($product->unlimited) == 0 && ($quantity + $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->order_quantity) > $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->quantity_available) {
                    $quantity = $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->quantity_available;
                    TppStoreMessages::getInstance()->addMessage('message', array(
                        'cart'   =>  'We are sorry, the product: ' . $product->product_title . ' is extremely popular and the quantity you ordered is no longer available. Your cart has been updated accordingly'
                    ));
                } else {
                    $quantity += $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->order_quantity;
                }

                $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->setData(array(
                    'order_quantity'        =>  $quantity,
                    'tax_rate'              =>  $product->tax_rate,
                    'price'                 =>  $product->price,
                    'price_includes_tax'    =>  $product->price_includes_tax,
                    'quantity_available'    =>  $product->quantity_available,
                    'unlimited'             =>  $product->unlimited
                ));

            }


        }


        $this->_calculateTotals();

        $this->save();

    }

    public function update(TppStoreModelProduct $product, $quantity = 0)
    {

        $this->load();

        if (isset($this->_cart['stores'][$product->store_id]['products'][$product->product_id])) {
            $quantity = intval($quantity);

            if ($quantity <= 0) {
                $this->removeItem($product->product_id, $product->store_id);
                return;
            } elseif (intval($product->unlimited) == 0 && $product->quantity_available < $quantity) {
                $quantity = $product->quantity_available;
                TppStoreMessages::getInstance()->addMessage('message',
                    array(
                        'cart'   =>  'We are sorry, the product: ' . $product->product_title . ' is extremely popular and the quantity you ordered is no longer available. Your cart has been updated accordingly'
                    ));
            }

            $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->setData(array(
                'order_quantity'        =>  $quantity,
                'tax_rate'              =>  $product->tax_rate,
                'price'                 =>  $product->price,
                'price_includes_tax'    =>  $product->price_includes_tax,
                'quantity_available'    =>  $product->quantity_available,
                'unlimited'             =>  $product->unlimited
            ));

            $this->_calculateTotals();
            $this->save();
        } else {
            $this->add($product, $quantity);
        }
    }

    private function addOption($product, $option_id, $quantity)
    {
        if (!isset($this->_cart['stores'][$product->store_id]['products'][$product->product_id]->options[$option_id])) {
            $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->options[$option_id] = array(
                'quantity_available'    =>  $product->getProductOptionsModel()->option_quantity_available,
                'price'                 =>  $product->getProductOptionsModel()->option_price
            );
        } else {
            $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->options[$option_id]['quantity_available'] = $product->getProductOptionsModel()->option_quantity_available;
        }

        if ($this->_cart['stores'][$product->store_id]['products'][$product->product_id]->options[$option_id]['quantity_available'] < $quantity) {
            $quantity = $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->options[$option_id]['quantity_available'];

            TppStoreMessages::getInstance()->addMessage('message', array(
                'cart'   =>  'We are sorry, the product: ' . $product->product_title . ' is extremely popular and the quantity you ordered is no longer available. Your cart has been updated accordingly'
            ));

            //$product->getProductOptionsModel()->option_price
            //$option_id
            //$product->getProductOptionsModel()->option_name
        } else {
            $quantity += $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->options[$option_id]['order_quantity'];
        }

        $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->options[$option_id]['order_quantity'] = $quantity;

    }

    private function _calculateTotals()
    {

/*
 * The product should display price with tax included.
 * The discount should include tax
 *
 *
 */


        $total = 0;
        if (!empty($this->_cart['stores'])) {

            if (!class_exists('TppStoreAdapterDiscount')) {
                include TPP_STORE_PLUGIN_DIR . 'adapters/discounts/discount.php';
            }

            foreach ($this->_cart['stores'] as $store_id => $store_products) {


                //sub total should not contain tax information
                $this->_cart['stores'][$store_id]['sub_total'] = 0;
                $this->_cart['stores'][$store_id]['tax'] = 0;
                $this->_cart['stores'][$store_id]['discount'] = TppStoreAdapterDiscount::getInstance()->getUserDiscountByStore($store_products['products']);
                $this->_cart['stores'][$store_id]['total'] = 0;

                $this->_cart['stores'][$store_id]['recalculated_total'] = 0;

                foreach ($store_products['products'] as $product) {

                    $this->_cart['stores'][$store_id]['sub_total'] += $product->getPriceWithoutTax($product->order_quantity);
                    $this->_cart['stores'][$store_id]['tax'] += $product->getTax($product->order_quantity);



                    //the store total should be the net amount to pay
                    /*test orders
                    $this->_cart['stores'][$store_id]['total'] =
                        $this->_cart['stores'][$store_id]['sub_total']
                        +
                        $this->_cart['stores'][$store_id]['tax']
                        -
                        $this->_cart['stores'][$store_id]['discount'];
                    */

                    //discount should be on the discounted total
                    $this->_cart['stores'][$store_id]['products'][$product->product_id]->discount = TppStoreAdapterDiscount::getInstance()->getUserDiscountByProduct($product) * (1 + ($product->tax_rate/100));
//                    $this->_cart['stores'][$store_id]['products'][$product->product_id]->discount_with_tax = $this->_cart['stores'][$store_id]['products'][$product->product_id]->discount;
//                    $this->_cart['stores'][$store_id]['products'][$product->product_id]->discount_without_tax = TppStoreAdapterDiscount::getInstance()->getUserDiscountByProduct($product);

//                    $this->_cart['stores'][$store_id]['products'][$product->product_id]->tax_with_discount =
//                        ($this->_cart['stores'][$store_id]['products'][$product->product_id]->price - $this->_cart['stores'][$store_id]['products'][$product->product_id]->discount) *  ($this->_cart['stores'][$store_id]['products'][$product->product_id]->tax_rate / 100);
//                    $this->_cart['stores'][$store_id]['products'][$product->product_id]->tax_without_discount =
//                        ($this->_cart['stores'][$store_id]['products'][$product->product_id]->price) *
//                         ($this->_cart['stores'][$store_id]['products'][$product->product_id]->tax_rate / 100);


                    $this->_cart['stores'][$store_id]['total'] +=
                        $this->_cart['stores'][$store_id]['products'][$product->product_id]->getLineItemFormattedTotal(false, true);



//                    if ($product->price_includes_tax == 1) {
//                        $this->_cart['stores'][$store_id]['sub_total'] += $product->order_quantity * $product->price * (1-($product->tax_rate/100));
//
//                        //tax is only applicable on discounted prices
//                        $this->_cart['stores'][$store_id]['tax'] +=
//
//                            ($product->price - $this->_cart['stores'][$store_id]['products'][$product->product_id]->discount) *
//
//                            $product->order_quantity;
//
//                        $this->_cart['stores'][$store_id]['discount'] += $this->_cart['stores'][$store_id]['products'][$product->product_id]->discount * $product->order_quantity;
//
//
//                    } else {
//                        $this->_cart['stores'][$store_id]['sub_total'] += $product->order_quantity * $product->price;
//
//                        //tax is only applicable on discounted prices
//                        $this->_cart['stores'][$store_id]['tax'] +=
//
//                            ($product->price - $this->_cart['stores'][$store_id]['products'][$product->product_id]->discount) *
//
//                            $product->order_quantity * (1 + ($product->tax_rate/100));
//
//                        $this->_cart['stores'][$store_id]['discount'] += $this->_cart['stores'][$store_id]['products'][$product->product_id]->discount * $product->order_quantity * (1 + ($product->tax_rate/100));
//
//                    }
//
//
//                    //TODO: fix options!
//                    if (is_array($product->options) && !empty($product->options)) {
//                        foreach ($product->options as $option) {
//                            $this->_cart['stores'][$store_id]['tax'] += $option['order_quantity'] * $option['price'] * $product->tax_rate / 100;
//                            $this->_cart['stores'][$store_id]['sub_total'] += $option['order_quantity'] * $option['price'];
//                        }
//                    }
                }

                //$this->_cart['stores'][$store_id]['discount'] = TppStoreAdapterDiscount::getInstance()->getUserDiscountByStore($store_products['products']);


//                $this->_cart['stores'][$store_id]['total'] = $this->_cart['stores'][$store_id]['tax'] + $this->_cart['stores'][$store_id]['sub_total'] - $this->_cart['stores'][$store_id]['discount'];

                $total += $this->_cart['stores'][$store_id]['total'];
            }
        }



        $this->_cart['total'] = $total;
    }

    public function getItemCount()
    {

        $count = 0;

        if (isset($this->_cart['stores'])) {

            foreach ($this->_cart['stores'] as $store_products) {
                $count+=count($store_products);
            }



        }

        return $count;

    }


    /*
     * returns an array of products in the cart without their store affiliations
     */
    public function getProductsFlat()
    {
        $this->load();
        $return = array();
        if (!empty($this->_cart['stores'])) {
            foreach ($this->_cart['stores'] as $store) {
                if (!empty($store['products'])) {
                    foreach ($store['products'] as $product) {
                        $return[$product->product_id] = $product;
                    }
                }
            }
        }
        return $return;
    }

    public function getProducts()
    {
        $this->load();
        return $this->_cart['stores'];

    }

    public function getCart($refresh = false)
    {
        $this->load($refresh);
        return $this->_cart;
    }

    public function load($refresh = false)
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        if (!isset($_SESSION['tpp_cart'])) {
            $this->_cart = array();
        } else {
            $this->_cart = unserialize($_SESSION['tpp_cart']);

            if (isset($this->_cart['stores'])) {

                //for every product we need to make sure that the properties are correct.
                if (!empty($this->_cart['stores'])) {

                    $product_ids = array();

                    $break = true;

                    foreach ($this->_cart['stores'] as $store_id => $store_products) {
                        if (empty($store_products['products'])) {
                            $this->removeStore($store_id, false);
                        } else {
                            $break = false;
                            foreach ($store_products['products'] as $store_product) {
                                $product_ids[] = $store_product->product_id;
                            }
                        }
                    }

                    if (false === $break) {
                        $products = TppStoreModelProducts::getInstance()->getProductsByIDs($product_ids, true);

                        unset($product_ids);

                        $products_to_keep = array();

                        foreach ($products as $product) {



                            if (isset($this->_cart['stores'][$product->store_id]) && isset($this->_cart['stores'][$product->store_id]['products'][$product->product_id])) {

                                if (true === $refresh) {

                                    $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->setData($product);

                                    $this->_cart['stores'][$product->store_id]['currency'] = $product->currency;

                                    $image = $product->getMainImage('main');

                                    $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->setData(array(
                                        'product_image' =>  $image['main']
                                    ));
                                }

                                unset($image);

                                $products_to_keep[$product->product_id] = $product->product_id;

                                if ((intval($product->unlimited) == 0) && $product->quantity_available < $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->order_quantity) {

                                    TppStoreMessages::getInstance()->addMessage(
                                        'error',
                                        array(
                                            'cart'   =>  'We are sorry, the product: ' . $product->product_title . ' is extremely popular and the quantity you ordered is no longer available. Your cart has been updated accordingly'
                                        )
                                    );
                                    $this->_cart['stores'][$product->store_id]['products'][$product->product_id]->setData(array('order_quantity'    =>  $product->quantity_available));
                                }

                            }


                        }

                        unset($product);
                        unset($products);
                        unset($store);
                        unset($store_product);

                        $tmp = $this->_cart['stores'];

                        //remove any items which are not enabled
                        foreach ($tmp as $store_id => $store_products) {
                            foreach ($store_products['products'] as $product_id => $product) {
                                if (!in_array($product_id, $products_to_keep)) {
                                    $this->removeItem($product_id, $store_id, false);
                                }
                            }
                        }

                    }

                    $this->_calculateTotals();

                    $this->save();

                }
            }

        }

    }


    public function save()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }


        $_SESSION['tpp_cart'] = serialize($this->_cart);

    }

    public function removeStore($store_id = 0, $load = true)
    {

        if (true === $load) {
            $this->load();
        }

        $this->_cart['stores'][$store_id] = null;
        unset($this->_cart['stores'][$store_id]);

        if (true === $load) {
            $this->_calculateTotals();
            $this->save();
        }
    }

    public function removeItem($product_id = 0, $store_id = 0, $load = true)
    {

        if (true === $load) {
            $this->load();
        }


        if (isset($this->_cart['stores'][$store_id]['products'][intval($product_id)])) {
            $this->_cart['stores'][$store_id]['products'][intval($product_id)] = null;
            unset($this->_cart['stores'][$store_id]['products'][intval($product_id)]);
            if (empty($this->_cart['stores'][$store_id]['products'])) {
                $this->removeStore($store_id, false);
            }
            if (true === $load) {
                $this->_calculateTotals();
            }
        } else {
            $this->removeStore($store_id, false);
        }
        if (true === $load) {
            $this->save();
        }
    }

    public function getTotalsFormatted()
    {
        $this->load(true);
        $t = isset($this->_cart['total'])?$this->_cart['total']:0;
        return number_format($t, 2);
    }


    public function delete()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        $_SESSION['tpp_cart'] = null;
        unset($_SESSION['tpp_cart']);
    }

    public function validate()
    {

    }

    public function getSToreTotal($store_id = 0)
    {
        if (intval($store_id) < 1 || !isset($this->_cart['stores'][$store_id])) {
            return 0;
        }





        return TppStoreModelCurrency::getInstance()->formatAmount($this->_cart['stores'][$store_id]['total'], false, false);


    }

}