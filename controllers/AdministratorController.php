<?php
require_once(ROOT."/models/Group.php");
require_once(ROOT."/models/Discipline.php");
require_once(ROOT."/models/Journal.php");
require_once(ROOT."/models/Year.php");
require_once(ROOT."/models/Student.php");
require_once(ROOT."/models/Chair.php");

class AdministratorController {

	public function actionJournal() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "administrator") header("Location: /");
		} else {
			header("Location: /");
		}

		$groupList = Group::getGroupList();
		$chairList = Chair::getChairList();
		$yearList = Year::getYearList();

		require_once(ROOT."/views/administrator/journal.php");
		return true;
	}

	public function actionReport() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "administrator") header("Location: /");
		} else {
			header("Location: /");
		}

		$monthList = Year::getMonthList();
		$groupList = Group::getGroupList();
		$yearList = Year::getYearList();

		require_once(ROOT."/views/administrator/report.php");
		return true;
	}

	public function actionSummary() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "administrator") header("Location: /");
		} else {
			header("Location: /");
		}

		$groupList = Group::getGroupList();
		$yearList = Year::getYearList();

		require_once(ROOT."/views/administrator/summary.php");
		return true;
	}

	public function actionCreditBook() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "administrator") header("Location: /");
		} else {
			header("Location: /");
		}

		$groupList = Group::getGroupList();

		require_once(ROOT."/views/administrator/creditBook.php");
		return true;
	}

	public function actionSummaryData($groupId, $yearId) {
		$studentList = Student::getStudentsByGroup($groupId);
		$disciplineList = Discipline::getDisciplineList();

		$summaryInfo = Journal::getSummeryInfoByYearId($yearId, $groupId);
		$passesInfo = Journal::getPassesInfoByYearId($yearId, $groupId);

		echo json_encode([$studentList, $disciplineList, $summaryInfo, $passesInfo]);

		return true;
	}

	public function actionAJAXProgress($disciplineId, $groupId, $yearId) {
		//$studentList = Student::getStudentsByGroup($groupId);
		//$passesInfo = Journal::getPassesInfoByDisciplineId($yearId, $groupId, $disciplineId);
		//$serviceInfo  = Journal::getLessonList($groupId, $disciplineId, $yearId);
		//$resultsList = Journal::getResultsByLessonIdList($groupId, $disciplineId, $yearId);
		//$totalInfoList = Journal::getTotalInfoListByStudentList($groupId, $disciplineId, $yearId);
		$averageResult = Journal::getAverageResult($yearId, $groupId, $disciplineId);

		echo json_encode([/*$studentList, $passesInfo, $serviceInfo, $resultsList, $totalInfoList, */$averageResult]);

		return true;
	}

	public function actionAJAXReport($groupId, $monthId, $yearId) {

		$studentList = Student::getStudentsByGroup($groupId);
		$disciplineList = Discipline::getDisciplineList();
		$passesList = Journal::getPassesList($groupId, "%-".$monthId, $yearId);
		$serviseList = Journal::getSetviseInfoByMonth($groupId, "%-".$monthId, $yearId);
		$passesInfo = Journal::getPassesInfoByMonth("%-".$monthId, $groupId, $yearId);

		echo json_encode([$studentList, $disciplineList, $passesList, $serviseList, $passesInfo]);

		return true;
	}

}