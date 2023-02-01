<?php
require_once(ROOT."/components/Db.php");

class Teacher {

	public static function getGroupIdByTeacherId($teacherId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM teacher WHERE id = :teacherId");
		$result->bindParam(":teacherId", $teacherId, PDO::PARAM_INT);
		$result->execute();
		$teacher = $result->fetch();
		$groupId = $teacher['groupId'];

		return $groupId;
	}

	public static function deleteTeacher($teacherList) {
		$db = Db::getConnection();

		$result = $db->prepare("DELETE FROM teacher WHERE id IN (:teacherList)");
		$result->bindParam(":teacherList", $teacherList, PDO::PARAM_STR);

		return $result->execute();
	}

	public static function updateTeacher($teacherInfo, $teacherId) {
		$db = Db::getConnection();

		$result = $db->prepare("UPDATE teacher SET groupId = :groupId WHERE id = :teacherId");
		$result->bindParam(":groupId", $teacherInfo[5], PDO::PARAM_INT);
		$result->bindParam(":teacherId", $teacherId, PDO::PARAM_INT);

		return $result->execute();
	}

	public static function addTeacher($teacherInfo, $teacherId) {
		$db = Db::getConnection();

		$result = $db->prepare("INSERT INTO teacher (id, groupId) VALUES (:teacherId, :groupId)");
		$result->bindParam(":groupId", $teacherInfo[5], PDO::PARAM_INT);
		$result->bindParam(":teacherId", $teacherId, PDO::PARAM_INT);

		return $result->execute();
	}

}