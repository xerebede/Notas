<section id="notas">
		<h1>Professor :-.-:</h1>
		
	<?php
		// Se não houver sessão aberta abre uma nova
		if(!isset($_SESSION))
		{
			session_start();
		}

		// Se houver um erro na sessão, notifica-o via Noty
		if(isset($_SESSION['erro']))
		{
			echo "<script>notyAlert(\"" . $_SESSION['erro'] . "\", \"error\")</script>";
			unset($_SESSION['erro']);
		}
		
		// Se houver o id do usuario da seção da inicio a seleção de notas
		if(isset($_SESSION['usuario_id']))
		{
			$usuario_id = $_SESSION['usuario_id'];
			
			$sql_usuario = "SELECT usuarios.nome, turmas.id
							FROM usuarios
							INNER JOIN usuarios_turmas ON usuarios_turmas.id_usuario = usuarios.id
							INNER JOIN turmas ON usuarios_turmas.id_turma = turmas.id
							WHERE usuarios.id = $usuario_id
							ORDER BY nome";
			
			$res = mysql_query($sql_usuario);
			
			while($usuario = mysql_fetch_array($res))
			{
				$usuario_nome = $usuario['nome'];
				$turma_id = $usuario['id'];
				
				$sql_turma = mysql_query("SELECT * FROM turmas WHERE id=$turma_id");
			
				while($turma = mysql_fetch_array($sql_turma))
				{
					$turma_nome = $turma['nome'];
					$turma_id = $turma['id'];
					$curso_id = $turma['id_curso'];
					
					$sql_curso = mysql_query("SELECT * FROM cursos WHERE id=$curso_id");
			
					while($curso = mysql_fetch_array($sql_curso))
					{
						$curso_nome = $curso['nome'];
						$curso_id = $curso['id'];
						$instituicao_id = $curso['id_instituicao'];
						
						$sql_instituicao = mysql_query("SELECT nome FROM instituicoes WHERE id=$instituicao_id");
			
						while($instituicao = mysql_fetch_array($sql_instituicao))
						{
							$instituicao_nome = $instituicao['nome'];
						}	
					}
				}
	?>
				
		<table>
			<tr>
				<th colspan="2"> <?php echo $instituicao_nome ?> - <?php echo $curso_nome ?> - <?php echo $turma_nome ?> </th>
			</tr>
			<tr>
				<th>Disciplina</th>
				<th>Nota</th>
			</tr>
			<?php
				$sql_disciplinas = mysql_query("SELECT id, nome FROM disciplinas WHERE id_turma=$turma_id");
								
				while($disciplina = mysql_fetch_array($sql_disciplinas))
				{
					$disciplina_nome = $disciplina['nome'];
					$disciplina_id = $disciplina['id'];
					
					$sql_notas = mysql_query("SELECT nota FROM notas WHERE id_usuario=$usuario_id AND id_disciplina=$disciplina_id");
					
					while($notas = mysql_fetch_array($sql_notas))
					{
						$nota = $notas['nota'];
					}
				
					if(isset($nota))
					{
						echo "
						<tr>
							<td>$disciplina_nome</td>
							<td>$nota</td>
						</tr>";
					}
					else
					{
						echo "
						<tr>
							<td>$disciplina_nome</td>
							<td>A avaliar</td>
						</tr>";
					}
				
				
				}
			?>
		</table>		
						
	<?php
				
			}					
		}
	?>

</section>