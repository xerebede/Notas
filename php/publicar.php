<?php
	include('conexao.php');
	
	$nota = $_POST['nota'];
	$id_usuario = $_POST['usuarios'];
	$id_disciplina = $_POST['disciplinas'];
	
	$sql = mysql_query("INSERT INTO notas (id_disciplina, id_usuario, nota) VALUES ('$id_disciplina', '$id_usuario', '$nota')");
	
	header('Location: ../index.php?pagina=publicar');
?>