<?php

$how_many_days_to_cache_prices = 1;



function scanPage($url,$postvars='') {
 $c = curl_init();
 curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');
 curl_setopt($c, CURLOPT_URL, $url);
 curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($c, CURLOPT_HEADER, true);
 curl_setopt($c, CURLOPT_FRESH_CONNECT, true);
 curl_setopt($c, CURLOPT_MAXREDIRS, 7);
 curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
 curl_setopt($c, CURLOPT_TIMEOUT, 15);
 if ($postvars) {
  curl_setopt($c, CURLOPT_POST, true);
  curl_setopt($c, CURLOPT_POSTFIELDS, $postvars);
 }
 curl_setopt($c, CURLOPT_COOKIEFILE, 'cookies/ck');
 curl_setopt($c, CURLOPT_COOKIEJAR, 'cookies/ck');
 $out = curl_exec($c);
 curl_close($c);
 return $out;
}
function hm_between($source,$start,$end) { // hm_between v1.0
 list($ggg,$out) = explode($start,$source,2);
 list($out,$ggg) = explode($end,$out,2);
 return $out;
}

function getProductInfo($id) {
 global $list,$how_many_days_to_cache_prices;
 if (isset($list[$id]) && is_array($list[$id]) && $list[$id][2] > time()) return $list[$id];
 $a = scanPage('https://res.cloudinary.com/stampin-up/image/upload/w_360,f_auto,d_missing_image.png/v1572892574/prod/images/default-source/product-image/='.$id);
 if ($a) {
  $title = hm_between($a,'ctl00_MainContentContainer_lblItemTitle">','<');
  $price = hm_between($a,'ctl00_MainContentContainer_lblItemPrice">','<');
  $sale = hm_between($a,'ctl00_MainContentContainer_lblSalePrice" class="salePrice">','<');
  if ($sale) $price = $sale;

  if ($title || $price > 0) {
   $list[$id] = array($title,$price,time()+($how_many_days_to_cache_prices * 86400));
   $fprice = preg_replace('/[^0-9\.]/','',$price);
   if ($fprice > 0) {
    $f = fopen('stampinupcache','w');
    if ($f) {
     fputs($f,serialize($list));
     fclose($f);
    }
   }

   return array($title,$price);
  }
 }
 return false;
}
function cleanUpCache() {
 $list = @file_get_contents('stampinupcache');
 if ($list) {
  $list = unserialize($list);

  foreach ($list as $id => $item) {
   if ($item[2] <= time()) {
    unset($list[$id]);
   }
  }

  $f = fopen('stampinupcache','w');
  if ($f) {
   fputs($f,serialize($list));
   fclose($f);
  }
 } else $list = array();

 return $list;
}

// test
// $item = getProductInfo('138335');//7
// $item = getProductInfo('133630');//2.38
// print_r($item);
// exit;

$a = substr(urldecode($_GET['a']),0,300);
$a = preg_replace('/[^0-9]/',',',$a);
$a = preg_replace('/[,]+/',',',$a);
$a = trim($a,',');
$b = explode(',',$a);
$out = array();
$errors = array();

if (count($b)) {

 $list = cleanUpCache();

 foreach ($b as $c) {
//  if ($c > 99999  && $c < 999999) {
  if ($c > 99) {

   $item = getProductInfo($c);

   if ($item !== false) {
    $out[] = array($c,$item[0],$item[1]);
    continue;
   }
  }
  $errors[] = $c;
 }
}

echo json_encode(array($out,$errors));