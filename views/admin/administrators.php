<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Администрация</title>
	<link rel="stylesheet" type="text/css" href="../../static/style/style.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/select.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/lightTheme.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/adminPanel.css?<?php echo time();?>">
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
		<symbol viewBox="0 0 469.336 469.336" id="pencil">
			<g>
				<g>
					<path d="M456.836,76.168l-64-64.054c-16.125-16.139-44.177-16.17-60.365,0.031L45.763,301.682
						c-1.271,1.282-2.188,2.857-2.688,4.587L0.409,455.73c-1.063,3.722-0.021,7.736,2.719,10.478c2.031,2.033,4.75,3.128,7.542,3.128
						c0.979,0,1.969-0.136,2.927-0.407l149.333-42.703c1.729-0.5,3.302-1.418,4.583-2.69l289.323-286.983
						c8.063-8.069,12.5-18.787,12.5-30.192S464.899,84.237,456.836,76.168z M285.989,89.737l39.264,39.264L120.257,333.998
						l-14.712-29.434c-1.813-3.615-5.5-5.896-9.542-5.896H78.921L285.989,89.737z M26.201,443.137L40.095,394.5l34.742,34.742
						L26.201,443.137z M149.336,407.96l-51.035,14.579l-51.503-51.503l14.579-51.035h28.031l18.385,36.771
						c1.031,2.063,2.708,3.74,4.771,4.771l36.771,18.385V407.96z M170.67,390.417v-17.082c0-4.042-2.281-7.729-5.896-9.542
						l-29.434-14.712l204.996-204.996l39.264,39.264L170.67,390.417z M441.784,121.72l-47.033,46.613l-93.747-93.747l46.582-47.001
						c8.063-8.063,22.104-8.063,30.167,0l64,64c4.031,4.031,6.25,9.385,6.25,15.083S445.784,117.72,441.784,121.72z"/>
				</g>
			</g>
		</symbol>
		<symbol viewBox="0 0 329.26933 329" id="cross">
			<path d="m194.800781 164.769531 128.210938-128.214843c8.34375-8.339844 8.34375-21.824219 0-30.164063-8.339844-8.339844-21.824219-8.339844-30.164063 0l-128.214844 128.214844-128.210937-128.214844c-8.34375-8.339844-21.824219-8.339844-30.164063 0-8.34375 8.339844-8.34375 21.824219 0 30.164063l128.210938 128.214843-128.210938 128.214844c-8.34375 8.339844-8.34375 21.824219 0 30.164063 4.15625 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921875-2.089844 15.082031-6.25l128.210937-128.214844 128.214844 128.214844c4.160156 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921874-2.089844 15.082031-6.25 8.34375-8.339844 8.34375-21.824219 0-30.164063zm0 0"/>
		</symbol>
		<symbol viewBox="0 0 448 448" id="add">
			<path d="m408 184h-136c-4.417969 0-8-3.582031-8-8v-136c0-22.089844-17.910156-40-40-40s-40 17.910156-40 40v136c0 4.417969-3.582031 8-8 8h-136c-22.089844 0-40 17.910156-40 40s17.910156 40 40 40h136c4.417969 0 8 3.582031 8 8v136c0 22.089844 17.910156 40 40 40s40-17.910156 40-40v-136c0-4.417969 3.582031-8 8-8h136c22.089844 0 40-17.910156 40-40s-17.910156-40-40-40zm0 0"/>
		</symbol>
		<symbol viewBox="0 0 372.09 372.09" id="ok">
			<g>
				<g>
					<polygon points="339.719,27.855 120.922,282.666 29.143,196.838 0,228.001 124.293,344.235 372.09,55.65 		"/>
				</g>
			</g>
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

			<div class="admin__panel">
				<div class="container">
					<div class="admin__inner">
						<h1 class="content__title">Администрация</h1>
						<form action="#" method="POST" enctype="multipart/form-data">
							<input type="file" name="filename" id="file">
							<div class="admin__table">
								<table>
									<tr class="tr__add">
										<td><input type="text" name="surname" placeholder="Фамилия"></td>
										<td><input type="text" name="name" placeholder="Имя"></td>
										<td><input type="text" name="patronymic" placeholder="Отчество"></td>
										<td><input type="text" name="login" placeholder="Логин"></td>
										<td><input type="text" name="password" placeholder="Пароль"></td>
										<td>
											<div class="select__td">
												<select>
													<option selected disabled>Группа</option>
													<?php foreach($groupList as $groupItem): ?>
													<option value="<?php echo $groupItem['id']; ?>"><?php echo $groupItem['name']; ?></option>
													<?php endforeach; ?>
												</select>
												<div>
													<svg class="select__btn">
														<use xlink:href="#arrow"></use>
													</svg>
												</div>
											</div>
										</td>
										<td>
											<div class="td__add">
												<div class="admin__btn btn__add">
													<svg>
														<use xlink:href="#add"></use>
													</svg>
												</div>
											</div>
										</td>
									</tr>
									<tr class="add__objects"> 
										<td colspan="6">Работа над несколькими объектами:</td>
										<td>
											<div class="td__btn">
												<div class="admin__btn btn__add">
													<label for="file">
														<svg>
															<use xlink:href="#add"></use>
														</svg>
													</label>
												</div>
												<div class="admin__btn btn__upd">
													<svg>
														<use xlink:href="#pencil"></use>
													</svg>
												</div>
												<div class="admin__btn btn__del">
													<svg>
														<use xlink:href="#cross"></use>
													</svg>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</div>
							<div class="fileBlock hidden">
								<h3 class="fileBlock__title">Отправить файл:</h3>
								<p class="fileBlock__filename"></p>
								<div class="fileBlock__btn">
									<button type="submit" name="submit" class="fileBlock__ok">
										<svg>
											<use xlink:href="#ok"></use>
										</svg>
									</button>	
									<div class="fileBlock__close">
										<svg>
											<use xlink:href="#cross"></use>
										</svg>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="fileWrapper hidden">
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
	<script type="text/javascript" src="../../static/script/AJAX/adminPage.js?<?php echo time();?>"></script>
</body>
</html>