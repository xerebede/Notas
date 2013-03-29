<?php
	if(!isset($_SESSION))
	{
		session_start();
	}

	require_once('php/usuario.class.php');
	$sistemaLogin = new Usuario();

	$pagina =(isset($_GET['pagina'])) ? $_GET['pagina'] : "";

	switch ($pagina) 
	{
		case 'login':
			include('paginas/login.php');
			break;
		case 'notas':
			if($sistemaLogin->usuarioLogado())
			{
				include('paginas/notas.php');
			}
			else
			{
				$_SESSION['erro'] = 'Faça login para acessar o conteudo do site';
				header('Location:index.php?pagina=login');
			}
			break;
		case 'admin':
			if($sistemaLogin->usuarioLogado() && $_SESSION['usuario_nome'] == 'Administrador')
			{
				include('paginas/cadastro-alunos.php');
			}
			else
			{
				$_SESSION['erro'] = 'A pagina que você tentou acessar é restrita';
				header('Location:index.php?pagina=notas');
			}
			break;
		case 'cadastro':
			if($sistemaLogin->usuarioLogado() && $_SESSION['usuario_nome'] == 'Administrador')
			{
				include('paginas/cadastro-alunos.php');
			}
			else
			{
				$_SESSION['erro'] = 'A pagina que você tentou acessar é restrita';
				header('Location:index.php?pagina=notas');
			}
			break;
		case 'publicar':
			if($sistemaLogin->usuarioLogado() && $_SESSION['usuario_nome'] == 'Administrador')
			{
				include('paginas/publicar-notas.php');
			}
			else
			{
				$_SESSION['erro'] = 'A pagina que você tentou acessar é restrita';
				header('Location:index.php?pagina=notas');
			}
			break;
		case 'alterar':
			if($sistemaLogin->usuarioLogado() && $_SESSION['usuario_nome'] == 'Administrador')
			{
				include('paginas/alterar-notas.php');
			}
			else
			{
				$_SESSION['erro'] = 'A pagina que você tentou acessar é restrita';
				header('Location:index.php?pagina=notas');
			}
			break;
		case 'verificar':
			if($sistemaLogin->usuarioLogado() && $_SESSION['usuario_nome'] == 'Administrador')
			{
				include('paginas/verificar-notas.php');
			}
			else
			{
				$_SESSION['erro'] = 'A pagina que você tentou acessar é restrita';
				header('Location:index.php?pagina=notas');
			}
			break;
		case 'pesquisa':
			if($sistemaLogin->usuarioLogado() && $_SESSION['usuario_nome'] == 'Administrador')
			{
				include('paginas/pesquisa.php');
			}
			else
			{
				$_SESSION['erro'] = 'A pagina que você tentou acessar é restrita';
				header('Location:index.php?pagina=notas');
			}
			break;
		default:
			include('paginas/login.php');
			break;
	}
?>