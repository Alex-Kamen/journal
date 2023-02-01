<?php
require_once(ROOT."/components/Db.php");
require_once(ROOT."/models/Journal.php");

class Year {

	public static function getLastYearId() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM year WHERE yearId = (SELECT max(yearId) FROM year)");
		$result->execute();

		return $result->fetch()['yearId'];
	}

	public static function getYearList() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM year");
		$result->execute();

		$yearList = array();
		while($row = $result->fetch()) {
			$yearList[$row['yearId']] = $row['value'];
		}

		return $yearList;
	}

	public static function getMonthList() {
		$db = Db::getConnection();

		return array( "01" => "Январь",
						"02" => "Февраль",
						"03" => "Март",
						"04" => "Апрель",
						"05" => "Май",
						"06" => "Июнь",
						"07" => "Июль",
						"08" => "Август",
						"09" => "Сентябрь",
						"10" => "Октябрь",
						"11" => "Ноябрь",
						"12" => "Декабрь",);

	}

	public static function addYear($year, $semester) {
		$db = Db::getConnection();

		$value = $year."_".$semester;

		$result = $db->prepare("INSERT INTO year (value) VALUES (:value)");
		$result->bindParam(":value", $value, PDO::PARAM_STR);

		return $result->execute();
	}

	public static function delYear($yearId) {
		$db = Db::getConnection();

		$result = $db->prepare("DELETE FROM year WHERE yearId = :yearId");
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);

		$result->execute();

		Journal::delResultsByYearId($yearId);
	}

	public static function editYear($yearId, $year, $semester) {
		$db = Db::getConnection();

		$value = $year."_".$semester;

		$result = $db->prepare("UPDATE year SET value = :value WHERE yearId = :yearId");
		$result->bindParam(":value", $value, PDO::PARAM_STR);
		$result->bindParam(":yearId", $yearId, PDO::PARAM_INT);

		return $result->execute();
	}

}