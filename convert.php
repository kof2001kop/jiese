<?php

header('content-type:application/rss+xml');


//echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


//echo $ret;
//$id=$_GET["url"];
//$name=$_GET["name"];
//echo $id;
//echo $name; 'http://kof2001kop.tumblr.com/rss'
$ret = file_get_contents($_GET["url"]);
preg_match_all('/<category[^>]*>([^<]*)<\/category><category>IFTTT<\/category>/i', $ret, $_match);



function &str_replace_limit($search, $replace, $subject, $limit=-1){  
    if(is_array($search)){  
        foreach($search as $k=>$v){  
            $search[$k] = '`'. preg_quote($search[$k], '`'). '`';  
        }  
    }else{  
        $search = '`'. preg_quote($search, '`'). '`';  
    }  
    return preg_replace($search, $replace, $subject, $limit);  
}  




for($i=0;$i<count($_match[0]);$i++){

     $ret = str_replace_limit("notitle", strstr($_match[0][$i], 'I', TRUE ), $ret, 1); // user/order_list  
    
}


echo $ret;
?>
