<?php

  $tag_pool = array();
  $pool_size = 20;
  $timeout = 10;
  $fetch_size = 20;
  $word = strtolower($_POST['lastWord']);
  
  for($i = 0; $i<$timeout; $i++){
    $req = new HttpRequest(
    'http://localhost:8983/solr/select/?q=hashtags:'.trim($word).'*&fl=hashtags&start='.$i*$fetch_size.'&rows='.$fetch_size.'&wt=json&indent=true');
    $result = json_decode($req->send()->getBody());
    foreach($result->response->docs as $tweet){
      $tags = explode(" ",$tweet->hashtags);
      foreach($tags as $tag){
        $lc = strtolower(trim($tag));
        if((!in_array("#".$lc, $tag_pool)) && strpos($lc, $word) === 0) {
          $tag_pool[] = "#".$lc;
        }
      }
    }
    if(count($tag_pool) >= $pool_size)
      break;
  }
  
  $tag_pool = array_splice($tag_pool, 0, $pool_size);

/**/
    header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
/**/
	print(json_encode($tag_pool));	  
?>
