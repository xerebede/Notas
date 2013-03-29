<!doctype html>

<html lang="pt">

<head>
	<meta charset="UTF-8">
	<title>Notas Online</title>

	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/estilo.css">
	<link rel="stylesheet" href="css/login.css">

	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/jquery.noty.js"></script>
	<script src="js/default.js"></script>
	<script src="js/topCenter.js"></script>

	<script src="js/aplicacoes.js"></script>
</head>

<body>
<?php
	$pagina =(isset($_GET['pagina'])) ? $_GET['pagina'] : "login";

	if($pagina != 'login')
	{
		include('paginas/cabecalho.php');

		switch ($pagina) 
		{
			case 'admin':
			case 'cadastro':
			case 'publicar':
			case 'alterar':
			case 'verificar':
			case 'pesquisa':
				include('paginas/painel-admin.php');
				break;
		}
	}
?>

<section id="conteudo">
<?php 
	include('paginas/paginas.php');
?>

</section>
</body>

</html>