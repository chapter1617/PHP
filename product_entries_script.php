<?php
/**
* Template Name: Add Products
*/
get_header(); ?>
<?php
global $wpdb; 
$table_name = 'sudswp_2_products_info';
$ctry = array();
$country = array();
$country = get_field('insert_data_for_following_countries');
/*echo '<pre>';
print_r($country);
die();*/
function read_json($url,$city) {
    $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $html = curl_exec($ch);
    if (!$html) {
        echo "<br />cURL error number:" .curl_errno($ch);
        echo "<br />cURL error:" . curl_error($ch);
        echo "<br/><h4>Check Url for: " . $city.'</h4>';
    }
    else{
        //return $html;
        $dom  = new DOMDocument();
        libxml_use_internal_errors( 1 );
        $dom->loadHTML( $html );
        $xpath = new DOMXpath( $dom );
        $jsonScripts = $xpath->query( '//script[@type="application/json"]' );
        $json2 = trim( $jsonScripts->item(0)->nodeValue);
        $data = json_decode($json2);
        return $data;
    }
    //End of cURL function
}
if(!empty(get_field('delete_all_data_from_product'))){
      $res = $wpdb->query("DELETE FROM $table_name WHERE 1=1"); 
      if($res){
        echo '<h2>All data deleted successfully</h2>' ; 
     }
}
if(!empty(get_field('delete_selected_products_from_db'))) {
    $pids = do_shortcode(get_field('insert_product_ids_to_delete'));
    //echo $pids;
          if(!empty($pids)){
              $qry = $wpdb->query("DELETE FROM $table_name WHERE product_id IN ('".$pids."')");
              if($qry){
                  echo "<h2>Selected Products deleted successfully</h2>" ;
              }else{
                   echo "<h2>Some error in deleting products , please check products again</h2>" ;
              }
          }
}
/////Canada ///////////// 
if(in_array("CA", $country)){
      $store_url = get_field('store_link_ca'); 
      $pids = "";
      if($store_url){
            $result_ca = array();
            $product_data_ca = array();
            if (strpos($store_url,'/products/') !== false) {
                $result_ca = read_json($store_url,'CA');
                $product_data_ca = $result_ca->state->shop->product;
                $postId = $product_data_ca->id ;
                $postPrice = $product_data_ca->itemPrice ;
                $postTitle = $product_data_ca->title; 
                $postSlug = $product_data_ca->slug ;
                $postImage = $product_data_ca->images[0];
                $pid = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $postId");
                if($pid[0]){
                     echo '<h5>'. $pid[0] .' Already Exist in table</h5>';
                     $result_check_ca =  $wpdb->update($table_name, 
                      array('ca_product_name' => utf8_decode($postTitle),'ca_price' => $postPrice , 'ca_slug' => utf8_decode($postSlug)),
                      array('product_id'=>$pid[0]));
                      if($result_check_ca){
                              echo '<h5>and record updated</h5>';
                      }
                }else{
                      $result_check_ca =  $wpdb->insert($table_name, 
                        array('product_id' => $pid[0], 'ca_product_name' => utf8_decode($postTitle),'image'=>$postImage,'ca_price' => $postPrice,'ca_slug'=> utf8_decode($postSlug)
                      )); 
                      if($result_check_ca){
                          echo $i .' <h5> record inserted</h5>'.'<br>';
                      }  
                }
            } else {
                $result_ca = read_json($store_url,'CA');
                $totalPages = $result_ca->state->shop->filterOptions->totalPages;
                $i=0;
                for ($j=1; $j <= $totalPages ; $j++) { 
                    $store_url_new = $store_url . '?pages='.$j;
                    $result_ca = read_json($store_url_new,'CA');
                    $product_data_ca[] = $result_ca->state->shop->filterOptions->products;
                }
                foreach($product_data_ca as $product_page){
                    foreach ($product_page as $d1) {
                       $pids .= $d1->id . ",";
                       $post_id = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $d1->id"); 
                        if(!empty($post_id)){
                              echo '<h5>'. $post_id[0] .' Already Exist</h5>' ;
                            $result_check_ca =  $wpdb->update($table_name, 
                              array('ca_product_name' => utf8_decode($d1->title),'image'=>$d1->images[0],'ca_price' => $d1->itemPrice,'ca_slug'=>utf8_decode($d1->slug)),
                              array('product_id'=>$post_id[0])); 
                            if($result_check_ca){
                                echo '<h5> and record updated</h5>'.'<br>';
                            }
                        }else{
                              $result_check_ca =  $wpdb->insert($table_name, array(
                                'product_id' => $d1->id, 
                                'ca_product_name' => utf8_decode($d1->title),
                                'image'=>$d1->images[0],
                                'ca_price' => $d1->itemPrice,
                                'ca_slug'=>utf8_decode($d1->slug)
                              )); 
                               if($result_check_ca){
                                  echo $i .' <h5> record inserted</h5>'.'<br>';
                               }  else{
                                  echo ' <h5> record inserted failed</h5>';
                               } 
                        } 
                        $i++;
                    }
                }
                echo "<h6>------------CA Products for this run are-----------------</h6>".$pids;
            } 
      }
}

/////Australia ///////////// 
if (in_array("AU", $country)){
    $store_url = get_field('store_link_au'); 
    $pids = "";
    if($store_url){
        $result_au = array();
        $product_data_au = array();
        if (strpos($store_url,'/products/') !== false) {
            $result_au = read_json($store_url,'AU');
            $product_data_au = $result_au->state->shop->product;
            $postId = $product_data_au->id ;
            $postPrice = $product_data_au->itemPrice ;
            $postTitle = $product_data_au->title; 
            $postSlug = $product_data_au->slug ;
            $postImage = $product_data_au->images[0];
            $pid = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $postId");
            if($pid[0]){
                 echo '<h5>'. $pid[0] .' Already Exist in table</h5>';
                 $result_check_au =  $wpdb->update($table_name, array('au_product_name' => utf8_decode($postTitle),'au_price' => $postPrice , 'au_slug' => utf8_decode($postSlug)),array('product_id'=>$pid[0]));
                if($result_check_au){
                          echo '<h5>and record updated</h5><br>';
                }
            }else{
                  $result_check_au =  $wpdb->insert($table_name, array('product_id' => $pid[0], 'au_product_name' => utf8_decode($postTitle),'image'=>$postImage,'au_price' => $postPrice,'au_slug'=> utf8_decode($postSlug))); 
                  if($result_check_au){
                      echo $i .' <h5> record inserted</h5>'.'<br>';
                  }  
            }
        } else {
            $result_au = read_json($store_url,'AU');
            $totalPages = $result_au->state->shop->filterOptions->totalPages;
            for ($j=1; $j <= $totalPages ; $j++) { 
                    $store_url_new = $store_url . '?pages='.$j;
                    $result_au = read_json($store_url_new,'AU');
                    $product_data_au[] = $result_au->state->shop->filterOptions->products;
            }
            $i=0;
            foreach($product_data_au as $product_page){
                    foreach ($product_page as $d1) {
                        $pids .= $d1->id . ","; 
                        $post_id = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $d1->id"); 
                        if(!empty($post_id)){
                                echo '<h5>'. $post_id[0] .' Already Exist</h5>';
                                $result_check_au =  $wpdb->update($table_name, 
                                  array('au_product_name' => utf8_decode($d1->title),'au_price' => $d1->itemPrice , 'au_slug' => utf8_decode($d1->slug)),
                                  array('product_id'=>$post_id[0])
                                );
                                if($result_check_au){
                                      echo '<h5>and record updated</h5>'.'<br>';
                                }
                        }else{
                              $result_check_au =  $wpdb->insert($table_name, array('product_id' => $d1->id, 'au_product_name' => utf8_decode($d1->title),'image'=>$d1->images[0],'au_price' => $d1->itemPrice,'au_slug'=>utf8_decode($d1->slug))); 
                              if($result_check_au){
                                  echo $i .' <h5> record inserted</h5>'.'<br>';
                              }
                        } 
                        $i++;
                    }
            }
            echo "<h6>------------AU Products for this run are-----------------</h6>".$pids;
        }
    }
}

/////France ///////////// 
if(in_array("FR", $country)){
      $store_url = get_field('store_link_fr'); 
      $pids = "";
      if($store_url){
            $result_fr = array();
            $product_data_fr = array();
            if (strpos($store_url,'/products/') !== false) {
                $result_fr = read_json($store_url,'FR');
                $product_data_fr = $result_fr->state->shop->product;
                $postId = $product_data_fr->id ;
                $postPrice = $product_data_fr->itemPrice ;
                $postTitle = $product_data_fr->title; 
                $postSlug = $product_data_fr->slug ;
                $postImage = $product_data_fr->images[0];
                $pid = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $postId");
                if($pid[0]){
                     echo '<h5>'. $pid[0] .' Already Exist in table</h5>';
                     $result_check_fr =  $wpdb->update($table_name, 
                      array('fr_product_name' => utf8_decode($postTitle),'fr_price' => $postPrice , 'fr_slug' => utf8_decode($postSlug)),
                      array('product_id'=>$pid[0]));
                     if($result_check_fr){
                              echo ' <h5>and record updated</h5>';
                      }
                }else{
                      $result_check_fr =  $wpdb->insert($table_name, 
                        array('product_id' => $pid[0], 'fr_product_name' => utf8_decode($postTitle),'image'=>$postImage,'fr_price' => $postPrice,'fr_slug'=> utf8_decode($postSlug))
                      ); 
                      if($result_check_fr){
                          echo $i .' <h5>  record inserted</h5>'.'<br>';
                      }  
                }
            } else {
                $result_fr = read_json($store_url,'FR');
                $totalPages = $result_fr->state->shop->filterOptions->totalPages;
                for ($j=1; $j <= $totalPages ; $j++) { 
                    $store_url_new = $store_url . '?pages='.$j;
                    $result_fr = read_json($store_url_new,'FR');
                    $product_data_fr[] = $result_fr->state->shop->filterOptions->products;
                }
                $i=0;
               
                foreach($product_data_fr as $product_page){
                    foreach ($product_page as $d1) {
                        $pids .= $d1->id . ","; 
                        $post_id = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $d1->id"); 
                        if($post_id){
                            echo '<h5>'. $post_id[0] .' Already Exist</h5>' . '<br>';
                              $result_check_fr =  $wpdb->update($table_name, 
                                array('fr_product_name' => utf8_decode($d1->title),'image'=>$d1->images[0],'fr_price' => $d1->itemPrice,'fr_slug'=>utf8_decode($d1->slug)),
                                array('product_id'=>$d1->id)); 
                                if($result_check_fr){
                                      echo '<h5> and record updated</h5>'.'<br>';
                                }
                        }else{
                              $result_check_fr =  $wpdb->insert($table_name, 
                                array('product_id' => $d1->id, 'fr_product_name' => utf8_decode($d1->title),'image'=>$d1->images[0],'fr_price' => $d1->itemPrice,'fr_slug'=>utf8_decode($d1->slug))
                              ); 
                                if($result_check_fr){
                                  echo $i .' <h5> record inserted</h5>'.'<br>';
                                }
                        } 
                        $i++;  
                    }
                } 
                 echo "<h6>------------FR Products for this run are-----------------</h6>".$pids;
            }
      }
}

/////United Kingdom ///////////// 
if(in_array("UK", $country)){
      $store_url = get_field('store_link_uk'); 
      $pids = "";
      if($store_url){
            $result_uk = array();
            $product_data_uk = array();
            if (strpos($store_url,'/products/') !== false) {
                $result_uk = read_json($store_url,'UK');
                $product_data_uk = $result_uk->state->shop->product;
                $postId = $product_data_uk->id ;
                $postPrice = $product_data_uk->itemPrice ;
                $postTitle = $product_data_uk->title; 
                $postSlug = $product_data_uk->slug ;
                $postImage = $product_data_uk->images[0];
                $pid = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $postId");
                if($pid[0]){
                     echo '<h5>'. $pid[0] .' Already Exist in table</h5>';
                     $result_check_uk =  $wpdb->update($table_name, array('uk_product_name' => utf8_decode($postTitle),'uk_price' => $postPrice , 'uk_slug' => utf8_decode($postSlug)),array('product_id'=>$pid[0]));
                     if($result_check_uk){
                              echo '<h5> and record updated</h5>';
                      }
                }else{
                      $result_check_uk =  $wpdb->insert($table_name, array('product_id' => $pid[0], 'uk_product_name' => utf8_decode($postTitle),'image'=>$postImage,'uk_price' => $postPrice,'uk_slug'=> utf8_decode($postSlug))); 
                      if($result_check_uk){
                          echo $i .' <h5> record inserted</h5>'.'<br>';
                      }  
                }
            } else {
                $result_uk = read_json($store_url,'UK');
                $totalPages = $result_uk->state->shop->filterOptions->totalPages;
                for ($j=1; $j <= $totalPages ; $j++) { 
                    $store_url_new = $store_url . '?pages='.$j;
                    $result_uk = read_json($store_url_new,'UK');
                    $product_data_uk[] = $result_uk->state->shop->filterOptions->products;
                }
                $i=0;
                
                 foreach($product_data_uk as $product_page){
                    foreach ($product_page as $d1) {
                        $pids .= $d1->id . ",";
                        $post_id = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $d1->id ");
                        if($post_id){
                            echo '<h5>'. $post_id[0] .' Already Exist</h5>' . '<br>';
                            $result_check_uk =  $wpdb->update($table_name, array('uk_product_name' => utf8_decode($d1->title),'image'=>$d1->images[0],'uk_price' => $d1->itemPrice,'uk_slug'=>utf8_decode($d1->slug)),array('product_id'=>$d1->id)); 
                              if($result_check_uk){
                                      echo '<h5> and record updated'.'<br>';
                              }
                        }else{
                            $result_check_uk =  $wpdb->insert($table_name, array('product_id' => $d1->id, 'uk_product_name' => utf8_decode($d1->title),'image'=>$d1->images[0],'uk_price' => $d1->itemPrice,'uk_slug'=>utf8_decode($d1->slug))); 
                             if($result_check_uk){
                                echo $i .' <h5> record inserted</h5>'.'<br>';
                              }
                        } 
                      $i++; 
                    } 
                } 
               echo "<h6>------------UK Products for this run are-----------------</h6>".$pids;
            }
      }
}

/////Newzealand /////////////     
if(in_array("NZ", $country)){
        $store_url = get_field('store_link_nz'); 
        $pids = "";
        if($store_url){
            $result_nz = array();
            $product_data_nz = array();
            if (strpos($store_url,'/products/') !== false) {
                $result_nz = read_json($store_url,'NZ');
                $product_data_nz = $result_nz->state->shop->product;
                $postId = $product_data_nz->id ;
                $postPrice = $product_data_nz->itemPrice ;
                $postTitle = $product_data_nz->title; 
                $postSlug = $product_data_nz->slug ;
                $postImage = $product_data_nz->images[0];
                $pid = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $postId");
                if($pid[0]){
                     echo '<h5>'.$pid[0] .' Already Exist in table</h5>';
                     $result_check_nz =  $wpdb->update($table_name, 
                      array('nz_product_name' => utf8_decode($postTitle),'nz_price' => $postPrice , 'nz_slug' => utf8_decode($postSlug)),
                      array('product_id'=>$pid[0])
                    );
                     if($result_check_nz){
                              echo '<h5> and record updated</h5>';
                      }
                }else{
                      $result_check_nz =  $wpdb->insert($table_name, 
                        array('product_id' => $pid[0], 'nz_product_name' => utf8_decode($postTitle),'image'=>$postImage,'nz_price' => $postPrice,'nz_slug'=> utf8_decode($postSlug)
                        )); 
                      if($result_check_nz){
                          echo $i .' <h5> record inserted</h5>'.'<br>';
                      } else{
                          echo ' <h5> record inserted failed</h5>';
                      }  
                }
            } else {
                $result_nz = read_json($store_url,'NZ');
                $totalPages = $result_nz->state->shop->filterOptions->totalPages;
                for ($j=1; $j <= $totalPages ; $j++) { 
                    $store_url_new = $store_url . '?pages='.$j;
                    $result_nz = read_json($store_url_new,'NZ');
                    $product_data_nz[] = $result_nz->state->shop->filterOptions->products;
                }
                  $i=0;
                foreach($product_data_nz as $product_page){
                    foreach ($product_page as $d1) {
                    $pids .= $d1->id . ",";
                        $post_id = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $d1->id"); 
                        if($post_id){
                            echo '<h5>'. $post_id[0] .' Already Exist</h5>' . '<br>';
                            $result_check_nz =  $wpdb->update($table_name, 
                              array('nz_product_name' => utf8_decode($d1->title),'image'=>$d1->images[0],'nz_price' => $d1->itemPrice,'nz_slug'=>utf8_decode($d1->slug)),
                              array('product_id'=>$d1->id)); 
                              if($result_check_nz){
                                        echo '<h5> and record updated</h5>'.'<br>';
                              }
                        }else{
                            $result_check_nz =  $wpdb->insert($table_name, 
                              array('product_id' => $d1->id, 'nz_product_name' => utf8_decode($d1->title),'image'=>$d1->images[0],'nz_price' => $d1->itemPrice,'nz_slug'=>utf8_decode($d1->slug)
                            )); 
                             if($result_check_nz){
                                echo ' <h5> ' . $i .'record inserted</h5>'.'<br>';
                             }
                        } 
                        $i++;  
                    }
                } 
                echo "<h6>------------NZ Products for this run are-----------------</h6>".$pids;
            }
        }
}

/////United States ///////////// 
if (in_array("US", $country)) {
    $store_url = get_field('storel_link_us'); 
    $pids = "";
    if($store_url){
          $result_us = array();
          $product_data_us = array();
          if(strpos($store_url,'/products/') !== false) {
              $result_us = read_json($store_url,'US');
              $product_data_us = $result_us->state->shop->product;
              $postId = $product_data_us->id ;
              $postPrice = $product_data_us->itemPrice ;
              $postTitle = $product_data_us->title; 
              $postSlug = $product_data_us->slug ;
              $postImage = $product_data_us->images[0];
              $pid = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $postId");
              if($pid[0]){
                   echo '<h5>'. $pid[0] .' Already Exist in table</h5>';
                   $result_check_us =  $wpdb->update($table_name, array('us_product_name' => utf8_decode($postTitle),'us_price' => $postPrice , 'us_slug' => utf8_decode($postSlug)),array('product_id'=>$pid[0]));
                   if($result_check_us){
                            echo ' and record updated <br>';
                    }
              }else{
                    $result_check_us =  $wpdb->insert($table_name, array('product_id' => $pid[0], 'us_product_name' => utf8_decode($postTitle),'image' => $postImage,'us_price' => $postPrice,'us_slug'=> utf8_decode($postSlug))); 
                    if($result_check_us){
                        echo ' <h5>record inserted</h5><br>';
                    }  
              }  
          } else {
              $result_us = read_json($store_url,'US');
              $totalPages = $result_us->state->shop->filterOptions->totalPages;
              for ($j=1; $j <= $totalPages ; $j++) { 
                $store_url_new = $store_url . '?pages='.$j;
                $result_us = read_json($store_url_new,'US');
                $product_data_us[] = $result_us->state->shop->filterOptions->products;
             }
              $i = 0;
              foreach($product_data_us as $product_page){
                foreach ($product_page as $d1) {
                    $pids .= $d1->id . ",";
                    $post_id = $wpdb->get_col("SELECT product_id FROM $table_name WHERE product_id = $d1->id"); 
                    if(!empty($post_id)){
                           echo '<h5>'. $post_id[0] .' Already Exist</h5>' . '<br>';
                            $result_check_us =  $wpdb->update($table_name, 
                              array('us_product_name' => utf8_decode($d1->title),'us_price' => $d1->itemPrice , 'us_slug' => utf8_decode($d1->slug)),
                              array('product_id'=>$post_id[0])
                            );
                              
                            if($result_check_us){
                                  echo '<h5> and record updated'.'<br></h5>';
                            }
                    }else{
                          $result_check_us =  $wpdb->insert($table_name, array('product_id' => $d1->id, 'us_product_name' => utf8_decode($d1->title),'image'=>$d1->images[0],'us_price' => $d1->itemPrice,'us_slug'=>utf8_decode($d1->slug))); 
                          if($result_check_us){
                              echo '<h5>'. $i .'record inserted</h5>'.'<br>';
                          }  
                    } 
                    $i++;
                }
              }
              echo "<h6>------------US Products for this run are-----------------</h6>".$pids;
          }
    }
}


get_footer(); ?>
