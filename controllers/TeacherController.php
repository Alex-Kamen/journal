<?php
require_once(ROOT."/models/Discipline.php");
require_once(ROOT."/models/Group.php");
require_once(ROOT."/models/Journal.php");
require_once(ROOT."/models/Student.php");
require_once(ROOT."/models/Teacher.php");
require_once(ROOT."/models/Year.php");
require_once(ROOT."/models/Chair.php");

class TeacherController {

	public function actionJournal() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "teacher") header("Location: /");
		} else {
			header("Location: /");
		}

		$groupList = Group::getGroupList();
		$chairList = Chair::getChairList();
		$yearList = Year::getYearList();

		$isCurator = Teacher::getGroupIdByTeacherId($_SESSION['user'][0]);

		require_once(ROOT.'/views/teacher/journal.php');
		return true;
	}

	public function actionJournalAjax($disciplineId, $groupId, $yearId) {
		$serviceInfo  = Journal::getLessonList($groupId, $disciplineId, $yearId);
		$resultsList = Journal::getResultsByLessonIdList($groupId, $disciplineId, $yearId);
		if($_POST['subgroupId'] != "null") {
			$studentList = Student::getStudentsBySubgroup($groupId, $disciplineId, $yearId, $_POST['subgroupId']);
		} else {
			$studentList = Student::getStudentsByGroup($groupId);
		}
		$subgroupInfo = Group::getSubgroupInfo($groupId, $disciplineId, $yearId, $_POST['subgroupId']);
		$totalInfoList = Journal::getTotalInfoListByStudentList($groupId, $disciplineId, $yearId);

		echo json_encode([$resultsList, $serviceInfo, $totalInfoList, $studentList, $subgroupInfo]);
		return true;
	}

	public function actionUpdateSubgroupInfo() {
		if($_POST['subgroupNumber'] == "null") {
			echo Group::delSubgroupInfo($_POST['groupId'], $_POST['disciplineId'], $_POST['yearId'], $_POST['subgroupNumber'], $_POST['studentId']);
		} else {
			echo Group::addSubgroupInfo($_POST['groupId'], $_POST['disciplineId'], $_POST['yearId'], $_POST['subgroupNumber'], $_POST['studentId']);
		}

		return true;
	}

	public function actionAddResult() {

		$data = $_POST['data'];
		$lessonId = $_POST['lesson'];
		$studentId = $_POST['student'];

		echo Journal::addResult($data, $lessonId, $studentId);
		
		return true;
	}

	public function actionUpdateResult() {

		$data = $_POST['data'];
		$studentId = $_POST['student'];
		$lessonId = $_POST['lesson'];

		echo Journal::updateResult($data, $lessonId, $studentId);

		return true;
	}

	public function actionAddServiceInfo() {

		$data = json_decode($_POST['data']);
		$disciplineId = $_POST['discipline'];
		$groupId = $_POST['group'];
		$yearId = $_POST['year'];

		echo Journal::addlesson($data, $disciplineId, $groupId, $yearId);

		return true;
	}

	public function actionUpdateServiceInfo() {

		$data = json_decode($_POST['data']);
		$lessonId = $_POST['id'];

		echo Journal::updatelesson($data, $lessonId);

		return true;
	}

	public function actionAddTotalInfo() {

		$mark = json_decode($_POST['data'])[0];
		$studentId = $_POST['student'];
		$type = $_POST['type'];
		$disciplineId = $_POST['discipline'];
		$yearId = $_POST['year'];

		echo Journal::addTotalInfo($mark, $studentId, $type, $disciplineId, $yearId);

		return true;
	}

	public function actionUpdateTotalInfo() {

		$mark = json_decode($_POST['data'])[0];
		$studentId = $_POST['student'];
		$type = $_POST['type'];
		$disciplineId = $_POST['discipline'];
		$yearId = $_POST['year'];

		echo Journal::updateTotalInfo($mark, $studentId, $type, $disciplineId, $yearId);

		return true;
	}

	public function actionProgress() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "teacher") header("Location: /");
		} else {
			header("Location: /");
		}

		$chairList = Chair::getChairList();
		$yearList = Year::getYearList();

		require_once(ROOT."/views/teacher/progress.php");
		return true;
	}

	public function actionCreditBook() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "teacher") header("Location: /");
		} else {
			header("Location: /");
		}

		$groupId = Teacher::getGroupIdByTeacherId($_SESSION['user'][0]);

		$studentList = Student::getStudentsByGroup($groupId);

		require_once(ROOT."/views/teacher/creditBook.php");
		return true;
	}

	public function actionReport() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "teacher") header("Location: /");
		} else {
			header("Location: /");
		}

		$monthList = Year::getMonthList();
		$yearList = Year::getYearList();

		require_once(ROOT."/views/teacher/report.php");
		return true;
	}

	public function actionSummary() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "teacher") header("Location: /");
		} else {
			header("Location: /");
		}

		$yearList = Year::getYearList();

		require_once(ROOT."/views/teacher/summary.php");
		return true;
	}

	public function actionSummaryData($yearId) {
		$groupId = Teacher::getGroupIdByTeacherId($_SESSION['user'][0]);
		$studentList = Student::getStudentsByGroup($groupId);
		$disciplineList = Discipline::getDisciplineList();

		$summaryInfo = Journal::getSummeryInfoByYearId($yearId, $groupId);
		$passesInfo = Journal::getPassesInfoByYearId($yearId, $groupId);

		echo json_encode([$studentList, $disciplineList, $summaryInfo, $passesInfo]);

		return true;
	}

	public function actionAJAXProgress($disciplineId, $yearId) {

		$groupId = Teacher::getGroupIdByTeacherId($_SESSION['user'][0]);
		$studentList = Student::getStudentsByGroup($groupId);
		$passesInfo = Journal::getPassesInfoByDisciplineId($yearId, $groupId, $disciplineId);
		$serviceInfo  = Journal::getLessonList($groupId, $disciplineId, $yearId);
		$resultsList = Journal::getResultsByLessonIdList($groupId, $disciplineId, $yearId);
		$totalInfoList = Journal::getTotalInfoListByStudentList($groupId, $disciplineId, $yearId);
		$averageResult = Journal::getAverageResult($yearId, $groupId, $disciplineId);

		echo json_encode([$studentList, $passesInfo, $serviceInfo, $resultsList, $totalInfoList, $averageResult]);

		return true;
	}

	public function actionAJAXReport($monthId, $yearId) {

		$groupId = Teacher::getGroupIdByTeacherId($_SESSION['user'][0]);
		$studentList = Student::getStudentsByGroup($groupId);
		$disciplineList = Discipline::getDisciplineList();
		$passesList = Journal::getPassesList($groupId, "%-".$monthId, $yearId);
		$serviseList = Journal::getSetviseInfoByMonth($groupId, "%-".$monthId, $yearId);
		$passesInfo = Journal::getPassesInfoByMonth("%-".$monthId, $groupId, $yearId);

		echo json_encode([$studentList, $disciplineList, $passesList, $serviseList, $passesInfo]);

		return true;
	}

	public function actionRemoveLesson($lessonId) {
		Journal::removeLessonByLessonId($lessonId);
		Journal::removeResultByLessonId($lessonId);

		return true;
	}
}