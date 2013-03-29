<?php
include('conexao.php');

/**
 * Classe Usuario
 *  gerencia controle de login e permissões de usuário
 * 
 * @todo Criar a funcionalidade "Esqueci minha senha"
 * 
 */
class Usuario {
	/**
	 * Nome do banco de dados onde está a tabela de usuários
	 *
	 * @var string
	 */
	var $bancoDeDados = 'notas';
	
	/**
	 * Nome da tabela de usuários
	 *
	 * @var string
	 */
	var $tabelaUsuarios = 'usuarios';
	
	/**
	 * Nomes dos campos onde ficam o usuário, a senha e o e-mail de cada usuário
	 * Formato: tipo => nome do campo na tabela
	 * O campo (email) só é necessário para o "Esqueci minha senha"
	 * 
	 * @var array
	 */
	var $campos = array(
		'usuario' => 'usuario',
		'senha' => 'senha'
	);
	
	/**
	 * Nomes dos campos que serão pegos da tabela de usuarios e salvos na sessão,
	 * caso o valor seja false nenhum dado será consultado
	 * 
	 * @var mixed
	 */
	var $dados = array('id', 'nome');
	
	/**
	 * Inicia a sessão se necessário?
	 * 
	 * @var boolean
	 */
	var $iniciarSessao = true;
	
	/**
	 * Prefixo das chaves usadas na sessão
	 * 
	 * @var string
	 */
	var $prefixoChaves = 'usuario_';
	
	/**
	 * Usa um cookie para melhorar a segurança?
	 * 
	 * @var boolean
	 */
	var $cookie = true;
	
	/**
	 * O usuário e senha são case-sensitive?
	 * 
	 * @var boolean
	 */
	var $caseSensitive = true;
	
	/**
	 * Filtra os dados antes de consultá-los usando mysql_real_escape_string()?
	 * 
	 * @var boolean
	 */
	var $filtrarDados = true;
	
	/**
	 * Quantidade (em dias) que o sistema lembrará os dados do usuário ("Lembrar minha senha")
	 * 
	 * Usado apenas quando o terceiro parâmetro do método Usuario::loginUsuario() for true
	 * Os dados salvos serão encriptados usando base64
	 * 
	 * @var integer
	 */
	var $lembrarTempo = 7;
	
	/**
	 * Diretório a qual o cookie vai pertencer
	 * 
	 * @var string
	 */
	var $cookiePath = '/';
	
	/**
	 * Armazena as mensagens de erro
	 * 
	 * @var string
	 */
	var $erro = '';
	
	/**
	 * Método codificarSenha()
	 *  codifica a senha do usuário
	 * 
	 * @access public
	 * @param string $senha - a senha que será codificada
	 * @return string - a senha já codificada
	 */
	function codificarSenha($senha)
	{
		# return sha1($senha);
		# return md5($senha);
		return $senha;
	}
	
	/**
	 * Método validarUsuario()
	 *  verifica se um usuário existe no sistema
	 * 
	 * @access public
	 * @uses Usuario::codificarSenha()
	 *
	 * @param string $usuario - o usuário que será validado
	 * @param string $senha - a senha que será validada
	 * @return boolean - se o usuário existe
	 */
	function validarUsuario($usuario, $senha)
	{
		$senha = $this->codificarSenha($senha);
		
		// Filtra os dados?
		if ($this->filtrarDados)
		{
			$usuario = mysql_escape_string($usuario);
			$senha = mysql_escape_string($senha);
		}
		
		// Os dados são case-sensitive?
		$binary = ($this->caseSensitive) ? 'BINARY' : '';

		// Procura por usuários com o mesmo usuário e senha do parametro
		$sql = "SELECT COUNT(*) AS total
				FROM `{$this->bancoDeDados}`.`{$this->tabelaUsuarios}`
				WHERE
					{$binary} `{$this->campos['usuario']}` = '{$usuario}'
					AND
					{$binary} `{$this->campos['senha']}` = '{$senha}'";

		$query = mysql_query($sql);

		if ($query)
		{
			// Total de usuários encontrados
			$total = mysql_result($query, 0, 'total');
			
			// Limpa a consulta da memória
			mysql_free_result($query);
		}
		else 
		{
			// A consulta foi mal sucedida, retorna false
			return false;
		}
		
		// Se houver apenas um usuário, retorna true
		return ($total == 1) ? true : false;
	}
	
	/**
	 * Método loginUsuario()
	 *  tenta logar um usuário no sistema salvando seus dados na sessão e cookies
	 * 
	 * @access public
	 * @uses Usuario::validarUsuario()
	 * @uses Usuario::lembrarDados()
	 *
	 * @param string $usuario - o usuário que será logado
	 * @param string $senha  - a senha do usuário
	 * @param boolean $lembrar - salvar os dados em cookies? (Lembrar minha senha)
	 * @return boolean - se o usuário foi logado
	 */
	function loginUsuario($usuario, $senha, $lembrar = false)
	{			
		// Verifica se é um usuário válido
		if ($this->validarUsuario($usuario, $senha))
		{
			// Inicia a sessão?
			if ($this->iniciarSessao AND !isset($_SESSION))
			{
				session_start();
			}
		
			// Filtrar os dados?
			if ($this->filtrarDados)
			{
				$usuario = mysql_real_escape_string($usuario);
				$senha = mysql_real_escape_string($senha);
			}
			
			// Traz dados da tabela?
			if ($this->dados != false)
			{
				// Adiciona o campo do usuário na lista de dados
				if (!in_array($this->campos['usuario'], $this->dados))
				{
					$this->dados[] = 'usuario';
				}
			
				// Monta o formato SQL da lista de campos
				$dados = '`' . join('`, `', array_unique($this->dados)) . '`';
		
				// Os dados são case-sensitive?
				$binary = ($this->caseSensitive) ? 'BINARY' : '';

				// Consulta os dados
				$sql = "SELECT {$dados}
						FROM `{$this->bancoDeDados}`.`{$this->tabelaUsuarios}`
						WHERE {$binary} `{$this->campos['usuario']}` = '{$usuario}'";

				$query = mysql_query($sql);
				
				// Se a consulta falhou
				if (!$query)
				{
					// A consulta foi mal sucedida, retorna false
					$this->erro = 'A consulta dos dados é inválida';
					return false;
				}
				else
				{
					// Traz os dados encontrados para um array
					$dados = mysql_fetch_assoc($query);
					// Limpa a consulta da memória
					mysql_free_result($query);
					
					// Passa os dados para a sessão
					foreach ($dados AS $chave=>$valor)
					{
						$_SESSION[$this->prefixoChaves . $chave] = $valor;
					}
				}
			}
			
			// Usuário logado com sucesso
			$_SESSION[$this->prefixoChaves . 'logado'] = true;
			
			// Define um cookie para maior segurança?
			if ($this->cookie)
			{
				// Monta uma cookie com informações gerais sobre o usuário: usuario, ip e navegador
				$valor = join('#', array($usuario, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']));
				
				// Encripta o valor do cookie
				$valor = sha1($valor);
				
				// Cria o cookie
				setcookie($this->prefixoChaves . 'token', $valor, 0, $this->cookiePath);
			}
			
			// Salva os dados do usuário em cookies? ("Lembrar minha senha")
			if ($lembrar) $this->lembrarDados($usuario, $senha);
			
			// Fim da verificação, retorna true
			return true;
			
						
		} 
		else
		{
			$this->erro = 'Usuário ou senha invalidos';
			return false;
		}
	}
	
	/**
	 * Método usuarioLogado()
	 *  verifica se há um usuário logado no sistema
	 * 
	 * @access public
	 * @uses Usuario::verificarDadosLembrados()
	 *
	 * @param boolean $cookies - verifica também os cookies?
	 * @return boolean - se há um usuário logado
	 */
	function usuarioLogado($cookies = true)
	{
		// Inicia a sessão?
		if ($this->iniciarSessao AND !isset($_SESSION))
		{
			session_start();
		}
		
		// Verifica se não existe o valor na sessão
		if (!isset($_SESSION[$this->prefixoChaves . 'logado']) OR !$_SESSION[$this->prefixoChaves . 'logado'])
		{
			// Não existem dados na sessão
			
			// Verifica os dados salvos nos cookies?
			if ($cookies)
			{
				// Se os dados forem válidos o usuário é logado automaticamente
				return $this->verificarDadosLembrados();
			} 
			else
			{
				// Não há usuário logado
				$this->erro = 'Não há usuário logado';
				return false;
			}
		}
		
		// Faz a verificação do cookie?
		if ($this->cookie)
		{
			// Verifica se o cookie não existe
			if (!isset($_COOKIE[$this->prefixoChaves . 'token']))
			{
				$this->erro = 'Não há usuário logado';
				return false;
			}
			else
			{
				// Monta o valor do cookie
				$valor = join('#', array($_SESSION[$this->prefixoChaves . 'usuario'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']));
	
				// Encripta o valor do cookie
				$valor = sha1($valor);
	
				// Verifica o valor do cookie
				if ($_COOKIE[$this->prefixoChaves . 'token'] !== $valor)
				{
					$this->erro = 'Não há usuário logado';
					return false;
				}
			}
		}
		
		// A sessão e o cookie foram verificados, há um usuário logado
		return true;
	}
	
	/**
	 * Método logoutUsuario()
	 *  faz logout do usuário logado
	 * 
	 * @access public
	 * @uses Usuario::limparDadosLembrados()
	 * @uses Usuario::usuarioLogado()
	 * 
	 * @param boolean $cookies - limpa também os cookies de "Lembrar minha senha"?
	 * @return boolean
	 */
	function logoutUsuario($cookies = true)
	{
		// Inicia a sessão?
		if ($this->iniciarSessao AND !isset($_SESSION))
		{
			session_start();
		}
		
		// Tamanho do prefixo
		$tamanho = strlen($this->prefixoChaves);

		// Destroi todos os valores da sessão relativos ao sistema de login
		foreach ($_SESSION AS $chave=>$valor)
		{
			// Remove apenas valores cujas chaves comecem com o prefixo correto
			if (substr($chave, 0, $tamanho) == $this->prefixoChaves)
			{
				unset($_SESSION[$chave]);
			}
		}
		
		// Destrói asessão se ela estiver vazia
		if (count($_SESSION) == 0)
		{
			session_destroy();
			
			// Remove o cookie da sessão se ele existir
			if (isset($_COOKIE['PHPSESSID']))
			{
				setcookie('PHPSESSID', false, (time() - 3600));
				unset($_COOKIE['PHPSESSID']);
			}
		}
		
		// Remove o cookie com as informações do visitante
		if ($this->cookie AND isset($_COOKIE[$this->prefixoChaves . 'token']))
		{
			setcookie($this->prefixoChaves . 'token', false, (time() - 3600), $this->cookiePath);
			unset($_COOKIE[$this->prefixoChaves . 'token']);
		}
		
		// Limpa também os cookies de "Lembrar minha senha"?
		if ($cookies) $this->limparDadosLembrados();
		
		// Retorna SE não há um usuário logado (sem verificar os cookies)
		return !$this->usuarioLogado(false);
	}
	
	/**
	 * Método lembrarDados()
	 *  salva os dados do usuário em cookies ("Lembrar minha senha")
	 * 
	 * @access public
	 * 
	 * @param string $usuario - o usuário que será lembrado
	 * @param string $senha - a senha do usuário
	 * @return void
	 */
	function lembrarDados($usuario, $senha)
	{	
		// Calcula o timestamp final para os cookies expirarem
		$tempo = strtotime("+{$this->lembrarTempo} day", time());

		// Encripta os dados do usuário usando base64
		// O rand(1, 9) cria um digito no início da string que impede a descriptografia
		$usuario = rand(1, 9) . base64_encode($usuario);
		$senha = rand(1, 9) . base64_encode($senha);
	
		// Cria um cookie com o usuário
		setcookie($this->prefixoChaves . 'lu', $usuario, $tempo, $this->cookiePath);
		// Cria um cookie com a senha
		setcookie($this->prefixoChaves . 'ls', $senha, $tempo, $this->cookiePath); 
		// Mantem o usuario logado
		setcookie($this->prefixoChaves . 'loginAutomatico', true, $tempo, $this->cookiePath);
	}
	
	/**
	 * Método verificarDadosLembrados()
	 *  verifica os dados do cookie (caso eles existam)
	 * 
	 * @access public
	 * @uses Usuario::loginUsuario()
	 * 
	 * @return boolean - os dados são validos?
	 */
	function verificarDadosLembrados()
	{
		// Os cookies de "Lembrar minha senha" existem?
		if (isset($_COOKIE[$this->prefixoChaves . 'lu']) AND isset($_COOKIE[$this->prefixoChaves . 'ls']))
		{
			// Pega os valores salvos nos cookies removendo o digito e desencriptando
			$usuario = base64_decode(substr($_COOKIE[$this->prefixoChaves . 'lu'], 1));
			$senha = base64_decode(substr($_COOKIE[$this->prefixoChaves . 'ls'], 1));
			
			// Tenta logar o usuário com os dados encontrados nos cookies
			return $this->loginUsuario($usuario, $senha, true);		
		}
		
		// Não há nenhum cookie, dados inválidos
		return false;
	}
	
	/**
	 * Método limparDadosLembrados()
	 *  limpa os dados lembrados dos cookies ("Lembrar minha senha")
	 * 
	 * @access public
	 * 
	 * @return void
	 */
	function limparDadosLembrados()
	{
		// Deleta o cookie com o usuário
		if (isset($_COOKIE[$this->prefixoChaves . 'lu']))
		{
			setcookie($this->prefixoChaves . 'lu', false, (time() - 3600), $this->cookiePath);
			unset($_COOKIE[$this->prefixoChaves . 'lu']);			
		}

		// Deleta o cookie com a senha
		if (isset($_COOKIE[$this->prefixoChaves . 'ls']))
		{
			setcookie($this->prefixoChaves . 'ls', false, (time() - 3600), $this->cookiePath);
			unset($_COOKIE[$this->prefixoChaves . 'ls']);			
		}

		// Não mantem o usuario logado
		setcookie($this->prefixoChaves . 'loginAutomatico', false, $tempo, $this->cookiePath);	
	}
}

?>