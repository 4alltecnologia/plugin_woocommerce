<?php

  include_once 'woocommerce-4all-gateway.php';

  class WC_Gateway_4all extends WC_Payment_Gateway
    {
      function __construct()
      {
        $this->id = '4all';
        $this->has_fields = true;
        $this->method_title = '4all';
        $this->method_description = __('Official 4all payment gateway for WooCommerce.', 'woocommerce-4all');
        $this->icon = apply_filters( 'wc_gateway_4all_icon', plugins_url( 'assets/images/favicon-32x32.png', plugin_dir_path( __FILE__ ) ) );

        $this->init_form_fields();
        $this->init_settings();

        $this->enabled = $this->settings['enabled'];
        $this->title = $this->settings['title'];
        $this->description = $this->settings['description'];
        $this->merchantKey = $this->settings['merchantKey'];
        $this->environment = $this->settings['environment'];
        $this->gatewaySettings = ["merchantKey" => $this->settings['merchantKey'], "environment" => $this->settings['environment']];

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
      }

      /**
      * Returns a value indicating the the Gateway is available or not. It's called
      * automatically by WooCommerce before allowing customers to use the gateway
      * for payment.
      *
      * @return bool
      */
      public function is_available() {
        $available = 'yes' == $this->get_option( 'enabled' );

        return $available;
      }

      /**
      * Payment fields.
      */
      public function payment_fields() {
        include('form-template.php');
      }

      public function validate_fields()
      {
        $error = false;

        if (empty( $_REQUEST['cardholderName'] )) {
          wc_add_notice( '<strong>"' . __('Card name', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $error = true;
        } elseif (!preg_match('/([A-z])/', $_REQUEST['cardholderName']) || strlen($_REQUEST['cardholderName']) < 2 
        || strlen($_REQUEST['cardholderName']) > 28){
          wc_add_notice( '<strong>"' . __('Card name', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $error = true;
        }

        if (empty( $_REQUEST['cardNumber'] )) {
          wc_add_notice( '<strong>"' . __('Card number', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $error = true;
        } elseif (!preg_match('/([0-9])/', $_REQUEST['cardNumber']) || strlen($_REQUEST['cardNumber']) < 12 
        || strlen($_REQUEST['cardNumber']) > 19) {
          wc_add_notice( '<strong>"' . __('Card number', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $error = true;
        }

        if (empty( $_REQUEST['expirationDate'] )) {
          wc_add_notice( '<strong>"' . __('Expiration date', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $error = true;
        } elseif (!preg_match('/([0-1]{1}[0-9]{1}[\/]{1}[0-9])/', $_REQUEST['expirationDate']) || strlen($_REQUEST['expirationDate']) != 5) {
          wc_add_notice( '<strong>"' . __('Expiration date', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $error = true;
        }

        if (empty( $_REQUEST['securityCode'] )) {
          wc_add_notice( '<strong>"' . __('Security code', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $error = true;
        } elseif (!preg_match('/([0-9])/', $_REQUEST['securityCode']) || strlen($_REQUEST['securityCode']) < 3 
        || strlen($_REQUEST['securityCode']) > 4) {
          wc_add_notice( '<strong>"' . __('Security code', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $error = true;
        }

        if ($error) {
          return false;
        }

        return true;
      }

      /**
      * Initialize Gateway Settings Form Fields
      **/
      public function init_form_fields() { 
        $this->form_fields = apply_filters( 'wc_gateway_4all_form_fields', array(
              
            'enabled' => array(
                'title'   => __( 'Enable/Disable', 'woocommerce-4all' ),
                'type'    => 'checkbox',
                'label'   => ' ',
                'description' => __('If you do not already have 4all merchant account, <a href="https://autocredenciamento.4all.com" target="_blank">please register in Production</a>', 'woocommerce-4all'),
                'default' => 'yes'
            ),

            'title' => array(
                'title'       => __( 'Title:', 'woocommerce-4all' ),
                'type'        => 'text',
                'description' => __( 'Title of 4all Payment Gateway that users sees on Checkout page.', 'woocommerce-4all' ),
                'default'     => __( '4all', 'woocommerce-4all' ),
                'desc_tip'    => true,
            ),

            'description' => array(
                'title'       => __( 'Description:', 'woocommerce-4all' ),
                'type'        => 'textarea',
                'description' => __( 'Description of 4all Payment Gateway that users sees on Checkout page.', 'woocommerce-4all' ),
                'default'     => __( '4all is a leading payment services provider.', 'woocommerce-4all' ),
                'desc_tip'    => true,
            ),

            'integration' => array(
                'title'       => __( 'Integration Settings', 'woocommerce-4all' ),
                'type'        => 'title',
                'description' => '',
            ),

            'merchantKey' => array(
              'title'             => __( 'MerchantKey', 'woocommerce-4all' ),
              'type'              => 'text',
              'description'       => __( 'Please enter your 4all MerchantKey. This is needed to process the payment.'),
              'default'           => '',
              'custom_attributes' => array(
                'required' => 'required',
              ),
            ),

            'environment' => array(
              'title'             => __( 'Server Endpoint', 'woocommerce-4all' ),
              'type'              => 'select',
              'description'       => __( 'URL of the service that will be used to make calls.'),
              'default'           => 'https://gateway.homolog.4all.com/',
              'options' => array(
                'https://gateway.homolog.4all.com/' => 'https://gateway.homolog.4all.com/',
                'https://gateway.api.4all.com/' => 'https://gateway.api.4all.com/'
              )
            ),

        ) );
      } // close init_form_fields

      public function add_customer_4all(){

        $data = [];

        if ($_REQUEST["billing_first_name"] && $_REQUEST["billing_last_name"]) {
          $fullName = $_REQUEST["billing_first_name"] . ' ' . $_REQUEST["billing_last_name"];
          $data["fullName"] = $fullName;
        }

        if ($_REQUEST["billing_address_1"]) {
          $data["address"] = $_REQUEST["billing_address_1"];
        }

        if ($_REQUEST["billing_city"]) {
          $data["city"] = $_REQUEST["billing_city"];
        }

        if ($_REQUEST["billing_state"]) {
          $data["state"] = $_REQUEST["billing_state"];
        }

        if ($_REQUEST["billing_postcode"]) {
          $data["zipCode"] = $_REQUEST["billing_postcode"];
        }

        if ($_REQUEST["billing_phone"]) {
          $data["phoneNumber"] = $_REQUEST["billing_phone"];
        }

        if ($_REQUEST["billing_email"]) {
          $data["emailAddress"] = $_REQUEST["billing_email"];
        }

        if (sizeof($data) > 0 ) {
          return $data;
        }

        return null;
      }

      /*
      * Try make the payment
      */
      public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );
        $gateway_4all = new woocommerce_4all_gateway($this->gatewaySettings);
        $metaData = [
          "cardData" => [
            "cardholderName" => $_REQUEST["cardholderName"],
            "cardNumber" => $_REQUEST["cardNumber"],
            "expirationDate" => $_REQUEST["expirationDate"],
            "securityCode" => $_REQUEST["securityCode"]
            ],
          "installment" => $_REQUEST['installment'],
          "total" => (int)$order->get_total() * 100,
          "metaId" => "" . $order_id
          ];

        $metaData["customer"] = $this->add_customer_4all();

        $tryPay = $gateway_4all->paymentFlow_4all($metaData);

        if ($tryPay["error"]) {
          wc_add_notice( __('Payment error: ', 'woothemes') . __($tryPay["error"]["message"], 'woocommerce-4all'), 'error' );
          return;
        }
        
        // Payment complete
        $order->payment_complete();
                
        // Return thank you redirect
        return array(
            'result'    => 'success',
            'redirect'  => $this->get_return_url( $order )
        );
      } // process_payment

    } // close class