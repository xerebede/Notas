<h2>Publicar Notas</h2>


<form action="php/publicar.php" method="post" name="publicarNotas">
	<select name="instituicoes" class="combo">
		<option selected="selected" disabled="disabled">===========| instituicoes |===========</option>
		
		<?php
			include('php/conexao.php');
			
			$sql_inst = mysql_query('SELECT * FROM instituicoes');
			
			while($campo = mysql_fetch_array($sql_inst))
			{
				$instituicao = $campo['nome'];
				$id = $campo['id'];
				
				echo '<option value="' . $id . '">' . $instituicao .'</option>';            	
			}
		?>            
	</select>
	
	<select name="cursos" class="combo">
   		
	</select>
	
	<select name="turmas" class="combo">
		 
	</select>
	
	<select name="disciplinas" class="combo">
		 
	</select>
	
	<select name="usuarios" class="combo">
			   
	</select>
	
	<input type="text" name="nota" class="campo" placeholder="Nota">
	
	<input type="submit" class="bt" value="Cadastrar">
    <a href="" class="bt">Cancelar</a>
</form>