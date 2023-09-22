<?php


switch ($argv[1]) {
    case '0.51':
		$mysqlConnect = mysql_connect('192.168.0.51', 'root', 'kv5y5kl2');
		echo 'Using mysql on 0.51';
		break;
	case '10.37':
		$mysqlConnect = mysql_connect('192.168.10.37', 'root', 'klight');
		echo 'Using mysql on 10.37';
		break;
	default:
		$mysqlConnect = mysql_connect('192.168.10.37', 'root', 'kv5y5kl2');
		echo 'Using mysql on default (10.37)';
		break;
}

if($mysqlConnect) {
	$dbSelect = mysql_select_db('music_portal', $mysqlConnect);
	if($dbSelect) {
		$latestDateQuery = "SELECT MAX(date_played) AS date
			FROM played_tracks";
		$latestDateQueryResult = mysql_query($latestDateQuery);
		while($row = mysql_fetch_array($latestDateQueryResult)) {
			$date = $row['date'];
		}
		unset($row);		
		$timestamp = strtotime($date);
		$firebirdDate = date('d.m.Y, H:i:s.B', $timestamp);		
		
		$dbSelect = mysql_select_db('marketing_portal', $mysqlConnect);
		if($dbSelect) {
			$companyId = 1004;
			$venuesQuery = "SELECT Venue.entity_id
				FROM venues Venue
				WHERE Venue.company_id=".$companyId;
			$venuesQueryResult = mysql_query($venuesQuery);
			$array = array();
			while($row = mysql_fetch_array($venuesQueryResult)) {
				array_push($array, $row['entity_id']);
			}
			$comma_separated = implode(",", $array);
			
			$dbODLink = '192.168.10.35:/opt/firebird/ipnotify/ipnotify.gdb';
			$dbOD = ibase_connect($dbODLink, 'SYSDBA', 'masterkey');
			if(!$dbOD) {
				$this->log("Did not connect to the db");
				exit;
			} else {	
				$query = "SELECT cp.date_time, mc.guid, sy.entity_id
							FROM clipsplayed cp
							JOIN systems sy on cp.system=sy.system_id
							JOIN mediaclips mc ON mc.mclip_id=cp.mclip_id
							WHERE sy.entity_id IN (".$comma_separated.")
							AND cp.date_time >'".$firebirdDate."'
							ORDER BY cp.date_time desc";
				$res = ibase_query($dbOD, $query);
				$count = 0;
				while ($row = ibase_fetch_object($res)) {
					$dbSelect = mysql_select_db('marketing_portal', $mysqlConnect);
					if($dbSelect) {
						$venueIdQuery = "SELECT *
							FROM venues Venue
							WHERE entity_id =".$row->ENTITY_ID;
						$venueIdQueryResult = mysql_query($venueIdQuery);
						while($mktptlRow = mysql_fetch_array($venueIdQueryResult)) {
							$venueId = $mktptlRow['id'];
							$companyId = $mktptlRow['company_id'];
						}
					}
					$dbSelect = mysql_select_db('music_portal', $mysqlConnect);
					if($dbSelect) {
						if($row->GUID !== 'GAP') {
							$insertQuery = "INSERT into played_tracks (`id`, `clip_uid`, `date_played`, `venue_id`, `company_id`) VALUES (null, '".$row->GUID."', '".$row->DATE_TIME."', '".$venueId."', '".$companyId."')";
							mysql_query($insertQuery);
						}
					}
				}
			}
		}
	}
}
		
?>