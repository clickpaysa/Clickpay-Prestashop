<?php


class Clickpay_PayPagePaymentModuleFrontController extends ModuleFrontController
{
  public $ssl = true;

  private $paymentType;

  /**
   * Order submitted, redirect to Clickpay
   */
  public function initContent()
  {
    parent::initContent();

    $paymentKey = Tools::getValue('method');
    $this->paymentType = ClickpayHelper::paymentType($paymentKey);

    $endpoint = $this->getConfig('endpoint');
    $merchant_id = $this->getConfig('profile_id');
    $merchant_key = $this->getConfig('server_key');

    $clickpayApi = ClickpayApi::getInstance($endpoint, $merchant_id, $merchant_key);

    //

    $cart = $this->context->cart;

    $request_param = $this->prepare_order($cart, $paymentKey);


    // Create paypage
    $paypage = $clickpayApi->create_pay_page($request_param);

    $success = $paypage->success;
    $message = @$paypage->message;

    $_logMsg = 'Clickpay: ' . json_encode($paypage);
    PrestaShopLogger::addLog($_logMsg, ($paypage->success ? 1 : 3), null, 'Cart', $cart->id, true, $cart->id_customer);

    //

    if ($success) {
      $payment_url = $paypage->payment_url;
      Tools::redirect($payment_url);
    } else {
      $url_step3 = $this->context->link->getPageLink('order', true, null, ['step' => '3']);

      $this->module->_redirectWithWarning($this->context, $url_step3, $message);
      return;
    }
  }


  function prepare_order($cart, $paymentKey)
  {
    $hide_shipping = (bool) $this->getConfig('hide_shipping');
    $allow_associated_methods = (bool) $this->getConfig('allow_associated_methods');

    $currency = new Currency((int) ($cart->id_currency));
    $customer = new Customer(intval($cart->id_customer));

    $address_invoice = new Address(intval($cart->id_address_invoice));
    $address_shipping = new Address(intval($cart->id_address_delivery));

    $invoice_country = new Country($address_invoice->id_country);
    $shipping_country = new Country($address_shipping->id_country);

    if ($address_invoice->id_state)
      $invoice_state = new State((int) ($address_invoice->id_state));

    if ($address_shipping->id_state)
      $shipping_state = new State((int) ($address_shipping->id_state));

    // Amount
    $totals = $cart->getSummaryDetails();
    $amount = $totals['total_price']; // number_format($cart->getOrderTotal(true, Cart::BOTH), 2, '.', '');

    // $total_discount = $totals['total_discounts']; // $total_product_ammout + $cart->getOrderTotal(true, Cart::ONLY_SHIPPING) - $amount;
    // $total_shipping = $totals['total_shipping'];
    // $total_tax = $totals['total_tax'];

    // $amount += $total_discount;


    $products = $cart->getProducts();

    $items_arr = array_map(function ($p) {
      $name = $p['name'];
      $qty = $p['quantity'];
      $qty_str = $qty > 1 ? "({$qty})" : '';
      return "{$name} $qty_str";
    }, $products);

    $cart_desc = implode(', ', $items_arr);

    //

    $lang_ = $this->context->language->iso_code;
    // if ($this->context->language->iso_code == "ar") {
    //   $lang_  = "Arabic";
    // }

    // $siteUrl = Context::getContext()->shop->getBaseURL(true);
    $return_url = Context::getContext()->link->getModuleLink($this->module->name, 'validation', ['p' => $paymentKey]);
    $callback_url = Context::getContext()->link->getModuleLink($this->module->name, 'callback', ['p' => $paymentKey]);

    //

    // $country_details = ClickpayHelper::getCountryDetails($invoice_country->iso_code);

    $phone_number = ClickpayHelper::getNonEmpty(
      $address_invoice->phone,
      $address_invoice->phone_mobile
    );

    $shipping_phone_number = ClickpayHelper::getNonEmpty(
      $address_shipping->phone,
      $address_shipping->phone_mobile
    );

    if (empty($invoice_state)) {
      $invoice_state = null;
    } else {
      $invoice_state = $invoice_state->name;
    }

    if (empty($shipping_state)) {
      $shipping_state = null;
    } else {
      $shipping_state = $shipping_state->name;
    }

    $ip_customer = Tools::getRemoteAddr();

    $pt_holder = new ClickpayRequestHolder();
    $pt_holder
      ->set01PaymentCode($this->paymentType, $allow_associated_methods, strtoupper($currency->iso_code))
      ->set02Transaction(ClickpayEnum::TRAN_TYPE_SALE, ClickpayEnum::TRAN_CLASS_ECOM)
      ->set03Cart(
        $cart->id,
        strtoupper($currency->iso_code),
        $amount,
        $cart_desc
      )
      ->set04CustomerDetails(
        $address_invoice->firstname . ' ' . $address_invoice->lastname,
        $customer->email,
        $phone_number,
        $address_invoice->address1 . ' ' . $address_invoice->address2,
        $address_invoice->city,
        $invoice_state,
        $invoice_country->iso_code,
        $address_invoice->postcode,
        $ip_customer
      )
      ->set05ShippingDetails(
        false,
        $address_shipping->firstname . ' ' . $address_shipping->lastname,
        $customer->email,
        $shipping_phone_number,
        $address_shipping->address1 . ' ' . $address_shipping->address2,
        $address_shipping->city,
        $shipping_state,
        $shipping_country->iso_code,
        $address_shipping->postcode,
        null
      )
      ->set06HideShipping($hide_shipping)
      ->set07URLs($return_url, $callback_url)
      ->set08Lang($lang_)
      ->set99PluginInfo('PrestaShop', _PS_VERSION_, CLICKPAY_PAYPAGE_VERSION);

    $post_arr = $pt_holder->pt_build();

    return $post_arr;
  }

  //

  private function getConfig($key)
  {
    return Configuration::get("{$key}_{$this->paymentType}");
  }
}
