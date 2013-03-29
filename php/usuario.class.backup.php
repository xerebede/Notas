<?php 
	$servidor = mysql_connect('localhost', 'root', '');
	$senha = mysql_select_db('notas', $servidor);

	class Usuario
	{
		var $bancoDados = 'notas';
		var $tabelaUsuarios = 'usuarios';

		var $campos = array('usuario' => 'usuario', 
							'senha' => 'senha');

		var $dados = array('id', 'nome');

		var $iniciarSessao = true;

		var $prefixoChaves = 'usuario_';

		var $erro = '';

		var $cookie = false;

		var $cookiePath;

		var $filtraDados;

		var $caseSensitive;

		var $lembrarTempo = 7;



		function codificarSenha($senha)
		{
			return $senha;
		}

		function validarUsuario($usuario, $senha)
		{
			$senha = $this->codificarSenha($senha);

			$binary = ($this->caseSensitive) ? 'BINARY' : '';

			$sql = "SELECT COUNT(*) AS total
                	FROM `{$this->bancoDados}`.`{$this->tabelaUsuarios}`
                	WHERE {$binary} `{$this->campos['usuario']}` = '{$usuario}'
                    AND   {$binary} `{$this->campos['senha']}` = '{$senha}'";

			$query = mysql_query($sql);

			if ($query)
			{
				$total = mysql_result($query, 0, 'total');
			}
			else
			{
				return false;
			}

			return ($total == 1) ? true : false;
		}

		function loginUsuario($usuario, $senha, $lembrar = false)
		{
			if ($this->validarUsuario($usuario, $senha))
			{

				if ($this->iniciarSessao AND !isset($_SESSION))
				{
					session_start();
				}

				if ($this->dados != false) 
				{
					if (!in_array($this->campos['usuario'], $this->dados)) 
					{
						$this->dados[] = 'usuario';	
					}

					$dados = '`' . join("`, `", array_unique($this->dados)) . '`';

					$binary = ($this->caseSensitive) ? 'BINARY' : '';

					$sql = "SELECT {$dados}
                        	FROM  `{$this->bancoDados}`.`{$this->tabelaUsuarios}`
                        	WHERE  {$binary} `{$this->campos['usuario']}` = '{$usuario}'";

	           		$query = mysql_query($sql);


	           		if (!$query) 
	           		{
	           			$this->erro = 'A consulta de dados e invalida';

	           			return false;
	           		}
	           		else
	           		{
	           			$dados = mysql_fetch_assoc($query);
	           			mysql_free_result($query);

	           			foreach ($dados as $chave => $valor) 
	           			{
	           				$_SESSION[$this->prefixoChaves . $chave] = $valor;
	           			}
	           		}

				}

				$_SESSION[$this->prefixoChaves . 'logado'] = true;

				if ($this->cookie) 
				{
					$valor = join("#", array($usuario, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']));

					sha1($valor);

					setcookie($this->prefixoChaves . 'token', $valor, 0, '/');
				}

				if ($lembrar) $this->lembrarDados($usuario, $senha);

				return true;
			}
			else
			{
				$this->erro = 'Usuario invalido ou senha invalidos';

				return false;
			}
		}

		function usuarioLogado()
		{
			if ($this->iniciarSessao && !isset($_SESSION))
			{
				session_start();
			}

			if (!isset($_SESSION[$this->prefixoChaves . 'logado']) || !$_SESSION[$this->prefixoChaves . 'logado'])
			{
				return false;
			}

			if ($this->cookie)
			{
				if (!isset($_cookie[$this->prefixoChaves . 'token']))
				{
					return false;
				}
				else
				{
					$valor = array(join($_SESSION[$this->prefixoChaves . 'usuario']), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);

					$valor = sha1($valor);

					if ($_cookie[$this->prefixoChaves . 'token'] != $valor)
					{
						return false;
					}
				}
			}

			return true;
		}

		function logoutUsuario()
		{
			if ($this->iniciarSessao && !isset($_SESSION))
			{
				session_start();
			}

			$tamanhoPrefixo = strlen($this->prefixoChaves);

			foreach ($_SESSION as $chave => $valor)
			{
				if (substr($chave, 0, $tamnhoPrefixo) == $this->prefixoChaves)
				{
					unset($_SESSION[$chave]);
				}
			}

			if (count($_SESSION == 0))
			{
				session_destroy();

				if (isset($_COOKIE['PHPSESSID'])) 
				{
					setcookie('PHPSESSID', false, (time() - 3600));
					unset($_COOKIE['PHPSESSID']);
				}
			}

			if ($this->cookie && isset($_COOKIE[$this->prefixoChaves . 'token']))
			{
				setcookie($this->prefixoChaves . 'token', flase, (time() - 3600), '/');

				unset($_COOKIE[$this->prefixoChaves . 'token']);
			}

			return $this->usuarioLogado();
		}

		function lembrarDados($usuario, $senha)
		{
			$tempo = strtotime("+{$this->lembrarTempo} day", time());

			$usuario = rand(1, 9) . base64_encode($usuario);
			$senha = rand(1,9) . base64_encode($senha);

			setcookie($this->prefixoChaves . 'loginUsuario', $usuario, $tempo, $this->cookiePath);
			setcookie($this->prefixoChaves . 'senhaUsuario', $senha, $tempo, $this->cookiePath);
		}

		function verificarDadosLembrados()
		{
			if (isset($_COOKIE[$this->prefixoChaves . 'loginUsuario']) && isset($_COOKIE[$this->prefixoChaves . 'senhaUsuario']))
			{
				$usuario = base64_decode(substr($_COOKIE[$this->prefixoChaves . 'loginUsuario'], 1));
				$senha = base64_decode($_COOKIE[$this->prefixoChaves . 'senhaUsuario'], 1);

				return $this->loginUsuario($usuario, $senha, true);
			}

			return false;
		}

		function limpaDadosLembrados()
		{
			if (isset($_COOKIE[$this->prefixoChaves . 'loginUsuario'])) 
			{
        		setcookie($this->prefixoChaves . 'loginUsuario', false, (time() - 3600), $this->cookiePath);
        		unset($_COOKIE[$this->prefixoChaves . 'loginUsuario']);
   			}

			if (isset($_COOKIE[$this->prefixoChaves . 'senhaUsuario'])) 
			{
        		setcookie($this->prefixoChaves . 'senhaUsuario', false, (time() - 3600), $this->cookiePath);
        		unset($_COOKIE[$this->prefixoChaves . 'senhaUsuario']);
    		}
		}
	}
?>