<?php

include_once('AtlasClient.php');

/**
  * This code will create and send an invoice to the customer Testcompany AB.
  * The customer will also be saved and stored. The total invoice sum will be 2500 SEK.
  * @author Carl Oscar Aaro at Agigen http://agigen.se/
  */

$data = array(
    'customer_name' => 'Testcompany AB',
    'customer_org_no' => '555555-5555',
    'customer_address' => 'Teststreet 1',
    'customer_zipcode' => '10000',
    'customer_city' => 'Stockholm',
    'customer_country' => 'se',
    'invoice_date' => date('Y-m-d'),
    'due_days' => 30,
    'delivery_type' => 'letter',
    'items' => array(
        array(
            'title' => 'Technical support',
            'description' => 'Setup of example.org',
            'unit' => 'hours',
            'price' => 1000,
            'num' => 4,
            'vat' => 25,
            'discount' => 2000,
        ),
    ),
    'callback' => 'https://mywebshop.example.org/example_callback_notify.php',
    'event' => 'send',
);
$invoice = AtlasClient::createInvoice($data);
