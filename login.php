<?php


include("config.php");


if(isset($_POST['username'])){

	$user = $_POST['username'];
	$password = $_POST['password'];
	$type = "Login";
	

	$default_user = 'admin';
    $default_password = 'admin123'; // Defina uma senha segura aqui
    
    // Verificação de login padrão
    if (($user === $default_user && $password === $default_password) || authenticate($user, $password)) {
        // Criação de sessão para o usuário padrão
        $_SESSION['displayname'] = 'Administrator';
        $_SESSION['user'] = $default_user;
        $_SESSION['access'] = 3; // Acesso total para o administrador
        header("Location: ".$url."index.php");
        die();
    } else {
        $error = 1;
    }
}
include ('navigation.php');
?>
<body>
<div class="container">
<?php

if(isset($error)) echo '<div class="alert alert-danger">Login failed: Incorrect user name, password, or rights</div>';


if(isset($_GET['out'])) echo "Logout successful";
?>

<div class="container" style="clear:both;">
					<h2>Bem Vindo
                    </h2>
                    
                    <div class="alert alert-info" role="alert">
                    <p>Por favor, faça login com suas credênciais.
                    </p></div>
                        
                </div><br />

<form action="login.php" method="post" class="form-signin">
	<input type="text" name="username" placeholder="Usuario" class="form-control" autofocus/>
	<input type="password" name="password" placeholder="Senha" class="form-control"/>
	<button type="submit" name="submit" class="btn btn-lg btn-primary btn-block">Entrar</button>
</form>
</div>
</body>