<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Электронный журнал</title>
	<link rel="stylesheet" type="text/css" href="../../static/style/style.css?<?php echo time();?>">
	<link rel="stylesheet" type="text/css" href="../../static/style/startPage.css?<?php echo time();?>">
    <link rel="stylesheet" type="text/css" href="../../static/style/lightTheme.css?<?php echo time();?>">
</head>
<body class="startPage">
	<div class="background"></div>
	<div class="wrapper">
		<div class="header">
			<div class="container">
				<div class="header__inner">
					<p class="header__title">Электронный журнал</p>
				</div>
			</div>
		</div>
		<div class="login">
			<div class="container">
				<div class="login__inner">
					<div class="login__text">
						<h1 class="login__title">Электронный журнал</h1>
						<h3 class="login__subtitle">ВФ УО БГАС</h3>
					</div>			
					<div class="login__form">
						<form action="#" method="POST">
							<h3 class="form__title">Войти</h3>
							<input type="text" name="login" placeholder="Логин">
							<input type="password" name="pass" placeholder="Пароль">
							<?php if(isset($errors)): ?>
								<p class="error"><?php echo $errors[0]; ?></p>
							<?php endif; ?>
							<input type="submit" name="submit" value="Войти">
						</form>
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
</body>
</html>