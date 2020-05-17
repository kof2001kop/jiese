<?php
	//发送地址
	$url = "https://cccat.io/";

	//请求头内容
	$headers = array(
    "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
    "Accept-Encoding:gzip, deflate, br",
    "Accept-Language:zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
    "Connection:keep-alive",
    "Host:cccat.io",
    "Upgrade-Insecure-Requests:1",
    "User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36"
	);

	//使用curl发送
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, false);
	$result = curl_exec($ch);
	curl_close($ch);
	$result = substr($result, 0, strpos($result, "\r\n\r\n"));
	$result = explode("\r\n", $result);

	//请求网址:https://cccat.io/cdn-cgi/beacon/performance?req_id=$cf-ray
	$cf_ray = "";
	$Cookies = "";
	foreach($result as $value)
	{
  		if (strpos($value, "CF-RAY: ") !== false)
  		{
			$cf_ray = str_replace("CF-RAY: ", "", $value);
			$cf_ray = substr($cf_ray, 0, strpos($cf_ray, "-"));
  		}
  		else if (strpos($value, "Set-Cookie: ") !== false)
  		{
			$Cookies = str_replace("Set-Cookie: ", "", $value);
			$Cookies = substr($Cookies, 0, strpos($Cookies, ";"));
  		}
 	}

 
	$headers2 = array(
    "Accept:*/*",
    "Accept-Encoding:gzip, deflate, br",
    "Accept-Language:zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
    "Connection:keep-alive",
    "Content-Length:4804",
    "content-type:application/json",
    "Host:cccat.io",
    "Origin:https://cccat.io/",
    "Referer:https://cccat.io/",
    "TE:Trailers",
    "User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36"
	);

	array_push($headers2, $Cookies);

	$ch = curl_init("https://cccat.io/cdn-cgi/beacon/performance?req_id=".$cf_ray);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
	curl_setopt($ch, CURLOPT_POST, false);
	$result = curl_exec($ch);
	curl_close($ch);

	//login
	if (false)
	{
	$post_data['email'] = 'kof2001kopkpr@gmail.com';
	$post_data['passwd'] = 'nmpvvg';

	$headers3 = array(
    "Accept:application/json, text/javascript, */*; q=0.01",
    "Accept-Encoding:gzip, deflate, br",
    "Accept-Language:zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
    "Connection:keep-alive",
    "Content-Length:45",
    "content-type:application/x-www-form-urlencoded; charset=UTF-8",
    "Host:cccat.io",
    "Origin:https://cccat.io/",
    "Referer:https://cccat.io/user/login.php",
    "TE:Trailers",
    "User-Agent:Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:76.0) Gecko/20100101 Firefox/76.0",
    "X-Requested-With:XMLHttpRequest"
	);

	array_push($headers3, $Cookies);

	$ch = curl_init("https://cccat.io/user/_login.php");
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers3);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$result = curl_exec($ch);
	curl_close($ch);

}
	//print_r ($result);
	echo $result;

?>
