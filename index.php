<?php

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$s = $_POST['secret'];

if($s!=='2XwRW5DtCNe1O1VxLFEB7nM7TgzpMGhI'){
  echo "Wrong token";
  die;
}

if( isset($_POST['paypal_token']) && isset($_POST['payment_id']) && isset($_POST['payer_id']) ){

  $result = executePayment($_POST['payment_id'], $_POST['payer_id'], $_POST['paypal_token']);
  print_r($result);

}else if(isset($_POST['paypal_token'])){

  /**
  * New payment with token
  */

  $return = $_POST['return_url'];
  $cancel = $_POST['cancel_url'];
  $amount = $_POST['amount'];

  $token = $_POST['paypal_token'];
  $result = getPaymentURL($token, $return, $cancel, $amount);
  $result = json_decode($result);

  $return = array();
  $return['token']['access_token'] = $token;
  $return['data'] = $result;
  echo json_encode($return);

}else if(isset($_POST['paypal_un']) && isset($_POST['paypal_pw'])){

  /**
  * New payment WITHOUT token
  */

  $return = $_POST['return_url'];
  $cancel = $_POST['cancel_url'];
  $amount = $_POST['amount'];

  $pun = $_POST['paypal_un'];
  $ppw = $_POST['paypal_pw'];

  $token = getToken($s, $pun, $ppw);
  $token = json_decode($token);
  $result = getPaymentURL($token->access_token, $return, $cancel, $amount);
  $result = json_decode($result);

  $return = array();
  $return['token'] = $token;
  $return['data'] = $result;
  echo json_encode($return);

}

function getToken($s, $pun, $ppw){

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paypal.com/v1/oauth2/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "grant_type=client_credentials",
    CURLOPT_USERPWD => "$pun:$ppw",
    CURLOPT_HTTPHEADER => array(
      "cache-control: no-cache",
      "content-type: application/x-www-form-urlencoded",
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #1:" . $err;
    die;
  } else {
    return $response;
  }

}

function getPaymentURL($token, $return, $cancel, $amount){

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paypal.com/v1/payments/payment",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => '{"intent":"sale","redirect_urls":{"return_url":"'.$return.'","cancel_url":"'.$cancel.'"},"payer":{"payment_method":"paypal"},"transactions":[{"amount":{"total":"'.$amount.'","currency":"HKD"}}]}',
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $token",
      "cache-control: no-cache",
      "content-type: application/json",
      "postman-token: 7ca12352-aa45-2c4e-d87d-fdd09795121d"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    echo "cURL Error #2:" . $err;
    die;
  } else {
    return $response;
  }

}

function executePayment($payment_id, $payer_id, $paypal_token){

  $curl = curl_init();
  $data = array("payer_id" => $payer_id);
  $data = json_encode($data);

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paypal.com/v1/payments/payment/$payment_id/execute",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => '{"payer_id":"'.$payer_id.'"}',
    CURLOPT_HTTPHEADER => array(
      "authorization: Bearer $paypal_token",
      "cache-control: no-cache",
      "content-type: application/json",
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    return "cURL Error #:" . $err;
  } else {
    return $response;
  }
}
