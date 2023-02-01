<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Настройки</title>
	<link rel="stylesheet" type="text/css" href="../../static/style/style.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../static/style/lightTheme.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/settings.css?<?php echo time();?>">
</head>
<body>
	<svg style="display: none;">
		<symbol viewBox="0 0 490.688 490.688" id="arrow">
			<path d="M472.328,120.529L245.213,347.665L18.098,120.529c-4.237-4.093-10.99-3.975-15.083,0.262
			c-3.992,4.134-3.992,10.687,0,14.82l234.667,234.667c4.165,4.164,10.917,4.164,15.083,0l234.667-234.667
			c4.237-4.093,4.354-10.845,0.262-15.083c-4.093-4.237-10.845-4.354-15.083-0.262c-0.089,0.086-0.176,0.173-0.262,0.262
			L472.328,120.529z"/>
			<path d="M245.213,373.415c-2.831,0.005-5.548-1.115-7.552-3.115L2.994,135.633c-4.093-4.237-3.975-10.99,0.262-15.083
			c4.134-3.992,10.687-3.992,14.82,0l227.136,227.115l227.115-227.136c4.093-4.237,10.845-4.354,15.083-0.262
			c4.237,4.093,4.354,10.845,0.262,15.083c-0.086,0.089-0.173,0.176-0.262,0.262L252.744,370.279
			C250.748,372.281,248.039,373.408,245.213,373.415z"/>
		</symbol>
	</svg>
	<div class="wrapper">
		<div class="content">
			<div class="header">
				<div class="container">
					<div class="header__inner">
						<p class="header__title">Электронный журнал</p>
						<div class="menu">
							<div class="menu__title"><?php echo $_SESSION['user'][3]." ".mb_substr($_SESSION['user'][2], 0, 1).".".mb_substr($_SESSION['user'][4], 0, 1)."." ?>
								<svg class="arrow">
									<use xlink:href="#arrow"></use>
								</svg>
							</div>
							<?php if($_SESSION['user'][1] == "admin"): ?>
								<div class="menu__list">
									<a href="/admin/student" class="menu__item">Студенты</a>
									<a href="/admin/teacher" class="menu__item">Преподаватели</a>
									<a href="/admin/administrator" class="menu__item">Администрация</a>
									<a href="/admin/others" class="menu__item">Прочее</a>
									<a href="/settings" class="menu__item">Настройки</a>
									<a href="/logout" class="menu__item">Выйти</a>
								</div>
							<?php elseif($_SESSION['user'][1] == "student"): ?>
								<div class="menu__list">
									<a href="/student/progress" class="menu__item">Успеваемость</a>
									<a href="/student/creditBook" class="menu__item">Зачётная книга</a>
									<a href="/settings" class="menu__item">Настройки</a>
									<a href="/logout" class="menu__item">Выйти</a>
								</div>
							<?php elseif($_SESSION['user'][1] == "administrator"): ?>
								<div class="menu__list">
									<a href="/administrator/journal" class="menu__item">Журналы</a>
									<a href="/administrator/creditBook" class="menu__item">Зачётные книги</a>
									<a href="/administrator/report" class="menu__item">Рапортички</a>
									<a href="/administrator/summary" class="menu__item">Сводные листы</a>
									<a href="/settings" class="menu__item">Настройки</a>
									<a href="/logout" class="menu__item">Выйти</a>
								</div>	
							<?php elseif($_SESSION['user'][1] == "teacher"): ?>
								<?php if(isset($isCurator)): ?>
									<div class="menu__list">
										<a href="/teacher/journal" class="menu__item">Журнал</a>
										<a href="/teacher/creditBook" class="menu__item">Зачётная книга</a>
										<a href="/teacher/progress" class="menu__item">Успеваемость</a>
										<a href="/teacher/report" class="menu__item">Рапортичка</a>
										<a href="/teacher/summary" class="menu__item">Сводный лист</a>
										<a href="/settings" class="menu__item">Настройки</a>
										<a href="/logout" class="menu__item">Выйти</a>
									</div>
								<?php else: ?>
									<div class="menu__list">
										<a href="/teacher/journal" class="menu__item">Журнал</a>
										<a href="/settings" class="menu__item">Настройки</a>
										<a href="/logout" class="menu__item">Выйти</a>
									</div>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<div class="settings">
				<div class="container">
					<form class="settings__inner" action="#" method="POST">
						<h1 class="settings__title">Настройки</h1>
						<div class="settings__list">
							<div class="settings__item">
								<h3 class="settings__name">Изменить ФИО</h3>
								<input type="" name="userSurname" placeholder="Фамилия" value="<?php echo $userInfo['surname']; ?>">
								<input type="" name="userName" placeholder="Имя" value="<?php echo $userInfo['name']; ?>">
								<input type="" name="userPatronymic" placeholder="Отчество" value="<?php echo $userInfo['patronymic']; ?>">
								<p class="error">
									<?php 
										if(isset($errors[0])) {
											echo $errors[0];
										} 
									?>
								</p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Изменить логин</h3>
								<input type="" name="userLogin" placeholder="Старый логин">
								<input type="" name="userNewLogin" placeholder="Новый логин">
								<p class="error">
									<?php 
										if(isset($errors[1])) {
											echo $errors[1];
										} 
									?>
								</p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Изменить пароль</h3>
								<input type="password" name="userPass" placeholder="Старый пароль">
								<input type="password" name="userNewPass" placeholder="Новый пароль">
								<input type="password" name="userNewPassDouble" placeholder="Повторите пароль">
								<p class="error">
									<?php 
										if(isset($errors[2])) {
											echo $errors[2];
										} 
									?>
								</p>
							</div>
						</div>
						<input type="submit" name="save" value="Сохранить">
					</form>
				</div>
			</div>

			<div class="themeSettings">
			    <div class="container">
			        <div class="themeSettings__inner">
			            <p class="settings__name">Тёмная тема:</p>
			            <input type="checkbox" class="themCheckbox">
			        </div>
			    </div>
			</div>

		</div>
		<div class="footer">
			<div class="container">
				<p class="copyright">ВФ УО БГАС</p>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="../../static/script/theme.js?<?php echo time();?>"></script>
	<script type="text/javascript" src="../../static/script/script.js?<?php echo time();?>"></script>
</body>
</html>