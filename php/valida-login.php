<?php
	include("usuario.class.php");
	$sistemaLogin = new Usuario();

	$usuario = $_POST['usuario'];
	$senha = $_POST['senha'];
	$lembrar = (isset($_POST['lembrar'])) ? true : false;

	if ($sistemaLogin->loginUsuario($usuario, $senha, $lembrar)) 
	{
		$pagina = ($_SESSION['usuario_nome'] == "Administrador") ? 'admin' : 'notas';
    	header("Location: ../index.php?pagina=". $pagina);
    	exit;
	} 
	else 
	{
		session_start();
		$_SESSION['erro'] = $sistemaLogin->erro;

		header("Location: ../index.php?pagina=login");
	}
?>