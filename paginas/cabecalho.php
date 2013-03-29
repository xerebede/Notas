<?php
	require_once('php/usuario.class.php');
	$sistemaUsuario = new Usuario();
?>

<header>
	<img src="img/logo.png" alt="Logomarca">
	<?php
		if ($sistemaUsuario->usuarioLogado())
		{
			echo "<p>" . $_SESSION['usuario_nome'] . " <span>|</span> <a href=\"php/logout.php\">Sair</a> </p>";
		}
		else
		{
			echo "
					<div class=\"icons\">
						<a href=\"https://plus.google.com\" class=\"icon-google-plus\" title=\"Contato no Google +\"></a>
						<a href=\"https://facebook.com\" class=\"icon-facebook\" title=\"Contato no Facebook\"></a>
						<a href=\"https://twitter.com\" class=\"icon-twitter\" title=\"Siga no Twitter\"></a>
						<a href=\"https://mail.google.com\" class=\"icon-mail\" title=\"Envie um E-mail\"></a>
					</div>";
		}
	?>
</header><!-- fim HEADER-->
