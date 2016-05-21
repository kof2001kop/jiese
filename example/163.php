<?php

// Require
require('../vendor/CyrilPerrin/Rss/Rss.php');

// HTTP header
header('content-type:application/rss+xml; charset=utf-8');

$ret = file_get_contents('http://news.163.com/special/0001386F/rank_news.html');


/* 得到指定新闻的搜索起点  */
$ret = substr($ret,strrpos($ret,'tabContents active'), 10000);

//preg_match_all('/<a.*?href="http:\/\/news\.163\.com\/\d+\/\d+\/\d+\/[^>]*>([^<]*)<\/a>/i', $ret, $_match);
//echo $_match[0][1];

for($i=0;$i < 20; )
{
    $ret = substr($ret, strpos($ret,'<a href="http://news.163.com/')+29);

	if (is_numeric($ret[0]))
	{
	$newsIDArr[$i] = substr($ret, 11,  strpos($ret, '.html')-11);     // 匹配连接到newsIDArr 
	$beg = strpos($ret, ">") + 1;
	$titArr[$i] = iconv('GBK', 'UTF-8', substr($ret, $beg, strpos($ret, '<')-$beg));    // 匹配标题到titArr   
    //  匹配连接到评论comlinkArr  
    $comlinkArr[$i] = 'http://comment.news.163.com/api/v1/products/a2869674571f77b5a0867c3d71db5856/threads/'.$newsIDArr[$i].'/comments/hotTopList?offset=0&limit=40&showLevelThreshold=72&headLimit=1&tailLimit=2&callback=getData&ibc=newspc'; 

    ++$i;
    }
}

///获取描述

for($i=0; $i < 5;$i++)
{
    $coment =file_get_contents($comlinkArr[$i]);
    
    ///获取id与json的唯一分隔符']'的位置
    $onlyDiv = strpos($coment,'],');
    
    //// id, 其中idT[1]为全部所需id
    $id = substr($coment, 25, $onlyDiv-26);  
    $idT = explode('","',$id);
    
    /// 评论内容的json
    $json = json_decode(substr($coment, $onlyDiv + 13, -4));  

    // 遍历开始
    $loop = count($idT); 
    for ($j=0;$j< $loop; ++$j)
    {
        $div = explode(',',$idT[$j]);
        $effect = count($div);
        for ($m=0;$m<$effect; ++$m)
        {
            $vote = ($m == $effect - 1) ? '['.$json->{$div[$m]}->{'vote'}.'] ' : ''; 
       
         	$deArr[$i] .= '<p>'.$vote.$json->{$div[$m]}->{'user'}->{'nickname'}.': '. $json->{$div[$m]}->{'content'}.'</p>';
        }
         $deArr[$i] .= '<p>-------</p>';    
    }
}

//////////////////////////////////

$rss = new CyrilPerrin\Rss\Rss(
    '163 News', 'http://news.163.com/special/0001386F/rank_news.html', '跟帖排行榜', 'zh', '120', $_SERVER['REQUEST_TIME']
);
//$rss->setImage('Tux', 'tux.png', 'http://www.example.org');

//读取20条新闻
for($i=0;$i<5;++$i)
{
    
	$rss->addItem(
    $i + 1, $titArr[$i], 'http://www.example.org', $deArr[$i], 'Rank News',
    'Cyril', $_SERVER['REQUEST_TIME'], 'Comments', 'http://www.example.org'
);
}


echo $rss;



?>
