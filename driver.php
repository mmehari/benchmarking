<?php
	$server = "localhost";
	$username = "CREW_BM";
	$password = "CREW_BM";
	$database_name = "benchmarking";
	$baseDir = "/var/www/mmehari/";
//	$baseDir = "/var/www/benchmarking/";

	function testbed_getPlatformList(&$TB_platformList)
	{
		global $server, $username, $password, $database_name;

		mysql_connect($server, $username, $password) or die(mysql_error());
		mysql_select_db($database_name) or die(mysql_error());
		$platformList = mysql_query("SELECT platform FROM platformTable") or die(mysql_error());

		while($row = mysql_fetch_array($platformList))
			$TB_platformList[] = $row[0];
		return TRUE;
	}

//	if(testbed_getPlatformList($platformList))
//		foreach($platformList as $platform)
//			echo $platform;

	function testbed_getAppList($TB_platformName, &$TB_appList)
	{
		global $server, $username, $password, $database_name;

		mysql_connect($server, $username, $password) or die(mysql_error());
		mysql_select_db($database_name) or die(mysql_error());
		$appList = mysql_query("SELECT application FROM applicationTable WHERE platform = '$TB_platformName'") or die(mysql_error());

		while($row = mysql_fetch_array($appList))
			$TB_appList[] = $row[0];
		return TRUE;
	}
	
//	testbed_getAppList("tinyOS",$appList);
//		foreach($appList as $app)
//			echo $app;

	function testbed_getAppInfo($TB_platformName, $TB_appName, &$TB_appInfo)
	{
		global $server, $username, $password, $database_name;

		mysql_connect($server, $username, $password) or die(mysql_error());
		mysql_select_db($database_name) or die(mysql_error());
		$appInfo = mysql_query("SELECT version,description FROM applicationTable WHERE platform = '$TB_platformName' and application = '$TB_appName'") or die(mysql_error());
		
		$row = mysql_fetch_array($appInfo);
		$TB_appInfo[] = $row[0];
		$TB_appInfo[] = $row[1];
		return TRUE;
	}
	
//	testbed_getAppInfo("x86Linux","iperf",$appInfo);
//	echo $appInfo[0];
//	echo $appInfo[1];

	function testbed_getAppInputFormatNameList($TB_platformName, $TB_appName, &$TB_appInputFormatNameList)
	{		
		mysql_connect("localhost", "CREW_BM", "CREW_BM") or die(mysql_error());
		mysql_select_db("benchmarking") or die(mysql_error());
		$appInputFormatList = mysql_query("SELECT inputFormat FROM applicationTable WHERE platform = '$TB_platformName' and application = '$TB_appName'") or die(mysql_error());
		
		$inputFormatList = mysql_fetch_array($appInputFormatList);
		$inputFormatArray = split(";",$inputFormatList[0]);
		$TB_appInputFormatNameList = "<inputFormat>";
		foreach($inputFormatArray as $inputFormatItemList)
		{
			$inputFormatItemArray = split("#",$inputFormatItemList);
			$TB_appInputFormatNameList = $TB_appInputFormatNameList."<parameter name='".$inputFormatItemArray[0]."' dscr='".$inputFormatItemArray[1]."' type='".$inputFormatItemArray[2]."' />";
		}		
		$TB_appInputFormatNameList = $TB_appInputFormatNameList."</inputFormat>";
		return TRUE;
	}

//	if(testbed_getAppInputFormatNameList("x86Linux","iperf",$appInputformat));
//		echo $appInputformat;

	function testbed_getAppOutputFormatNameList($TB_platformName, $TB_appName, &$TB_appOutputFormatNameList)
	{		
		mysql_connect("localhost", "CREW_BM", "CREW_BM") or die(mysql_error());
		mysql_select_db("benchmarking") or die(mysql_error());
		$appOutputFormatList = mysql_query("SELECT outputFormat FROM applicationTable WHERE platform = '$TB_platformName' and application = '$TB_appName'") or die(mysql_error());
		
		$outputFormatList = mysql_fetch_array($appOutputFormatList);
		$MPArray = explode("|",$outputFormatList[0]);
		$TB_appOutputFormatNameList = "<outputFormat>";
		foreach($MPArray as $MP)
		{
			$MPList = explode("%",$MP);
			$TB_appOutputFormatNameList = $TB_appOutputFormatNameList."<mp name='".$MPList[0]."'>";

			$outputFormatArray = explode(";",$MPList[1]);
			foreach($outputFormatArray as $outputFormatItemList)
			{
				$outputFormatItemArray = explode("#",$outputFormatItemList);
				$TB_appOutputFormatNameList = $TB_appOutputFormatNameList."<parameter name='".$outputFormatItemArray[0]."' dscr='".$outputFormatItemArray[1]."' type='".$outputFormatItemArray[2]."' unit='".$outputFormatItemArray[3]."' />";
			}
			$TB_appOutputFormatNameList = $TB_appOutputFormatNameList."</mp>";
		}
		$TB_appOutputFormatNameList = $TB_appOutputFormatNameList."</outputFormat>";
		return TRUE;
	}

//	if(testbed_getAppOutputFormatNameList("x86Linux","iperf",$appOutputformat))
//		echo $appOutputformat;


	function testbed_getSensorParamList($TB_sensorAppName, &$TB_sensorParamList)
	{
		mysql_connect("localhost", "CREW_BM", "CREW_BM") or die(mysql_error());
		mysql_select_db("benchmarking") or die(mysql_error());
		$sensorParamList = mysql_query("SELECT property,value FROM sensorParameterTable WHERE application = '$TB_sensorAppName'") or die(mysql_error());
		
		while($row = mysql_fetch_array($sensorParamList))
			$TB_sensorParamList[]=array($row[0],$row[1]);
		return TRUE;
	}

//	if(testbed_getSensorParamList("RadioPerf",$sensorParamList))
//		foreach($sensorParamList as $sensorParam)
//			echo $sensorParam[0];
	
	function testbed_getNodeInfo(&$TB_nodeInfo,$platform)
	{
		mysql_connect("localhost", "CREW_BM", "CREW_BM") or die(mysql_error());
		mysql_select_db("benchmarking") or die(mysql_error());
		$nodeInfo = mysql_query("SELECT * FROM nodeInfoTable where platform='".$platform."'") or die(mysql_error());
		
		while($row = mysql_fetch_array($nodeInfo))
			$TB_nodeInfo[]=array($row[0],$row[1],$row[2],$row[3]);
		return TRUE;
	}
	
//	if(testbed_getNodeInfo($nodeInfo),"x86Linux")
//		foreach($nodeInfo as $info)
//			echo $info[3];
			
	function testbed_getImageList($TB_platformName, &$TB_imageList)
	{
		mysql_connect("localhost", "CREW_BM", "CREW_BM") or die(mysql_error());
		mysql_select_db("benchmarking") or die(mysql_error());
		$imageList = mysql_query("SELECT image FROM imageTable WHERE platform = '$TB_platformName'") or die(mysql_error());
	
		while($row = mysql_fetch_array($imageList))
			$TB_imageList[] = $row[0];
		return TRUE;
	}
	
//	if(testbed_getImageList("x86Linux",$imageList))
//		foreach($imageList as $image)
//			echo $image;

	function testbed_getInterfaceList($TB_nodeName, &$TB_interfaceList)
	{
		mysql_connect("localhost", "CREW_BM", "CREW_BM") or die(mysql_error());
		mysql_select_db("benchmarking") or die(mysql_error());
		$interfaceList = mysql_query("SELECT interface FROM hardConfigTable WHERE node = '$TB_nodeName'") or die(mysql_error());
	
		$row = mysql_fetch_array($interfaceList);
		$TB_interfaceList = split(",",$row[0]);
		return TRUE;
	}
	
//	if(testbed_getWiFiInterfaceList("zotac1",$interfaceList))
//		foreach($interfaceList as $interface)
//			echo $interface;

	function testbed_getWirelessModeList($TB_nodeName, &$TB_wirelessModeList)
	{
		mysql_connect("localhost", "CREW_BM", "CREW_BM") or die(mysql_error());
		mysql_select_db("benchmarking") or die(mysql_error());
		$wirelessModeList = mysql_query("SELECT mode FROM hardConfigTable WHERE node = '$TB_nodeName'") or die(mysql_error());
	
		$row = mysql_fetch_array($wirelessModeList);
		$TB_wirelessModeList = split(",",$row[0]);
		return TRUE;
	}
	
//	if(testbed_getWirelessModeList("zotac1",$wirelessModeList))
//		foreach($wirelessModeList as $wirelessMode)
//			echo $wirelessMode;

	function testbed_getChannelList($TB_nodeName, &$TB_channelList)
	{
		mysql_connect("localhost", "CREW_BM", "CREW_BM") or die(mysql_error());
		mysql_select_db("benchmarking") or die(mysql_error());
		$channelList = mysql_query("SELECT channel FROM hardConfigTable WHERE node = '$TB_nodeName'") or die(mysql_error());
	
		$row = mysql_fetch_array($channelList);
		$TB_channelList = split(",",$row[0]);
		return TRUE;
	}
	
//	if(testbed_getChannelList("zotac1",$channelList))
//		foreach($channelList as $channel)
//			echo $channel;

	function runED($ED_Name,&$PID,&$exprID,&$exprLogFile)
	{
		global $baseDir;

		$exprLogFile = "tmp/".date('Y-m-d\tH.i.s').".log";
		$cmdResult = shell_exec("cd ".$baseDir."tmp/ && omf exec ".$ED_Name." > ".$baseDir.$exprLogFile." & echo $!");	// start ED and retrieve PID
		$resultArray = explode("\n",$cmdResult);
		$PID = $resultArray[0];

		usleep(500000);	// need to wait sometime until the log file start populating

		$handle = fopen($baseDir.$exprLogFile, "r");
		while (strstr($line = fgets($handle),"Experiment ID") == false); // search until the string "Experiment ID" is found
		$temp = explode(":",$line);
		$exprID = trim($temp[2]);
		fclose($handle);
	}

//	runED("tmp/ED.rb",$PID,$exprID,$exprLogFile);
//	echo $PID ."\n".$exprID ."\n".$exprLogFile;
	
?>
