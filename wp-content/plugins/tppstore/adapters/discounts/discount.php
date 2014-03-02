<?php
/**
 * User: leeparsons
 * Date: 08/01/2014
 * Time: 07:57
 */
 
class TppStoreAdapterDiscount extends TppStoreAbstractBase {

    public static $discounts = false;

    /*
     * Gets the product discount for the product passed in, which is either a cart item or an actual product
     */
    public function getUserDiscountByProduct($product = false)
    {

        $discount = 0;

        if (false !== $product) {
            $this->getDiscounts();

            if (isset(TppStoreAdapterDiscount::$discounts[$product->product_id])) {
                if ($product instanceof TppStoreModelCartItem) {
                    $discount = TppStoreAdapterDiscount::$discounts[$product->product_id] * $product->price;
                } else {
                    $discount = TppStoreAdapterDiscount::$discounts[$product->product_id] * $product->price;
                }

            }



        }

        return $discount;

    }

    /*
     * Gets the store discount
     *
     * @param $store_products an array of store products in the cart to get the discount from
     */
    public function getUserDiscountByStore($store_products = array())
    {



        $discount = 0;

        if (!empty($store_products)) {

            $this->getDiscounts();


            foreach ($store_products as $product) {
                if (isset(TppStoreAdapterDiscount::$discounts[$product->product_id])) {



                    if (is_array($product->options) && !empty($product->options)) {
                        foreach ($product->options as $option) {
                            if ($product->currency !== geo::getInstance()->getCurrency()) {
                                if (false === ($price = geo::getInstance()->convertCurrency($option['price'], $product->currency))) {
                                    $price = $option['price'];
                                }
                            } else {
                                $price = $option['price'];
                            }

                            $discount += TppStoreAdapterDiscount::$discounts[$product->product_id] * $option['order_quantity'] * $price;
                        }
                    } else {
                        if ($product->currency !== geo::getInstance()->getCurrency()) {
                            if (false === ($price = geo::getInstance()->convertCurrency($product->price, $product->currency))) {
                                $price = $product->price;
                            }
                        } else {
                            $price = $product->price;
                        }

                        if (intval($product->price_includes_tax) == 1) {
                            $discount += TppStoreAdapterDiscount::$discounts[$product->product_id] * $product->order_quantity * $price;
                        } else {
                            $discount += (TppStoreAdapterDiscount::$discounts[$product->product_id] * $product->order_quantity * $price);// * (1 + ($product->tax_rate/100));
                        }

                    }

                }
            }
        }


        return $discount;
    }


    /*
     * Gets the total discount value for the cart
     */
    public function getDiscountValue(TppStoreModelCart $cart)
    {
        $discounts = $this->getDiscounts();

        $discount = 0;

        if (!empty($discounts)) {
            $products = $cart->getProductsFlat();

            if (!empty($products)) {
                foreach ($discounts as $product_id => $discount) {
                    if (isset($products[$product_id])) {
                        $discount += (float)($products[$product_id]->order_quantity * $products[$product_id]->price);
                    }
                }
            }

        }

        return $discount;

    }

    private function getDiscounts()
    {
        if ( false === $this::$discounts ) {
            TppStoreAdapterDiscount::$discounts = $this->getUserDiscountModel()->getDiscounts();
        }
    }



}