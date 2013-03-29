window.onload = function()
{
	// Resgata a URL da pagina
	var url = window.location.toString();
	var pagina = url.substring(url.indexOf("=") + 1);
	
	
	// Verifica se está na pagina de login
	if(pagina == 'login' || pagina == url)
	{
		// Valida o login
		document.formLogin.onsubmit = function()
		{
			// Resgata os campos do formulario de login						
			var usuario = document.formLogin.usuario;
			var senha = document.formLogin.senha;
			
			// Valida o nome de usuario
			if(usuario.value == '')
			{
				notyAlert("Informe seu nome de usario", "error");
				usuario.focus();
				return false;
			}
			// Valida a senha
			else if (senha.value == '')
			{
				notyAlert("Informe sua senha", "error");
				senha.focus();
				return false;
			}
		}
	}
	
	// Verifica se está na pagina de Aministração
	if(pagina == 'admin' || pagina == 'cadastro')
	{		
		/*========================================
		------------------------------------------
		============ CADASTRO USUARIOS ===========
		------------------------------------------
		=========================================*/		
		$('form[name=cadastroUsuarios] select').change(function()		
		{				
			var options = '';
			var optgroup = '';
			var idCadastrado = new Array();
			
			
			// Gera campos de texto para novo aluno				
			if(/Cadastrar/.test($(this).children('option:selected').text()))
			{
				var novo = '<input type="text" class="campo" name="nome" placeholder="Nome completo">';
					novo +='<input type="text" class="campo" name="usuario" placeholder="Usuario">';
					novo +='<input type="password" class="campo" name="senha" placeholder="Senha">';
					novo +='<input type="text" class="campo" name="email" placeholder="E-mail">';
				
				$(this).after(novo)
					   .remove();					
			}		
			
			// Adiciona a turmas um optgroup de turmas em que o aluno selecionado ja é cadastrado
			if($(this).attr('name') == 'usuarios' && !isNaN($(this).val()))
			{				
				$.getJSON('php/funcao.ajax.php', {id: $(this).val(), tabela: 'turmas'}, function(data)
				{					
					for(var i = 0; i < data.length; i++)
					{
						options += '<option value="' + data[i].id + '" disabled="disabled">' + data[i].valor + '</option>';
						
						idCadastrado[i] = data[i].id;										
					}

					$('input[name=turmaCadastrada]').attr('value', idCadastrado);
					
					optgroup += '<optgroup label="Cadastrado">' + options + '</optgroup>';					
					
					$('select[name=turmas]').children('option:selected').siblings('optgroup[label=Cadastrado]').remove();
					$('select[name=turmas]').children('option:selected').after(optgroup);
				});
			}
			
			// Adiciona a turmas um optgroup com turmas que o aluno selecionado não é cadastrado
			// de acordo com o curso selecionado
			if($(this).attr('name') == 'cursos')
			{				
				/* -= Verifica se o select de usuarios existe =- */
				// Se existir seleciona apenas as turmas em que o usuario selecionado não é cadastrado
				// De acor do o curso selecionada
				// Obs.: Usuario existente
				if($('select[name=usuarios]')[0])
				{
					$.getJSON('php/funcao.ajax.php', {funcao: 'notin', idCadastrado: $('input[name=turmaCadastrada]').val(), id: $(this).val(), tabela: 'turmas'}, function(data)
					{
						for(var i = 0; i < data.length; i++)
						{
							options += '<option value="' + data[i].id + '">' + data[i].valor + '</option>';
						}
						
						optgroup += '<optgroup label="Não cadastrado">' + options + '</optgroup>';
						
						$('select[name=turmas]').children('option:selected').after(optgroup);
					});
					
					$(this).nextAll('select[name=turmas]').children('optgroup[label!=Cadastrado]').remove();
					$('select[name=turmas]').children('option[disabled]').after(options);
				}
				// Se não existir seleciona todas as turmas de acordo com curso selecionado
				// Obs.: Usuario novo
				else
				{
					$.getJSON('php/funcao.ajax.php', {funcao: 'estrangeira', id: $(this).val(), tabela: 'turmas'}, function(data)
					{
						for(var i = 0; i < data.length; i++)
						{
							options += '<option value="' + data[i].id + '">' + data[i].valor + '</option>';
						}
						
						optgroup += '<optgroup label="Turmas disponiveis">' + options + '</optgroup>';				
						$('select[name=turmas]').children('option:selected').after(optgroup);

					});
					
					$(this).nextAll('select[name=turmas]').children('optgroup').remove();
					$('select[name=turmas]').children('option[disabled]').after(options);
				}
			}
			
			if($('form[name=cadastroUsuarios]').children('input[type=text]').length == 0)
			{
				var idUsuario = $('select[name=usuarios]').children('option:selected').attr('value',  $('select[name=usuarios]').children('option:selected').attr('data-idUsuario'));				
			}	
		});		
		
		// Valida o cadastro de usuarios ja cadastrados
		$('form[name=cadastroUsuarios]').submit(function()
		{
			if($('form[name=cadastroUsuarios]').children('input[type=text]').length == 0)
			{
				if($('select[name=usuarios]').length > 0 && !/[0-9]+/.test($('select[name=usuarios]').val()))
				{
					notyAlert('Selecione ou cadastre um usuario', 'error');
					return false;
				}
				else if(!/[0-9]+/.test($('select[name=cursos]').val()))
				{
					notyAlert('Selecione um curso para cadastrar o usuario', 'error');
					return false;
				}
				else if(!/[0-9]+/.test($('select[name=turmas]').val()))
				{
					notyAlert('Selecione uma turma para cadastrar o usuario', 'error');
					return false;
				}
			}
		});
		
		// Valida o cadastro de novos usuarios.
		document.cadastroUsuarios.onsubmit = function()
		{
			var nome = document.cadastroUsuarios.nome;
			var usuario = document.cadastroUsuarios.usuario;
			var senha = document.cadastroUsuarios.senha;
			var email = document.cadastroUsuarios.email;
			var curso = document.getElementsByName('cursos')[0];
			var turma = document.getElementsByName('turmas')[0];
			
			
			if(!/[a-zAZ]+\s[a-zA-Z]+\s[a-zA-Z]+/.test(nome.value))
			{
				notyAlert('Preencha o campo com nome completo', 'error');
				nome.focus();
				return false;
			}
			else if(usuario.value == '')
			{
				notyAlert('Informe um nome de usuario para conta', 'error');
				usuario.focus();
				return false;
			}
			else if(senha.value == '')
			{
				notyAlert('Informe uma senha para conta', 'error');
				senha.focus();
				return false;
			}
			else if(senha.value.length < 8)
			{
				notyAlert('A senha deve conter no minimo 8 digitos', 'error');
				senha.focus();
				return false;
			}
			else if(email.value == '')
			{
				notyAlert('Informe um email para conta', 'error');
				email.focus();
				return false;
			}
			else if(!/[a-zA-Z]+[\.-_]*[0-9]*@[a-zA-Z]+\.[a-zA-Z]{2,4}/.test(email.value))
			{
				notyAlert("Informe um endereço de email válido", 'error');
				email.focus();
				return false;
			}
			else if(curso.value == '')
			{
				notyAlert('Selecione um curso para cadastrar o usuario', 'error');
				return false;
			}
			else if(turma.value == '')
			{
				notyAlert('Selecione uma turma para cadastrar o usuario', 'error');
				return false;
			}
		}
		
		/*========================================
		------------------------------------------
		========= CADASTRO INSTITUICOES ==========
		------------------------------------------
		=========================================*/
		// Select dinamico: cadastro de cursos e instituicoes
		$('form[name=cadastroInstituicoes] select').nextAll('.combo').removeClass('combo').addClass('invisivel');
		// ↓↓
		$('form[name=cadastroInstituicoes] select').change(function()
		{
			var atual = $(this).attr('name');
			var tabela = $(this).next().attr('name');
			var options = "<option>===========| Cadastrar |===========</option>";
			
			$(this).nextAll('select').children(':not(:disabled)').remove();			
						
			if(/Cadastrar/.test($(this).children(':selected').text()))
			{
				$(this).removeClass('combo').addClass('invisivel')				
					   .after('<input type="text" class="campo" name="' + $(this).attr('name') + '" placeholder="Cadastrar ' + $(this).attr('name') +'">');
			}
			
			$(this).next('.invisivel').removeClass('invisivel').addClass('combo');
			
			
			$.getJSON('php/funcao.ajax.php', {funcao: 'estrangeira', id: $(this).val(), tabela: tabela }, function(data) 
			{				
				if(data != null)
				{	
					for(var i = 0; i < data.length; i++)
					{
						options += '<option value="' + data[i].id + '">' + data[i].valor + '</option>';
					}
					
					$('select[name=' + atual + ']').next().children('option').after(options);
				}
				else
				{
					$('select[name=' + atual + ']').next().children('option').after(options);
				}
			})
			.error(function()
			{
				$('select[name=' + atual + ']').next().children('option').after(options);
			});
			
		});
		
		
		// Validação do cadastro de instituicoes
		$('form[name=cadastroInstituicoes]').submit(function()
		{												
			if($(this).children('.combo').last().val() == '' || $(this).children('.campo').last().val() == '')
			{						
				notyAlert('Preencha todos os campos', 'error');
				
				return false;				
			}
			else if(/[0-9]+/.test($(this).children('select[name=disciplinas]').val()))
			{
				notyAlert('Cadastre um campo novo', 'error');
				notyAlert('Todos os campos já estão cadastrados');
				return false;
			}
		});	
		
		//Cancelar cadastro de instituicoes
		$('form[name=cadastroInstituicoes] a').click(function() 
		{
			location.refresh();
			return false;
		});	
	}
	
	
	/*========================================
	------------------------------------------
	============= PUBLICAR NOTAS =============
	------------------------------------------
	=========================================*/
	if(pagina == 'publicar')
	{
		$('form[name=publicarNotas] select').nextAll('.combo').removeClass('combo').addClass('invisivel');
		$('form[name=publicarNotas] select').nextAll('.campo').removeClass('campo').addClass('invisivel');
				
		$('select').focus(function()
			{
				while($(this).next().hasClass('combo'))
				{
					$(this).nextAll('select').removeClass('combo').addClass('invisivel');
				}
				
				$('input[type=text]').last().removeClass('campo').addClass('invisivel');
				
			});
			
			$('select').change(function()
			{						
				var atual = $(this).attr('name');
				var tabela = $(this).next().attr('name');
								
				
				if(tabela != 'nota' && tabela != 'usuarios')
				{
					$.getJSON('php/funcao.ajax.php', {funcao: 'estrangeira', id: $(this).val(), tabela: tabela }, function(data)
					{
						if(data != null)
						{	
							var options = '<option selected="selected" disabled="disabled">===========| ' + tabela + ' |===========</option>'; 
							
							for(var i = 0; i < data.length; i++)
							{
								options += '<option value="' + data[i].id + '">' + data[i].valor + '</option>';
							}
							
							$('select[name=' + atual + ']').next().removeClass('invisivel').addClass('combo').html(options);
						}						
						else
						{
							var campo1 = $('select[name=' + atual + ']').next().attr('name');							
							var campo2 = $('select[name=' + atual + ']').attr('name');
							var artigo = 'o';
														
							if(campo2 == 'instituicoes')
							{
								campo2 = 'instituicao';
								artigo = 'a';
							}
							else if(campo2.substring(campo2.length - 2, campo2.length) == 'os')
							{
								 campo2 = campo2.replace(/os/, "o");
								 artigo = 'o';
							}
							else if(campo2.substring(campo2.length - 2, campo2.length) == 'as')
							{
								campo2 = campo2.replace(/as/, "a");
								artigo = 'a';
							}								
							
							notyAlert('Não há ' + campo1 + ' para ' + artigo + ' ' + campo2 + ' selecionad' + artigo, 'error');
						}					
					});
				}
				else if(tabela == 'usuarios')
				{
					$.getJSON('php/funcao.ajax.php', {funcao: 'mpm', id: $('select[name=turmas]').val(), tabela: tabela }, function(data)
					{
						if(data != null)
						{	
							var options = '<option selected="selected" disabled="disabled">===========| ' + tabela + ' |===========</option>'; 
							
							for(var i = 0; i < data.length; i++)
							{
								options += '<option value="' + data[i].id + '">' + data[i].valor + '</option>';
							}
							
							$('select[name=' + atual + ']').next().removeClass('invisivel').addClass('combo').html(options);
						}						
						else
						{
							var campo1 = $('select[name=' + atual + ']').next().attr('name');							
							var campo2 = $('select[name=' + atual + ']').attr('name');
							var artigo = 'o';
														
							if(campo2 == 'instituicoes')
							{
								campo2 = 'instituicao';
								artigo = 'a';
							}
							else if(campo2.substring(campo2.length - 2, campo2.length) == 'os')
							{
								 campo2 = campo2.replace(/os/, "o");
								 artigo = 'o';
							}
							else if(campo2.substring(campo2.length - 2, campo2.length) == 'as')
							{
								campo2 = campo2.replace(/as/, "a");
								artigo = 'a';
							}								
							
							notyAlert('Não há ' + campo1 + ' para ' + artigo + ' ' + campo2 + ' selecionad' + artigo, 'error');
						}					
					});
				}
				else
				{
					$('input[type=text]').removeClass('invisivel').addClass('combo');
				}
			});
	}
}


