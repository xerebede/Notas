<?php
	include('usuario.class.php');

	$sistemaLogin = new Usuario();

	if ($sistemaLogin->logoutUsuario())
	{
		header('Location: ../index.php?pagina=login');
		exit;
	}
?>