<?php
require_once(ROOT."/components/Db.php");
require_once(ROOT."/models/Student.php");
require_once(ROOT."/models/Journal.php");

class Group {

	public static function getGroupList() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM groups");
		$result->execute();
		$groupList = array();
		$i = 0;
		while($row = $result->fetch()) {
			$groupList[$i]["id"] = $row["groupId"];
			$groupList[$i]["name"] = $row["name"];
			$i++;
		}
		return $groupList;
	}

	public static function getGroupListByGroupName() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM groups");
		$result->execute();
		$groupList = array();
		while($row = $result->fetch()) {
			$groupList[$row["name"]] = $row["groupId"];
		}
		return $groupList;
	}	

	public static function getGroupNameByGroupId($groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM groups WHERE groupId = :groupId");
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->execute();
		$groupName = $result->fetch()['name'];

		return array($groupName, $groupId);
	}

	public static function addGroup($name) {
		$db = Db::getConnection();

		$result = $db->prepare("INSERT INTO groups (name) VALUES (:name)");
		$result->bindParam(":name", $name, PDO::PARAM_STR);

		return $result->execute();
	}

	public static function delGroup($groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("DELETE FROM groups WHERE groupId = :groupId");
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);

		$result->execute();

		$studentIdList = implode(",", Student::getStudentsIdList($groupId));

		Student::delStudenByStudentIdList($studentIdList, $groupId);
		Journal::delResultByStudentIdList($studentIdList, $groupId);
	}

	public static function editGroup($name, $groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("UPDATE groups SET name = :name WHERE groupId = :groupId");
		$result->bindParam(":name", $name, PDO::PARAM_STR);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);

		return $result->execute();
	}

	public static function getSubgroupInfo($groupId, $disciplineId, $yearId, $subgroupNumber) {
		$db = Db::getConnection();

		if($subgroupNumber != "null") {
			$result = $db->prepare("SELECT * FROM subgroups WHERE groupId = :groupId AND disciplineId = :disciplineId AND yearId = :yearId AND subgroupNumber = :subgroupNumber");
			$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
			$result->bindParam(":subgroupNumber", $subgroupNumber, PDO::PARAM_INT);
			$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
			$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		} else {
			$result = $db->prepare("SELECT * FROM subgroups WHERE groupId = :groupId AND disciplineId = :disciplineId AND yearId = :yearId");
			$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
			$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
			$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		}
		
		$result->execute();

		$subgroupInfo = array();

		while($row = $result->fetch()) {
			$subgroupInfo[$row['studentId']] = $row['subgroupNumber'];
		}

		return $subgroupInfo;
	}

	public static function addSubgroupInfo($groupId, $disciplineId, $yearId, $subgroupNumber, $studentId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM subgroups WHERE groupId = :groupId AND disciplineId = :disciplineId AND yearId = :yearId AND studentId = :studentId");
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->execute();

		if(!$result->fetch()) {
			$result = $db->prepare("INSERT INTO subgroups (groupId, disciplineId, yearId, subgroupNumber, studentId) VALUES (:groupId, :disciplineId, :yearId, :subgroupNumber, :studentId)");
			$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
			$result->bindParam(":subgroupNumber", $subgroupNumber, PDO::PARAM_INT);
			$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
			$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
			$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);						
		} else {
			$result = $db->prepare("UPDATE subgroups SET subgroupNumber = :subgroupNumber WHERE groupId = :groupId AND disciplineId = :disciplineId AND yearId = :yearId AND studentId = :studentId");
			$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
			$result->bindParam(":subgroupNumber", $subgroupNumber, PDO::PARAM_INT);
			$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
			$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
			$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);			
		}

		echo $result->execute();
	}

	public static function delSubgroupInfo($groupId, $disciplineId, $yearId, $subgroupNumber, $studentId) {
		$db = Db::getConnection();

		$result = $db->prepare("DELETE FROM subgroups WHERE groupId = :groupId AND disciplineId = :disciplineId AND yearId = :yearId AND studentId = :studentId");
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		
		echo $result->execute();
	}

}