<?php 
array_shift($argv);
$stocks = $argv;
foreach ($stocks as $key => $value) {
	$stocks[$key] = "tse_".$value.".tw";
}
$row = "|%5.5s | %5.5s |%11.11s |%8.8s |%8.8s |%10.10s |%10.10s |%10.10s |\n";
while (true) {
	try {
		$time = time();
		$url = 'http://mis.twse.com.tw/stock/api/getStockInfo.jsp?ex_ch='.implode("|", $stocks).'&_='.$time;
		echo chr(27)."[H".chr(27)."[2J";
		printf($row, 'Code', 'Open', 'Limit Down', 'Day Low', 'Current', 'Day High', 'Limit Up', 'Time');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$exec = curl_exec($ch);
		$returnData = trim($exec);
		$data = json_decode($returnData);
		displayStock($data->msgArray, $row);
		curl_close($ch);
		
	} catch (Exception $e) {
		var_dump($e);
	}
	sleep(5);
}

/**
 * msgArray = [
 *   [
 *     f => 五擋賣量
 *     g => 五擋買量
 *     b => 五擋買價
 *     a => 五擋賣價
 *     c => 代號
 *     n => 名稱
 *     o => 開盤價
 *     l => 最低成交價
 *     h => 最高成交價
 *     w => 跌停價
 *     v => 累積成交量
 *     u => 漲停價
 *     t => 揭示時間
 *     s => 當盤成交量
 *     z => 最近成交價
 *     y => 昨日成交價
 *   ]
 * ]
 */
function displayStock($data, $row) {
	foreach ($data as $stock) {
		printf($row, $stock->c, $stock->o, $stock->w, $stock->l, $stock->z, $stock->h, $stock->u, $stock->t);
	}
}