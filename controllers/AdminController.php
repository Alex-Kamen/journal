<?php
require_once(ROOT."/models/User.php");
require_once(ROOT."/models/Group.php");
require_once(ROOT."/models/Chair.php");
require_once(ROOT."/models/Discipline.php");
require_once(ROOT."/models/Year.php");
require_once(ROOT."/models/Student.php");
require_once(ROOT."/models/Teacher.php");

class AdminController {

	public function actionUser($userType) {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "admin") header("Location: /");
		} else {
			header("Location: /");
		}

		if ($_FILES && $_FILES["filename"]["error"]== UPLOAD_ERR_OK) {
			$file = explode(".", $_FILES["filename"]["name"]);
			$extension = $file[count($file)-1];

			if(in_array($extension, array('xls', 'xlsx'))) {
				$path = ROOT."/static/xls/".$_FILES["filename"]["name"];
			    move_uploaded_file($_FILES["filename"]["tmp_name"], $path);

			    User::addUserFromExcel($path);
			}
			header("Location: "."/admin"."/".$userType);
		}

		if($userType == "student") {
			$groupList = Group::getGroupList();
			require_once(ROOT."/views/admin/students.php");
		} else if($userType == "teacher") {
			require_once(ROOT."/views/admin/teachers.php");
		} else if($userType == "administrator") {
			require_once(ROOT."/views/admin/administrators.php");
		}
		
		return true;
	}

	public function actionAJAXUser($userType, $groupId = null) {

		if($userType == "student") {
			$studetsList = User::getUserListByGroupId($groupId);
			$groupName = Group::getGroupNameByGroupId($groupId);
			$groupList = Group::getGroupList();

			echo json_encode([$studetsList, $groupName, $groupList]);
		} else if($userType == "teacher") {
			$teacherList = User::getTeacherList();
			$groupList = Group::getGroupList();

			echo json_encode([$teacherList, $groupList]);
		} else if($userType == "administrator") {
			$administratorList = User::getAdministratorList();

			echo json_encode($administratorList);
		}

		return true;
	}

	public function actionAJAXDeleteUser() {
		[$userIdList, $userType] = json_decode($_POST['data']);

		echo User::deleteUser($userIdList, $userType);

		return true;
	}

	public function actionAJAXUpateUser() {
		[$userInfo, $userType]  = json_decode($_POST['data']);

		echo User::updateUser($userInfo, $userType);

		return true;
	}

	public function actionAJAXAddUser() {
		[$userInfo, $userType] = json_decode($_POST['data']);

		/*print_r($userInfo);
		echo $userType;*/

		echo User::addUser($userInfo, $userType);

		return true;
	}

	public function actionOthers() {
		if(isset($_SESSION['user'][1])) {
			if($_SESSION['user'][1] != "admin") header("Location: /");
		} else {
			header("Location: /");
		}

		$disciplineList = Discipline::getAllDisciplineList();
		$chairList = Chair::getAllChairList();
		$groupList = Group::getGroupList();
		$yearList = Year::getYearList();

		$errorList = array();


		if(isset($_POST['add__discipline'])) {
			if(	!empty($_POST['add__discipline__name']) && 
				!empty($_POST['add__discipline__shortName']) &&
				!empty($_POST['add__discipline__chair'])) {
				Discipline::addDiscipline(	$_POST['add__discipline__name'], 
											$_POST['add__discipline__shortName'],
											$_POST['add__discipline__chair']);
			
			} else {
				$errorList['add__discipline'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['del__discipline'])) {
			if(	!empty($_POST['del__discipline__discipline'])) {
				Discipline::delDiscipline($_POST['del__discipline__discipline']);
			
			} else {
				$errorList['del__discipline'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['edit__discipline'])) {
			if(	!empty($_POST['edit__discipline__name']) && 
				!empty($_POST['edit__discipline__shortName']) &&
				!empty($_POST['edit__discipline__chair2']) &&
				!empty($_POST['edit__discipline__discipline'])) {
				Discipline::editDiscipline(	$_POST['edit__discipline__name'], 
											$_POST['edit__discipline__shortName'],
											$_POST['edit__discipline__chair2'],
											$_POST['edit__discipline__discipline']);
			
			} else {
				$errorList['edit__discipline'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['add__chair'])) {
			if(	!empty($_POST['add__chair__name']) && 
				!empty($_POST['add__chair__shortName'])) {
				Chair::addChair($_POST['add__chair__name'], 
								$_POST['add__chair__shortName']);
			
			} else {
				$errorList['add__chair'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['del__chair'])) {
			if(	!empty($_POST['del__chair__chair'])) {
				Chair::delChair($_POST['del__chair__chair']);
			
			} else {
				$errorList['del__chair'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['edit__chair'])) {
			if(	!empty($_POST['edit__chair__name']) && 
				!empty($_POST['edit__chair__shortName']) &&
				!empty($_POST['edit__chair__chair'])) {
				Chair::editChair($_POST['edit__chair__name'], 
								$_POST['edit__chair__shortName'],
								$_POST['edit__chair__chair']);
			
			} else {
				$errorList['edit__chair'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['add__group'])) {
			if(	!empty($_POST['add__group__name'])) {
				Group::addGroup($_POST['add__group__name']);
			
			} else {
				$errorList['add__group'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['del__group'])) {
			if(	!empty($_POST['del__group__group'])) {
				Group::delGroup($_POST['del__group__group']);
			
			} else {
				$errorList['del__group'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['edit__group'])) {
			if(	!empty($_POST['edit__group__name']) && 
				!empty($_POST['edit__group__group'])) {
				Group::editGroup($_POST['edit__group__name'], 
								$_POST['edit__group__group']);
			
			} else {
				$errorList['edit__group'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['add__year'])) {
			if(	!empty($_POST['add__year__name']) &&
				!empty($_POST['add__year__semester'])) {
				Year::addYear($_POST['add__year__name'], 
							$_POST['add__year__semester']);
			
			} else {
				$errorList['add__year'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['del__year'])) {
			if(	!empty($_POST['del__year__name'])) {
				Year::delYear($_POST['del__year__name']);
			
			} else {
				$errorList['del__year'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		} else if(isset($_POST['edit__year'])) {
			if(	!empty($_POST['edit__year__name']) && 
				!empty($_POST['edit__year__year']) &&
				!empty($_POST['edit__year__semester'])) {
				Year::editYear($_POST['edit__year__name'], 
								$_POST['edit__year__year'],
								$_POST['edit__year__semester']);
			
			} else {
				$errorList['edit__year'] = "Не все поля заполнены";
			}
			header("Location:". "/admin/others");
			
		}

		require_once(ROOT."/views/admin/others.php");
		return true;
	}

	public function actionAJAXOther() {
		$disciplineList = Discipline::getAllDisciplineList();
		$chairList = Chair::getAllChairList();
		$groupList = Group::getGroupList();
		$yearList = Year::getYearList();

		echo json_encode([$disciplineList, $chairList, $groupList, $yearList]);

		return true;
	}

}