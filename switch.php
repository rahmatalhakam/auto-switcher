<?php
//developed by rahmatalhakam
//rahmatalhakam@gmail.com
//from https://github.com/rahmatalhakam/
//check hanifhash.io for our mining services
date_default_timezone_set('asia/jakarta');	//set the time zone
$upcounter = 1;								
$delay = 600;	//time for sleep/delay before looping again (secons)
$runningProgram = 'default'; 
	
	//open and read the data from data.csv and wallet.csv//
	$csv = array_map('str_getcsv', file('data.csv'));
	$worker_name = $csv[0][1];
	$email = $csv[1][1];
	$El_Price = $csv[2][1] * $csv[2][2];
	$Ethash_El_Cost = $csv[3][2] * $El_Price / 1000 *24;
	$Cryptonight_El_Cost = $csv[4][2] * $El_Price / 1000 *24;
	$Equihash_El_Cost = $csv[5][2] * $El_Price / 1000 *24;
	$Ethash_Hashrate = $csv[3][1];
	$Cryptonight_Hashrate = $csv[4][1];
	$Equihash_Hashrate = $csv[5][1];

	$csv = array_map('str_getcsv', file('wallet.csv'));
	$eth_wallet = $csv[0][1];
	$etc_wallet = $csv[1][1];
	$xmr_wallet = $csv[2][1];
	$xmr_payment_id = $csv[3][1];
	$etn_wallet = $csv[4][1];
	$etn_payment_id = $csv[5][1];
	$zec_wallet = $csv[6][1];
	//the end of opening data from data.csv and wallet.csv// 

while (true) {

	
	$now = date('Y-m-d H:i:s');

	echo '--------------------------------------' . PHP_EOL;
	echo 'FETCHING -- Upcounter = ' . $upcounter . ' -- ' . $now . PHP_EOL;
	echo '--------------------------------------' . PHP_EOL;
	
	//get data from whattomine.com 
	$output = file_get_contents("https://whattomine.com/coins.json");
	$outjson = json_decode($output, true);

	//get data from exchanger
	$output2 = file_get_contents("https://vip.bitcoin.co.id/api/btc_idr/ticker");
	$outjson2 = json_decode($output2, true);
	$lastPrice = $outjson2['ticker']['sell'];
	
	//get the difficulty of the coins
	$Ethereum_diff = $outjson['coins']['Ethereum']['difficulty24'];
	$EthereumClassic_diff = $outjson['coins']['EthereumClassic']['difficulty24'];
	$Monero_diff = $outjson['coins']['Monero']['difficulty24'];
	$Electroneum_diff = $outjson['coins']['Electroneum']['difficulty24'];
	$Zcash_diff = $outjson['coins']['Zcash']['difficulty24'];

	//get the block reward of the coins
	$Ethereum_reward = $outjson['coins']['Ethereum']['block_reward24'];
	$EthereumClassic_reward = $outjson['coins']['EthereumClassic']['block_reward24'];
	$Monero_reward = $outjson['coins']['Monero']['block_reward24'];
	$Electroneum_reward = $outjson['coins']['Electroneum']['block_reward24'];
	$Zcash_reward = $outjson['coins']['Zcash']['block_reward24'];

	//get the exchanger rate of the coins
	$Ethereum_ex = $outjson['coins']['Ethereum']['exchanger_rate24'];
	$EthereumClassic_ex = $outjson['coins']['EthereumClassic']['exchanger_rate24'];
	$Monero_ex = $outjson['coins']['Monero']['exchanger_rate24'];
	$Electroneum_ex = $outjson['coins']['Electroneum']['exchanger_rate24'];
	$Zcash_ex = $outjson['coins']['Zcash']['exchanger_rate24'];

	//logic to know the reward for 1 day 
	$ETH_reward = $Ethash_Hashrate / $Ethereum_diff * $Ethereum_reward * 3600 * 24;
	$ETC_reward = $Ethash_Hashrate / $EthereumClassic_diff * $EthereumClassic_reward * 3600 * 24;
	$XMR_reward = $Cryptonight_Hashrate / $Monero_diff * $Monero_reward* 3600 * 24;
	$ETN_reward = $Cryptonight_Hashrate / $Electroneum_diff * $Electroneum_reward* 3600 * 24;
	$ZEC_reward = $Equihash_Hashrate / ($Zcash_diff * 2**13)  * $Zcash_reward * 3600 * 24;

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
	$ETH_profitability = $ETH_BTC_IDR - $Ethash_El_Cost;
	$ETC_profitability = $ETC_BTC_IDR - $Ethash_El_Cost;
	$XMR_profitability = $XMR_BTC_IDR - $Cryptonight_El_Cost;
	$ETN_profitability = $ETN_BTC_IDR - $Cryptonight_El_Cost;
	$ZEC_profitability = $ZEC_BTC_IDR - $Equihash_El_Cost;

	//each data are collected  in one array
	$profitability["eth"] = $ETH_profitability;
	$profitability["etc"] = $ETC_profitability;
	$profitability["xmr"] = $XMR_profitability;
	$profitability["etn"] = $ETN_profitability;
	$profitability["zec"] =$ZEC_profitability;

	//show the data in console
	$file_date = date('Ymd');
	$ETH=  "[ETH_REWARD : $ETH_reward] [BTC : $ETH_BTC] [IDR : $ETH_BTC_IDR] [EL_COST : $Ethash_El_Cost] [Profit : $ETH_profitability]".PHP_EOL;
	$ETC= "[ETC_REWARD : $ETC_reward] [BTC : $ETC_BTC] [IDR : $ETC_BTC_IDR] [EL_COST : $Ethash_El_Cost] [Profit : $ETC_profitability]".PHP_EOL;
	$XMR=  "[XMR_REWARD : $XMR_reward] [BTC : $XMR_BTC] [IDR : $XMR_BTC_IDR] [EL_COST : $Cryptonight_El_Cost] [Profit : $XMR_profitability]".PHP_EOL;
	$ETN=  "[ETN_REWARD : $ETN_reward] [BTC : $ETN_BTC] [IDR : $ETN_BTC_IDR] [EL_COST : $Cryptonight_El_Cost] [Profit : $ETN_profitability]".PHP_EOL;
	$ZEC=  "[ZEC_REWARD : $ZEC_reward] [BTC : $ZEC_BTC] [IDR : $ZEC_BTC_IDR] [EL_COST : $Equihash_El_Cost] [Profit : $ZEC_profitability]".PHP_EOL;
	echo $ETH,$ETC,$XMR,$ETN,$ZEC;
	echo "The Most profits : ".array_search(max($profitability), $profitability)." => ".max($profitability).PHP_EOL;
		
		#############################################################
		## switch app logic                                        ##
		#############################################################

	//this variable contains the most profit coin (in string)
	$bestCoinProfitability = array_search(max($profitability), $profitability);

	//this switch case function is used to run and kill mining program. Divided by the coin's name. 
	//Each case have 3 conditions: 
	//		1) when the program is just running for the first time
	//		2) when the most profit coin is still mined by the mining program
	//		3) when the most profit coin is changing. So. it will kill the running program and then run the most profit program  
	//$epools variable is used to rewrite the epools.txt, it will rewrite the default setting as well as $run variable.
	//$check variable is used to check the running miner program in windows system.
	switch ($bestCoinProfitability) {
		case 'eth':
		if($runningProgram=='default'){
			$epools = "POOL: eth-eu2.nanopool.org:9999, WALLET: ".$eth_wallet."/".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: eth-us-east1.nanopool.org:9999, WALLET: ".$eth_wallet."/".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: eth-us-west1.nanopool.org:9999, WALLET: ".$eth_wallet."/".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: eth-asia1.nanopool.org:9999, WALLET: ".$eth_wallet."/".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0";
			$run = "@echo off
set current_dir=%cd%
start eth\EthDcrMiner64.exe -epool eth-eu1.nanopool.org:9999 -ewal ".$eth_wallet."/".$worker_name."/".$email." -epsw x -mode 1 -ftime 10";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
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
			$epools = "POOL: eth-eu2.nanopool.org:9999, WALLET: ".$eth_wallet."/".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: eth-us-east1.nanopool.org:9999, WALLET: ".$eth_wallet."/".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: eth-us-west1.nanopool.org:9999, WALLET: ".$eth_wallet."/".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: eth-asia1.nanopool.org:9999, WALLET: ".$eth_wallet."/".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0";
			$run = "@echo off
set current_dir=%cd%
start eth\EthDcrMiner64.exe -epool eth-eu1.nanopool.org:9999 -ewal ".$eth_wallet."/".$worker_name."/".$email." -epsw x -mode 1 -ftime 10";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('eth');
			$runningProgram='eth';
		}
		break;
		case 'etc':
		if($runningProgram=='default'){
			$epools = "POOL: etc-eu1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-eu2.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-us-east1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-us-west1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-asia1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-jp1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-au1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0";
			$run = "@echo off
set current_dir=%cd%
start eth\EthDcrMiner64.exe -epool etc-eu1.nanopool.org:19999 -ewal ".$etc_wallet."/".$worker_name."/".$email." -epsw x -mode 1 -ftime 10";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
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
			$epools = "POOL: etc-eu1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-eu2.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-us-east1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-us-west1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-asia1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-jp1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0
POOL: etc-au1.nanopool.org:19999, WALLET: ".$etc_wallet.".".$worker_name."/".$email.", PSW: x, WORKER: , ESM: 0, ALLPOOLS: 0";
			$run = "@echo off
set current_dir=%cd%
start eth\EthDcrMiner64.exe -epool etc-eu1.nanopool.org:19999 -ewal ".$etc_wallet."/".$worker_name."/".$email." -epsw x -mode 1 -ftime 10";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'eth'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('eth');
			$runningProgram='etc';
		}
		break;
		case 'xmr':
		if($runningProgram=='default'){
			if($xmr_payment_id==""){
			$epools = "POOL: stratum+ssl://xmr-eu1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-eu2.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-asia1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-us-east1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-us-west1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0";
			}
			else{
			$epools = "POOL: stratum+ssl://xmr-eu1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-eu2.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-asia1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-us-east1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-us-west1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0";
			}

			$run = "@echo off
set current_dir=%cd%
start xmr\NsGpuCNMiner.exe";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'xmr'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
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
			if($xmr_payment_id==""){
			$epools = "POOL: stratum+ssl://xmr-eu1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-eu2.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-asia1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-us-east1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-us-west1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0";
			}
			else{
			$epools = "POOL: stratum+ssl://xmr-eu1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-eu2.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-asia1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-us-east1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0
POOL: stratum+ssl://xmr-us-west1.nanopool.org:14433, WALLET: ".$xmr_wallet.".".$xmr_payment_id.".".$worker_name."/".$email.", PSW: x, ALLPOOLS: 0";
			}
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
			if ($etn_payment_id=="") {
			$epools = "POOL: ssl://etn-eu1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-eu2.nanopool.org:13433, WALLET: ".$etn_wallet.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-asia1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-us-east1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-us-west1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1";
			}
			else{
			$epools = "POOL: ssl://etn-eu1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-eu2.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-asia1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-us-east1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-us-west1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1";
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
			$epools = "POOL: ssl://etn-eu1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-eu2.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-asia1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-us-east1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
POOL: ssl://etn-us-west1.nanopool.org:13433, WALLET: ".$etn_wallet.".".$etn_payment_id.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 1
			";
			$run = "@echo off
set current_dir=%cd%
start xmr\NsGpuCNMiner.exe";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'xmr'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'xmr'.DIRECTORY_SEPARATOR.'runApp.bat',$run);
			runProgram('xmr');
			$runningProgram='etn';
		}
		break;
		case 'zec':
		if($runningProgram=='default'){
			$epools = "POOL: ssl://zec-eu1.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 0
POOL: ssl://zec-eu2.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email." PSW: z, ALLPOOLS: 0
POOL: ssl://zec-asia1.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email." PSW: z, ALLPOOLS: 0
POOL: ssl://zec-us-east1.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email." PSW: z, ALLPOOLS: 0
POOL: ssl://zec-us-west1.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email." PSW: z, ALLPOOLS: 0";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'zec'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
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
			$epools = "POOL: ssl://zec-eu1.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email.", PSW: z, ALLPOOLS: 0
POOL: ssl://zec-eu2.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email." PSW: z, ALLPOOLS: 0
POOL: ssl://zec-asia1.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email." PSW: z, ALLPOOLS: 0
POOL: ssl://zec-us-east1.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email." PSW: z, ALLPOOLS: 0
POOL: ssl://zec-us-west1.nanopool.org:6633, WALLET: ".$zec_wallet.".".$worker_name."/".$email." PSW: z, ALLPOOLS: 0";
			file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'zec'.DIRECTORY_SEPARATOR.'epools.txt',$epools);
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