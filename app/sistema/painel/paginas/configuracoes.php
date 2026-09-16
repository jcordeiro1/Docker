<?php 
require_once("verificar.php");
require_once("../conexao.php");
$pag = 'configuracoes';
$data_atual = date('Y-m-d');

//verificar se ele tem a permissão de estar nessa página
if(@$configuracoes == 'ocultar'){
	echo "<script>window.location='../index.php'</script>";
	exit();
}
?>

<div class="row">

<form method="post" id="form-config" enctype="multipart/form-data">
	<div class="modal-body">

		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="exampleInputEmail1">Nome Barbearia</label>
					<input type="text" class="form-control" id="nome_sistema" name="nome_sistema" placeholder="Nome da Barbearia" value="<?php echo $nome_sistema ?>" required>    
				</div> 	
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label for="exampleInputEmail1">Email Barbearia</label>
					<input type="email" class="form-control" id="email_sistema" name="email_sistema" placeholder="Email" value="<?php echo $email_sistema ?>" required>    
				</div> 	
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label for="exampleInputEmail1">Whatsapp Barbearia</label>
					<input type="text" class="form-control" id="whatsapp_sistema" name="whatsapp_sistema" placeholder="Whatsapp" value="<?php echo $whatsapp_sistema ?>" required>    
				</div> 	
			</div>
		</div>


		<div class="row">

			<div class="col-md-3">
				<div class="form-group">
					<label for="exampleInputEmail1">Tel Fixo Barbearia</label>
					<input type="text" class="form-control" id="telefone_fixo_sistema" name="telefone_fixo_sistema" placeholder="Fixo" value="<?php echo $telefone_fixo_sistema ?>" required>    
				</div> 	
			</div>

			<div class="col-md-7">
				<div class="form-group">
					<label for="exampleInputEmail1">Endereço Barbearia</label>
					<input type="text" class="form-control" id="endereco_sistema" name="endereco_sistema" placeholder="Rua X Numero X Bairro Cidade" value="<?php echo $endereco_sistema ?>">    
				</div> 	
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Tipo Relatório</label>
					<select class="form-control" name="tipo_rel" id="tipo_rel">
						<option value="PDF" <?php if($tipo_rel == 'PDF'){?> selected <?php } ?> >PDF</option>
						<option value="HTML" <?php if($tipo_rel == 'HTML'){?> selected <?php } ?> >HTML</option>
					</select>   
				</div> 	
			</div>

		</div>


		<div class="row">

			<div class="col-md-6">
				<div class="form-group">
					<label for="exampleInputEmail1">Instagram</label>
					<input type="text" class="form-control" id="instagram_sistema" name="instagram_sistema" placeholder="Link do Perfil no Instagram" value="<?php echo $instagram_sistema ?>">   
				</div> 	
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="exampleInputEmail1">Mapa Site <small>(Url incorporada)</small></label>
					<input type="text" class="form-control" id="mapa" name="mapa" placeholder="" value='<?php echo $mapa ?>'>  
				</div> 	
			</div>

		</div>


		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="exampleInputEmail1">Texto Rodapé Site <small>(255) Caracteres</small></label>
					<input maxlength="255" type="text" class="form-control" id="texto_rodape" name="texto_rodape" placeholder="Texto para o Rodapé do site" value="<?php echo $texto_rodape ?>">   
				</div> 
			</div>
		</div>


		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="exampleInputEmail1">Texto Sobre (Site) <small>(600) Caracteres</small></label>
					<input maxlength="600" type="text" class="form-control" id="texto_sobre" name="texto_sobre" placeholder="Texto para a área Sobre a empresa no site" value="<?php echo $texto_sobre ?>">   
				</div> 
			</div>
		</div>


		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="exampleInputEmail1">Texto Cartão Fidelidade</label>
					<input maxlength="255" type="text" class="form-control" id="texto_fidelidade" name="texto_fidelidade" placeholder="Parabéns, você completou seus cartões, você ganhou ..." value="<?php echo @$texto_fidelidade ?>">   
				</div> 
			</div>
		</div>


		<div class="row">

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Cartões Troca</label>
					<input type="number" class="form-control" id="quantidade_cartoes" name="quantidade_cartoes" placeholder="Quantidade Cartões Troca" value="<?php echo $quantidade_cartoes ?>">   
				</div> 
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Texto Agendamento</label>
					<input maxlength="30" type="text" class="form-control" id="texto_agendamento" name="texto_agendamento" placeholder="Selecionar Cabelereira" value="<?php echo $texto_agendamento ?>">   
				</div> 
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Mensagem Api</label>
					<select class="form-control" name="msg_agendamento" id="msg_agendamento">
						<option value="Sim" <?php if($msg_agendamento == 'Sim'){?> selected <?php } ?> >Sim</option>
						<option value="Não" <?php if($msg_agendamento == 'Não'){?> selected <?php } ?> >Não</option>
						<option value="Api" <?php if($msg_agendamento == 'Api'){?> selected <?php } ?> >Api Paga</option>
					</select>      
				</div> 
			</div>


			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Seletor de Api</label>
					<select class="form-control" name="api" id="api">
						<option value="menuia" <?php if($api == 'menuia'){?> selected <?php } ?> >Menuia</option>
						<option value="outros" <?php if($api == 'outros'){?> selected <?php } ?> >Word Mensagens</option>
					</select>      
				</div> 
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Token (appkey)</label>
					<input type="text" class="form-control" id="token" name="token" placeholder="Token APP Key" value="<?php echo @$token ?>">   
				</div> 
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Instancia (authkey)</label>
					<input type="text" class="form-control" id="instancia" name="instancia" placeholder="Instância AuthKey" value="<?php echo @$instancia ?>">   
				</div> 
			</div>

		</div>


		<div class="row">

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Horas Confirmação</label>
					<input type="number" class="form-control" id="minutos_aviso" name="minutos_aviso" placeholder="Alerta Agendamento" value="<?php echo @$minutos_aviso ?>">   
				</div> 
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="exampleInputEmail1">CNPJ</label>
					<input type="text" class="form-control" id="cnpj_sistema" name="cnpj_sistema" value="<?php echo $cnpj_sistema ?>">    
				</div> 
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Cidade</label>
					<input type="text" class="form-control" id="cidade_sistema" name="cidade_sistema" value="<?php echo $cidade_sistema ?>" placeholder="Cidade para o contrato">    
				</div> 
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="exampleInputEmail1">Manter Agendamento Dias</label>
					<input type="number" class="form-control" id="agendamento_dias" name="agendamento_dias" value="<?php echo $agendamento_dias ?>" placeholder="Manter no Banco de Dados">    
				</div> 
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Itens Paginação</label>
					<input type="number" class="form-control" id="itens_pag" name="itens_pag" value="<?php echo $itens_pag ?>" placeholder="">    
				</div> 
			</div>

		</div>



		<div class="row">

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Taxa Pgto Serviço</label>
					<select class="form-control" name="taxa_sistema" id="taxa_sistema">
						<option value="Cliente" <?php if(@$taxa_sistema == 'Cliente'){?> selected <?php } ?> >Cliente Paga</option>
						<option value="Empresa" <?php if(@$taxa_sistema == 'Empresa'){?> selected <?php } ?> >Salão Paga</option>
					</select>      
				</div> 
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Tipo Comissão</label>
					<select class="form-control" name="tipo_comissao" id="tipo_comissao">
						<option value="Porcentagem" <?php if($tipo_comissao == 'Porcentagem'){?> selected <?php } ?> >Porcentagem</option>
						<option value="R$" <?php if($tipo_comissao == 'R$'){?> selected <?php } ?> >R$ Reais</option>
					</select>   
				</div> 	
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Lançamento Comissão </label>
					<select class="form-control" name="lanc_comissao" id="lanc_comissao">
						<option value="Sempre" <?php if($lanc_comissao == 'Sempre'){?> selected <?php } ?> >Serviço Pendente e Pago</option>
						<option value="Pago" <?php if($lanc_comissao == 'Pago'){?> selected <?php } ?> >Serviço Pago</option>
					</select>   
				</div> 	
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">% Agendamento</label>
					<input type="number" class="form-control" id="porc_servico" name="porc_servico" placeholder="% pagar Agendamento" value="<?php echo $porc_servico ?>">   
				</div> 
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Api Pgto</label>
					<select class="form-control" name="pgto_api" id="pgto_api">
						<option value="Sim" <?php if($pgto_api == 'Sim'){?> selected <?php } ?> >Sim</option>
						<option value="Não" <?php if($pgto_api == 'Não'){?> selected <?php } ?> >Não</option>
					</select>      
				</div> 
			</div>

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Entrada Site</label>
					<select class="form-control" name="entrada" id="entrada">
						<option value="Site" <?php if($entrada == 'Site'){?> selected <?php } ?> >Site</option>
						<option value="Agendamento" <?php if($entrada == 'Agendamento'){?> selected <?php } ?> >Agendamento</option>
						<option value="Login" <?php if($entrada == 'Login'){?> selected <?php } ?> >Login</option>
					</select>      
				</div> 
			</div>

		</div>



		<div class="row">

			<div class="col-md-2">
				<div class="form-group">
					<label for="exampleInputEmail1">Api Pagamento</label>
					<select class="form-control" name="api_pagamento" id="api_pagamento">
						<option value="Mercado Pago" <?php if($api_pagamento == 'Mercado Pago'){?> selected <?php } ?> >Mercado Pago</option>
						<option value="Asaas" <?php if($api_pagamento == 'Asaas'){?> selected <?php } ?> >Asaas</option>								
					</select>      
				</div> 
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="exampleInputEmail1">Public Key (Mercado Pago)</label>
					<input type="text" class="form-control" id="public_key_mp" name="public_key_mp" placeholder="Chave Public Key do Mercado Pago" value="<?php echo $public_key_mp ?>">   
				</div> 
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="exampleInputEmail1">Access Token (Mercado Pago)</label>
					<input type="text" class="form-control" id="access_token_mp" name="access_token_mp" placeholder="Chave Access Token do Mercado Pago" value="<?php echo $access_token_mp ?>">   
				</div> 
			</div>

			<div class="col-md-4">
				<div class="form-group">
					<label for="exampleInputEmail1">Chave Token do Asaas</label>
					<input type="text" class="form-control" id="asaas" name="asaas" placeholder="Chave Token Asaas" value="<?php echo $asaas ?>">  
				</div> 
			</div>

		</div>

		<hr>

		<div class="row">

			<div class="col-md-3">
				<div class="form-group">
					<label for="exampleInputEmail1">Nome (Bot)</label>
					<input type="text" class="form-control" id="nome_bot" name="nome_bot" placeholder="Nome do Bot" value="<?php echo @$nome_bot ?>">
				</div>
			</div>

			<div class="col-md-3">
				<div class="form-group">
					<label for="exampleInputEmail1">BarberBot Ativo</label>
					<select class="form-control" name="barberbot_ativo" id="barberbot_ativo">
						<option value="Sim" <?php if(@$barberbot_ativo == 'Sim'){?> selected <?php } ?> >Sim</option>
						<option value="Não" <?php if(@$barberbot_ativo == 'Não'){?> selected <?php } ?> >Não</option>
					</select>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="exampleInputEmail1">OpenAI Key</label>
					<input type="text" class="form-control" id="openai_key" name="openai_key" placeholder="sk-..." value="<?php echo @$openai_key ?>">
				</div>
			</div>

		</div>


		<div class="row">

			<div class="col-md-6">
				<div class="form-group">
					<label for="exampleInputEmail1">BarberBot Boas-vindas <small>(600 Caracteres)</small></label>
					<textarea maxlength="600" class="form-control" id="barberbot_boas_vindas" name="barberbot_boas_vindas" rows="4" placeholder="Mensagem de boas-vindas"><?php echo @$barberbot_boas_vindas ?></textarea>
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="exampleInputEmail1">OpenAI Prompt <small>(2000 Caracteres)</small></label>
					<textarea maxlength="2000" class="form-control" id="openai_prompt" name="openai_prompt" rows="4" placeholder="Prompt do sistema"><?php echo @$openai_prompt ?></textarea>
				</div>
			</div>

		</div>

		<hr>


		<div class="row">

			<div class="col-md-4">						
				<div class="form-group"> 
					<label>Logo (*PNG)</label> 
					<input class="form-control" type="file" name="foto-logo" onChange="carregarImgLogo();" id="foto-logo">
				</div>						
			</div>
			<div class="col-md-2">
				<div id="divImg">
					<img src="../img/<?php echo $logo_sistema ?>" width="80px" id="target-logo">									
				</div>
			</div>


			<div class="col-md-4">						
				<div class="form-group"> 
					<label>Ícone (*Png)</label> 
					<input class="form-control" type="file" name="foto-icone" onChange="carregarImgIcone();" id="foto-icone">
				</div>						
			</div>
			<div class="col-md-2">
				<div id="divImg">
					<img src="../img/<?php echo $icone_sistema ?>" width="50px" id="target-icone">									
				</div>
			</div>

		</div>



		<div class="row">

			<div class="col-md-4">						
				<div class="form-group"> 
					<label>Logo Relatório (*Jpg)</label> 
					<input class="form-control" type="file" name="foto-logo-rel" onChange="carregarImgLogoRel();" id="foto-logo-rel">
				</div>						
			</div>
			<div class="col-md-2">
				<div id="divImg">
					<img src="../img/<?php echo $logo_rel ?>" width="80px" id="target-logo-rel">									
				</div>
			</div>


			<div class="col-md-4">						
				<div class="form-group"> 
					<label>Ícone Site (*png)</label> 
					<input class="form-control" type="file" name="foto-icone-site" onChange="carregarImgIconeSite();" id="foto-icone-site">
				</div>						
			</div>
			<div class="col-md-2">
				<div id="divImg">
					<img src="../../images/<?php echo $icone_site ?>" width="50px" id="target-icone-site">							
				</div>
			</div>

		</div>


		<div class="row">
			<div class="col-md-3">
				<label>Pgto Agendamento</label>	
				<select class="form-control" name="opcao_pagar">
					<option value="Sim" <?php if($opcao_pagar == 'Sim'){ echo 'selected'; } ?>>Sim</option>
					<option value="Não" <?php if($opcao_pagar == 'Não'){ echo 'selected'; } ?>>Não</option>
				</select>
			</div>
		</div>



		<div class="row">

			<div class="col-md-4">						
				<div class="form-group"> 
					<label>Favicon 192 x 192 (*png)</label> 
					<input class="form-control" type="file" name="foto-favicon192" onChange="carregarImgFavicon192();" id="foto-favicon192">
				</div>						
			</div>
			<div class="col-md-2">
				<div id="divImg">
					<img src="../../images/favicon_192.png" width="80px" id="target-favicon192">							
				</div>
			</div>


			<div class="col-md-4">						
				<div class="form-group"> 
					<label>Favicon 512 x 512 (*png)</label> 
					<input class="form-control" type="file" name="foto-favicon512" onChange="carregarImgFavicon512();" id="foto-favicon512">
				</div>						
			</div>
			<div class="col-md-2">
				<div id="divImg">
					<img src="../../images/favicon_512.png" width="80px" id="target-favicon512">							
				</div>
			</div>

		</div>


		<hr>



		<div class="row">

			<div class="col-md-4">						
				<div class="form-group"> 
					<label>Imagem Área Sobre (Site)</label> 
					<input class="form-control" type="file" name="foto-sobre" onChange="carregarImgSobre();" id="foto-sobre">
				</div>						
			</div>
			<div class="col-md-2">
				<div id="divImg">
					<img src="../../images/<?php echo $imagem_sobre ?>" width="80px" id="target-sobre">							
				</div>
			</div>


			<div class="col-md-4">						
				<div class="form-group"> 
					<label>Imagem Banner Index <small>(1500x1000)</small></label> 
					<input class="form-control" type="file" name="foto-banner-index" onChange="carregarImgBannerIndex();" id="foto-banner-index">
				</div>						
			</div>
			<div class="col-md-2">
				<div id="divImg">
					<img src="../../images/<?php echo $img_banner_index ?>" width="80px" id="target-banner-index">							
				</div>
			</div>

		</div>


		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label>Fundo Login <small>(Imagem) 1920 x 1080</small></label>
					<input class="form-control" type="file" name="fundo_login" onChange="carregarImgFundo();" id="fundo_login">
				</div>
			</div>
			<div class="col-md-2">
				<div id="divImg">
					<img src="../img/<?php echo $fundo_login ?>" width="80px" id="target-fundo">
					<a title="Excluir Imagem" href="#" onclick="excluirImg('Fundo')"><i class="fa fa-close text-danger"></i></a>
				</div>
			</div>
		</div>



		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="exampleInputEmail1">Url do Vídeo Index</label>
					<input type="text" class="form-control" id="url_video" name="url_video" value="<?php echo $url_video ?>" placeholder="Url do Youtube Incorporada">    
				</div> 
			</div>	

			<div class="col-md-4">
				<div class="form-group">
					<label for="exampleInputEmail1">Posição do Vídeo</label>
					<select class="form-control" name="posicao_video" id="posicao_video">
						<option value="sobre" <?php if($posicao_video == 'sobre'){?> selected <?php } ?> >Encima da Imagem Sobre</option>
						<option value="abaixo" <?php if($posicao_video == 'abaixo'){?> selected <?php } ?> >Abaixo da Área Sobre</option>
					</select>      
				</div> 
			</div>
		</div>

		<br>
		<small><div id="mensagem-config" align="center"></div></small>
	</div>

	<div class="modal-footer">      
		<button type="submit" class="btn btn-primary">Salvar Dados</button>
	</div>

</form>	

</div>



<script type="text/javascript">
	function carregarImgFavicon512() {
		var target = document.getElementById('target-favicon512');
		var file = document.querySelector("#foto-favicon512").files[0];

		var reader = new FileReader();

		reader.onloadend = function () {
			target.src = reader.result;
		};

		if (file) {
			reader.readAsDataURL(file);
		} else {
			target.src = "";
		}
	}
</script>



<script type="text/javascript">
	function carregarImgFavicon192() {
		var target = document.getElementById('target-favicon192');
		var file = document.querySelector("#foto-favicon192").files[0];

		var reader = new FileReader();

		reader.onloadend = function () {
			target.src = reader.result;
		};

		if (file) {
			reader.readAsDataURL(file);
		} else {
			target.src = "";
		}
	}
</script>



<script type="text/javascript">
	function excluirImg(p){

		const swalWithBootstrapButtons = Swal.mixin({
			customClass: {
				confirmButton: "btn btn-success",
				cancelButton: "btn btn-danger me-1",
				container: 'swal-whatsapp-container'
			},
			buttonsStyling: false
		});

		swalWithBootstrapButtons.fire({
			title: "Deseja Excluir?",
			text: "Você não conseguirá recuperá-lo novamente!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Sim, Excluir!",
			cancelButtonText: "Não, Cancelar!",
			reverseButtons: true
		}).then((result) => {

			if (result.isConfirmed) {

				$.ajax({
					url: 'excluir_imagens.php',
					method: 'POST',
					data: { p },
					dataType: "html",
					success: function (mensagem) {

						if (mensagem.trim() == "Excluído com Sucesso") {

							swalWithBootstrapButtons.fire({
								title: mensagem,
								text: 'Fecharei em 1 segundo.',
								icon: "success",
								timer: 1000,
								timerProgressBar: true,
								confirmButtonText: 'OK',
								customClass: {
									container: 'swal-whatsapp-container'
								}
							});

							location.reload();

						} else {

							swalWithBootstrapButtons.fire({
								title: "Opss!",
								text: mensagem,
								icon: "error",
								confirmButtonText: 'OK',
								customClass: {
									container: 'swal-whatsapp-container'
								}
							});

						}
					}
				});

			} else if (result.dismiss === Swal.DismissReason.cancel) {

				swalWithBootstrapButtons.fire({
					title: "Cancelado",
					text: "Fecharei em 1 segundo.",
					icon: "error",
					timer: 1000,
					timerProgressBar: true,
				});

			}
		});
	}
</script>



<script type="text/javascript">
	function carregarImgFundo() {
		var target = document.getElementById('target-fundo');
		var file = document.querySelector("#fundo_login").files[0];

		var reader = new FileReader();

		reader.onloadend = function() {
			target.src = reader.result;
		};

		if (file) {
			reader.readAsDataURL(file);
		} else {
			target.src = "";
		}
	}
</script>



<script type="text/javascript">
	$("#form-config").submit(function (e) {
		e.preventDefault();

		var formData = new FormData(this);

		$.ajax({
			url: "../editar-config.php",
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			success: function (mensagem) {
				$("#mensagem-config").removeClass();

				if (mensagem.trim() == "Editado com Sucesso") {
					$("#mensagem-config").addClass("text-success");
				} else {
					$("#mensagem-config").addClass("text-danger");
				}

				$("#mensagem-config").text(mensagem);
			}
		});
	});
</script>
