<form action="php/cadastrar.php?cadastro=usuario" method="post" name="cadastroUsuarios" id="cadastro-usuarios">
	<h2>Cadastro de Alunos</h2>
	
	<select class="combo" name="usuarios">
		<option selected="selected" disabled="disabled" value="">Alunos</option>
		<option value="cadastrar">===========| Cadastrar |===========</option>
		<?php
			include_once('php/conexao.php');			
			
			$sql_usuario = mysql_query('SELECT usuarios.nome, usuarios.id, usuarios_turmas.id_turma FROM usuarios, usuarios_turmas WHERE usuarios.id=usuarios_turmas.id_usuario ORDER BY nome');
			
			while($usuario = mysql_fetch_array($sql_usuario))
			{
				$turma_id = $usuario['id_turma'];
				$usuario_id = $usuario['id'];
				$usuario_nome = $usuario['nome'];
				
				echo '<option value="' . $turma_id . '" data-idUsuario="' . $usuario_id . '">' . $usuario_nome . '</option>';
			}			
		?>
	</select>
	
    <select class="combo" name="cursos">
    	<option selected="selected" disabled="disabled" value="">Cursos</option>
    	<?php
    		$sql_instituicoes = mysql_query('SELECT nome, id FROM instituicoes ORDER BY nome');
    		
    		while($instituicao = mysql_fetch_array($sql_instituicoes))
    		{
    			$instituicao_id = $instituicao['id'];
    			$instituicao_nome = $instituicao['nome'];
    			
    			echo '<optgroup label="' . $instituicao_nome . '">';
    		
				$sql_cursos = mysql_query("SELECT nome, id FROM cursos WHERE id_instituicao=$instituicao_id ORDER BY nome");
				
				while($curso = mysql_fetch_array($sql_cursos))
				{
					$curso_id = $curso['id'];
					$curso_nome = $curso['nome'];
					
					echo '<option value="' . $curso_id . '">' . $curso_nome . '</option>';
				}
    			echo '</optgroup>';
    		}
    	?>
    </select> 
    
    <input type="hidden" name="turmaCadastrada">
    
    <select class="combo" name="turmas">
    	<option selected="selected" disabled="disabled" value="">Turmas</option>
    </select>    

	<input type="submit" class="bt" value="Cadastrar">
	<a href="" class="bt">Cancelar</a>
</form> 	


<form action="php/cadastrar.php?cadastro=instituicao" method="post" name="cadastroInstituicoes" id="cadastro-instituicoes">
    <h2>Cadastro de Escolas e Cursos</h2>
    
    <select class="combo" name="instituicoes">
    	<option selected="selected" disabled="disabled" value="">Escola / Instituição</option>    	
    	<option>===========| Cadastrar |===========</option>
    	
    		<?php				
				$sql = mysql_query('SELECT id, nome FROM instituicoes ORDER BY nome');
				
				while($campo = mysql_fetch_array($sql))
				{
    				$id = $campo['id'];
    				$nome = $campo['nome'];
    				
    				echo '<option value="' . $id . '">' . $nome . '</option>';
    			}
    		?>
    	
    </select>
    
    <select class="combo" name="cursos">
    	<option selected="selected" disabled="disabled" value="">Curso</option>
    </select>
    
    <select class="combo" name="turmas">
    	<option selected="selected" disabled="disabled" value="">Turma</option>
    </select>
    
    <select class="combo" name="disciplinas">
    	<option selected="selected" disabled="disabled" value="">Disciplina</option>
    </select>
    
    <input type="submit" class="bt" value="Cadastrar">
    <a href="" class="bt">Cancelar</a>
</form>