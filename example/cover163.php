<?php

// Require
require('../vendor/CyrilPerrin/Rss/Rss.php');

// HTTP header
header('content-type:application/rss+xml');

$xml_data = simplexml_load_file('http://feed43.com/3337137686340764.xml');

$loopTime = count($xml_data->channel->item);

for ($i = 0; $i < $loopTime; ++$i)
{
    $titleArr[$i] = $xml_data->channel->item[0]->title[0]->__toString();    /////获取id
    $contentArr[$i] = $xml_data->channel->item[0]->description[0]->__toString();    ////获取内容
}


// Example
$rss = new CyrilPerrin\Rss\Rss(
    '163 News', 'http://news.163.com/special/0001386F/rank_news.html', '跟帖排行榜', 'zh', '120', $_SERVER['REQUEST_TIME']
);

for ($i = 0; $i < $loopTime; ++$i)
{
	$rss->addItem(
    $i + 1, $titleArr[$i], 'http://www.example.org', $contentArr[$i], 'Rank News',
    'Cyril', $_SERVER['REQUEST_TIME'], 'Comments', 'http://www.example.org'
);
}


echo $rss;

?>