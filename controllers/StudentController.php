<?php
require_once(ROOT."/models/Student.php");
require_once(ROOT."/models/Journal.php");
require_once(ROOT."/models/Year.php");
require_once(ROOT."/models/Discipline.php");

class StudentController {

	public function actionProgress() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "student") header("Location: /");
		} else {
			header("Location: /");
		}

		$yearList = Year::getYearList();

		require_once(ROOT."/views/student/progress.php");
		return true;
	}

	public function actionAJAXProgress($yearId) {
		$yearList = Year::getYearList();

		$studentId = $_SESSION['user'][0];
		$groupId = Student::getGroupIdByStudentId($studentId);

		$disciplineList = Discipline::getAllDisciplineList();
		$passesInfo = Journal::getPassesInfoByStudentId($yearId, $studentId, $groupId);
		$averageResult = Journal::getAverageResultByStudentId($yearId, $studentId, $groupId);
		$totalInfoList = Journal::getTotalInfoListByStudentId($studentId, $yearId, $groupId);
		$serviceInfo = Journal::getLessonListByAllDiscipline($yearId, $groupId);
		$resultsList = Journal::getResultsByStudentId($groupId, $studentId, $yearId);

		echo json_encode([$disciplineList, $passesInfo, $averageResult, $totalInfoList, $serviceInfo, $resultsList]);

		return true;
	}

	public function actionCreditBook() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "student") header("Location: /");
		} else {
			header("Location: /");
		}

		$studentId = $_SESSION['user'][0];

		[$data, $yearList, $disciplineList] = Journal::getCreditBook($studentId);
		
		require_once(ROOT."/views/student/creditBook.php");
		return true;
	}

	public function actionStudentList($groupId) {

		echo json_encode(Student::getStudentsByGroup($groupId));

		return true;
	}

}