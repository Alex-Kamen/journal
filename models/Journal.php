<?php
require_once(ROOT."/components/Db.php");
require_once(ROOT."/models/Student.php");

class Journal {

	public static function getLessonList($groupId, $disciplineId, $yearId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM lesson WHERE groupId = :groupId AND disciplineId = :disciplineId AND yearId = :yearId ORDER BY date");
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->execute();
		$serviceInfo = array();
		
		while($row = $result->fetch()) {
			$serviceInfo[$row['lessonId']]['type'] = $row['type'];
			$serviceInfo[$row['lessonId']]['lessonNumber'] = $row['lessonNumber'];
			$serviceInfo[$row['lessonId']]['date'] = $row['date'];
			$serviceInfo[$row['lessonId']]['theme'] = $row['theme'];
		}

		return $serviceInfo;
	}

	public static function getLessonIdList($groupId, $disciplineId, $yearId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM lesson WHERE groupId = :groupId AND disciplineId = :disciplineId AND yearId = :yearId ORDER BY date");
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->execute();
		$lessonIdList = array();
		$i = 0;
		while($row = $result->fetch()) {
			$lessonIdList[$i] = $row['lessonId'];
			$i++;
		}
		return $lessonIdList;
	}

	public static function getResultsByLessonIdList($groupId, $disciplineId, $yearId) {
		$db = Db::getConnection();

		$lessonIdList = self::getLessonIdList($groupId, $disciplineId, $yearId);

		$lessonIdList = implode(",", $lessonIdList);

		$result = $db->prepare("SELECT * FROM result WHERE lessonId IN ($lessonIdList)");
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result->execute();
		$resultList = array();
		while($row = $result->fetch()) {
			$resultList[$row['studentId']][$row['lessonId']] = array($row['resultId'], json_decode($row['results']));
		}

		return $resultList;

	}

	public static function getTotalInfoListByStudentList($groupId, $disciplineId, $yearId) {
		$db = Db::getConnection();

		$studentsIdListString = implode(",", Student::getStudentsIdList($groupId));

		$result = $db->prepare("SELECT * FROM totalInfo WHERE studentId IN ($studentsIdListString) AND disciplineId = :disciplineId AND yearId = :yearId");
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->execute();

		$totalInfoList = array();
		while($row = $result->fetch()) {
			$totalInfoList[$row['studentId']]['attestation'] = $row['attestation'];
			$totalInfoList[$row['studentId']]['exam'] = $row['exam'];
			$totalInfoList[$row['studentId']]['semester'] = $row['semester'];
			$totalInfoList[$row['studentId']]['total'] = $row['total'];
		}

		return $totalInfoList;

	}

	public static function updateLesson($data, $lessonId) {
		$db = Db::getConnection();

		if(in_array($data[1], ['otr', 'defoult'])) {
			$result = $db->prepare("UPDATE lesson SET lessonNumber = :lessonNumber, type = :type, date = :date, theme = :theme WHERE lessonId = :lessonId");
			$result->bindParam(":lessonNumber", $data[0], PDO::PARAM_STR);
			$result->bindParam(":type", $data[1], PDO::PARAM_STR);
			$result->bindParam(":date", $data[2], PDO::PARAM_STR);
			$result->bindParam(":theme", $data[3], PDO::PARAM_STR);
			$result->bindParam(":lessonId", $lessonId, PDO::PARAM_INT);
			
			return $result->execute();
		}

	}	

	public static function addlesson($data, $disciplineId, $groupId, $yearId) {
		$db = Db::getConnection();

		if(in_array($data[1], ['otr', 'defoult'])) {
			$result = $db->prepare("INSERT INTO lesson (disciplineId, groupId, yearId, lessonNumber, type, date, theme) VALUES (:disciplineId, :groupId, :yearId, :lessonNumber, :type, :date, :theme)");
			$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
			$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
			$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
			$result->bindParam(":lessonNumber", $data[0], PDO::PARAM_STR);
			$result->bindParam(":type", $data[1], PDO::PARAM_STR);
			$result->bindParam(":date", $data[2], PDO::PARAM_STR);
			$result->bindParam(":theme", $data[3], PDO::PARAM_STR);

			$result->execute();

			$result = $db->prepare("SELECT MAX(lessonId) FROM lesson WHERE disciplineId = :disciplineId AND groupId = :groupId AND yearId = :yearId AND lessonNumber = :lessonNumber AND type = :type AND date = :date AND theme = :theme");

			$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
			$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
			$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
			$result->bindParam(":lessonNumber", $data[0], PDO::PARAM_STR);
			$result->bindParam(":type", $data[1], PDO::PARAM_STR);
			$result->bindParam(":date", $data[2], PDO::PARAM_STR);
			$result->bindParam(":theme", $data[3], PDO::PARAM_STR);

			$result->execute();

			return $result->fetch()[0];
		}

	}

	public static function updateTotalInfo($mark, $studentId, $type, $disciplineId, $yearId) {
		$db = Db::getConnection();

		$result = $db->prepare("UPDATE totalInfo SET ".$type." = :mark WHERE studentId = :studentId AND disciplineId = :disciplineId AND yearId = :yearId");
		$result->bindParam(":mark", $mark, PDO::PARAM_STR);
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);

		return $result->execute();
	}

	public static function addTotalInfo($mark, $studentId, $type, $disciplineId, $yearId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM totalInfo WHERE studentId = :studentId AND disciplineId = :disciplineId AND yearId = :yearId");
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->execute();

		if($result->fetch()) {
			return self::updateTotalInfo($mark, $studentId, $type, $disciplineId, $yearId);
		} else {
			$result = $db->prepare("INSERT INTO totalInfo (".$type.", studentId, disciplineId, yearId) VALUES (:mark, :studentId, :disciplineId, :yearId)");
			$result->bindParam(":mark", $mark, PDO::PARAM_STR);
			$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
			$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
			$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);

			return $result->execute();
		}
	}

	public static function updateResult($data, $lessonId, $studentId) {
		$db = Db::getConnection();

		$result = $db->prepare("UPDATE result SET results = :data WHERE lessonId = :lessonId AND studentId = :studentId");
		$result->bindParam(":data", preg_replace('/(<font style=\\\"vertical-align: inherit;\\\">)|(<\/font>)/', '', $data), PDO::PARAM_STR);
		$result->bindParam(":lessonId", $lessonId, PDO::PARAM_INT);
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);

		return $result->execute();
	}

	public static function addResult($data, $lessonId, $studentId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM result WHERE studentId = :studentId AND lessonId = :lessonId");
		$result->bindParam(":lessonId", $lessonId, PDO::PARAM_INT);
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->execute();

		if($result->fetch()) {
			return self::updateResult($data, $lessonId, $studentId);
		} else {
			$result = $db->prepare("INSERT INTO result (studentId, lessonId, results) VALUES (:studentId, :lessonId, :data)");
			$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
			$result->bindParam(":lessonId", $lessonId, PDO::PARAM_INT);
			$result->bindParam(":data", preg_replace('/(<font style=\\\"vertical-align: inherit;\\\">)|(<\/font>)/', '', $data), PDO::PARAM_STR);

			return $result->execute();
		}
	}

	public static function getCreditBook($studentId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM totalInfo WHERE studentId = :studentId");
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->execute();

		$creditBook = array();
		$disciplineIdList =  array();
		$yearIdList = array();

		while($row = $result->fetch()) {
			$creditBook[$row['yearId']][$row['disciplineId']] = $row['total'];
			$disciplineIdList[] = $row['disciplineId'];
			$yearIdList[] = $row['yearId'];

		}

		$disciplineIdList = implode(",", $disciplineIdList);
		$yearIdList = implode(",", $yearIdList);

		$result = $db->prepare("SELECT * FROM year WHERE yearId IN ($yearIdList) ORDER BY value");
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result->execute();

		$yearList = array();

		while($row = $result->fetch()) {
			$yearList[$row['yearId']] = $row['value'];
		}

		$result = $db->prepare("SELECT * FROM discipline WHERE disciplineId IN ($disciplineIdList)");
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result->execute();

		$disciplineList = array();

		while($row = $result->fetch()) {
			$disciplineList[$row['disciplineId']] = $row['shortName'];
		}

		return  array($creditBook, $yearList, $disciplineList);

	}

	public static function getSummeryInfoByYearId($yearId, $groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM totalInfo WHERE yearId = :yearId AND studentId IN (SELECT id FROM student WHERE groupId = :groupId)");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result->execute();

		$summeryInfo = array();
		$markIndexList = array();

		$resultType = array('attestation', 'semester', 'exam', 'total');

		while($row = $result->fetch()) {
			$summeryInfo[$row['disciplineId']][$row['studentId']] = array($row['attestation'], $row['semester'], $row['exam'], $row['total']);
			if(!isset($markIndexList[$row['disciplineId']])) $markIndexList[$row['disciplineId']] = 0;

			if($row['semester'] && $markIndexList[$row['disciplineId']] < 1) {
				$markIndexList[$row['disciplineId']] = 1;
			}
			if($row['exam'] && $markIndexList[$row['disciplineId']] < 2) {
				$markIndexList[$row['disciplineId']] = 2;
			}
			if($row['total'] && $markIndexList[$row['disciplineId']] < 3) {
				$markIndexList[$row['disciplineId']] = 3;
			}
		}

		foreach($summeryInfo as $disciplineId => $discipline) {
			foreach ($discipline as $studentId => $student) {
				$summeryInfo[$disciplineId][$studentId] = array($summeryInfo[$disciplineId][$studentId][$markIndexList[$disciplineId]], $resultType[$markIndexList[$disciplineId]]);
			}
		}

		return $summeryInfo;
	}

	public static function getPassesInfoByYearId($yearId, $groupId) {
		$db = Db::getConnection();

		$result= $db->prepare("SELECT * FROM result WHERE lessonId in (SELECT lessonId FROM lesson WHERE yearId = :yearId AND groupId = :groupId)");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->execute();

		$resultInfo = array();

		while($row = $result->fetch()) {
			$resultInfo[$row['studentId']][] = json_decode($row['results']);
		}

		$passInfo = array();

		foreach ($resultInfo as $studentId => $results) {
			$negativePass = 0;
			$positivePass = 0;
			$neutralPass = 0;
			foreach ($results as $resultData) {
				if (stripos($resultData[0], "н/п")) {
					$negativePass++;
				} else if (stripos($resultData[0], "у/п")) {
					$positivePass++;
				} else if (stripos($resultData[0], "п")) { 
					$neutralPass++;
				}
					
				if (isset($resultData[1])) {
					if (stripos($resultData[1], "н/п")) {
						$negativePass++;
					} else if (stripos($resultData[1], "у/п")) {
						$positivePass++;
					} else if (stripos($resultData[1], "п")) { 
						$neutralPass++;
					}
				}
			}
			$passInfo[$studentId] = array($negativePass, $positivePass, $neutralPass);

		}

		return $passInfo;
	}

	public static function getPassesInfoByDisciplineId($yearId, $groupId, $disciplineId) {
		$db = Db::getConnection();

		$result= $db->prepare("SELECT * FROM result WHERE lessonId in (SELECT lessonId FROM lesson WHERE yearId = :yearId AND groupId = :groupId AND disciplineId = :disciplineId)");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->execute();

		$resultInfo = array();

		while($row = $result->fetch()) {
			$resultInfo[$row['studentId']][] = json_decode($row['results']);
		}

		$passInfo = array();

		foreach ($resultInfo as $studentId => $results) {
			$negativePass = 0;
			$positivePass = 0;
			$neutralPass = 0;
			foreach ($results as $resultData) {
				if (stripos($resultData[0], "н/п")) {
					$negativePass++;
				} else if (stripos($resultData[0], "у/п")) {
					$positivePass++;
				} else if (stripos($resultData[0], "п")) { 
					$neutralPass++;
				}
					
				if (isset($resultData[1])) {
					if (stripos($resultData[1], "н/п")) {
						$negativePass++;
					} else if (stripos($resultData[1], "у/п")) {
						$positivePass++;
					} else if (stripos($resultData[1], "п")) { 
						$neutralPass++;
					}
				}
			}
			$passInfo[$studentId] = array($negativePass, $positivePass, $neutralPass);

		}

		return $passInfo;
	}

	public static function getPassesInfoByMonth($monthId, $groupId, $yearId) {
		$db = Db::getConnection();

		$result= $db->prepare("SELECT * FROM result WHERE lessonId in (SELECT lessonId FROM lesson WHERE date LIKE :monthId AND groupId = :groupId AND yearId = :yearId)");
		$result->bindParam(":monthId", $monthId, PDO::PARAM_STR);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->execute();

		$resultInfo = array();

		while($row = $result->fetch()) {
			$resultInfo[$row['studentId']][] = json_decode($row['results']);
		}

		$passInfo = array();

		foreach ($resultInfo as $studentId => $results) {
			$negativePass = 0;
			$positivePass = 0;
			$neutralPass = 0;
			foreach ($results as $resultData) {
				if (stripos($resultData[0], "н/п")) {
					$negativePass++;
				} else if (stripos($resultData[0], "у/п")) {
					$positivePass++;
				} else if (stripos($resultData[0], "п")) { 
					$neutralPass++;
				}
					
				if (isset($resultData[1])) {
					if (stripos($resultData[1], "н/п")) {
						$negativePass++;
					} else if (stripos($resultData[1], "у/п")) {
						$positivePass++;
					} else if (stripos($resultData[1], "п")) { 
						$neutralPass++;
					}
				}
			}
			$passInfo[$studentId] = array($negativePass, $positivePass, $neutralPass);

		}

		return $passInfo;
	} 

	public static function getAverageResult($yearId, $groupId, $disciplineId) {
		$db = Db::getConnection();

		$result= $db->prepare("SELECT * FROM result WHERE lessonId in (SELECT lessonId FROM lesson WHERE yearId = :yearId AND groupId = :groupId AND disciplineId = :disciplineId)");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->execute();

		$resultInfo = array();

		while($row = $result->fetch()) {
			$resultInfo[$row['studentId']][] = json_decode($row['results']);
		}

		$averageResult = array();
		foreach ($resultInfo as $studentId => $results) {
        	$sum = 0;
        	$count = 0;
        	foreach ($results as $resultData) {
        		$resultData[0] = explode('/', preg_replace('/[^0-9\/]/', '', $resultData[0]));

        		foreach ($resultData[0] as $result) {
        			if(is_numeric($result)) {
	        			$sum += $result;
	        			$count++;
	        	    }
        		}
        		
        		if(isset($resultData[1])) {
        			$resultData[1] = explode('/', preg_replace('/[^0-9\/]/', '', $resultData[1]));
        			foreach ($resultData[1] as $result) {
	        			if(is_numeric($result)) {
		        			$sum += $result;
		        			$count++;
		        	    }
	        		}
        	    }
        	}
			if($count != 0) {
				$averageResult[$studentId] = round($sum / $count, 2);
			} else {
				$averageResult[$studentId] = 0;
			}
			
		}

		return $averageResult;
	}

	public static function getPassesInfoByStudentId($yearId, $studentId, $groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM lesson WHERE groupId = :groupId AND yearId = :yearId");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->execute();

		$disciplineList = array();

		while($row = $result->fetch()) {
			$disciplineList[$row['lessonId']] = $row['disciplineId'];
		}

		$result = $db->prepare("SELECT * FROM result WHERE studentId = :studentId AND lessonId IN (SELECT lessonId FROM lesson WHERE yearId = :yearId AND groupId = :groupId)");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->execute();

		$resultInfo = array();

		while($row = $result->fetch()) {
			$resultInfo[$disciplineList[$row['lessonId']]][] = json_decode($row['results']);
		}

		$passInfo = array();
		foreach ($resultInfo as $disciplineId => $results) {
			$negativePass = 0;
			$positivePass = 0;
			$neutralPass = 0;
			foreach ($results as $resultData) {
				if (stripos($resultData[0], "н/п")) {
					$negativePass++;
				} else if (stripos($resultData[0], "у/п")) {
					$positivePass++;
				} else if (stripos($resultData[0], "п")) { 
					$neutralPass++;
				}
					
				if (isset($resultData[1])) {
					if (stripos($resultData[1], "н/п")) {
						$negativePass++;
					} else if (stripos($resultData[1], "у/п")) {
						$positivePass++;
					} else if (stripos($resultData[1], "п")) { 
						$neutralPass++;
					}
				}
			}

			$passInfo[$disciplineId] = array($negativePass, $positivePass, $neutralPass);

		}

		return $passInfo;

	}
	public static function getAverageResultByStudentId($yearId, $studentId, $groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM lesson WHERE groupId = :groupId AND yearId = :yearId");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->execute();

		$disciplineList = array();

		while($row = $result->fetch()) {
			$disciplineList[$row['lessonId']] = $row['disciplineId'];
		}

		$result = $db->prepare("SELECT * FROM result WHERE studentId = :studentId AND lessonId IN (SELECT lessonId FROM lesson WHERE yearId = :yearId AND groupId = :groupId)");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->execute();

		$resultInfo = array();

		while($row = $result->fetch()) {
			$resultInfo[$disciplineList[$row['lessonId']]][] = json_decode($row['results']);
		}

		$averageResult = array();
		foreach ($resultInfo as $disciplineId => $results) {
			$sum = 0;
			$count = 0;
			foreach ($results as $resultData) {
				$resultData[0] = explode('/', preg_replace('/[^0-9\/]/', '', $resultData[0]));
				foreach ($resultData[0] as $result) {
	        			if(is_numeric($result)) {
		        			$sum += $result;
		        			$count++;
		        	    }
	        		}
				if(isset($resultData[1])) {
					$resultData[1] = explode('/', preg_replace('/[0-9\/]/', '', $resultData[1]));
					foreach ($resultData[1] as $result) {
	        			if(is_numeric($result)) {
		        			$sum += $result;
		        			$count++;
		        	    }
	        		}
				}
			}
			if($count != 0) {
				$averageResult[$disciplineId] = round($sum / $count, 2);
			} else {
				$averageResult[$disciplineId] = 0;
			}
			
		}

		return $averageResult;
	}

	public static function getTotalInfoListByStudentId($studentId, $yearId, $groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM totalInfo WHERE studentId = :studentId AND yearId = :yearId");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->execute();

		$totalInfo = array();
		while($row = $result->fetch()) {
			$totalInfo[$row['disciplineId']]['attestation'] = $row['attestation'];
			$totalInfo[$row['disciplineId']]['exam'] = $row['exam'];
			$totalInfo[$row['disciplineId']]['semester'] = $row['semester'];
			$totalInfo[$row['disciplineId']]['total'] = $row['total'];
		}

		return $totalInfo;
	}

	public static function getLessonListByAllDiscipline($yearId, $groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM lesson WHERE yearId = :yearId AND groupId = :groupId");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->execute();

		$serviceInfo = array();
		while($row = $result->fetch()) {
			$serviceInfo[$row['disciplineId']][$row['lessonId']]['date'] = $row['date'];
			$serviceInfo[$row['disciplineId']][$row['lessonId']]['type'] = $row['type'];
			$serviceInfo[$row['disciplineId']][$row['lessonId']]['theme'] = $row['theme'];
		}

		return $serviceInfo;
	}

	public static function getResultsByStudentId($groupId, $studentId, $yearId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM lesson WHERE groupId = :groupId AND yearId = :yearId");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->execute();

		$disciplineList = array();

		while($row = $result->fetch()) {
			$disciplineList[$row['lessonId']] = $row['disciplineId'];
		}

		$result = $db->prepare("SELECT * FROM result WHERE studentId = :studentId AND lessonId IN (SELECT lessonId FROM lesson WHERE yearId = :yearId AND groupId = :groupId)");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->execute();

		$resultInfo = array();

		while($row = $result->fetch()) {
			$resultInfo[$disciplineList[$row['lessonId']]][$row['lessonId']] = json_decode($row['results']);
		}

		return $resultInfo;
	}

	public static function getPassesList($groupId, $monthId, $yearId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM result WHERE lessonId IN (SELECT lessonId FROM lesson WHERE groupId = :groupId AND yearId = :yearId AND date LIKE :monthId)");
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":monthId", $monthId, PDO::PARAM_STR);
		$result->execute();

		$passesList = array();
		while($row = $result->fetch()) {
			$passesList[$row['studentId']][$row['lessonId']] = array($row['resultId'], json_decode($row['results']));
		}

		return $passesList;
	}

	public static function getSetviseInfoByMonth($groupId, $monthId, $yearId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM lesson WHERE groupId = :groupId AND yearId = :yearId AND date LIKE :monthId ORDER BY date, lessonNumber");
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":monthId", $monthId, PDO::PARAM_STR);
		$result->execute();

		$serviceInfo = array();
		while($row = $result->fetch()) {
			$serviceInfo[$row['date']][] = array($row['disciplineId'], $row['lessonNumber'], $row['lessonId']);
		}

		return $serviceInfo;
 	}

 	public static function delResultsByDisciplineList($disciplineList) {
 		$db = Db::getConnection();

 		$result = $db->prepare("DELETE FROM totalInfo WHERE disciplineId IN ($disciplineList)");
 		$result->setFetchMode(PDO::FETCH_ASSOC);

 		$result -> execute();

 		$result = $db->prepare("DELETE FROM result WHERE lessonId IN (SELECT lessonId FROM lesson WHERE disciplineId IN ($disciplineList))");
 		$result->setFetchMode(PDO::FETCH_ASSOC);

 		$result -> execute();

 		$result = $db->prepare("DELETE FROM lesson WHERE disciplineId IN ($disciplineList)");
 		$result->setFetchMode(PDO::FETCH_ASSOC);

 		$result -> execute();
 	}

 	public static function delResultByStudentIdList($studentIdList, $groupId) {
 		$db = Db::getConnection();

 		$result = $db->prepare("DELETE FROM totalInfo WHERE studentId IN ($studentIdList)");
 		$result->setFetchMode(PDO::FETCH_ASSOC);

 		$result -> execute();

 		$result = $db->prepare("DELETE FROM result WHERE studentId IN ($studentIdList)");
 		$result->setFetchMode(PDO::FETCH_ASSOC);

 		$result -> execute();

 		$result = $db->prepare("DELETE FROM lesson WHERE groupId = :groupId");
 		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);

 		$result -> execute();
 	}

 	public static function delResultsByYearId($yearId) {
 		$db = Db::getConnection();

 		$result = $db->prepare("DELETE FROM totalInfo WHERE yearId = :yearId");
 		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);

 		$result -> execute();

 		$result = $db->prepare("DELETE FROM result WHERE lessonId IN (SELECT lessonId FROM lesson WHERE yearId = :yearId)");
 		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
 		$result->setFetchMode(PDO::FETCH_ASSOC);

 		$result -> execute();

 		$result = $db->prepare("DELETE FROM lesson WHERE yearId = :yearId");
 		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);

 		$result -> execute();
 	}

 	public static function removeLessonByLessonId($lessonId) {
 		$db = Db::getConnection();

 		$result = $db->prepare("DELETE FROM lesson WHERE lessonId = :lessonId");
 		$result->bindParam(":lessonId", $lessonId, PDO::PARAM_INT);

 		$result -> execute();
 	}

 	public static function removeResultByLessonId($lessonId) {
 		$db = Db::getConnection();
 		
 		$result = $db->prepare("DELETE FROM result WHERE lessonId = :lessonId");
 		$result->bindParam(":lessonId", $lessonId, PDO::PARAM_INT);

 		$result -> execute();
 	}


}