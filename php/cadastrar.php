<?php
	include('conexao.php');
	

	if($_GET['cadastro'] == 'usuario')
	{
		if(isset($_POST['nome']))
		{
			$nome = $_POST['nome'];
			$usuario = $_POST['usuario'];
			$senha = $_POST['senha'];
			$email = $_POST['email'];
			$turma_id = $_POST['turmas'];
			
			$sql1 = mysql_query("INSERT INTO usuarios (nome, usuario, senha, email) VALUES ('$nome', '$usuario', '$senha', '$email')");
			
			$usuario_id = mysql_insert_id($con);
			
			$sql2 = mysql_query("INSERT INTO usuarios_turmas (id_usuario, id_turma) VALUES ('$usuario_id', '$turma_id')");
			
			header('Location: ../index.php?pagina=admin');
		}

		if(isset($_POST['usuarios']))
		{
			$usuario_id = $_POST['usuarios'];
			$turma_id = $_POST['turmas'];
			
			$sql = mysql_query("INSERT INTO usuarios_turmas (id_usuario, id_turma) VALUES ('$usuario_id', '$turma_id')");
			
			header('Location: ../index.php?pagina=admin');
		}
	
		
	}
	
	if($_GET['cadastro'] == 'instituicao')
	{
		if(isset($_POST['instituicoes']) && !is_numeric($_POST['instituicoes']))
		{
			$instituicao = $_POST['instituicoes'];
			
			$sql = mysql_query("INSERT INTO instituicoes (nome) VALUES ('$instituicao')") or die(mysql_error());
			
			header('Location: ../index.php?pagina=admin');
		}
		
		if(isset($_POST['cursos']) && !is_numeric($_POST['cursos']) && is_numeric($_POST['instituicoes']))
		{
			$curso = $_POST['cursos'];
			$id_instituicao = $_POST['instituicoes'];
			
			$sql = mysql_query("INSERT INTO cursos (id_instituicao, nome) VALUES ('$id_instituicao', '$curso')");
			
			header('Location: ../index.php?pagina=admin');
		}
		
		if(isset($_POST['turmas']) && !is_numeric($_POST['turmas']) && is_numeric($_POST['cursos']))
		{
			$turma = $_POST['turmas'];
			$id_curso = $_POST['cursos'];
			
			$sql = mysql_query("INSERT INTO turmas (id_curso, nome) VALUES ('$id_curso', '$turma')");
			
			header('Location: ../index.php?pagina=admin');
		}
		
		if(isset($_POST['disciplinas']) && !is_numeric($_POST['disciplinas']) && is_numeric($_POST['turmas']))
		{
			$disciplina = $_POST['disciplinas'];
			$id_turma = $_POST['turmas'];
			
			$sql = mysql_query("INSERT INTO disciplinas (id_turma, nome) VALUES ('$id_turma', '$disciplina')");
			
			header('Location: ../index.php?pagina=admin');
		}				
	}
?>