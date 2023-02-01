<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Зачётная книга</title>
	<link rel="stylesheet" type="text/css" href="../../static/style/lightTheme.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/style.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/select.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/tables.css?<?php echo time();?>">
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
								<a href="/teacher/journal" class="menu__item">Журнал</a>
								<a href="/teacher/creditBook" class="menu__item">Зачётная книга</a>
								<a href="/teacher/progress" class="menu__item">Успеваемость</a>
								<a href="/teacher/report" class="menu__item">Рапортичка</a>
								<a href="/teacher/summary" class="menu__item">Сводный лист</a>
								<a href="/settings" class="menu__item">Настройки</a>
								<a href="/logout" class="menu__item">Выйти</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="creditBook">
				<div class="container">
					<div class="creditBook__inner">
						<div class="select__title">
							<h1 class="content__title">Зачётная книга: </h1>
							<select name="student">
								<option selected disabled>Студент</option>
								<?php foreach ($studentList as $studentId => $studentName): ?>
									<option value="<?php echo $studentId; ?>"><?php echo $studentName['surname'] . " " . $studentName['name']; ?></option>
								<?php endforeach; ?>
							</select>
							<svg class="select__btn">
								<use xlink:href="#arrow"></use>
							</svg>
						</div>
						<div class="creditBook__table">
						</div>
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
	<script type="text/javascript" src="../../static/script/AJAX/creditBook.js?<?php echo time();?>"></script>
</body>
</html>