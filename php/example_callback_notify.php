<?php

include_once('AtlasClient.php');

/**
  * Callback script for the Atlas Invoice REST API using the PHP helper class.
  * @author Carl Oscar Aaro at Agigen http://agigen.se/
  */

if (!AtlasClient::verifyCallback(isset($_POST) ? $_POST : null)) die;

$order = Orders::get($_POST['custom']);
if (!$order->paid && $_POST['state'] == 'Paid') {
    $order->paid = 1;
    $order->save();
}
