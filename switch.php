<?php
//developed by rahmatalhakam and muh hanif
//rahmatalhakam@gmail.com
//from https://github.com/rahmatalhakam/
//check hanifhash.io for our mining services
date_default_timezone_set('asia/jakarta');
$upcounter = 1;								
$delay = 10;
$runningProgram = 'default'; 
	
	$csv = array_map('str_getcsv', file('data.csv'));
	$wn = $csv[0][1];
	$mail = $csv[1][1];
	$El_Price = $csv[2][1] * $csv[2][2];
	$Eth_El_Cst = $csv[3][2] * $El_Price / 1000 *24;
	$CN_El_Cst = $csv[4][2] * $El_Price / 1000 *24;
	$EQ_El_Cst = $csv[5][2] * $El_Price / 1000 *24;
	$ethashrate = $csv[3][1];
	$CNhashrate = $csv[4][1];
	$EQhashrate = $csv[5][1];

	$csv = array_map('str_getcsv', file('wallet.csv'));
	$Eth_wl = $csv[0][1];
	$Etc_wl = $csv[1][1];
	$xmr_wl = $csv[2][1];
	$xmr_PID = $csv[3][1];
	$etn_wl = $csv[4][1];
	$etn_PID = $csv[5][1];
	$zec_wl = $csv[6][1];

while (true) {
	
	$now = date('Y-m-d H:i:s');

	echo '--------------------------------------' . PHP_EOL;
	echo 'FETCHING -- Upcounter = ' . $upcounter . ' -- ' . $now . PHP_EOL;
	echo '--------------------------------------' . PHP_EOL;
	
	$out = file_get_contents("https://whattomine.com/coins.json");
	$outjs = json_decode($out, true);

	$out2 = file_get_contents("https://vip.bitcoin.co.id/api/btc_idr/ticker");
	$outjs2 = json_decode($out2, true);
	$lastPrice = $outjs2['ticker']['sell'];
	
	//get the difficulty of the coins
	$Eth_diff = $outjs['coins']['Ethereum']['difficulty24'];
	$Etc_diff = $outjs['coins']['EthereumClassic']['difficulty24'];
	$XMR_diff = $outjs['coins']['Monero']['difficulty24'];
	$Etn_diff = $outjs['coins']['Electroneum']['difficulty24'];
	$zec_diff = $outjs['coins']['Zcash']['difficulty24'];

	//get the block reward of the coins
	$Ethereum_reward = $outjs['coins']['Ethereum']['block_reward24'];
	$EthereumClassic_reward = $outjs['coins']['EthereumClassic']['block_reward24'];
	$Monero_reward = $outjs['coins']['Monero']['block_reward24'];
	$Electroneum_reward = $outjs['coins']['Electroneum']['block_reward24'];
	$Zcash_reward = $outjs['coins']['Zcash']['block_reward24'];

	//get the exchanger rate of the coins
	$Ethereum_ex = $outjs['coins']['Ethereum']['exchange_rate24'];
	$EthereumClassic_ex = $outjs['coins']['EthereumClassic']['exchange_rate24'];
	$Monero_ex = $outjs['coins']['Monero']['exchange_rate24'];
	$Electroneum_ex = $outjs['coins']['Electroneum']['exchange_rate24'];
	$Zcash_ex = $outjs['coins']['Zcash']['exchange_rate24'];

	//logic to know the reward for 1 day 
	$ETH_reward = $ethashrate / $Eth_diff * $Ethereum_reward * 3600 * 24;
	$ETC_reward = $ethashrate / $Etc_diff * $EthereumClassic_reward * 3600 * 24;
	$XMR_reward = $CNhashrate / $XMR_diff * $Monero_reward* 3600 * 24;
	$ETN_reward = $CNhashrate / $Etn_diff * $Electroneum_reward* 3600 * 24;
	$ZEC_reward = $EQhashrate / ($zec_diff * 2**13)  * $Zcash_reward * 3600 * 24;

	//logic to know reward in btc
	$ETH_BTC = $ETH_reward  * $Ethereum_ex;
	$ETC_BTC = $ETC_reward * $EthereumClassic_ex;
	$XMR_BTC = $XMR_reward * $Monero_ex ;
	$ETN_BTC = $ETN_reward * $Electroneum_ex ;
	$ZEC_BTC = $ZEC_reward * $Zcash_ex ;

	//logic to know the reward in IDR (rupiah)
	$ETH_BTC_IDR = $ETH_BTC * $lastPrice;
	$ETC_BTC_IDR = $ETC_BTC * $lastPrice;
	$XMR_BTC_IDR = $XMR_BTC * $lastPrice;
	$ETN_BTC_IDR = $ETN_BTC * $lastPrice;
	$ZEC_BTC_IDR = $ZEC_BTC * $lastPrice;

	//logic to know the net profitability after reduced by electricity cost
	$ETH_profitability = $ETH_BTC_IDR - $Eth_El_Cst;
	$ETC_profitability = $ETC_BTC_IDR - $Eth_El_Cst;
	$XMR_profitability = $XMR_BTC_IDR - $CN_El_Cst;
	$ETN_profitability = $ETN_BTC_IDR - $CN_El_Cst;
	$ZEC_profitability = $ZEC_BTC_IDR - $EQ_El_Cst;

	//each data are collected  in one array
	$profitability["eth"] = $ETH_profitability;
	$profitability["etc"] = $ETC_profitability;
	$profitability["xmr"] = $XMR_profitability;
	$profitability["etn"] = $ETN_profitability;
	$profitability["zec"] =$ZEC_profitability;

	//show the data in console
	$file_date = date('Ymd');
	$ETH=  "[ETH_REWARD : $ETH_reward] [BTC : $ETH_BTC] [IDR : $ETH_BTC_IDR] [EL_COST : $Eth_El_Cst] [Profit : $ETH_profitability]".PHP_EOL;
	$ETC= "[ETC_REWARD : $ETC_reward] [BTC : $ETC_BTC] [IDR : $ETC_BTC_IDR] [EL_COST : $Eth_El_Cst] [Profit : $ETC_profitability]".PHP_EOL;
	$XMR=  "[XMR_REWARD : $XMR_reward] [BTC : $XMR_BTC] [IDR : $XMR_BTC_IDR] [EL_COST : $CN_El_Cst] [Profit : $XMR_profitability]".PHP_EOL;
	$ETN=  "[ETN_REWARD : $ETN_reward] [BTC : $ETN_BTC] [IDR : $ETN_BTC_IDR] [EL_COST : $CN_El_Cst] [Profit : $ETN_profitability]".PHP_EOL;
	$ZEC=  "[ZEC_REWARD : $ZEC_reward] [BTC : $ZEC_BTC] [IDR : $ZEC_BTC_IDR] [EL_COST : $EQ_El_Cst] [Profit : $ZEC_profitability]".PHP_EOL;
	echo $ETH,$ETC,$XMR,$ETN,$ZEC;
	echo "The Most profits : ".array_search(max($profitability), $profitability)." => ".max($profitability).PHP_EOL;
		
		#############################################################
		## switch app logic                                        ##
		#############################################################

	//this variable contains the most profit coin (in string)
	$BestOpit = array_search(max($profitability), $profitability);

	//this switch case function is used to run and kill mining program. Divided by the coin's name. 
	//Each case have 3 conditions: 
	//		1) when the program is just running for the first time
	//		2) when the most profit coin is still mined by the mining program
	//		3) when the most profit coin is changing. So. it will kill the running program and then run the most profit program  
	//$epools variable is used to rewrite the epools.txt, it will rewrite the default setting as well as $run variable.
	//$check variable is used to check the running miner program in windows system.
	switch ($BestOpit) {
		case 'eth':
		if($runningProgram=='default'){
			$run = "@echo off
set current_dir=%cd%
start eth\EthDcrMiner64.exe -epool eth-eu1.nanopool.org:9999 -ewal ".$Eth_wl."/".$wn."/".$mail." -epsw x -mode 1 -ftime 10";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('eth');
			$runningProgram='eth';
			echo "Program eth is running now.".PHP_EOL;
		}
		else if ($runningProgram=='eth'){
			$check = exec('tasklist /fi "imagename eq EthDcrMiner64.exe" ');
			if ($check=='INFO: No tasks are running which match the specified criteria.') {
				echo "The Program is closed, will be executed again.".PHP_EOL;
				sleep(5);
				runProgram('eth');
			}
			else
				echo "Program eth is still running".PHP_EOL;
		}
		else{
			killProgram($runningProgram);
			$run = "@echo off
set current_dir=%cd%
start eth\EthDcrMiner64.exe -epool eth-eu1.nanopool.org:9999 -ewal ".$Eth_wl."/".$wn."/".$mail." -epsw x -mode 1 -ftime 10";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('eth');
			$runningProgram='eth';
		}
		break;
		case 'etc':
		if($runningProgram=='default'){
			$run = "@echo off
set current_dir=%cd%
start eth\EthDcrMiner64.exe -epool etc-eu1.nanopool.org:19999 -ewal ".$Etc_wl."/".$wn."/".$mail." -epsw x -mode 1 -ftime 10";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('eth');
			$runningProgram='etc';
			echo "Program etc is running now.".PHP_EOL;
		}
		else if ($runningProgram=='etc'){
			$check = exec('tasklist /fi "imagename eq EthDcrMiner64.exe" ');
			if ($check=='INFO: No tasks are running which match the specified criteria.') {
				echo "The Program is closed, will be executed again.".PHP_EOL;
				sleep(5);
				runProgram('eth');
			}
			else
				echo "Programs etc is still running".PHP_EOL;
		}
		else{
			killProgram($runningProgram);
			$run = "@echo off
set current_dir=%cd%
start eth\EthDcrMiner64.exe -epool etc-eu1.nanopool.org:19999 -ewal ".$Etc_wl."/".$wn."/".$mail." -epsw x -mode 1 -ftime 10";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('eth');
			$runningProgram='etc';
		}
		break;
		case 'xmr':
		if($runningProgram=='default'){
			if($xmr_PID==""){
			}
			else{
			
			$run = "@echo off
set current_dir=%cd%
start xmr\NsGpuCNMiner.exe";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'xmr'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('xmr');
			$runningProgram='xmr';
			echo "Program xmr is running now.".PHP_EOL;
		}
		else if ($runningProgram=='xmr'){
			$check = exec('tasklist /fi "imagename eq NsGpuCNMiner.exe" ');
			if ($check=='INFO: No tasks are running which match the specified criteria.') {
				echo "The Program is closed, will be executed again.".PHP_EOL;
				sleep(5);
				runProgram('xmr');
			}
			else
				echo "Programs xmr is still running".PHP_EOL;
		}
		else{
			killProgram($runningProgram);
			if($xmr_PID==""){

			$run = "@echo off
set current_dir=%cd%
start xmr\NsGpuCNMiner.exe";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'xmr'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'xmr'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('xmr');
			$runningProgram='xmr';
		}
		break;
		case 'etn':
		if($runningProgram=='default'){
			if ($etn_PID=="") {
			}
			
			$run = "@echo off
set current_dir=%cd%
start xmr\NsGpuCNMiner.exe";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'xmr'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'xmr'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('xmr');
			$runningProgram='etn';
			echo "Program etn is running now.".PHP_EOL;
		}
		else if ($runningProgram=='etn'){
			$check = exec('tasklist /fi "imagename eq NsGpuCNMiner.exe" ');
			if ($check=='INFO: No tasks are running which match the specified criteria.') {
				echo "The Program is closed, will be executed again.".PHP_EOL;
				sleep(5);
				runProgram('xmr');
			}
			else
				echo "Programs etn is still running".PHP_EOL;
		}
		else{
			killProgram($runningProgram);
			$run = "@echo off
set current_dir=%cd%
start xmr\NsGpuCNMiner.exe";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'xmr'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('xmr');
			$runningProgram='etn';
		}
		break;
		case 'zec':
		if($runningProgram=='default'){
			
			runProgram('zec');
			$runningProgram='zec';
			echo "Program zec is running now.".PHP_EOL;
		}
		else if ($runningProgram=='zec'){
			$check = exec('tasklist /fi "imagename eq ZecMiner64.exe" ');
			if ($check=='INFO: No tasks are running which match the specified criteria.') {
				echo "The Program is closed, will be executed again.".PHP_EOL;
				sleep(5);
				runProgram('zec');
			}
			else
				echo "Programs zec is still running".PHP_EOL;
		}
		else{
			killProgram($runningProgram);
			runProgram('zec');
			$runningProgram='zec';
		}
		break;
		default:
		echo "Error to get data from API or Check the internet connection". PHP_EOL;
		break;
	}

	$upcounter++;
	sleep($delay);
}

//function for running the 'runApp.bat' in mining program folder
function runProgram($coinn)
{
	pclose(popen("start /B ".$coinn."\\runApp.bat", "r"));
}

//function to kill the mining program
function killProgram($coinn)
{
	if ($coinn=='eth' || $coinn=='etc') {
		$killApp = "taskkill /f /im EthDcrMiner64.exe";

	}else
	if ($coinn=='etn' || $coinn=='xmr') {
		$killApp = "taskkill /f /im NsGpuCNMiner.exe";

	}
	else{
		$killApp = "taskkill /f /im ZecMiner64.exe";

	}
	exec($killApp);
	//this sleep function is used to wait until the mining program close properly
	sleep(10);
}
