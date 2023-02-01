<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Прочее</title>
	<link rel="stylesheet" type="text/css" href="../../static/style/style.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/adminPanel.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/lightTheme.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/select.css?<?php echo time();?>">
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
							<div class="menu__list">
								<a href="/admin/student" class="menu__item">Студенты</a>
								<a href="/admin/teacher" class="menu__item">Преподаватели</a>
								<a href="/admin/administrator" class="menu__item">Администрация</a>
								<a href="/admin/others" class="menu__item">Прочее</a>
								<a href="/settings" class="menu__item">Настройки</a>
								<a href="/logout" class="menu__item">Выйти</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="settings">
				<div class="container">
					<form class="settings__inner" action="#" method="post">
						<h1 class="settings__title">Прочее</h1>
						<h3 class="settings__subtitle">Работа над дисциплинами</h3>
						<div class="settings__list">
							<div class="settings__item">
								<h3 class="settings__name">Добавить дициплину</h3>
								<input type="text" name="add__discipline__name" placeholder="Название" class="input__field">
								<input type="text" name="add__discipline__shortName" placeholder="Сокращённое название" class="input__field">
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="add__discipline__chair">
										<option selected disabled>Кафедра</option>
										<?php foreach ($chairList as $chairId => $chairName): ?>
											<option value="<?php echo $chairId; ?>"><?php echo $chairName['shortName']; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<input type="submit" name="add__discipline" value="Добавить">
								<p class="error"> <?php if(isset($errorList['add__discipline'])) echo $errorList['add__discipline']; ?></p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Удалить дисциплину</h3>
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="del__discipline__chair">
										<option selected disabled>Кафедра</option>
										<?php foreach ($chairList as $chairId => $chairName): ?>
											<option value="<?php echo $chairId; ?>"><?php echo $chairName['shortName']; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="select del__discipline__discipline">
								</div>
								<input type="submit" name="del__discipline" value="Удалить">
								<p class="error"> <?php if(isset($errorList['del__discipline'])) echo $errorList['del__discipline']; ?></p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Изменить дисциплину</h3>
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="edit__discipline__chair1">
										<option selected disabled>Кафедра</option>
										<?php foreach ($chairList as $chairId => $chairName): ?>
											<option value="<?php echo $chairId; ?>"><?php echo $chairName['shortName']; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="select edit__discipline__discipline">
								</div>
								<div class="select block__edit__discipline__discipline">

								</div>
								<input type="submit" name="edit__discipline" value="Изменить">
								<p class="error"> <?php if(isset($errorList['edit__discipline'])) echo $errorList['edit__discipline']; ?></p>
							</div>
						</div>
						<h3 class="settings__subtitle">Работа над кафедрами</h3>
						<div class="settings__list">
							<div class="settings__item">
								<h3 class="settings__name">Добавить кафедру</h3>
								<input type="text" name="add__chair__name" placeholder="Название" class="input__field">
								<input type="text" name="add__chair__shortName" placeholder="Сокращённое название" class="input__field">
								<input type="submit" name="add__chair" value="Добавить">
								<p class="error"> <?php if(isset($errorList['add__chair'])) echo $errorList['add__chair']; ?></p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Удалить кафедру</h3>
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="del__chair__chair">
										<option selected disabled>Кафедра</option>
										<?php foreach ($chairList as $chairId => $chairName): ?>
											<option value="<?php echo $chairId; ?>"><?php echo $chairName['shortName']; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<input type="submit" name="del__chair" value="Удалить">
								<p class="error"> <?php if(isset($errorList['del__chair'])) echo $errorList['del__chair']; ?></p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Изменить кафедру</h3>
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="edit__chair__chair">
										<option selected disabled>Кафедра</option>
										<?php foreach ($chairList as $chairId => $chairName): ?>
											<option value="<?php echo $chairId; ?>"><?php echo $chairName['shortName']; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="block__edit__chair__chair">
									
								</div>
								<input type="submit" name="edit__chair" value="Изменить">
								<p class="error"> <?php if(isset($errorList['edit__chair'])) echo $errorList['edit__chair']; ?></p>
							</div>
						</div>
						<h3 class="settings__subtitle">Работа над группами</h3>
						<div class="settings__list">
							<div class="settings__item">
								<h3 class="settings__name">Добавить группу</h3>
								<input type="text" name="add__group__name" placeholder="Название" class="input__field">
								<input type="submit" name="add__group" value="Добавить">
								<p class="error"> <?php if(isset($errorList['add__group'])) echo $errorList['add__group']; ?></p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Удалить группу</h3>
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="del__group__group">
										<option selected disabled>Группа</option>
										<?php foreach ($groupList as $group): ?>
											<option value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<input type="submit" name="del__group" value="Удалить">
								<p class="error"> <?php if(isset($errorList['del__group'])) echo $errorList['del__group']; ?></p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Изменить группу</h3>
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="edit__group__group">
										<option selected disabled>Группа</option>
										<?php foreach ($groupList as $group): ?>
											<option value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="block__edit__group__group">
									
								</div>
								<input type="submit" name="edit__group" value="Изменить">
								<p class="error"> <?php if(isset($errorList['edit__group'])) echo $errorList['edit__group']; ?></p>
							</div>
						</div>
						<h3 class="settings__subtitle">Работа над учебным процессом</h3>
						<div class="settings__list">
							<div class="settings__item">
								<h3 class="settings__name">Добавить учебный год</h3>
								<input type="text" name="add__year__name" placeholder="Год" class="input__field">
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="add__year__semester">
										<option selected disabled>Семестр</option>
										<option value="1">1</option>
										<option value="2">2</option>
									</select>
								</div>
								<input type="submit" name="add__year" value="Добавить">
								<p class="error"> <?php if(isset($errorList['add__year'])) echo $errorList['add__year']; ?></p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Удалить учебный год</h3>
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="del__year__name">
										<option selected disabled>Учебный год</option>
										<?php foreach ($yearList as $yearId => $yearName): ?>
											<option value="<?php echo $yearId; ?>">
												<?php echo explode("_", $yearName)[0]." семестр - ".explode("_", $yearName)[1]; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
								<input type="submit" name="del__year" value="Удалить">
								<p class="error"> <?php if(isset($errorList['del__year'])) echo $errorList['del__year']; ?></p>
							</div>
							<div class="settings__item">
								<h3 class="settings__name">Изменить учебный год</h3>
								<div class="select">
									<svg class="select__btn">
										<use xlink:href="#arrow"></use>
									</svg>
									<select name="edit__year__name">
										<option selected disabled>Учебный год</option>
										<?php foreach ($yearList as $yearId => $yearName): ?>
											<option value="<?php echo $yearId; ?>">
												<?php echo explode("_", $yearName)[0]." семестр - ".explode("_", $yearName)[1]; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="block__edit__year__name">
									
								</div>
								<input type="submit" name="edit__year" value="Изменить">
								<p class="error"> <?php if(isset($errorList['edit__year'])) echo $errorList['edit__year']; ?></p>
							</div>
						</div>
					</form>
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
	<script type="text/javascript" src="../../static/script/AJAX/adminOther.js?<?php echo time();?>"></script>
</body>
</html>