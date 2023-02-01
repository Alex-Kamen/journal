<?php
require_once(ROOT."/models/User.php");
require_once(ROOT."/models/Journal.php");
require_once(ROOT."/models/Discipline.php");
require_once(ROOT."/models/Teacher.php");
require_once(ROOT."/models/Excel.php");

class SiteController {

	public function actionIndex() {
		if(isset($_POST['submit'])) {
			$login = $_POST['login'];
			$password = $_POST['pass'];

			$user = User::checkUserData($login, $password);
			if(!$user) {
				$errors[] = "Неверный логин или пароль";
			} else {
				User::auth($user);

				if($user[1] == "student") {
					header("Location: /student/progress");
				} else if ($user[1] == "teacher") {
					header("Location: /teacher/journal");
				} else if ($user[1] == "administrator") {
					header("Location: /administrator/journal");
				} else if ($user[1] == "admin") {
					header("Location: /admin/student");
				}
			}
		}
		require_once(ROOT."/views/site/index.php");
		return true;
	}

	public function actionCreditBook($studentId) {

		$creidtBookData = Journal::getCreditBook($studentId);

		echo json_encode([$creidtBookData[0], $creidtBookData[1], $creidtBookData[2]]);

		return true;
	}

	public function actionDisciplineList($chairId) {
		$disciplineList = Discipline::getDisciplineListByChair($chairId);

		echo json_encode($disciplineList);

		return true;
	} 

	public function actionLogout() {
		unset($_SESSION['user']);

		header("Location: /");

		return true;
	}

	public static function actionSettings() {
		if(!isset($_SESSION['user'])) {
			header("Location: /");
		} 

		if($_SESSION['user'][1] == "teacher") {
			$isCurator = Teacher::getGroupIdByTeacherId($_SESSION['user'][0]);
		}

		$userInfo = User::getUserInfoByUserId($_SESSION['user'][0]);

		$errors = array();

		if(isset($_POST['save'])) {
			if(!empty($_POST['userSurname']) && !empty($_POST['userName']) && !empty($_POST['userPatronymic'])) {
				User::updateUserName($_POST['userSurname'], $_POST['userName'], $_POST['userPatronymic'], $_SESSION['user'][0]);
			} else {
				$errors[0] = "Не все поля заполнены";
			}

			if(!empty($_POST['userLogin']) && !empty($_POST['userNewLogin'])) {
				$code = User::updateUserLogin($_POST['userLogin'], $_POST['userNewLogin'], $_SESSION['user'][0]);
				if($code != 1) $errors[1] = $code;
			} else {
				$errors[1] = "Не все поля заполнены";
			}

			if(!empty($_POST['userPass']) && !empty($_POST['userNewPass']) && !empty($_POST['userNewPassDouble'])) {
				if($_POST['userNewPass'] != $_POST['userNewPassDouble']) {
					$errors[2] = "Пароли не совпадают";
				} else {
					$code = User::updatePassword($_POST['userPass'], $_POST['userNewPass'], $_SESSION['user'][0]);
					if($code != 1) $errors[2] = $code;
				}
			} else {
				$errors[2] = "Не все поля заполнены";
			}
			header("Location: /settings");
		}

		require_once(ROOT."/views/site/settings.php");
		return true;
	}

    public function actionReportToExcel($groupId, $monthId, $yearId) {
        if($groupId == 0) {
            $flag = true;
            $groupId = Teacher::getGroupIdByTeacherId($_SESSION['user'][0]);
        }

        $studentList = Student::getStudentsByGroup($groupId);
        $disciplineList = Discipline::getDisciplineList();
        $passesList = Journal::getPassesList($groupId, "%-".$monthId, $yearId);
        $serviseList = Journal::getSetviseInfoByMonth($groupId, "%-".$monthId, $yearId);
        $passesInfo = Journal::getPassesInfoByMonth("%-".$monthId, $groupId, $yearId);

        if($flag) $groupId = 0;

        Excel::setReportPage($studentList, $disciplineList, $passesList, $serviseList, $passesInfo, $groupId, $monthId, $yearId);

        return true;
    }

    public function actionProgressToExcel($groupId, $disciplineId, $yearId) {
        if($groupId == 0) {
            $flag = true;
            $groupId = Teacher::getGroupIdByTeacherId($_SESSION['user'][0]);
        }

        $studentList = Student::getStudentsByGroup($groupId);
    	$passesInfo = Journal::getPassesInfoByDisciplineId($yearId, $groupId, $disciplineId);
    	$serviceInfo  = Journal::getLessonList($groupId, $disciplineId, $yearId);
    	$resultsList = Journal::getResultsByLessonIdList($groupId, $disciplineId, $yearId);
    	$totalInfoList = Journal::getTotalInfoListByStudentList($groupId, $disciplineId, $yearId);
    	$averageResult = Journal::getAverageResult($yearId, $groupId, $disciplineId);

    	if($flag) $groupId = 0;

        Excel::setProgressPage($studentList, $passesInfo, $serviceInfo, $resultsList, $totalInfoList, $averageResult, $groupId, $disciplineId, $yearId);

        return true;
    }

    public function actionSummaryToExcel($groupId, $yearId) {
        if($groupId == 0) {
            $flag = true;
            $groupId = Teacher::getGroupIdByTeacherId($_SESSION['user'][0]);
        }

        $studentList = Student::getStudentsByGroup($groupId);
        $disciplineList = Discipline::getDisciplineList();
        $summaryInfo = Journal::getSummeryInfoByYearId($yearId, $groupId);
        $passesInfo = Journal::getPassesInfoByYearId($yearId, $groupId);

        if($flag) $groupId = 0;

        Excel::setSummaryPage($studentList, $disciplineList, $summaryInfo, $passesInfo, $groupId, $yearId);

        return true;
    }
}