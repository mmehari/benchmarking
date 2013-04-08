<?php
	include "driver.php";
	
	$event=$_GET['event'];
	$baseDir = "/var/www/benchmarking/";
//	$baseDir = "/var/www/mmehari/";

//	$omlDir = "/home/mmehari/oml2/";
	$omlDir = "/home/michael/oml2/";
//	$omlDir = "/var/lib/oml2/";
//----------------------------------------------------------------------------------------------------//
	// when the button "Add Application" or "Default" is pressed
	if($event=="newAPP" || $event=="default")
	{
		echo "<table style='border:1px solid black;' border='0'><tr><td colspan='2'><button type='button' disabled='disabled'>Default</button><button type='button' id='custom'>Custom</button></td><td align='right'><button type='button' id='delete'>Delete</button></td></tr><tr><td>Platform</td><td><select id='platform'>";
		testbed_getPlatformList($platformList);
		foreach($platformList as $platform)
			echo "<option value='$platform'>$platform</option>";
		echo"</select></td></tr><tr><td>Application</td><td><select id='application'>";
		testbed_getAppList($platformList[0],$appList);
		foreach($appList as $app)
			echo "<option value='$app'>$app</option>";
		echo"</select></td></tr>";
		testbed_getAppInfo($platformList[0],$appList[0],$appinfo);
		echo"<tr><td>Version</td><td><input type='text' size='5' disabled='disabled' id='version' value='$appinfo[0]'></td></tr><tr><td>Description</td><td><input type='text' size='30' disabled='disabled' id='description' value='$appinfo[1]'></td></tr>";
		echo"<tr><td id='inputConfig' colspan='3'></td></tr>";
		echo"<tr><td id='outputConfig' colspan='3'></td></tr>";
		echo"</table>";
	}
	// when the button "Custom" is pressed
	elseif($event=="custom")
	{
		echo "<table style='border:1px solid black;' border='0'><tr><td colspan='2'><button type='button' id='default'>Default</button><button type='button' disabled='disabled'>Custom</button></td><td align='right'><button type='button' id='delete'>Delete</button></td></tr><tr><td>Platform</td><td><select id='platCustom'>";
		testbed_getPlatformList($platformList);
		foreach($platformList as $platform)
			echo "<option value='$platform'>$platform</option>";
		echo"</select></td></tr><tr id='loadFile'><td>Application</td><td colspan='2'><form enctype='multipart/form-data' id='binAPPForm' action='event_handler.php' method='POST'><input type='file' name='binAPP'><input type='submit' value='Load'/></form></td></tr><tr><td>Version</td><td><input type='text' size='5' id='verCustom' value='1.0.0'></td></tr><tr><td>Description</td><td><input type='text' size='30' id='desrCustom' value='This is a test description!'></td></tr>";
		echo"<tr><td id='inputConfig' colspan='3'></td></tr>";
		echo"<tr><td id='outputConfig' colspan='3'></td></tr>";
		echo"</table>";
	}
	// when a different platform is selected for default applications
	elseif($event=="platform")
	{
		$selectedPlatform=$_GET['platform'];
		testbed_getAppList($selectedPlatform,$appList);
		foreach($appList as $app)
			echo "<option value='$app'>$app</option>";
		echo "መለያ";
		testbed_getAppInfo($selectedPlatform,$appList[0],$appinfo);
		echo "$appinfo[0]መለያ$appinfo[1]";
	}
	// when a different platform is selected for custom applications
	elseif($event=="platCustom")
	{
		echo "<td>Application</td><td colspan='2'><form enctype='multipart/form-data' id='binAPPForm' action='event_handler.php' method='POST'><input type='file' name='binAPP'><input type='submit' value='Load'/></form></td>";
	}
	// when a different application is selected for default applications
	elseif($event=="application")
	{
		$selectedPlatform=$_GET['platform'];
		$selectedApplication=$_GET['application'];
		testbed_getAppInfo($selectedPlatform,$selectedApplication,$appinfo);
		echo "$appinfo[0]መለያ$appinfo[1]";
	}
	// used for displaying sensor input parameter list of default tinyOS applications 
	elseif($event=="sensorPar")
	{
		$appType=$_GET['appType'];
		if($appType == "default")
			$filePath='default_bin/'.$_GET['application'];
		elseif($appType == "custom")
			$filePath='custom_bin/'.$_GET['application'];
		
		if (file_exists($filePath))
		{
			$parList = sensorPar($filePath);
			echo "<hr /><ul id='sensorPar'><li class='closed'><span class='folder'><b>Parameters</b></span><ul id='parameters'><li><table>";
			foreach($parList as $parameter)
				echo "<tr><td id='parName'>$parameter[0]</td><td><input type='text' id='parValue' value='$parameter[1]'></td></tr>";
			echo "<tr id='saveParam'><td><button type='button'>save</button></td></tr></table></li></ul></li></ul>";
		}
		else
			echo "failed";
	}
	// when the button "save" is pressed to save sensor input parameter changes of tinyOS applications
	elseif($event=="saveParam")
	{
		$sourceFilePath = $_GET['appType']."_bin/".$_GET['sourceFile'];
		$destFilePath = "custom_bin/".$_GET['destFile'];

		$parameters = Array();
		$parList=$_POST['parList'];
		$parListArray = split(",",$parList);
		foreach($parListArray as $parameter)
		{
			$parArray = split(":",$parameter);
			$parameters[] = array($parArray[0],$parArray[1]);
		}
		saveParam($sourceFilePath,$destFilePath,$parameters);
		echo "success";
	}
	// checks everytime whether a custom file exists in temporary folder
	elseif($event=="checkFile")
	{
		$application=$_GET['application'];
		if($application != "")
		{
			if (file_exists('custom_bin/'.$application))
			{
				if((time()-filemtime('custom_bin/'.$application)) > 86400)
					echo "failed";
				else
					echo "success";
			}
			else
				echo "failed";			
		}
		else
			echo "failed";
	}
	// Displays input format of selected default application
	elseif($event=="inputFormat")
	{
		$selectedPlatform=$_GET['platform'];
		$selectedApplication=$_GET['application'];
		testbed_getAppInputFormatNameList($selectedPlatform,$selectedApplication,$appInputFormat);
		echo $appInputFormat;
	}
	// Displays output format of selected default application
	elseif($event=="outputFormat")
	{
		$selectedPlatform=$_GET['platform'];
		$selectedApplication=$_GET['application'];
		testbed_getAppOutputFormatNameList($selectedPlatform,$selectedApplication,$appOutputFormat);
		echo $appOutputFormat;
	}
	// when the button "Add Group" is pressed
	elseif($event=="newGroup")
	{
		$platformText = "Platform <select id='platform'>";
		testbed_getPlatformList($platformList);
		foreach($platformList as $platform)
			$platformText .= "<option value='$platform'>$platform</option>";
		$platformText .= "</select>";
		echo "<table style='border:1px solid black;' border='0'><tr><td>".$platformText."</td><td>Nodes</td><td id='showTB'><img src='figures/click.jpg' title='node selection section' /><div id='TB'></div></td><td id='nodeList'></td><td id='imageList'></td><td id='deleteNode' align='right'><button type='button'>Delete</button></td></tr>";
		echo "<tr><td colspan='6'>Group Name <input type='text' size='12' id='groupName' value='".randomString(8)."'></td></tr>";
		echo "<tr id='rulerAbove'></tr><tr><td id='intConfig' colspan='5'></td></tr><tr id='addIntButton'></tr><tr id='rulerBelow'></tr><tr><td id='appConfig' colspan='5'></td></tr><tr id='addAPPButton'></tr></table>";
	}
	// draws the figure of a testbed
	elseif($event=="drawTB")
	{
		$platform=$_GET['platform'];
		testbed_getNodeInfo($nodeInfo,$platform);
		$randLetter = randomString(8);
		echo "<map name='$randLetter'>";
		foreach($nodeInfo as $info)
			echo "<area title='$info[0]' platform='$info[1]' shape='circle' coords='$info[2],$info[3],10'/>";
		echo "</map><img src='figures/testbed-open-room-Zwijnaarde-map.jpg' usemap='#$randLetter' />";
	}
	// when an x86Linux platform node is selected from the testbed figure
	elseif($event=="x86nodeSelected")
	{
		testbed_getImageList("x86Linux",$imageList);
		echo "Image<select id='x86ImageList'><option value='noImage'>select image to flush nodes</option>";
		foreach($imageList as $image)
			echo "<option value='$image'>$image</option>";
		echo "</select>";
	}
	// when the button "Add Interface" is pressed
	elseif($event=="addInt")
	{
		$selectednode=$_GET['node'];
		echo "<td><select id='intName'>";
		testbed_getInterfaceList($selectednode,$interfaceList);
		foreach($interfaceList as $interface)
			echo "<option value='$interface'>$interface</option>";
		echo "</select></td><td><select id='wirelessMode'>";
		testbed_getWirelessModeList($selectednode,$wirelessModeList);
		foreach($wirelessModeList as $wirelessMode)
			echo "<option value='$wirelessMode'>$wirelessMode</option>";
		echo "</select></td><td><select id='channel'>";
		testbed_getChannelList($selectednode,$channelList);
		foreach($channelList as $channel)
			echo "<option value='$channel'>$channel</option>";

		$essid = randomString(8);
		$ip = "192.168.".rand(0,255).".".rand(1,254);
		echo "</select></td><td><input type='text' size='13' id='essid' value='".$essid."'/></td><td><input type='text' size='12' id='ip' value='".$ip."'/></td><td id='delInt' align='center'><img src='figures/Delete.png' title='delete interface' /></td>";
	}
	// Loads application definition of default applications
	elseif($event=="loadAppDef")
	{
		$content = file_get_contents($_GET['appPath']);
		echo $content;
	}
	// when the button "Save Configuration" is pressed
	elseif($event=="saveConfig")
	{
		$packageName=$_GET['packageName'];

		// save experiment description
		$exprDscr="<?xml version='1.0' encoding='UTF-8' ?>\n" . $_POST['exprDscr'];
		file_put_contents("tmp/".$packageName.".xml", $exprDscr);

		// save OEDL experiment description
		$exprDscrOEDL=$_POST['exprDscrOEDL'];
		file_put_contents("tmp/".$packageName.".rb", $exprDscrOEDL);

		// convert application list to tar ball
		$appList = json_decode($_POST['jsonAppList']);
		foreach($appList as $app)
			shell_exec("cd ".$app[1]." && tar -cf ../tmp/".$app[0].".tar ".$app[0]);

		$tarFile = $packageName.".tar";
		$fileList = $packageName.".xml ".$packageName.".rb";
		foreach($appList as $app)
			$fileList .= " ".$app[0].".tar";

		// create one big tar ball containing exprDscr, exprDscrOEDL, and appList
		shell_exec("cd tmp/ && tar -cf ".$tarFile." ".$fileList);
		shell_exec("cd tmp/ && rm ".$fileList);

		echo "<a href='tmp/$tarFile'>$tarFile</a> ";
	}
//----------------------------------------------------------------------------------------------------//
	elseif($event=="startExpr")
	{
//		runED($_GET['ED_Name'],$PID,$exprID,$exprLogFile);
		$PID = "2222";	$exprID = "default_slice-2013-02-18t18.24.39+01.00";	$exprLogFile = "interference_estimation/ED.log";
		echo $PID."|".$exprID."|".$exprLogFile;
	}
	elseif($event=="startPrePostExpr")
	{
		$scanMethod = $_GET['scanMethod'];
		if($scanMethod == "20MHz_Band")
		{
			$ED_Path = "interference_estimation/detectWiFiChannel.rb";
			$PID = "2222";	$exprID = "default_slice-2012-10-31t21.26.22+01.00";	$exprLogFile = "interference_estimation/WiFi.log";
		}
		elseif($scanMethod == "2.4GHz")
		{
			$ED_Path = "interference_estimation/detectZigbeeChannel.rb";
			$PID = "2222";	$exprID = "default_slice-2012-10-31t14.43.42+01.00";	$exprLogFile = "interference_estimation/zigbee.log";
		}
//		runED($ED_Path,$PID,$exprID,$exprLogFile);
		echo $PID."|".$exprID."|".$exprLogFile;
	}
	elseif($event=="exprFinished")
	{
		if(file_exists($_GET['exprLogFile']))
		{
			if(!file_exists("/proc/".$_GET['PID']))	// check if PID is destroyed
				echo "FINISHED";
			else
				echo "NOT_FINISHED";
		}
		else
			echo "NOT_STARTED";
		
	}
	elseif($event=="getEnvData")
	{
		$database = $omlDir.$_GET['exprID'].".sq3";
		$graphDomain = $_GET['graphDomain'];
		$freqBandArray = json_decode($_POST['jsonFreqBandArray']);

		if (file_exists($database))
		{
			try
			{
				$db = new PDO("sqlite:".$database);
				if($graphDomain == "time")
					$sqlCommand = "select usrpid,timestamp_us,".$freqBandArray[0]." from iriswrap_iriswrapmp ORDER BY timestamp_us";
				elseif($graphDomain == "freq")
				{
					$sqlCommand = "select usrpid,".implode(",", $freqBandArray)." from iriswrap_iriswrapmp where timestamp_us=(select MAX(timestamp_us) from iriswrap_iriswrapmp WHERE usrpid=62) UNION ALL select usrpid,".implode(",", $freqBandArray)." from iriswrap_iriswrapmp where timestamp_us=(select MAX(timestamp_us) from iriswrap_iriswrapmp WHERE usrpid=65) UNION ALL select usrpid,".implode(",", $freqBandArray)." from iriswrap_iriswrapmp where timestamp_us=(select MAX(timestamp_us) from iriswrap_iriswrapmp WHERE usrpid=69) UNION ALL select usrpid,".implode(",", $freqBandArray)." from iriswrap_iriswrapmp where timestamp_us=(select MAX(timestamp_us) from iriswrap_iriswrapmp WHERE usrpid=75) UNION ALL select usrpid,".implode(",", $freqBandArray)." from iriswrap_iriswrapmp where timestamp_us=(select MAX(timestamp_us) from iriswrap_iriswrapmp WHERE usrpid=81) UNION ALL select usrpid,".implode(",", $freqBandArray)." from iriswrap_iriswrapmp where timestamp_us=(select MAX(timestamp_us) from iriswrap_iriswrapmp WHERE usrpid=89)";
				}
				$result = $db->query($sqlCommand);
				$db = NULL;
			}
			catch(PDOException $e)
			{
				echo "failure";
			}

			$Data = "<data>";
			$startTime = 0;
			if($graphDomain == "time")
			{
				foreach($result as $value)
				{
					if($startTime == 0)
					{
						$Data .= "<value usrpid='".$value[0]."' x='0' y='".$value[2]."'/>";
						$startTime = $value[1];
					}
					else
					{
						$time_sec = ($value[1] - $startTime)/1000000;
						$Data .= "<value usrpid='".$value[0]."' x='".$time_sec."' y='".$value[2]."'/>";
					}
				}
			}
			elseif($graphDomain == "freq")
			{
				foreach($result as $value)
					for($i = 0; $i < count($freqBandArray); $i++)
						$Data .= "<value usrpid='".$value[0]."' x='".channelToFreq($freqBandArray[$i])."' y='".$value[$i+1]."'/>";
			}
			$Data .= "</data>";
			echo $Data;
		}
		else
			echo "<data><value usrpid='12' x='45' y='12'/></data>";
	}
	elseif($event=="getExprData")
	{
		$database = $omlDir.$_GET['exprID'].".sq3";
		$group = $_GET['group'];
		$table = $_GET['table'];
		$seq = $_GET['seq'];
		$metric = $_GET['metric'];

		if (file_exists($database))
		{
			try
			{
				$db = new PDO("sqlite:".$database);
				$sqlCommand = "select oml_ts_server,GROUP_CONCAT(".$metric.") from ".$table." where oml_sender_id=(select id from _senders where name='".$group."') group by oml_seq";
				$result = $db->query($sqlCommand);
				$db = NULL;
			}
			catch(PDOException $e)
			{
				echo "failure";
			}

			$Data = "<data>";
			$startTime = 0;
			foreach($result as $value)
			{
				$yValue = explode(",", $value[1]);
				if($startTime == 0)
				{
					$Data .= "<value x='0' y='".$yValue[$seq]."'/>";
					$startTime = $value[0];
				}
				else
				{
					$time_sec = $value[0] - $startTime;
					$Data .= "<value x='".$time_sec."' y='".$yValue[$seq]."'/>";
				}
			}
			$Data .= "</data>";
			echo $Data;
		}
	}
	elseif($event == "graphAnalysis")
	{
		$database=$baseDir."tmp/".$_POST['solDBPath'].".sq3";
		$metXGroup=$_GET['metXGroup'];
		$metXTable=$_GET['metXTable'];
		$metXSeq=$_GET['metXSeq'];
		$metXName=$_GET['metXName'];
		$metYGroup=$_GET['metYGroup'];
		$metYTable=$_GET['metYTable'];
		$metYSeq=$_GET['metYSeq'];
		$metYName=$_GET['metYName'];

		$sqlXCommand = "select GROUP_CONCAT(".$metXName.") from ".$metXTable." where oml_sender_id=(select id from _senders where name='".$metXGroup."') group by oml_seq";
		$sqlYCommand = "select GROUP_CONCAT(".$metYName.") from ".$metYTable." where oml_sender_id=(select id from _senders where name='".$metYGroup."') group by oml_seq";

		if (file_exists($database))
		{
			try 
			{
				$db = new PDO("sqlite:".$database);

				$xResult = $db->query($sqlXCommand);
				$yResult = $db->query($sqlYCommand);

				$xValue = $xResult->fetchAll();
				$yValue = $yResult->fetchAll();

				$db = NULL;
			}
			catch(PDOException $e)
			{
				echo "failure";
			}
		
			$solData = "<data>";
			for($i=0; ($i<sizeof($xValue) && $i<sizeof($yValue)) ; $i++)
			{
				$xData = explode(",", $xValue[$i][0]);
				$yData = explode(",", $yValue[$i][0]);
				$solData .= "<value x='".$xData[$metXSeq]."' y='".$yData[$metYSeq]."'/>";
			}
			$solData .= "</data>";
			echo $solData;
		}
	}
	elseif($event == "customGraphAnalysis")
	{
		$database=$baseDir."tmp/".$_POST['solDBPath'].".sq3";
		$customXSQL=$_POST['customXSQL'];
		$customYSQL=$_POST['customYSQL'];
		if (file_exists($database))
		{
			try 
			{
				$db = new PDO("sqlite:".$database);

				$xResult = $db->query($customXSQL);
				$yResult = $db->query($customYSQL);

				$xValue = $xResult->fetchAll();
				$yValue = $yResult->fetchAll();

				$db = NULL;
			}
			catch(PDOException $e)
			{
				echo "failure";
			}
		
			$solData = "<data>";
			for($i=0; ($i<sizeof($xValue) && $i<sizeof($yValue)) ; $i++)
				$solData .= "<value x='".$xValue[$i][0]."' y='".$yValue[$i][0]."'/>";
			$solData .= "</data>";
			echo $solData;
		}
	}
	elseif($event=="varValue")
	{
		$database=$baseDir."tmp/".$_POST['solDBPath'].".sq3";
		$varSQL=$_POST['varSQL'];

		if (file_exists($database))
		{
			try
			{
				$db = new PDO("sqlite:".$database);
				$result = $db->query($varSQL);
				$db = NULL;
			}
			catch(PDOException $e)
			{
				echo "failure";
			}

			foreach($result as $value)
			{
				echo $value[0];
			}
		}
	}
	elseif($event=="calcPrePostScore")
	{
		$database = $omlDir.$_GET['exprID'].".sq3";
		$freqBandArray = json_decode($_POST['jsonFreqBandArray']);

		if (file_exists($database))
		{
			try
			{
				$db = new PDO("sqlite:".$database);
				$sqlCommand[0] = "SELECT SUM(psd)/(SELECT count(*)*".count($freqBandArray).".0 as psd FROM iriswrap_iriswrapmp)*10.0 from (SELECT count(".$freqBandArray[0].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[0]."<-55";
				$sqlCommand[1] = "SELECT SUM(psd)/(SELECT count(*)*".count($freqBandArray).".0 as psd FROM iriswrap_iriswrapmp)*10.0 from (SELECT count(".$freqBandArray[0].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[0]."<-60";
				$sqlCommand[2] = "SELECT SUM(psd)/(SELECT count(*)*".count($freqBandArray).".0 as psd FROM iriswrap_iriswrapmp)*10.0 from (SELECT count(".$freqBandArray[0].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[0]."<-65";
				$sqlCommand[3] = "SELECT SUM(psd)/(SELECT count(*)*".count($freqBandArray).".0 as psd FROM iriswrap_iriswrapmp)*10.0 from (SELECT count(".$freqBandArray[0].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[0]."<-70";
				$sqlCommand[4] = "SELECT SUM(psd)/(SELECT count(*)*".count($freqBandArray).".0 as psd FROM iriswrap_iriswrapmp)*10.0 from (SELECT count(".$freqBandArray[0].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[0]."<-75";
				$sqlCommand[5] = "SELECT SUM(psd)/(SELECT count(*)*".count($freqBandArray).".0 as psd FROM iriswrap_iriswrapmp)*10.0 from (SELECT count(".$freqBandArray[0].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[0]."<-80";
				$sqlCommand[6] = "SELECT SUM(psd)/(SELECT count(*)*".count($freqBandArray).".0 as psd FROM iriswrap_iriswrapmp)*10.0 from (SELECT count(".$freqBandArray[0].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[0]."<-85";
				$sqlCommand[7] = "SELECT SUM(psd)/(SELECT count(*)*".count($freqBandArray).".0 as psd FROM iriswrap_iriswrapmp)*10.0 from (SELECT count(".$freqBandArray[0].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[0]."<-90";
				$sqlCommand[8] = "SELECT SUM(psd)/(SELECT count(*)*".count($freqBandArray).".0 as psd FROM iriswrap_iriswrapmp)*10.0 from (SELECT count(".$freqBandArray[0].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[0]."<-95";
				for($i = 1; $i < count($freqBandArray); $i++)
				{
					$sqlCommand[0] .= " UNION ALL SELECT count(".$freqBandArray[$i].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[$i]."<-55";
					$sqlCommand[1] .= " UNION ALL SELECT count(".$freqBandArray[$i].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[$i]."<-60";
					$sqlCommand[2] .= " UNION ALL SELECT count(".$freqBandArray[$i].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[$i]."<-65";
					$sqlCommand[3] .= " UNION ALL SELECT count(".$freqBandArray[$i].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[$i]."<-70";
					$sqlCommand[4] .= " UNION ALL SELECT count(".$freqBandArray[$i].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[$i]."<-75";
					$sqlCommand[5] .= " UNION ALL SELECT count(".$freqBandArray[$i].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[$i]."<-80";
					$sqlCommand[6] .= " UNION ALL SELECT count(".$freqBandArray[$i].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[$i]."<-85";
					$sqlCommand[7] .= " UNION ALL SELECT count(".$freqBandArray[$i].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[$i]."<-90";
					$sqlCommand[8] .= " UNION ALL SELECT count(".$freqBandArray[$i].") as psd FROM iriswrap_iriswrapmp where ".$freqBandArray[$i]."<-95";
				}
				$sqlCommand[0] .= ")";	$sqlCommand[1] .= ")";	$sqlCommand[2] .= ")";	$sqlCommand[3] .= ")";	$sqlCommand[4] .= ")";	$sqlCommand[5] .= ")";	$sqlCommand[6] .= ")";	$sqlCommand[7] .= ")";	$sqlCommand[8] .= ")";

				$result[0] = $db->query($sqlCommand[0]);	$result[1] = $db->query($sqlCommand[1]);	$result[2] = $db->query($sqlCommand[2]);	$result[3] = $db->query($sqlCommand[3]);	$result[4] = $db->query($sqlCommand[4]);	$result[5] = $db->query($sqlCommand[5]);	$result[6] = $db->query($sqlCommand[6]);	$result[7] = $db->query($sqlCommand[7]);	$result[8] = $db->query($sqlCommand[8]);
				$db = NULL;
			}
			catch(PDOException $e)
			{
				echo "failure";
			}

			echo "<table style='width:520px;'><tr>";
			foreach($result[0] as $value);	echo "<td>THLD_55=<b>".sprintf("%2.2f", $value[0])."</b></td>";
			foreach($result[1] as $value);	echo "<td>THLD_60=<b>".sprintf("%2.2f", $value[0])."</b></td>";
			foreach($result[2] as $value);	echo "<td>THLD_65=<b>".sprintf("%2.2f", $value[0])."</b></td></tr>";
			foreach($result[3] as $value);	echo "<tr><td>THLD_70=<b>".sprintf("%2.2f", $value[0])."</b></td>";
			foreach($result[4] as $value);	echo "<td>THLD_75=<b>".sprintf("%2.2f", $value[0])."</b></td>";
			foreach($result[5] as $value);	echo "<td>THLD_80=<b>".sprintf("%2.2f", $value[0])."</b></td></tr>";
			foreach($result[6] as $value);	echo "<tr><td>THLD_85=<b>".sprintf("%2.2f", $value[0])."</b></td>";
			foreach($result[7] as $value);	echo "<td>THLD_90=<b>".sprintf("%2.2f", $value[0])."</b></td>";
			foreach($result[8] as $value);	echo "<td>THLD_95=<b>".sprintf("%2.2f", $value[0])."</b></td></tr>";
			echo "</table>";
		}
	}
	elseif($event=="readLogFile")
	{
		$content = file_get_contents($_GET['exprLogFile']);
		echo $content;
	}
	elseif($event=="searchOptimumVal")
	{
		$groupArray = json_decode($_POST['jsonGroupArray']);
		$tableArray = json_decode($_POST['jsonTableArray']);
		$seqArray = json_decode($_POST['jsonSeqArray']);
		$metricArray = json_decode($_POST['jsonMetricArray']);
		$aggrArray = json_decode($_POST['jsonAggrArray']);
		$operArray = json_decode($_POST['jsonOperArray']);
		$bestRunExprIDArray = json_decode($_POST['jsonBestRunExprIDArray']);
		$context = $_GET['context'];

		if($context == "maximization")
		{
			$score = Array();
		
			for($i=0; $i<sizeof($bestRunExprIDArray); $i++)
				$score[$i] = calcScore($groupArray,$tableArray,$seqArray,$metricArray,$aggrArray,$operArray,$bestRunExprIDArray[$i][1]);

			$OPT_score = $score[0];
			$localOptmiumVal = $bestRunExprIDArray[0][0];
			for($i=1; $i<sizeof($score); $i++)
			{
				if($score[$i] > $OPT_score)
				{
					$OPT_score = $score[$i];
					$localOptmiumVal = $bestRunExprIDArray[$i][0];
				}
			}
		}
		elseif($context == "minimization")
		{
			$score = Array();
		
			for($i=0; $i<sizeof($bestRunExprIDArray); $i++)
				$score[$i] = calcScore($groupArray,$tableArray,$seqArray,$metricArray,$aggrArray,$operArray,$bestRunExprIDArray[$i][1]);

			$OPT_score = $score[0];
			$localOptmiumVal = $bestRunExprIDArray[0][0];
			for($i=1; $i<sizeof($score); $i++)
			{
				if($score[$i] < $OPT_score)
				{
					$OPT_score = $score[$i];
					$localOptmiumVal = $bestRunExprIDArray[$i][0];
				}
			}
		}
		echo $localOptmiumVal;
	}
	elseif($event=="calcScore")
	{
		$groupArray = json_decode($_POST['jsonGroupArray']);
		$tableArray = json_decode($_POST['jsonTableArray']);
		$seqArray = json_decode($_POST['jsonSeqArray']);
		$metricArray = json_decode($_POST['jsonMetricArray']);
		$aggrArray = json_decode($_POST['jsonAggrArray']);
		$operArray = json_decode($_POST['jsonOperArray']);
		$bestRunExprID = json_decode($_POST['jsonBestRunExprID']);

		echo calcScore($groupArray,$tableArray,$seqArray,$metricArray,$aggrArray,$operArray,$bestRunExprID);
	}
	elseif($event=="executeSQL")
	{
		$sqlCommand=$_POST['sqlCommand'];
		$database = $omlDir.$_GET['exprID'].".sq3";

		if (file_exists($database))
		{
			try
			{
				$db = new PDO("sqlite:".$database);
				$result = $db->query($sqlCommand);
				$db = NULL;
			}
			catch(PDOException $e)
			{
				echo "failure";
			}

			foreach($result as $value)
			{
				for($i = 0; $i < count($value)/2; $i++)
					echo $value[$i]."\t";
				echo "\n";
			}
		}
	}
	elseif($event=="updatePar")
	{
		$ED_Path = $baseDir."tmp/".$_GET['ED_Name'];
		$parameter = "defProperty('".$_GET['objParMetName']."'";
		$value = $_GET['value'];

		$fpRead = fopen($ED_Path, "r");
		$content = fread($fpRead, filesize($ED_Path));
		fclose($fpRead);

		$state = "IDLE";
		$lines = explode("\n",$content);

		for($i = 0; $i < count($lines); $i++)
		{
			$line = $lines[$i];
			switch ($state)
			{
				case "INPUT":
					if(strstr($line, $parameter))
					{
						$property =  explode(",",$line);
						if(strstr($property[1],"'"))
							$lines[$i] = trim($property[0]).", '".$value."', ".trim($property[2]);
						else
							$lines[$i] = trim($property[0]).", ".$value.", ".trim($property[2]);
					}
					break;
				case "IDLE":
					break;
			}

			if(strstr($line, "# Start input definition"))
				$state = "INPUT";
			elseif(strstr($line, "# End input definition"))
				$state = "IDLE";
			else
				$state = $state;
		}
		
		$fpWrite = fopen($ED_Path, "w");
		foreach($lines as $line)
			fwrite($fpWrite, $line."\n");
		fclose($fpWrite);
	}
	elseif($event=="saveExpr")
	{
		$ED_Path = $baseDir."tmp/".$_GET['ED_Name'];
		$baseName = $_GET['baseName'];
		$intDet = $_GET['intDet'];

		if($intDet == "OFF")
			$exprIDArray = json_decode($_POST['jsonExprIDArray']);
		elseif($intDet == "ON")
		{
			$preExprIDArray = json_decode($_POST['jsonPreExprIDArray']);
			$exprIDArray = json_decode($_POST['jsonExprIDArray']);
			$postExprIDArray = json_decode($_POST['jsonPostExprIDArray']);
		}
		$selectedExprArray = json_decode($_POST['jsonSelectedExprArray']);

		$xmlFile = $baseName.".xml";
		$xmlPath = "tmp/".$xmlFile;
		$tarFile = $baseName.".tar";
		$tarPath = "tmp/".$tarFile;

		$fpExprDesr = fopen($ED_Path,'r');

		$state = "IDLE";
		$dataED = "<?xml version='1.0' encoding='UTF-8' ?>\n<benchmarkDescription>\n";
		
		$content = fread($fpExprDesr, filesize($ED_Path));
		$lines = explode("\n",$content);

		for($i = 0; $i < count($lines); $i++)
		{
			$line = $lines[$i];
			switch ($state)
			{
				case "OUTPUT":
					if(!strstr($line, "=end"))
						$dataED .= $line."\n";
					break;
				case "IDLE":
					break;
			}

			if(strstr($line, "=begin"))
				$state = "OUTPUT";
			elseif(strstr($line, "=end"))
				$state = "IDLE";
			else
				$state = $state;
		}
		$dataED .= "</benchmarkDescription>";
		fclose($fpExprDesr);
		
		$fpXmlDesr = fopen($xmlPath,'w');
		fwrite($fpXmlDesr, $dataED);
		fclose($fpXmlDesr);

		$dbList = "";

		if($intDet == "OFF")
		{
			foreach($selectedExprArray as $selectedExpr)
			{
				$string = explode('|',$selectedExpr);	$exprID = $string[0];	$runID = $string[1]; $objParMetName = $string[2]; $workingVal = $string[3];
				$exprFileName = $baseName."#".$exprID."#".$runID."#".$objParMetName."#".$workingVal.".sq3";

				shell_exec("cp ".$omlDir.$exprIDArray[$exprID-1][$runID-1].".sq3 tmp/".$exprFileName);
				$dbList .= " ".$exprFileName;
			}
		}
		elseif($intDet == "ON")
		{
			foreach($selectedExprArray as $selectedExpr)
			{
				$string = explode('|',$selectedExpr);	$exprID = $string[0];	$runID = $string[1]; $objParMetName = $string[2]; $workingVal = $string[3];
				$preExprFileName = "preExpr#".$exprID."#".$runID.".sq3";	$exprFileName = $baseName."#".$exprID."#".$runID."#".$objParMetName."#".$workingVal.".sq3";	$postExprFileName = "postExpr#".$exprID."#".$runID.".sq3";

				shell_exec("cp ".$omlDir.$preExprIDArray[$exprID-1][$runID-1].".sq3 tmp/".$preExprFileName);
				shell_exec("cp ".$omlDir.$exprIDArray[$exprID-1][$runID-1].".sq3 tmp/".$exprFileName);
				shell_exec("cp ".$omlDir.$postExprIDArray[$exprID-1][$runID-1].".sq3 tmp/".$postExprFileName);
				$dbList .= " ".$preExprFileName." ".$exprFileName." ".$postExprFileName;
			}
		}

		shell_exec("cd tmp/ && tar -cf database.tar".$dbList." && rm".$dbList);
		shell_exec("cd tmp/ && tar -cf ".$tarFile." database.tar ".$xmlFile." && rm database.tar ".$xmlFile);

		echo "<a href='$tarPath'>".$tarFile."</a>";
	}
	elseif($event=="loadBMConfig")
	{
		$content = file_get_contents('tmp/'.$_GET['solConfigFileName']);
		echo $content;
	}
	elseif($event=="calcNewScore")
	{
		$sqlCommand=$_GET['sqlCommand'];
		$solDBFileName="tmp/".$_GET['solDBFileName'];
	
		try
		{
			$db = new PDO("sqlite:".$solDBFileName);
			$result = $db->query($sqlCommand);
			$db = NULL;
		}
		catch(PDOException $e)
		{
			echo "failure";
		}

		foreach($result as $value);
		echo $value[0];
	}
	elseif($event=="execCommand")
	{
		shell_exec($_POST['command']);
	}
	elseif($event=="retrDBList")
	{
		$solDBFolderPath = $baseDir."tmp/".$_GET['solDBFolder'];
		if ($handle = opendir($solDBFolderPath))
		{
			$DBArray = Array();
			$DBNameColumn = Array();
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry != "." && $entry != "..")
				{
					$entryArray = explode("#",$entry);
					$value = explode('.',$entryArray[4]);	// get the value by removing the tag ".sq3/.sqlite"
					$DBArray[] = array($entryArray[0],$entryArray[1],$entryArray[2],$entryArray[3],$value[0]);
					array_push($DBNameColumn,$entryArray[0]);
				}
			}
			closedir($handle);
			$searchPar = $DBArray[0][3];	// search parameter name

			$DBNameList = array_keys(array_count_values($DBNameColumn));
			$DBList = "<experiment searchPar='".$searchPar."'>";
			foreach($DBNameList as $DBName)
			{
				$DBList .= "<solution name='".$DBName."'>";
				foreach($DBArray as $DB)
				{
					if($DB[0] == $DBName)
						$DBList .= "<DB exprID='".$DB[1]."' runID='".$DB[2]."' value='".$DB[4]."' />";
				}
				$DBList .= "</solution>";
			}
				$DBList .= "</experiment>";
		}
		echo $DBList;
	}

	if(isset($_FILES['binAPP']))
	{
		$newPath = "custom_bin/".$_FILES['binAPP']['name'];
		$tmpPath = $_FILES['binAPP']['tmp_name'];
		if(move_uploaded_file($tmpPath, $newPath))
			echo $_FILES['binAPP']['name'];
		else
			echo "failure";
	}
	else if(isset($_FILES['configFile']))
	{
		$content = file_get_contents($_FILES['configFile']['tmp_name']);
		echo $content;
	}
	else if(isset($_FILES['loadExprDesr']))
	{
		$newExprDesrPath = "tmp/".$_FILES['loadExprDesr']['name'];
		$tmpExprDesrPath = $_FILES['loadExprDesr']['tmp_name'];
		if(move_uploaded_file($tmpExprDesrPath, $newExprDesrPath))
		{
			$fpExprDesr = fopen($newExprDesrPath,'r');
			
			$state = "IDLE";
			$dataED = "<propertyDef>\n";
			
			$content = fread($fpExprDesr, filesize($newExprDesrPath));				
			$lines = explode("\n",$content);
			for($i = 0; $i < count($lines); $i++)
			{
				$line = $lines[$i];
				switch ($state)
				{
					case "INPUT":
						if(strstr($line, "defProperty"))
						{
							$property = explode("'",$line);
							$dataED .= "<searchPar name='".trim($property[1])."' />";
						}							
						break;
					case "OUTPUT":
						if(!strstr($line, "=end"))
							$dataED .= $line."\n";	
						break;
					case "IDLE":
						break;
				}
				
				if(strstr($line, "# Start input definition"))
					$state = "INPUT";
				elseif(strstr($line, "# End input definition"))
					$state = "IDLE";
				elseif(strstr($line, "=begin"))
					$state = "OUTPUT";
				elseif(strstr($line, "=end"))
					$state = "IDLE";
				else
					$state = $state;
			}
			$dataED .= "</propertyDef>";
			fclose($fpExprDesr);
			
			if(isset($_FILES['loadPackage']))
			{
				$package = $_FILES['loadPackage']['name'];
				$newPackagePath = "tmp/".$package;
				$tmpPackagePath = $_FILES['loadPackage']['tmp_name'];
				if(move_uploaded_file($tmpPackagePath, $newPackagePath))
				{
					echo $dataED;
					if($_POST['untarPackage'] == "yes")
						shell_exec("cd tmp/ && tar -xf ".$package." && rm ".$package);
				}
				else
					echo "failure";
			}
			else
				echo $dataED;
		}
		else
			echo "failure";
	}
	else if(isset($_FILES['solConfig']) && isset($_FILES['solDB']))
	{
		$newConfigPath = "tmp/".$_FILES['solConfig']['name'];
		$tmpConfigPath = $_FILES['solConfig']['tmp_name'];
		$newDBPath = "tmp/".$_FILES['solDB']['name'];
		$tmpDBPath = $_FILES['solDB']['tmp_name'];
		if(move_uploaded_file($tmpConfigPath, $newConfigPath))
		{

			if(($_FILES['solDB']['type'] == "application/x-tar"))	// check if file is a tarball package
			{
				if(move_uploaded_file($tmpDBPath, $newDBPath))
					echo "success";
				else
					echo "failure";
			}
			else
				echo "failure";
		}
		else
			echo "failure";
	}
	
	function sensorPar($filePath)
	{
		$dataSymbolCommand = "msp430-objdump -t ".$filePath;
		$dataSegmentCommand = "msp430-objdump -h ".$filePath;
		$inputPar = Array();
		
		$symbols = retDataSymbol($dataSymbolCommand);
		retDataSegment($dataSegmentCommand,$segment_vma,$segment_off);
	
		$fp = fopen($filePath, "rb");
		$contents = fread($fp, filesize($filePath));
		foreach($symbols as $symbol)
		{
			$parPos = hexdec($segment_off) + (hexdec($symbol[1]) - hexdec($segment_vma));
			$binarydata = substr($contents,$parPos,hexdec($symbol[2]));
			
			$data = "";
			for($i = 0; $i < strlen($binarydata); $i++)
				$data .= sprintf('%02x ', ord($binarydata[$i]));
			$inputPar[] = array($symbol[0],trim($data));
		}
		fclose($fp);
		return $inputPar;
	}
	
	function saveParam($filenameRead,$filenameWrite,$parameters)
	{
		$dataSymbolCommand = "msp430-objdump -t ".$filenameRead;
		$dataSegmentCommand = "msp430-objdump -h ".$filenameRead;
		
		retDataSegment($dataSegmentCommand,$segment_vma,$segment_off);
		
		$fpRead = fopen($filenameRead, "rb");
		$contents = fread($fpRead, filesize($filenameRead));
		fclose($fpRead);
 		foreach($parameters as $parameter)
		{
			$string = split(" ",trim($parameter[1]));
			$value = "";
			foreach($string as $char)
				$value = $value.chr(hexdec($char));
		
			$parPos = parNameToParPos($parameter[0],$dataSymbolCommand,$segment_vma,$segment_off,$symSize);
			$zeroString = str_repeat(chr(0),hexdec($symSize));
		
			$contents = substr_replace($contents,$zeroString,$parPos,hexdec($symSize));
			$contents = substr_replace($contents,$value,$parPos,sizeof($string));
			
		}
		
		$fpWrite = fopen($filenameWrite, "wb");
		fwrite($fpWrite, $contents);
		fclose($fpWrite);
	}
	
	function retDataSymbol($dataSymbolCommand)
	{
		$symbols = Array();
		exec($dataSymbolCommand,$dataSymbols);
		foreach($dataSymbols as $dataSymbol)
		{
			$pos = strstr($dataSymbol, "O .data",true);
			if ($pos !== false)
			{
				$string = split(" ",trim($dataSymbol));
				$symAddr = trim($string[0]);
				$symSize = split("	",trim($string[7]));	$symSize = trim($symSize[1]);
				$symName = trim($string[8]);
					
				$symbols[] = array($symName,$symAddr,$symSize);
			}
		}
		return $symbols;
	}

	function retDataSegment($dataSegmentCommand,&$segment_vma,&$segment_off)
	{
		exec($dataSegmentCommand,$dataSegments);
		foreach ($dataSegments as $dataSegment)
		{
			$pos = strstr($dataSegment, ".data",true);
			if ($pos !== false)
			{
				$string = split(" ",trim($dataSegment));
				$segment_vma = trim($string[12]);
				$segment_off = trim($string[16]);
				return;
			}
		}
	}

	function parNameToParPos($parName,$dataSymbolCommand,$segment_vma,$segment_off,&$symSize)
	{
		exec($dataSymbolCommand,$dataSymbols);
		foreach($dataSymbols as $dataSymbol)
		{
			$pos = strstr($dataSymbol, $parName);
			if ($pos !== false)
			{
				$string = split(" ",trim($dataSymbol));
				$symAddr = trim($string[0]);
				$symSize = split("	",trim($string[7]));
				$symSize = trim($symSize[1]);

				return hexdec($segment_off) + (hexdec($symAddr) - hexdec($segment_vma));
			}
		}
	}

	function channelToFreq($channel)
	{
		switch($channel)
		{
			case "psd11":	return "2405";	break;
			case "psd12":	return "2410";	break;
			case "psd13":	return "2415";	break;
			case "psd14":	return "2420";	break;
			case "psd15":	return "2425";	break;
			case "psd16":	return "2430";	break;
			case "psd17":	return "2435";	break;
			case "psd18":	return "2440";	break;
			case "psd19":	return "2445";	break;
			case "psd20":	return "2450";	break;
			case "psd21":	return "2455";	break;
			case "psd22":	return "2460";	break;
			case "psd23":	return "2465";	break;
			case "psd24":	return "2470";	break;
			case "psd25":	return "2475";	break;
			case "psd26":	return "2480";	break;
			default:	return "2405";
		}
	}

	function calcScore ($groupArray,$tableArray,$seqArray,$metricArray,$aggrArray,$operArray,$exprID)
	{
		global $omlDir;
		$database = $omlDir.$exprID.".sq3";
		
		try
		{
			$result = Array();
			$db = new PDO("sqlite:".$database);
			for ($j=0; $j<sizeof($groupArray); $j++)
			{
				$sqlCommand = "select GROUP_CONCAT(".$metricArray[$j].") from ".$tableArray[$j]." where oml_sender_id=(select id from _senders where name='".$groupArray[$j]."') group by oml_seq";
				$result[$j] = $db->query($sqlCommand);
			}
			$db = NULL;
		}
		catch(PDOException $e)
		{
			echo "failure";
		}

		for($j=0; $j<sizeof($groupArray); $j++)
		{
			if($aggrArray[$j] == "AVG")
			{
				$metric = 0;
				$count = 1;
				foreach($result[$j] as $value)
				{
					$temp = explode(",", $value[0]);
					$metric = $metric + $temp[$seqArray[$j]];
					$count++;
				}
				$metric = $metric/($count-1);
			}
			elseif($aggrArray[$j] == "SUM")
			{
				$metric = 0;
				foreach($result[$j] as $value)
				{
					$temp = explode(",", $value[0]);
					$metric = $metric + $temp[$seqArray[$j]];
				}
			}
			elseif($aggrArray[$j] == "VAR" || $aggrArray[$j] == "STDEV")
			{
				$AVG = 0;
				$count = 1;	// initialization is set to one to avoid possible division by zero

				// make a copy of the sql result to use it for average and VAR/STDEV calculation 
				$resultCopy = $result[$j]->fetchALL();
				foreach($resultCopy as $value)
				{
					$temp = explode(",", $value[0]);
					$AVG = $AVG + $temp[$seqArray[$j]];
					$count++;
				}
				$AVG = $AVG/($count-1);

				$metric = 0;
				foreach($resultCopy as $value)
				{
					$temp = explode(",", $value[0]);
					$metric = $metric + ($temp[$seqArray[$j]]-$AVG)*($temp[$seqArray[$j]]-$AVG);
				}
				$metric = ($aggrArray[$j] == "VAR") ? $metric/($count-1) : sqrt($metric/($count-1));
			}
			elseif($aggrArray[$j] == "MAX")
			{
				// Initialize metric with the first element
				$value = $result[$j]->fetch();
				$temp = explode(",", $value[0]);
				$metric = $temp[$seqArray[$j]];

				foreach($result[$j] as $value)
				{
					$temp = explode(",", $value[0]);
					if($temp[$seqArray[$j]] > $metric)
						$metric = $temp[$seqArray[$j]];
				}
			}
			elseif($aggrArray[$j] == "MIN")
			{
				// Initialize metric with the first element
				$value = $result[$j]->fetch();
				$temp = explode(",", $value[0]);
				$metric = $temp[$seqArray[$j]];

				foreach($result[$j] as $value)
				{
					$temp = explode(",", $value[0]);
					if($temp[$seqArray[$j]] < $metric)
						$metric = $temp[$seqArray[$j]];
				}
			}
			elseif($aggrArray[$j] == "COUNT")
			{
				$metric = 0;
				foreach($result[$j] as $value)
					$metric ++;
			}

			if($j == 0)
				$score = $metric;
			else
			{
				if($operArray[$j-1] == "add")
					$score = $score + $metric;
				elseif($operArray[$j-1] == "sub")
					$score = $score - $metric;
				elseif($operArray[$j-1] == "mul")
					$score = $score * $metric;
				elseif($operArray[$j-1] == "div")
					$score = $score / $metric;
				elseif($operArray[$j-1] == "mod")
					$score = $score % $metric;
			}
		}
		return $score;
	}


	// function to generate random string
	function randomString($length)
	{
		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";

		$size = strlen($chars);
		for($i=0;$i<$length;$i++)
			$str.=$chars[rand(0,$size-1)];
		return $str;
	}
?>
