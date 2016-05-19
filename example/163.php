<?php

// Require
require('../vendor/CyrilPerrin/Rss/Rss.php');

// HTTP header
header('content-type:application/rss+xml');

$ret = file_get_contents("http://news.163.com/special/0001386F/rank_news.html");


/* 得到指定新闻的搜索起点  */
$ret = substr($ret,strrpos($ret,"tabContents active"));

preg_match_all('/<a.*?href="http:\/\/news\.163\.com\/\d+\/\d+\/\d+\/[^>]*>([^<]*)<\/a>/i', $ret, $_match);


for($i=0;$i<count($_match[0]);$i++)
{
    /* 匹配标题到titArr */
    preg_match('/>([^<]*)</i', $_match[0][$i], $title);
	$title[0] = substr($title[0],1);
	$title[0] = rtrim($title[0],'<'); 
    $titArr[$i] = $title[0];

    
    /* 匹配连接到newsIDArr */
    $newsIDArr[$i] = substr($_match[0][$i], 40, 16);
    /*  匹配连接到评论comlinkArr  */
    $comlinkArr[$i] = "http://comment.news.163.com/api/v1/products/a2869674571f77b5a0867c3d71db5856/threads/".$newsIDArr[$i]."/comments/hotTopList?offset=0&limit=40&showLevelThreshold=72&headLimit=1&tailLimit=2&callback=getData&ibc=newspc"; 

    /* 匹配新闻内容linkArr */
     preg_match('/"([^<]*)"/i', $_match[0][$i], $link);
	$link[0] = substr($link[0],1);
    $link[0] = rtrim($link[0],'"'); 
    $linkArr[$i] = $link[0];  
    
}

///获取描述
for($i=0;$i<count($_match[0]) && $i < 20;$i++)
{
     $coment = mb_convert_encoding(file_get_contents($comlinkArr[$i]), 'gbk', 'utf-8');
     preg_match_all('/content":"([^<].+?)","/i', $coment, $de); 
    
     $deT = "";
     for ($j=0;$j<count($de[0]); $j++)
     {
         $de[0][$j] = substr($de[0][$j],10);
         $de[0][$j] =  rtrim($de[0][$j],'","');    
         $deT .= "<p>".$de[0][$j]."<p/>";
        
     }
      $deArr[$i] = $deT; 
}

//////////////////////////////////

$rss = new CyrilPerrin\Rss\Rss(
    '163 News', 'http://news.163.com/special/0001386F/rank_news.html', '跟帖排行榜', 'zh', '120', time()
);
//$rss->setImage('Tux', 'tux.png', 'http://www.example.org');

//读取20条新闻
for($i=0;$i<20;$i++)
{
    
	$rss->addItem(
    $i + 1, $titArr[$i], $linkArr[$i], $deArr[$i], "Rank News",
    'Cyril', time(), 'Comments', $linkArr[$i]
);
}


echo $rss;


?>
