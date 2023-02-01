<?php
require_once(ROOT."/components/Db.php");

class Student {

	public static function getStudentsIdList($groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT id FROM student WHERE groupId = :groupId");
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->execute();
		$studentsIdList = array();
		$i = 0;
		while($row = $result->fetch()) {
			$studentsIdList[$i] = $row['id'];
			$i++;
		}

		return $studentsIdList;
	}

	public static function getStudentsByGroup($groupId) {
		$db = Db::getConnection();

		$studentsIdListString = implode(",", self::getStudentsIdList($groupId));

		$result = $db->prepare("SELECT * FROM user WHERE id IN ($studentsIdListString) ORDER BY surname ASC");
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result->execute();
		$studentsList = array();
		while($row = $result->fetch()) {
			$studentsList[$row['id']]['name'] = $row['name'];
			$studentsList[$row['id']]['surname'] = $row['surname'];
			$studentsList[$row['id']]['patronymic'] = $row['patronymic'];
		}

		return $studentsList;

	}

	public static function getGroupIdByStudentId($studentId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM student WHERE id = :studentId");
		$result->bindParam(":studentId", $studentId, PDO::PARAM_INT);
		$result->execute();
		$teacher = $result->fetch();
		$groupId = $teacher['groupId'];

		return $groupId;
	}

	public static function delStudenByStudentIdList($studentIdList, $groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("DELETE FROM user WHERE id IN ($studentIdList)");
		$result->setFetchMode(PDO::FETCH_ASSOC);

		$result->execute();

		$result = $db->prepare("DELETE FROM student WHERE groupId = :groupId");
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);

		$result->execute();
	}

	public static function getStudentsBySubgroup($groupId, $disciplineId, $yearId, $subgroupNumber) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM user WHERE id IN (SELECT studentId FROM subgroups WHERE groupId = :groupId AND disciplineId = :disciplineId AND yearId = :yearId AND subgroupNumber = :subgroupNumber) ORDER BY surname");
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);
		$result->bindParam(":subgroupNumber", $subgroupNumber, PDO::PARAM_INT);
		$result->execute();
		$studentsList = array();
		while($row = $result->fetch()) {
			$studentsList[$row['id']]['name'] = $row['name'];
			$studentsList[$row['id']]['surname'] = $row['surname'];
			$studentsList[$row['id']]['patronymic'] = $row['patronymic'];
		}

		return $studentsList;
	}	
	
}