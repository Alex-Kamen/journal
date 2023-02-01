<?php
require_once(ROOT."/components/Db.php");
require_once(ROOT."/models/Group.php");

class User {

	public static function checkUserData($login, $password) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM user WHERE login = :login AND password = :password");
		$result->bindParam(":login", $login, PDO::PARAM_STR);
		$result->bindParam(":password", $password, PDO::PARAM_STR);
		$result->execute();
		$user = $result->fetch();

		if($user) {
			return array($user['id'], $user['status'], $user['name'], $user['surname'], $user['patronymic']);
		}
		return false;
	}

	public static function auth($userInfo) {
		$_SESSION['user'] = $userInfo;
	}

	public static function getUserListByGroupId($groupId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM user WHERE id IN (SELECT id FROM student WHERE groupId = :groupId)");
		$result->bindParam(":groupId", $groupId, PDO::PARAM_INT);
		$result->execute();

		$studentList = array();
		while($row = $result->fetch()) {
			$studentList[$row['id']]['name'] = $row['name'];
			$studentList[$row['id']]['surname'] = $row['surname'];
			$studentList[$row['id']]['patronymic'] = $row['patronymic'];
			$studentList[$row['id']]['login'] = $row['login'];
			$studentList[$row['id']]['password'] = $row['password'];
		}

		return $studentList;
	}

	public static function deleteUser($userIdList, $userType) {
		$db = Db::getConnection();

		$flag;

		$userIdList = implode(", ", $userIdList);

		$result = $db->prepare("DELETE FROM user WHERE id IN ($userIdList)");
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$flag = $result->execute();

		if($userType == "student") {
			$result = $db->prepare("DELETE FROM student WHERE id IN ($userIdList)");
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$flag = $result->execute();

			$result = $db->prepare("DELETE FROM result WHERE studentId IN ($userIdList)");
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$flag = $result->execute();

			$result = $db->prepare("DELETE FROM totalinfo WHERE studentId IN ($userIdList)");
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$flag = $result->execute();
		} else if($userType == "teacher") {
			$result = $db->prepare("DELETE FROM teacher WHERE id IN ($userIdList)");
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$flag = $result->execute();
		}

		return $flag;
	}

	public static function addUser($userInfo, $userType) {
		$db = Db::getConnection();

		$result = $db->prepare("INSERT INTO user (login, password, surname, name, patronymic, status) VALUES (:login, :password, :surname, :name, :patronymic, :status)");
		$result->bindParam(":login", $userInfo->login, PDO::PARAM_STR);
		$result->bindParam(":password", $userInfo->password, PDO::PARAM_STR);
		$result->bindParam(":surname", $userInfo->surname, PDO::PARAM_STR);
		$result->bindParam(":name", $userInfo->name, PDO::PARAM_STR);
		$result->bindParam(":patronymic", $userInfo->patronymic, PDO::PARAM_STR);
		$result->bindParam(":status", $userType, PDO::PARAM_STR);

		$result->execute();

		$result = $db->prepare("SELECT * FROM user WHERE login = :login AND password = :password");

		$result->bindParam(":login", $userInfo->login, PDO::PARAM_STR);
		$result->bindParam(":password", $userInfo->password, PDO::PARAM_STR);

		$result->execute();

		$userId = $result->fetch()['id'];

		if($userType == "student") {
			$result = $db->prepare("INSERT INTO student (id, groupId) VALUES (:id, :groupId)");
			$result->bindParam(":id", $userId, PDO::PARAM_INT);
			$result->bindParam(":groupId", $userInfo->group, PDO::PARAM_INT);

			$result->execute();
		} else if($userType == "teacher") {
			if($userInfo->group != "Группа") {
				$result = $db->prepare("INSERT INTO teacher (id, groupId) VALUES (:id, :groupId)");
				$result->bindParam(":id", $userId, PDO::PARAM_INT);
				$result->bindParam(":groupId", $userInfo->group, PDO::PARAM_INT);
			} else {
				$result = $db->prepare("INSERT INTO teacher VALUES (:id, null)");
				$result->bindParam(":id", $userId, PDO::PARAM_INT);
			}
			
			$result->execute();
		}

		return $userId;
	}

	public static function getLastUserId() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM user WHERE id = (SELECT max(id) FROM user)");
		$result->execute();

		return $result->fetch()['id'];
	}

	public static function getTeacherList() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM user WHERE status = 'teacher'");
		$result->execute();

		$teacherList = array();
		while($row = $result->fetch()) {
			$teacherList[$row['id']]['name'] = $row['name'];
			$teacherList[$row['id']]['surname'] = $row['surname'];
			$teacherList[$row['id']]['patronymic'] = $row['patronymic'];
			$teacherList[$row['id']]['login'] = $row['login'];
			$teacherList[$row['id']]['password'] = $row['password'];
		}

		$result = $db->prepare("SELECT * FROM teacher WHERE id IN (SELECT id FROM user WHERE status = 'teacher')");
		$result->execute();
		while($row = $result->fetch()) {
			$teacherList[$row['id']]['group'] = $row['groupId'];
		}

		return $teacherList;
	}

	public static function addUserFromExcel($path) {
		if(!file_exists($path)) return;

		require_once ROOT."/components/PHPExcel.php";
		 
		$Excel = PHPExcel_IOFactory::load($path);

		$groupList = Group::getGroupListByGroupName();

		$i = 2;

		do {
			$flag = true;
			$terminator = false;
		    $userInfo = new stdClass();

		    $userInfo->surname = $Excel->getActiveSheet()->getCell('A'.$i )->getValue();
		    $flag = $flag && $userInfo->surname != null;
		    $terminator = $terminator || $userInfo->surname != null;

		    $userInfo->name = $Excel->getActiveSheet()->getCell('B'.$i )->getValue();
		    $flag = $flag && $userInfo->name != null;
		    $terminator = $terminator || $userInfo->name != null;

		    $userInfo->patronymic = $Excel->getActiveSheet()->getCell('C'.$i )->getValue();
		    $flag = $flag && $userInfo->patronymic != null;
		    $terminator = $terminator || $userInfo->patronymic != null;

		    $userInfo->login = $Excel->getActiveSheet()->getCell('D'.$i )->getValue();
		    $flag = $flag && $userInfo->login != null;
		    $terminator = $terminator || $userInfo->login != null;

		    $userInfo->password = $Excel->getActiveSheet()->getCell('E'.$i )->getValue();
		    $flag = $flag && $userInfo->password != null;
		    $terminator = $terminator || $userInfo->password != null;

		    $userInfo->status = $Excel->getActiveSheet()->getCell('F'.$i )->getValue();
		    $flag = $flag && $userInfo->status != null;
		    $terminator = $terminator || $userInfo->status != null;

		    $userInfo->group = $Excel->getActiveSheet()->getCell('G'.$i )->getValue();

		    if($userInfo->group != null) {
		    	$userInfo->group = $groupList[mb_strtoupper($userInfo->group)];
		    }
		 	
		 	if($userInfo->status == "teacher" && $userInfo->group == null) {
		 		$userInfo->group == "Группа";
		 	}

		 	if($flag) self::addUser($userInfo, $userInfo->status);

		 	$i++;
		} while($terminator);

		return;
	}

	public static function updateUser($userInfo, $userType) {
		$db = Db::getConnection();

		$result = $db->prepare("UPDATE user SET login = :login, password = :password, surname = :surname, name = :name, patronymic = :patronymic WHERE id = :userId");
		$result->bindParam(":login", $userInfo->login, PDO::PARAM_STR);
		$result->bindParam(":password", $userInfo->password, PDO::PARAM_STR);
		$result->bindParam(":surname", $userInfo->surname, PDO::PARAM_STR);
		$result->bindParam(":name", $userInfo->name, PDO::PARAM_STR);
		$result->bindParam(":patronymic", $userInfo->patronymic, PDO::PARAM_STR);
		$result->bindParam(":userId", $userInfo->id, PDO::PARAM_INT);
		
		$result->execute();

		if($userType == "student") {
			$result = $db->prepare("UPDATE student SET groupId = :groupId WHERE id = :userId");
			$result->bindParam(":groupId", $userInfo->group, PDO::PARAM_STR);
			$result->bindParam(":userId", $userInfo->id, PDO::PARAM_STR);

			$result->execute();
		} else if($userType == "teacher") {
			if($userInfo->group != "Группа") {
				$result = $db->prepare("UPDATE teacher SET groupId = :groupId WHERE id = :userId");
				$result->bindParam(":groupId", $userInfo->group, PDO::PARAM_STR);
				$result->bindParam(":userId", $userInfo->id, PDO::PARAM_STR);
			} else {
				$result = $db->prepare("UPDATE teacher SET groupId = null WHERE id = :userId");
				$result->bindParam(":userId", $userInfo->id, PDO::PARAM_STR);
			}
			
			$result->execute();
		}

	}

	public static function getAdministratorList() {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM user WHERE status = 'administrator'");
		$result->execute();

		$administratorList = array();
		while($row = $result->fetch()) {
			$administratorList[$row['id']]['name'] = $row['name'];
			$administratorList[$row['id']]['surname'] = $row['surname'];
			$administratorList[$row['id']]['patronymic'] = $row['patronymic'];
			$administratorList[$row['id']]['login'] = $row['login'];
			$administratorList[$row['id']]['password'] = $row['password'];
		}

		return $administratorList;
	}

	public static function getUserInfoByUserId($userId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM user WHERE id = :userId");
		$result->bindParam(":userId", $userId, PDO::PARAM_INT);
		$result->execute();

		$userInfo = array();

		$userInfo = $result->fetch();

		return $userInfo;
	}

	public static function updateUserName($surname, $name, $patronymic, $userId) {
		$db = Db::getConnection();

		$result = $db->prepare("UPDATE user SET surname = :surname, name = :name, patronymic = :patronymic WHERE id = :userId");
		$result->bindParam(":surname", $surname, PDO::PARAM_STR);
		$result->bindParam(":name", $name, PDO::PARAM_STR);
		$result->bindParam(":patronymic", $patronymic, PDO::PARAM_STR);
		$result->bindParam(":userId", $userId, PDO::PARAM_INT);

		return $result->execute();
	}

	public static function updateUserLogin($login, $newLogin, $userId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM user WHERE login = :login");
		$result->bindParam(":login", $login, PDO::PARAM_STR);
		$result->execute();

		$id = $result->fetch()['id'];

		$result = $db->prepare("SELECT * FROM user WHERE login = :login");
		$result->bindParam(":login", $newLogin, PDO::PARAM_STR);
		$result->execute();

		$newId = $result->fetch()['id'];

		if($userId == $id && empty($newId)) {
			$result = $db->prepare("UPDATE user SET login = :newLogin WHERE id = :userId");
			$result->bindParam(":newLogin", $newLogin, PDO::PARAM_STR);
			$result->bindParam(":userId", $userId, PDO::PARAM_INT);
			return $result->execute();
		} else {
			if($userId != $id) return "Введёный логин не верен";
			if(!empty($newId)) return "Пользователь с такм логином уже существует";
		}
	}

	public static function updatePassword($password, $newPassword, $userId) {
		$db = Db::getConnection();

		$result = $db->prepare("SELECT * FROM user WHERE password = :password AND id = :userId");
		$result->bindParam(":password", $password, PDO::PARAM_STR);
		$result->bindParam(":userId", $userId, PDO::PARAM_INT);
		$result->execute();

		if($result->fetch()) {
			$result = $db->prepare("UPDATE user SET password = :newPassword WHERE id = :userId");
			$result->bindParam(":newPassword", $newPassword, PDO::PARAM_STR);
			$result->bindParam(":userId", $userId, PDO::PARAM_INT);
			return $result->execute();
		} else {
			return "Введёный пароль не верен";
		}
	}

}