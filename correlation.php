<?php
	$event=$_GET['event'];
	$omlDir = "/home/mmehari/oml2/";
//	$omlDir = "/home/michael/oml2/";
//	$omlDir = "/var/lib/oml2/";

	$exprIDArray = json_decode($_POST['data']);
	$table = $_GET['table'];
	$seq = $_GET['seq'];
	$metric = $_GET['metric'];
	$group = $_GET['group'];
	
	$RECORDS = Array();
	$MEAN = Array();
	
	$RUN_SIZE = sizeof($exprIDArray);
	for ($i=0; $i<$RUN_SIZE; $i++)
	{
		$database = $omlDir.$exprIDArray[$i].".sq3";
		try
		{
			$db = new PDO("sqlite:".$database);
			$sqlCommand = "select GROUP_CONCAT(".$metric.") from ".$table." where oml_sender_id=(select id from _senders where name='".$group."') group by oml_seq";
			$result = $db->query($sqlCommand);
			$db = NULL;
		}
		catch(PDOException $e)
		{
			echo "failure";
		}
		
		$RECORDS_ROUND = Array();
		$MEAN_ROUND = 0;		
		foreach($result as $value)
		{
			$valueArray = explode(",", $value[0]);
			$RECORDS_ROUND[] = $valueArray[$seq];
			$MEAN_ROUND = $MEAN_ROUND + $valueArray[$seq];
		}

		// watch out division by zero
		if(sizeof($RECORDS_ROUND) != 0)
			$MEAN_ROUND = $MEAN_ROUND/sizeof($RECORDS_ROUND);
		else
			$MEAN_ROUND = 0;

		$RECORDS[$i] = $RECORDS_ROUND;
		$MEAN[$i] = $MEAN_ROUND;

	}

	// standard deviation calculation
	$STDEV = Array();
	for ($i=0; $i<$RUN_SIZE; $i++)
	{
		$DATA_SIZE = sizeof($RECORDS[$i]);
		$TEMP_VAR = 0;
		for ($k=0; $k<$DATA_SIZE; $k++)
			$TEMP_VAR = $TEMP_VAR + ($RECORDS[$i][$k]- $MEAN[$i])*($RECORDS[$i][$k]- $MEAN[$i]);
		$STDEV[$i] = sqrt($TEMP_VAR/($DATA_SIZE-1));
	}

	// covariance matrix calculation
	$COV = Array();
	for ($i=0; $i<$RUN_SIZE; $i++)
	{
		for ($j=0; $j<$i; $j++)
		{
			// select the shortest data size
			$DATA_SIZE = (sizeof($RECORDS[$i]) > sizeof($RECORDS[$j])) ? sizeof($RECORDS[$j]) : sizeof($RECORDS[$i]);
			$TEMP_VAR = 0;
			for ($k=0; $k<$DATA_SIZE; $k++)
				$TEMP_VAR = $TEMP_VAR + ($RECORDS[$i][$k]- $MEAN[$i])*($RECORDS[$j][$k]- $MEAN[$j]);
			$COV[$i][$j] = $TEMP_VAR/($DATA_SIZE-1);
		}
	}

	// correlation matrix calculation
	$COR = Array();
	for ($i=0; $i<$RUN_SIZE; $i++)
	{
		for ($j=0; $j<$i; $j++)
		{
			$COR[$i][$j] = $COV[$i][$j]/($STDEV[$i]*$STDEV[$j]);
			$COR[$j][$i] = $COR[$i][$j];	// create identical diagonal element
		}
	}

	// calculate mean correlation array from correlation matrix
	$MEAN_COR = Array();
	for ($i=0; $i<$RUN_SIZE; $i++)
	{
		$TEMP_VAR = 0;
		for ($j=0; $j<$RUN_SIZE; $j++)
		{
			if($i != $j)
				$TEMP_VAR = $TEMP_VAR + $COR[$i][$j];
		}
		$MEAN_COR[$i] = ($TEMP_VAR + 1) / $RUN_SIZE;
	}

	if($event=="bestRun")
	{
		$BEST_RUN = 0;
		$BEST_MEAN_COR = $MEAN_COR[0];
		for ($i=1; $i<$RUN_SIZE; $i++)
		{
			if($MEAN_COR[$i] > $BEST_MEAN_COR)
			{
				$BEST_RUN = $i;
				$BEST_MEAN_COR = $MEAN_COR[$i];
			}
		}
		echo $BEST_RUN+1;
	}
	elseif($event=="corrMat")
	{
		$selectedRunArray = json_decode($_POST['run']);
	 	echo "<b>CORRELATION MATRIX</b>";
	 	echo "<table border='1' cellpadding='7' bordercolor='#4875B7'>";
		for ($i=0; $i<=$RUN_SIZE; $i++)
		{
			echo "<tr>";
			for ($j=0; $j<=$RUN_SIZE; $j++)
			{
				if($i==0 && $j==0)
					echo "<td><b>ID</b></td>";
				elseif($i==0 && $j!=0)
					echo "<td align='center'><b>".$selectedRunArray[$j-1]."</b></td>";
				elseif($i!=0 && $j==0)
					echo "<td align='center'><b>".$selectedRunArray[$i-1]."</b></td>";
				elseif($i==$j)
					echo "<td align='center'>1</td>";
				else
					echo "<td align='center'>".sprintf("%.7f",$COR[$i-1][$j-1])."</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
?>


