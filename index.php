<?php
include 'conexion.php';
session_start();

if (isset($_POST["ingresar"])) {
	$usuario = strtoupper(trim($_POST["user"]));
	$password = trim($_POST["pass"]);
	$passwordEncrypt = password_hash($password, PASSWORD_BCRYPT);

	// Buscar el usuario en la base de datos
	$stmt = $conexion->prepare("SELECT id, password FROM usuarios WHERE usuario = ?");
	$stmt->bind_param("s", $usuario);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows > 0) {
		$stmt->bind_result($id, $hashed_password);
		$stmt->fetch();
		// Verificar la contraseña
		if (password_verify($password, $hashed_password)) {
			$_SESSION["id"] = $id;
			header("Location: admin.php");
			exit;
		} else {
			echo "<script>alert('Usuario o contraseña inválidos.')</script>";
		}
	} else {
		echo "<script>alert('Usuario o contraseña invalidos.')</script>";
	}
	// Cerrar la declaración
	$stmt->close();
}


// Registrar usuario
if (isset($_POST["registrar"])) {
	$nombre = strtoupper(trim($_POST["nombre"]));
	$correo = strtoupper(trim($_POST["correo"]));
	$usuario = strtoupper(trim($_POST["user"]));
	$password = trim($_POST["pass"]);
	$passwordR = trim($_POST["passr"]);

	if (empty($nombre) || empty($correo) || empty($usuario) || empty($password) || empty($passwordR)) {
		echo "<script>alert('Todos los campos son obligatorios.')</script>";
		exit;
	}

	// Validar correo
	if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
		echo "<script>alert('Correo no válido.')</script>";
		exit;
	}

	if ($password != $passwordR) {
		echo "<script>alert('Las contraseñas no son iguales.')</script>";
		exit;
	}

	// Encriptar contraseña
	$passwordEncrypt = password_hash($password, PASSWORD_BCRYPT);


	$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ? OR correo = ?");
	$stmt->bind_param("ss", $usuario, $correo);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows > 0) {
		echo "<script>alert('El usuario o correo ya se encuentra registrado.')</script>";
	} else {
		// Guardar usuario
		$stmt = $conexion->prepare("INSERT INTO usuarios(nombre, correo, usuario, password) VALUES(?, ?, ?, ?)");
		$stmt->bind_param("ssss", $nombre, $correo, $usuario, $passwordEncrypt);
		if ($stmt->execute() > 0) {
			echo "<script>alert('Cuenta registrada exitosamente.')</script>";
		} else {
			echo "<script>alert('Error en el registro.')</script>";
		}
	}

	// Cerrar la declaración
	$stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title>Login - Sistema de Usuarios</title>

	<meta name="description" content="User login page" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />

	<!-- text fonts -->
	<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />

	<!-- ace styles -->
	<link rel="stylesheet" href="assets/css/ace.min.css" />


	<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />

</head>

<body class="login-layout">
	<div class="main-container">
		<div class="main-content">
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<div class="login-container">
						<div class="center">
							<h1>
								<i class="ace-icon fa fa-leaf green"></i>
								<span class="red">Sistema </span>
								<span class="white" id="id-text2">de Usuarios</span>
							</h1>
						</div>

						<div class="space-6"></div>

						<div class="position-relative">
							<div id="login-box" class="login-box visible widget-box no-border">
								<div class="widget-body">
									<div class="widget-main">
										<h4 class="header blue lighter bigger">
											<i class="ace-icon fa fa-coffee green"></i>
											Ingresa tu Informacion
										</h4>

										<div class="space-6"></div>

										<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
											<fieldset>
												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="text" class="form-control" name="user" placeholder="Usuario" required />
														<i class="ace-icon fa fa-user"></i>
													</span>
												</label>

												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="password" name="pass" class="form-control" placeholder="Contraseña" required />
														<i class="ace-icon fa fa-lock"></i>
													</span>
												</label>

												<div class="space"></div>

												<div class="clearfix">
													<label class="inline">
														<input type="checkbox" class="ace" />
														<span class="lbl"> Recordarme</span>
													</label>

													<button type="submit" name="ingresar" class="width-35 pull-right btn btn-sm btn-dark">
														<i class="ace-icon fa fa-key"></i>
														<span class="bigger-110">Ingresar</span>
													</button>


												</div>

												<div class="space-4"></div>
											</fieldset>
										</form>

										<div class="social-or-login center">
											<span class="bigger-110">Seguir en:</span>
										</div>

										<div class="space-6"></div>

										<div class="social-login center">
											<a href="#" target="_blank" class="btn btn-danger">
												<i class="ace-icon fa fa-youtube"></i>
											</a>
											<a href="#" target="_blank" class="btn btn-primary">
												<i class="ace-icon fa fa-facebook"></i>
											</a>

											<a href="#" target="_blank" class="btn btn-info">
												<i class="ace-icon fa fa-twitter"></i>
											</a>

											<a href="#" target="_blank" class="btn btn-danger">
												<i class="ace-icon fa fa-instagram"></i>
											</a>
										</div>
									</div><!-- /.widget-main -->

									<div class="toolbar clearfix">
										<div>
											<a href="#" data-target="#forgot-box" class="forgot-password-link">
												<i class="ace-icon fa fa-arrow-left"></i>
												Olvide mi contraseña
											</a>
										</div>

										<div>
											<a href="#" data-target="#signup-box" class="user-signup-link">
												Nuevo Registro
												<i class="ace-icon fa fa-arrow-right"></i>
											</a>
										</div>
									</div>
								</div><!-- /.widget-body -->
							</div><!-- /.login-box -->

							<div id="forgot-box" class="forgot-box widget-box no-border">
								<div class="widget-body">
									<div class="widget-main">
										<h4 class="header red lighter bigger">
											<i class="ace-icon fa fa-key"></i>
											Recuperar Contraseña
										</h4>

										<div class="space-6"></div>
										<p>
											Ungresa tu correo electronico para recibir las instrucciones
										</p>

										<form>
											<fieldset>
												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="email" class="form-control" placeholder="Email" />
														<i class="ace-icon fa fa-envelope"></i>
													</span>
												</label>
												<div class="clearfix">
													<button type="button" class="width-35 pull-right btn btn-sm btn-danger">
														<i class="ace-icon fa fa-lightbulb-o"></i>
														<span class="bigger-110">Enviar</span>
													</button>
												</div>
											</fieldset>
										</form>
									</div><!-- /.widget-main -->

									<div class="toolbar center">
										<a href="#" data-target="#login-box" class="back-to-login-link">
											Regresar al Login
											<i class="ace-icon fa fa-arrow-right"></i>
										</a>
									</div>
								</div><!-- /.widget-body -->
							</div><!-- /.forgot-box -->

							<div id="signup-box" class="signup-box widget-box no-border">
								<div class="widget-body">
									<div class="widget-main">
										<h4 class="header green lighter bigger">
											<i class="ace-icon fa fa-users blue"></i>
											Registro de Nuevos Usuarios
										</h4>
										<div class="space-6"></div>
										<p>Ingresa los datos solicitados acontinuacion: </p>
										<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
											<fieldset>
												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="text" class="form-control" name="nombre" placeholder="Nombre Completo" required />
														<i class="ace-icon fa fa-users"></i>
													</span>
												</label>

												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="email" class="form-control" name="correo" placeholder="Email" required />
														<i class="ace-icon fa fa-envelope"></i>
													</span>
												</label>
												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="text" class="form-control" name="user" placeholder="Usuario" required />
														<i class="ace-icon fa fa-user"></i>
													</span>
												</label>
												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="password" class="form-control" name="pass" placeholder="Password" required />
														<i class="ace-icon fa fa-lock"></i>
													</span>
												</label>

												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="password" class="form-control" name="passr" placeholder="Repetir password" />
														<i class="ace-icon fa fa-retweet"></i>
													</span>
												</label>

												<label class="block">
													<input type="checkbox" class="ace" />
													<span class="lbl">
														Acepto los
														<a href="#">Terminos de Uso</a>
													</span>
												</label>
												<div class="space-24"></div>
												<div class="clearfix">
													<button type="reset" class="width-30 pull-left btn btn-sm">
														<i class="ace-icon fa fa-refresh"></i>
														<span class="bigger-110">Reset</span>
													</button>

													<button type="submit" name="registrar" class="width-65 pull-right btn btn-sm btn-success">
														<span class="bigger-110">Registrar</span>
														<i class="ace-icon fa fa-arrow-right icon-on-right"></i>
													</button>
												</div>
											</fieldset>
										</form>
									</div>

									<div class="toolbar center">
										<a href="#" data-target="#login-box" class="back-to-login-link">
											<i class="ace-icon fa fa-arrow-left"></i>
											Regresar al Login
										</a>
									</div>
								</div><!-- /.widget-body -->
							</div><!-- /.signup-box -->
						</div><!-- /.position-relative -->

						<div class="navbar-fixed-top align-right">
							<br />
							&nbsp;
							<a id="btn-login-dark" href="#">Oscuro</a>
							&nbsp;
							<span class="blue">/</span>
							&nbsp;
							<a id="btn-login-blur" href="#">Azul</a>
							&nbsp;
							<span class="blue">/</span>
							&nbsp;
							<a id="btn-login-light" href="#">Claro</a>
							&nbsp; &nbsp; &nbsp;
						</div>
					</div>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.main-content -->
	</div><!-- /.main-container -->

	<!-- basic scripts -->

	<!--[if !IE]> -->
	<script src="assets/js/jquery-2.1.4.min.js"></script>

	<!-- <![endif]-->

	<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
	<script type="text/javascript">
		if ('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		jQuery(function($) {
			$(document).on('click', '.toolbar a[data-target]', function(e) {
				e.preventDefault();
				var target = $(this).data('target');
				$('.widget-box.visible').removeClass('visible'); //hide others
				$(target).addClass('visible'); //show target
			});
		});



		//you don't need this, just used for changing background
		jQuery(function($) {
			$('#btn-login-dark').on('click', function(e) {
				$('body').attr('class', 'login-layout');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'blue');

				e.preventDefault();
			});
			$('#btn-login-light').on('click', function(e) {
				$('body').attr('class', 'login-layout light-login');
				$('#id-text2').attr('class', 'grey');
				$('#id-company-text').attr('class', 'blue');

				e.preventDefault();
			});
			$('#btn-login-blur').on('click', function(e) {
				$('body').attr('class', 'login-layout blur-login');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'light-blue');

				e.preventDefault();
			});

		});
	</script>
</body>

</html>