	<?php
		if(!isset($_SESSION))
		{
			session_start();
		}

		if($_SESSION['usuario_logado'] && $_COOKIE['usuario_loginAutomatico'])
		{
			$pagina = ($_SESSION['usuario_nome'] == 'Administrador') ? 'admin' : 'notas';
			header('Location:index.php?pagina=' . $pagina);
		}

		if(isset($_SESSION['erro']))
		{
			echo "<script>notyAlert(\"" . $_SESSION['erro'] . "\", \"error\")</script>";
			unset($_SESSION['erro']);
		}
	?>

	<form class="box" name="formLogin" method="post" action="php/valida-login.php">
		<fieldset class="boxBody">
			<label for="usuario">Usuario</label>
		  	<input type="text" tabindex="1" name="usuario" id="usuario" placeholder="Nome de Ususario">
		  	<label for="senha">Senha</label>
		  	<a href="#" class="rLink" tabindex="5">Esqueceu sua senha?</a>
		  	<input type="password" tabindex="2" name="senha" id="senha" placeholder="Senha">
		</fieldset>
		<footer>
		  	<label><input type="checkbox" tabindex="3" name="lembrar">Mantenha-me conectado</label>
		  	<input type="submit" class="btnLogin" value="Logar" tabindex="4">
		</footer>
	</form>