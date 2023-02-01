<?php
require_once(ROOT."/components/Db.php");
require_once(ROOT."/models/Journal.php");

class Discipline {

	public static function getDisciplineListByChair($chairId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM discipline WHERE chairId = :chairId");
		$result->bindParam(":chairId", $chairId, PDO::PARAM_INT);
		$result->execute();
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$disciplineList = array();
		$i = 0;
		while($row = $result->fetch()) {
			$disciplineList[$i]["id"] = $row["disciplineId"];
			$disciplineList[$i]["shortName"] = $row["shortName"];
			$i++;
		}

		return $disciplineList;
	}

	public static function getDisciplineList() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM discipline");
		$result->execute();
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$disciplineList = array();
		while($row = $result->fetch()) {
			$disciplineList[$row["disciplineId"]] = $row["shortName"];
		}

		return $disciplineList;
	}

	public static function getAllDisciplineList() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM discipline");
		$result->bindParam(":chairId", $chairId, PDO::PARAM_INT);
		$result->execute();
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$disciplineList = array();
		while($row = $result->fetch()) {
			$disciplineList[$row["disciplineId"]]["shortName"] = $row["shortName"];
			$disciplineList[$row["disciplineId"]]["name"] = $row["name"];
			$disciplineList[$row["disciplineId"]]["chairId"] = $row["chairId"];
		}

		return $disciplineList;
	}

	public static function getDisciplineIdList($chairId) {
		$db = Db::getConnection();
		
		$result = $db->prepare("SELECT * FROM discipline WHERE chairId = :chairId");
		$result->bindParam(":chairId", $chairId, PDO::PARAM_INT);
		$result->execute();
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$disciplineIdList = array();
		while($row = $result->fetch()) {
			$disciplineIdList[] = $row["disciplineId"];
		}

		return $disciplineIdList;
	}

	public static function addDiscipline($name, $shortName, $chairId) {
		$db = Db::getConnection();

		$result = $db->prepare("INSERT INTO discipline (name, shortName, chairId) VALUES (:name, :shortName, :chairId)");
		$result->bindParam(":name", $name, PDO::PARAM_STR);
		$result->bindParam(":shortName", $shortName, PDO::PARAM_STR);
		$result->bindParam(":chairId", $chairId, PDO::PARAM_INT);

		return $result->execute();
	}

	public static function delDiscipline($disciplineId) {
		$db = Db::getConnection();

		$result = $db->prepare("DELETE FROM discipline WHERE disciplineId IN ($disciplineId)");
		$result->setFetchMode(PDO::FETCH_ASSOC);

		$result->execute();

		Journal::delResultsByDisciplineList($disciplineId);

	}

	public static function editDiscipline($name, $shortName, $chairId, $disciplineId) {
		$db = Db::getConnection();

		$result = $db->prepare("UPDATE discipline SET name = :name, shortName = :shortName, chairId = :chairId WHERE disciplineId = :disciplineId");
		$result->bindParam(":name", $name, PDO::PARAM_STR);
		$result->bindParam(":shortName", $shortName, PDO::PARAM_STR);
		$result->bindParam(":chairId", $chairId, PDO::PARAM_INT);
		$result->bindParam(":disciplineId", $disciplineId, PDO::PARAM_INT);

		return $result->execute();
	}

}