<?php

  include_once 'woocommerce-4all-gateway.php';

  class WC_Gateway_4all extends WC_Payment_Gateway
    {
      function __construct()
      {
        $this->id = '4all';
        $this->has_fields = true;
        $this->method_title = '4all';
        $this->method_description = 'Official 4all payment gateway for WooCommerce.';
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
        echo  '<p>Name of the buyer (same as the card)</p>
          <input type="text" name="cardholderName">
          <p>Card number</p>
          <input type="text" name="cardNumber">
          <p>Expiration date</p>
          <input type="text" placeholder="MM/YY" name="expirationDate">
          <p>Security code</p>
          <input type="text" name="securityCode">';
      }

      public function validate_fields()
      {
        $error = false;

        if (empty( $_REQUEST['cardholderName'] )) {
          wc_add_notice( '<strong>Card name:</strong> ' . __( 'is a required field.', 'wc-4all' ), 'error' );

          $error = true;
        } elseif (!preg_match('/([A-z])/', $_REQUEST['cardholderName']) || strlen($_REQUEST['cardholderName']) < 2 
        || strlen($_REQUEST['cardholderName']) > 28){
          wc_add_notice( '<strong>Card name:</strong> ' . __( 'is not a valide value.', 'wc-4all' ), 'error' );
          
          $error = true;
        }

        if (empty( $_REQUEST['cardNumber'] )) {
          wc_add_notice( '<strong>Card number:</strong> ' . __( 'is a required field.', 'wc-4all' ), 'error' );

          $error = true;
        } elseif (!preg_match('/([0-9])/', $_REQUEST['cardNumber']) || strlen($_REQUEST['cardNumber']) < 12 
        || strlen($_REQUEST['cardNumber']) > 19) {
          wc_add_notice( '<strong>Card number:</strong> ' . __( 'is not a valide value.', 'wc-4all' ), 'error' );
          
          $error = true;
        }

        if (empty( $_REQUEST['expirationDate'] )) {
          wc_add_notice( '<strong>Expiration date:</strong> ' . __( 'is a required field.', 'wc-4all' ), 'error' );

          $error = true;
        } elseif (!preg_match('/([0-1]{1}[0-9]{1}[\/]{1}[0-9])/', $_REQUEST['expirationDate']) || strlen($_REQUEST['expirationDate']) != 5) {
          wc_add_notice( '<strong>Expiration date:</strong> ' . __( 'is not a valide value.', 'wc-4all' ), 'error' );
          
          $error = true;
        }

        if (empty( $_REQUEST['securityCode'] )) {
          wc_add_notice( '<strong>Security code:</strong> ' . __( 'is a required field.', 'wc-4all' ), 'error' );

          $error = true;
        } elseif (!preg_match('/([0-9])/', $_REQUEST['securityCode']) || strlen($_REQUEST['securityCode']) < 3 
        || strlen($_REQUEST['securityCode']) > 4) {
          wc_add_notice( '<strong>Security code:</strong> ' . __( 'is not a valide value.', 'wc-4all' ), 'error' );
          
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
              'description'       => sprintf( __( 'Please enter your 4all MerchantKey. This is needed to process the payment.')),
              'default'           => '',
              'custom_attributes' => array(
                'required' => 'required',
              ),
            ),

            'environment' => array(
              'title'             => __( 'Server Endpoint', 'woocommerce-4all' ),
              'type'              => 'select',
              'description'       => sprintf( __( 'URL of the service that will be used to make calls.')),
              'default'           => 'https://gateway.homolog-interna.4all.com/',
              'options' => array(
                'https://gateway.homolog-interna.4all.com/' => 'https://gateway.homolog-interna.4all.com/',
                'https://gateway.api.4all.com/' => 'https://gateway.api.4all.com/'
              )
            ),

        ) );
      } // close init_form_fields

      /*
      * Try make the payment
      */
      public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );
        $gateway_4all = new woocommerce_4all_gateway();

        $metaData = [
          "cardData" => [
            "cardholderName" => $_REQUEST["cardholderName"],
            "cardNumber" => $_REQUEST["cardNumber"],
            "expirationDate" => $_REQUEST["expirationDate"],
            "securityCode" => $_REQUEST["securityCode"]
            ],
          "total" => (int)$order->get_total() * 100,
          "metaId" => "" . $order_id
          ];

        $tryPay = $gateway_4all->paymentFlow($this->gatewaySettings, $metaData);

        if ($tryPay["error"]) {
          wc_add_notice( __('Payment error: ', 'woothemes') . $tryPay["error"]["message"], 'error' );
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