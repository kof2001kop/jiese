<?php

$myfile = fopen("results.txt", "r") or die("Unable to open file!");
$load = fread($myfile,filesize("results.txt"));
ob_flush();
flush();
fclose($myfile);

echo $load;
ob_flush();
flush();

if ($load)
{
	$loadBeg = strpos($load,'<title>');
	$loadEnd = strpos($load,'</title>');
	$thisID = substr($load, $loadBeg + 7, $loadEnd - $loadBeg - 7); // echo "this:".$thisID."\nnow:".$_GET['key']."\n";


	if ($thisID == $_GET['key'])
	{	
		return;
	}
}

// Require
require('vendor/CyrilPerrin/Rss/Rss.php');

// HTTP header
header('content-type:application/rss+xml; ');
//charset=gbk
$cookie_arr = array(
		'__utma' => '51854390.547175385.1465103152.1465103152.1465103152.1',
		'__utmb' => '51854390.4.10.1465103152',
		'__utmc' => '51854390',
		'__utmv' => '51854390.100-1|2=registration_date=20141126=1^3=entry_date=20141126=1',
		'__utmz' => '51854390.1465103152.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)',
		'_xsrf' => '35a10f6bcead0e69f14b00607866bf98',
		'_za' => '	7776c5b1-8a92-4184-98ea-94d91f8bd5b0',
		'_zap' => '9b92a5f1-a9eb-4aec-a170-f69b313d4a20',
    /**/'_zap' => '9349f159-e616-4d41-9799-11ee42b5c5eb',
		'cap_id' => '"ZGM2ZWIwNWQ5MDA4NDc4Y2E5YTM3ZDBlNGRmM2MwNGM=|1465103148|2cd8a5bf03efc76882b9b2b8834518eb89163d3d"',
		'd_c0' => '"AGAAeabQBwqPTqFiOFPcAndJwnLwUe_cCVI=|1465103150"',
		'l_cap_id' => '"MGE1ZjNjY2UyNTU0NGRjNjk0ZWZkZTAxMzJjZjBlMWM=|1465103148|e161e0f396aa88ad720a1288cd10f0e5f3175a54"',
		'l_n_c' => '1',
		'login' => '"NDAxZGJiZGQ3ZjEwNDk2Yjk5NWRmYjBmMzRlNzRiZTI=|1465103171|e76438bf82a982dcaecc5853e85eccc28742ca88"',
		'q_c1' => '	841b0eab49e2456f9a691673c8c64ca2|1465103148000|1465103148000',
    's-i' => '6',//
    's-q' => '%E6%85%A2%E6%80%A7%E8%83%83%E7%82%8E',//
    's-t' => 'autocomplete',//
    'sid' => 'e63rlk6q',//
		'z_c0' => '	Mi4wQUFDQW9QRkJBQUFBWUFCNXB0QUhDaGNBQUFCaEFsVk5VMEI3VndDWHZXZEFVZjNhQ1oyb1dmeEwtSTN6RC1QWE1n|1465103187|a007e3a57dd4f0552a748a34187bb9ba45942ddc'
	);


$cookie = '';
		foreach ($cookie_arr as $key => $value) {
			if($key != 'z_c0')
				$cookie .= $key . '=' . $value . ';';
			else
				$cookie .= $key . '=' . $value;
		}


$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_COOKIE, $cookie);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$timeout = 20;
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

//页数与key分离
//说明：关键字/搜索问题的起始页数/问题答案的起始页数
$result = explode('/',$_GET["key"]);

if (count($result) == 1)   //即没有写页数,默认为0
    $page = 0;
else
    $page = $result[1];

if (count($result) == 3)
	$secondpage = $result[2];
else 
	$secondpage = 0;

//转码
$key = urlencode(base64_decode($result[0]));

$save = array();
for ($i = $page * 20; $i < ($page * 20) + 20; $i += 10)    ///显示20条
{
	curl_setopt($ch, CURLOPT_URL, 'https://www.zhihu.com/r/search?q='.$key.'&type=content&offset='."$i");

	$ret = curl_exec($ch);
   
    $ret = json_decode($ret)->htmls;
    $save = array_merge($save, $ret);

}
curl_close($ch);
//////1

$i = 0;
foreach($save as &$value)
{ 
   //  $value = iconv("utf-8","gbk",$value);
    if (strpos($value,'http://zhuanlan.zhihu.com') || !strpos($value,'data-bind-votecount'))
    {
        continue;
    }
    
    $value = substr($value, strpos($value,'href="/question/') + 6); 
    $urlEnd = strpos($value,'"');
    $qid[$i] = substr($value, 10, $urlEnd - 10);   //问题id
    
    
    $urlBeg = strpos($value,'>');
    $urlEnd = strpos($value,'</a>');
    $qcontent[$i] = substr($value, $urlBeg + 1, $urlEnd - $urlBeg - 1);   //问题内容 
	$qcontent[$i] = strip_tags($qcontent[$i]);
    
    $value = substr($value, strpos($value,'data-bind-votecount'));
    $urlBeg = strpos($value,'>');
    $urlEnd = strpos($value,'</a>');
    $vote[$i] = substr($value, $urlBeg + 1, $urlEnd - $urlBeg - 1);   //赞同数
    
    
    $i++;
}

$pubTime = $_SERVER['REQUEST_TIME'];
$rss = new CyrilPerrin\Rss\Rss(
    $_GET['key'], 'https://www.zhihu.com', '知乎', 'zh', '120', $pubTime
);

//读取20条新闻
for($j=0; $j < $i; ++$j)
{
    
	$rss->addItem(
        $j + 1, '['.$vote[$j].']'.$qcontent[$j], 'http://php1-kof2001kop.rhcloud.com/zhihuAnswer.php?id='.$qid[$j].'/'.$secondpage, '', 'Zhihu',
    'Cyril', $pubTime, 'Comments', 'https://www.zhihu.com'
);
}

$myfile = fopen('results.txt', "w") or die("Unable to open file!");
fwrite($myfile, $rss);
fclose($myfile);

//echo $rss;
?>