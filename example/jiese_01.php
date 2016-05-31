<?php

// Require
require('../vendor/CyrilPerrin/Rss/Rss.php');

// HTTP header
header('content-type:application/rss+xml; charset=utf-8');

$ret = file_get_contents('http://tieba.baidu.com/mo/q----,sz@320_240-1-3---1/m?kw=%E6%88%92%E8%89%B2&lp=5011&lm=&pn=20');
$ret = substr($ret, strpos($ret,'</div>'));
$ret = substr($ret, strpos($ret,';is_bakan')+ 8);
$ret = substr($ret, strpos($ret,';is_bakan')+ 8); 
$ret = substr($ret, strpos($ret,'</a>')+ 4); 

for($i=0; $i < 18; $i++)
{
    $linkBeg = strpos($ret,'<a href="');
    $linkEnd = strpos($ret,';is_bakan');
    $linkArr[$i] = 'http://tieba.baidu.com'.substr($ret, $linkBeg + 9, $linkEnd - $linkBeg - 9)."&pn=0";
    
    
    $idBeg = strpos($linkArr[$i],'kz=');
    $idEnd = strpos($linkArr[$i],'&'); 
    $idArr[$i] = substr($linkArr[$i], $idBeg + 3, $idEnd - $idBeg - 3);
    
    $titleBeg =  strpos($ret,'pinf');
    $titleEnd =  strpos($ret,'</a>'); 
    $titleArr[$i] = substr($ret, $titleBeg + 2, $titleEnd - $titleBeg - 2); 
      $titleArr[$i] = substr($titleArr[$i], strpos($titleArr[$i],'>') + 1);    
    $titleArr[$i] = str_replace("&#160;","",$titleArr[$i]);
    
    $ret = substr($ret, $titleEnd + 4);
  
    //   echo $titleArr[$i]."\n";
    //  echo $linkArr[$i]."\n" ;
    // echo $idArr[$i]."\n";
}

//获取内容$conArr
//echo $linkArr[0]."\n";
for($j=0, $loop = 0, $pageBeg = 0; $j < 2;)
{
    
	$content = file_get_contents($linkArr[$j]);
	$content = substr($content, strpos($content,'</table>'));    

	$count = substr_count($content, '<div class="i">');
    // echo $count;
	for($i=0; $i < $count; $i++)
	{
   		$conBeg = strpos($content,'<p>');
    	$conEnd = strpos($content,'<table>');
    	$ct = substr($content, $conBeg, $conEnd - $conBeg);
        $ct = str_replace("&#160;", "", $ct);
        $ct = str_replace("<br/>", '</p>', $ct);        
        $ct = str_replace('<div class="i">', "<p>", $ct); 
        $ct = substr($ct, strpos($ct, '<p>'));  
        $conArr[$j] .= $ct;
    
   	 	$content = substr($content, $conEnd + 5);    
           
      
	}
    
    //   echo $linkArr[$j];
    $pageBeg += 30;
    //下一页
     if (strstr($content,'pn='.(string)$pageBeg) && $loop < 2)
	{
         //$pageBeg += 30;
        $linkArr[$j] = substr($linkArr[$j], 0, strpos($linkArr[$j],'&pn=') + 4).(string)$pageBeg;
     	$loop++;
	}
    else
    {
        $j++;
        $loop = 0;
        $pageBeg = 0;
    }
}

//echo $conArr[0]."\n";


$rss = new CyrilPerrin\Rss\Rss(
    'jieseba', 'http://tieba.baidu.com', '戒色吧', 'zh', '120', $_SERVER['REQUEST_TIME']
);

//读取18条 max = 18
for($i=0;$i<2;++$i)
{
    
	$rss->addItem(
        'http://'.$idArr[$i].'/'.$_SERVER['REQUEST_TIME'], $titleArr[$i], 'http://'.$idArr[$i].'/'.$_SERVER['REQUEST_TIME'], $conArr[$i], 'Rank News',
    'Cyril', $_SERVER['REQUEST_TIME'], 'Comments', null
);
}




echo $rss;


?>
