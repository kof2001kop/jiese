<?php

$myfile = fopen("content.html", "r") or die("Unable to open file!");
$load = fread($myfile,filesize("content.html"));
ob_flush();
flush();
fclose($myfile);

//页数分离
$result = explode('/',$_GET["id"]);
$getID = $result[0];
$sPage = $result[1];

if ($load)
{
	$loadBeg = strpos($load,'<div>');
	$loadEnd = strpos($load,'</div>');
	$thisID = substr($load, $loadBeg + 5, $loadEnd - $loadBeg - 5);  
	//echo "this:".$thisID."\nnow:".$_GET['id']."\n";

	if ($thisID == $_GET['id'])
	{	
		echo $load;
		ob_flush();
		flush();
		return;
	}
}

echo "<html>
<head>
<meta charset=\"GBK\">
<body><p>请再刷新一遍!</p></body>
</html>";
ob_flush();
flush();

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

$save = array();
//////////////////////////////
for ($i = $sPage * 40, $j = 0; $i < ($sPage * 40) + 40; $i += 10, $j++) //显示40条
{
	$data[$j] = array(
				'_xsrf' => '35a10f6bcead0e69f14b00607866bf98',
				'method' => 'next',
				'params' => "{\"url_token\":$getID,\"pagesize\":10,\"offset\":$i}"
			 	);
	$urls[$j] = 'https://www.zhihu.com/node/QuestionAnswerListV2';
}

ob_end_clean();

$mh = curl_multi_init();  
foreach ($urls as $i => $url)
{   
  $conn[$i] = curl_init($url);   
  curl_setopt($conn[$i], CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");   

  curl_setopt($conn[$i], CURLOPT_HEADER, 0); 
  curl_setopt($conn[$i], CURLOPT_CONNECTTIMEOUT, 15);  
  curl_setopt($conn[$i], CURLOPT_COOKIE, $cookie);
  curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, true);  // 设置不将爬取代码写到浏览器，而是转化为字符串 
  curl_setopt($conn[$i], CURLOPT_POST, true);
  curl_setopt($conn[$i], CURLOPT_POSTFIELDS, http_build_query($data[$i]));
  curl_multi_add_handle ($mh, $conn[$i]);   
}  

do 
{   
  curl_multi_exec($mh, $active);   
} while ($active);   

foreach ($urls as $i => $url)
{   
   $ret = curl_multi_getcontent($conn[$i]); // 获得爬取的代码字符串   
   $ret = json_decode($ret)->msg;
   $save = array_merge($save, $ret);
   //ob_flush();
   //flush(); 
} // 获得数据变量 

foreach ($urls as $i => $url)
{   
  curl_multi_remove_handle($mh, $conn[$i]);   
  curl_close($conn[$i]);   
}   

curl_multi_close($mh);  


///////////////////
/*$ch = curl_init();
$timeout = 15;
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
curl_setopt($ch, CURLOPT_URL, 'https://www.zhihu.com/node/QuestionAnswerListV2');

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_COOKIE, $cookie);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


/////
$save = array();
for ($i = $sPage * 40; $i < ($sPage * 40) + 40; $i += 10) //显示40条
{
	$data = array(
				'_xsrf' => '35a10f6bcead0e69f14b00607866bf98',
				'method' => 'next',
				'params' => "{\"url_token\":$getID,\"pagesize\":10,\"offset\":$i}"
			 	);

	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	$ret = curl_exec($ch);
   
    $ret = json_decode($ret)->msg;
    $save = array_merge($save, $ret);
}

curl_close($ch);

///////////////
*/
$i = 0;
$combine = "<html>
<head>
<meta charset=\"GBK\">
<div>$getID/$sPage</div>
<body>";

foreach($save as &$value)
{ 
	$value = iconv("utf-8", "gbk", $value);
	
	if (strpos($value,'author-link'))
	{
		$value = substr($value, strpos($value,'author-link')); 
		$urlBeg = strpos($value,'>');
		$urlEnd = strpos($value,'</a>');
		$author[$i] = substr($value, $urlBeg + 1, $urlEnd - $urlBeg - 1);   //作者
	}
	else
	{
		$author[$i] = "匿名";
	}
    
    
    $value = substr($value, strpos($value,'data-votecount="'));
    $urlBeg = strpos($value,'data-votecount="');
    $urlEnd = strpos($value,'">');
    $vote[$i] = substr($value, $urlBeg + 16, $urlEnd - $urlBeg - 16);   //赞同数
    
    $value = substr($value, strpos($value,'zm-editable-content clearfix'));   
    $urlBeg = strpos($value,'>');
    $urlEnd = strpos($value,'</div>');
    $content[$i] = substr($value, $urlBeg + 1, $urlEnd - $urlBeg - 1);   //内容    
    
    $combine .= '<p>['.$vote[$i].']'.$author[$i].':</p> <p>'.$content[$i].'</p><p>____________________________________________</p>';
    
    
    
    $i++;
} 

$combine .= '</body>
</html>';

$myfile1 = fopen('content.html', "w") or die("Unable to open file!");
fwrite($myfile1, $combine);
fclose($myfile1);

//echo $combine;

?>