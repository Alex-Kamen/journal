<?php
require_once(ROOT."/components/Db.php");
require_once(ROOT."/models/Discipline.php");
require_once(ROOT."/models/Journal.php");

class Chair {

	public static function getChairList() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM chair");
		$result->execute();

		$chairList = array();

		while($row = $result->fetch()) {
			$chairList[$row['chairId']] = $row['shortName'];
		}

		return $chairList;
	}

	public static function getAllChairList() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM chair");
		$result->execute();

		$chairList = array();

		while($row = $result->fetch()) {
			$chairList[$row['chairId']]['shortName'] = $row['shortName'];
			$chairList[$row['chairId']]['name'] = $row['name'];
		}

		return $chairList;
	}

	public static function addChair($name, $shortName) {
		$db = Db::getConnection();

		$result = $db->prepare("INSERT INTO chair (name, shortName) VALUES (:name, :shortName)");
		$result->bindParam(":name", $name, PDO::PARAM_STR);
		$result->bindParam(":shortName", $shortName, PDO::PARAM_STR);

		return $result->execute();
	}

	public static function delChair($chairId) {
		$db = Db::getConnection();

		$result = $db->prepare("DELETE FROM chair WHERE chairId = :chairId");
		$result->bindParam(":chairId", $chairId, PDO::PARAM_INT);

		$result->execute();

		$disciplineIdList = Discipline::getDisciplineIdList($chairId);

		$disciplineIdList = implode(",", $disciplineIdList);

		Discipline::delDiscipline($disciplineIdList);
		Journal::delResultsByDisciplineList($disciplineIdList);
	}

	public static function editChair($name, $shortName, $chairId) {
		$db = Db::getConnection();

		$result = $db->prepare("UPDATE chair SET name = :name, shortName = :shortName WHERE chairId = :chairId");
		$result->bindParam(":name", $name, PDO::PARAM_STR);
		$result->bindParam(":shortName", $shortName, PDO::PARAM_STR);
		$result->bindParam(":chairId", $chairId, PDO::PARAM_INT);

		return $result->execute();
	}

}