<?php
	include('conexao.php');

	$tabela = $_REQUEST['tabela'];
	$id = $_REQUEST['id'];

	if($tabela == 'cursos')
	{
		$chaveEstrangeira = 'id_instituicao';
	}
	else if($tabela == 'turmas')
	{
		$chaveEstrangeira = 'id_curso';
	}
	else if($tabela == 'usuarios')
	{
		$chaveEstrangeira = 'id_turma';
	}
	else if($tabela == 'disciplinas')
	{
		$chaveEstrangeira = 'id_turma';
	}
	

	if(isset($_REQUEST['funcao']) && $_REQUEST['funcao'] == 'notin')
	{
		$idCadastrado = $_REQUEST['idCadastrado'];
		
		$sql = "SELECT id, nome
			FROM $tabela
			WHERE id NOT IN ($idCadastrado)
			AND id_curso=$id
			ORDER BY nome";
	}
	else if(isset($_REQUEST['funcao']) && $_REQUEST['funcao'] == 'estrangeira')
	{
		$sql = "SELECT id, nome
			FROM $tabela
			WHERE $chaveEstrangeira=$id
			ORDER BY nome";		
	}
	else if(isset($_REQUEST['funcao']) && $_REQUEST['funcao'] == 'mpm')
	{
		$sql = "SELECT nome, id
				FROM usuarios
				WHERE id IN (SELECT id_usuario FROM usuarios_turmas WHERE id_turma = $id)
				ORDER BY nome";
	}
	else
	{	
		$sql = "SELECT id, nome
				FROM $tabela
				WHERE id=$id
				ORDER BY nome";	

		
	}

	$res = mysql_query($sql);
	
	while($campo = mysql_fetch_assoc($res))
	{
		$campos[] = array
		(
			'id'		=> $campo['id'],
			'valor'		=> $campo['nome'],
		);
	}

	echo (json_encode($campos));
?>