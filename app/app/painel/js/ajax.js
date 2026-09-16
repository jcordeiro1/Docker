$("#form").submit(function (event) {
	event.preventDefault();
	var formData = new FormData(this);
	$('#btn_salvar').hide();
	$('#btn_carregando').show();
	$.ajax({
		url: '../../sistema/painel/paginas/' + pag + "/salvar.php",
		type: 'POST',
		data: formData,
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Salvo com Sucesso") {
				$('#btn-fechar').click();
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				setTimeout(function () {
					location.reload();
				}, 1000);
			} else {
				toast(mensagem, 'vermelha'); // Dispara a notificação de erro
			}
			$('#btn_carregando').hide();
			$('#btn_salvar').show();
		},
		cache: false,
		contentType: false,
		processData: false,
	});
});

  $("#form-status").submit(function (event) {
    event.preventDefault();

    $('#btn_salvar_status').hide();
    $('#btn_carregando_status').show();

    var formData = new FormData(this);

    $.ajax({
      url: '../../sistema/painel/paginas/' + pag + "/mudar-status.php",
      type: 'POST',
      data: formData,
      success: function (mensagem) {
        $('#toast-message').text(mensagem.trim());
        if (mensagem.trim() == "Salvo com Sucesso") {
          $('#btn-fechar').click();
          $('#not_salvar').click();
          setTimeout(function () {
            location.reload();
          }, 1000);
        } else {
          toast(mensagem, 'vermelha');
        }
      },
      error: function () {
        toast('Erro ao processar requisição', 'vermelha');
      },
      complete: function () {
        $('#btn_carregando_status').hide();
        $('#btn_salvar_status').show();
      },
      cache: false,
      contentType: false,
      processData: false
    });
  });



$("#form2").submit(function (event) {
	event.preventDefault();
	var formData = new FormData(this);
	$('#btn_salvar').hide();
	$('#btn_carregando').show();
	$.ajax({
		url: '../../sistema/painel/paginas/' + pag + "/inserir.php",
		type: 'POST',
		data: formData,
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Salvo com Sucesso") {
				$('#btn-fechar').click();
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				setTimeout(function () {
					location.reload();
				}, 1000);
			} else {
				toast(mensagem, 'vermelha'); // Dispara a notificação de erro
			}
			$('#btn_carregando').hide();
			$('#btn_salvar').show();
		},
		cache: false,
		contentType: false,
		processData: false,
	});
});


$("#form-anot").submit(function (event) {
	event.preventDefault();
	var formData = new FormData(this);

	// Atualiza o textarea oculto com o conteúdo do editor antes de enviar
	var content = quill.root.innerHTML;
	document.getElementById('hiddenTextarea').value = content;

	$('#btn_salvar').hide();
	$('#btn_carregando').show();
	$.ajax({
		url: '../../sistema/painel/paginas/' + pag + "/salvar.php",
		type: 'POST',
		data: formData,
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Salvo com Sucesso") {
				$('#btn-fechar').click();
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				setTimeout(function () {
					location.reload();
				}, 1000);
			} else {
				toast(mensagem, 'vermelha'); // Dispara a notificação de erro
			}
			$('#btn_carregando').hide();
			$('#btn_salvar').show();
		},
		cache: false,
		contentType: false,
		processData: false,
	});
});



//####### MUDAR STSTUS ##########################
$("#form_status").submit(function (event) {
	event.preventDefault();
	var formData = new FormData(this);
	$('#btn_salvar_status').hide();
	$.ajax({
		url: '../../sistema/painel/paginas/' + pag + "/mudar-status.php",
		type: 'POST',
		data: formData,
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Salvo com Sucesso") {
				//$('#btn-fechar').click();
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				location.reload();
			} else {
				toast(mensagem, 'vermelha');
			}
			$('#btn_salvar_status').show();
		},
		cache: false,
		contentType: false,
		processData: false,
	});
});







function limparFiltro() {
	$('#listar li').show();
}
function filtrar() {
	var termo = $('#buscar').val().toUpperCase();
	$('#listar li').each(function () {
		if ($(this).html().toUpperCase().indexOf(termo) === -1) {
			$(this).hide();
		}
	});
}


//####### ATIVAR ##########################
function ativar(id, acao) {
	$.ajax({
		url: '../../sistema/painel/paginas/' + pag + "/mudar-status.php",
		method: 'POST',
		data: { id, acao },
		dataType: "html",
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Alterado com Sucesso") {
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				setTimeout(function () {
					location.reload();
				}, 1000); // 2000 milissegundos = 2 segundos 
			} else {
				toast(mensagem, 'vermelha');
			}
		}
	});
}



//####### ARQUIVOS ##########################
function arquivo(id, nome) {
	const botao = document.getElementById('btn_arquivos');
	$('#id-arquivo').val(id);
	$('#titulo_arquivo').text(nome);
	botao.click();
	$('#arquivo_conta').val('');
	listarArquivos();
}


$("#form_arquivos").submit(function (event) {
	event.preventDefault();
	var formData = new FormData(this);
	$.ajax({
		url: "../../sistema/painel/paginas/" + pag + "/arquivos.php",
		type: "POST",
		data: formData,
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Inserido com Sucesso") {
				//$('#btn-fechar-arquivos').click();
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				$("#nome-arq").val("");
				$("#arquivo_conta").val("");
				$("#target-arquivos").attr("src", "images/arquivos/sem-foto.png");
				listarArquivos();
			} else {
				toast(mensagem, "vermelha");
			}
		},
		cache: false,
		contentType: false,
		processData: false,
	});
});



function listarArquivos() {
	var id = $('#id-arquivo').val();
	$.ajax({
		url: 'paginas/' + pag + "/listar-arquivos.php",
		method: 'POST',
		data: { id },
		dataType: "text",
		success: function (result) {
			//alert(result)
			$("#listar-arquivos").html(result);
		}
	});
}


function excluirArquivo(id, nome) {
	$.ajax({
		url: '../../sistema/painel/paginas/' + pag + "/excluir-arquivo.php",
		method: 'POST',
		data: { id, nome },
		dataType: "text",
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message-excluir').text(mensagem.trim());
			if (mensagem.trim() == "Excluído com Sucesso") {
				listarArquivos();
				$('#not_excluido').click(); // Dispara a notificação de sucesso
				//$('#btn_fechar_excluir_arquivos').click();              
			} else {
				toast(mensagem, 'vermelha');
			}
		},
	});
}

function carregarImgArquivos() {
	var target = document.getElementById('target-arquivos');
	var file = document.querySelector("#arquivo_conta").files[0];
	var arquivo = file['name'];
	resultado = arquivo.split(".", 2);
	if (resultado[1] === 'pdf') {
		$('#target-arquivos').attr('src', "images/pdf.png");
		return;
	}
	if (resultado[1] === 'rar' || resultado[1] === 'zip') {
		$('#target-arquivos').attr('src', "images/rar.png");
		return;
	}
	if (resultado[1] === 'doc' || resultado[1] === 'docx' || resultado[1] === 'txt') {
		$('#target-arquivos').attr('src', "images/word.png");
		return;
	}
	if (resultado[1] === 'xlsx' || resultado[1] === 'xlsm' || resultado[1] === 'xls') {
		$('#target-arquivos').attr('src', "images/excel.png");
		return;
	}
	if (resultado[1] === 'xml') {
		$('#target-arquivos').attr('src', "images/xml.png");
		return;
	}
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




$("#form_parcelar").submit(function (event) {
	$('#btn_salvar_parcelar').hide();
	$('#btn_carregando_parcelar').show();
	event.preventDefault();
	var formData = new FormData(this);
	$.ajax({
		url: '../../sistema/painel/paginas/' + pag + "/parcelar.php",
		type: 'POST',
		data: formData,
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Parcelado com Sucesso") {
				$('#btn_fechar_parcelar').click();
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				location.reload();
				
			} else {
				toast(mensagem, 'vermelha');
			}
			$('#btn_salvar_parcelar').show();
			$('#btn_carregando_parcelar').hide();
		},
		cache: false,
		contentType: false,
		processData: false,
	});
});




$("#form_baixar").submit(function (event) {
	event.preventDefault();
	var formData = new FormData(this);

	$('#btn_salvar_baixar').hide();
	$('#btn_carregando_baixar').show();

	$.ajax({
		url: '../../sistema/painel/paginas/' + pag + "/baixar.php",
		type: 'POST',
		data: formData,
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Baixado com Sucesso") {
				$('#btn-fechar-baixar').click(); // Fechar a modal
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				location.reload();
			} else {
				toast(mensagem, 'vermelha');
			}
			$('#btn_salvar_baixar').show();
			$('#btn_carregando_baixar').hide();
		},
		cache: false,
		contentType: false,
		processData: false,
	});
});


  $("#form_baixar_pagar").submit(function (event) {
	event.preventDefault();
	var formData = new FormData(this);

	$('#btn_salvar_baixar').hide();
	$('#btn_carregando_baixar').show();

	$.ajax({
		url: '../../sistema/painel/paginas/' + pag + "/baixar.php",
		type: 'POST',
		data: formData,
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Baixado com Sucesso") {
				$('#btn-fechar-baixar').click(); // Fechar a modal
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				location.reload();
			} else {
				toast(mensagem, 'vermelha');
			}
			$('#btn_salvar_baixar').show();
			$('#btn_carregando_baixar').hide();
		},
		cache: false,
		contentType: false,
		processData: false,
	});
});



$("#form_baixar_cliente").submit(function (event) {
	event.preventDefault();
	var formData = new FormData(this);
	$.ajax({
		url: '../../sistema/painel/paginas/receber/baixar.php',
		type: 'POST',
		data: formData,
		success: function (mensagem) {
			//ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
			if (mensagem.trim() == "Baixado com Sucesso") {
				$('#not_salvar').click(); // Dispara a notificação de sucesso
				$('#btn_fechar_baixar').click();
				var id = $('#id_contas').val();
				listarDebitos(id);
			} else {
				toast(mensagem, 'vermelha');
			}
		},
		cache: false,
		contentType: false,
		processData: false,
	});
});





//############### MÁSCRAS MOEDAS #########################################
function mascaraMoeda(elemento) {
	let value = elemento.value;
	// Remove todos os caracteres que não são números
	value = value.replace(/\D/g, '');
	// Converte o valor para um número decimal com duas casas
	value = (value / 100).toFixed(2);
	// Substitui ponto por vírgula
	value = value.replace('.', ',');
	// Adiciona separadores de milhar
	value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
	// Atualiza o valor do input sem o prefixo "R$"
	elemento.value = value;
}

function alertWarning(result) {
    Swal.fire({
        title: 'Alerta!',
        text: result,
        icon: "warning",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}

//############### ALERTAS #########################################
function toast(mensagem, cor) {
	if (cor == 'verde') {
		cor = 'linear-gradient(90deg, rgba(36,199,106,1) 0%, rgba(68,247,145,1) 82%)';
	}

	if (cor == 'vermelha') {
		cor = 'linear-gradient(90deg, rgba(201,68,38,1) 0%, rgba(233,89,57,1) 82%)';
	}

	if (cor == '') {
		cor = '#4a4949';
	}

	Toastify({
		text: mensagem,
		duration: 3000,
		close: true,
		gravity: "top", // `top` or `bottom`
		position: "center", // `left`, `center` or `right`
		stopOnFocus: true, // Prevents dismissing of toast on hover
		style: {
			background: cor, //verde #24c76a    vermelha #d4483b
			borderRadius: "30px",
		},
		onClick: function () { } // Callback after click
	}).showToast();
}





//############### ALERTAS #########################################

// ALERT SALVAR FINAL ##############
function alertsucesso(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: 'Sucesso!',
        text: mensagem,
        icon: "success",
        timer: 2000,
        timerProgressBar: true,
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}


function alertsucessoInfo(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: 'Sucesso!',
        text: mensagem,
        icon: "success",
        timer: 1000,
        timerProgressBar: true,
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}

function alertCobrar(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: 'Sucesso!',
        text: mensagem,
        icon: "success",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}

// ALERT ERRO ##############
function alertErro(mensagem) {
    $('body').removeClass('timer-alert');
    Swal.fire({
        title: 'Opss...',
        text: mensagem,
        icon: "error",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        },
        // Adicione a opção `target` se necessário
        target: document.body // ou um seletor específico se necessário
    });
}


function alertWhatsapp(result) {
    Swal.fire({
        title: 'Alerta!',
        text: result,
        icon: "success",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}

function alertCNPJ(result) {
    Swal.fire({
        title: 'Alerta!',
        text: result,
        icon: "info",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}

function alertAlert(result) {
    Swal.fire({
        title: 'Alerta!',
        text: result,
        icon: "info",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}

function alertWarning(result) {
    Swal.fire({
        title: 'Alerta!',
        text: result,
        icon: "warning",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}

function alertErroresult(result) {
    Swal.fire({
        title: 'Erro!',
        text: result,
        icon: "error",
        confirmButtonText: 'OK',
        customClass: {
            container: 'swal-whatsapp-container'
        }
    })
}



// ALERT EXCLUIR #######################################
function excluir_reg(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
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
            // Realiza a requisição AJAX para excluir o item
            $.ajax({
                url: '../../sistema/painel/paginas/' + pag + "/excluir.php",
                method: 'POST',
                data: { id },
                dataType: "html",
                success: function (mensagem) {
                    if (mensagem.trim() == "Excluído com Sucesso") {
                        // Exibe mensagem de sucesso após a exclusão
                        swalWithBootstrapButtons.fire({
                            title: 'Sucesso!',
                            text: 'Excluido com sucesso!',
                            icon: "success",
                            timer: 2000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK',

                        });
                       setTimeout(function () {
							location.reload();
						}, 2000); // 2000 milissegundos = 2 segundos
                    } else {
                        // Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
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


// EXCLUIR MARKETING #######################################
	function excluirMarketing(id) {
		const swalWithBootstrapButtons = Swal.mixin({
			customClass: {
				confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
				cancelButton: "btn btn-danger me-1"
			},
			buttonsStyling: false
		});
		swalWithBootstrapButtons.fire({
			title: "Excluir do Marketing?",
			text: "Ele não receberá mais mensagens!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Sim, Excluir!",
			cancelButtonText: "Não, Cancelar!",
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: '../../sistema/painel/paginas/' + pag + "/excluir_marketing.php",
					method: 'POST',
					data: { id },
					dataType: "html",

					success: function (mensagem) {
						if (mensagem.trim() == "Excluído com Sucesso") {

							// Ação de exclusão aqui
							Swal.fire({
								title: 'Sucesso!',
								text: 'Excluido com sucesso!',
								icon: "success",
								timer: 2000
							})
							setTimeout(function () {
								location.reload();
							}, 2000); // 2000 milissegundos = 2 segundos


						} else {
							// Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
                        });
						}
					}
				});
			} else if (result.dismiss === Swal.DismissReason.cancel) {
				swalWithBootstrapButtons.fire({
					title: "Cancelado",
					text: "Fecharei em 1 segundo.",
					icon: "error",
					timer: 2000,
					timerProgressBar: true,
				});
			}
		});
	}






	// ALERT EXCLUIR #######################################
function cancelarRec(id) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
            cancelButton: "btn btn-danger me-1",
            container: 'swal-whatsapp-container'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Cancelar Recorrência?",
        text: "Deseja cancelar a recorrência?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, Cancelar!",
        cancelButtonText: "Não, Cancelar!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Realiza a requisição AJAX para excluir o item
            $.ajax({
                url: '../../sistema/painel/paginas/' + pag + "/cancelar_recorrencia.php",
                method: 'POST',
                data: { id },
                dataType: "html",
                success: function (mensagem) {
                    if (mensagem.trim() == "Cancelado com Sucesso") {
                        // Exibe mensagem de sucesso após a exclusão
                        swalWithBootstrapButtons.fire({
                            title: 'Sucesso!',
                            text: 'Recorrência Cancelada com sucesso.',
                            icon: "success",
                            timer: 2000,
                            timerProgressBar: true,
                            confirmButtonText: 'OK',

                        });
                       setTimeout(function () {
							location.reload();
						}, 2000); // 2000 milissegundos = 2 segundos
                    } else {
                        // Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
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




// EXCLUIR MARKETING #######################################
	function concluirTarefa(id) {
		const swalWithBootstrapButtons = Swal.mixin({
			customClass: {
				confirmButton: "btn btn-success", // Adiciona margem à direita do botão "Sim, Excluir!"
				cancelButton: "btn btn-danger me-1"
			},
			buttonsStyling: false
		});
		swalWithBootstrapButtons.fire({
			title: "Concluir Tarefa?",
			text: "Deseja concluir a tarefa?",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Sim, Concluir!",
			cancelButtonText: "Não, Cancelar!",
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: '../../sistema/painel/paginas/tarefas/concluir.php',
					method: 'POST',
					data: { id },
					dataType: "html",

					success: function (mensagem) {
						if (mensagem.trim() == "Tarefa Concluída") {

							// Ação de exclusão aqui
							Swal.fire({
								title: 'Sucesso!',
								text: 'Tarefa Concluída com sucesso.',
								icon: "success",
								timer: 2000
							})
							setTimeout(function () {
								location.reload();
							}, 2000); // 2000 milissegundos = 2 segundos


						} else {
							// Exibe mensagem de erro se a requisição falhar
                        swalWithBootstrapButtons.fire({
                            title: "Opss!",
                            text: mensagem,
                            icon: "error",
                            confirmButtonText: 'OK',
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




function mascara_valor(valor) {
  var valorAlterado = $('#'+valor).val();
  valorAlterado = valorAlterado.replace(/\D/g, ""); // Remove todos os não dígitos
  valorAlterado = valorAlterado.replace(/(\d+)(\d{2})$/, "$1,$2"); // Adiciona a parte de centavos
  valorAlterado = valorAlterado.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // Adiciona pontos a cada três dígitos
  valorAlterado = valorAlterado;
  $('#'+valor).val(valorAlterado);
}



 $("#form-servico").submit(function (event) {
  event.preventDefault();
  var formData = new FormData(this);
  $('#btn_salvar_servico').hide();
  $('#btn_carregando_servico').show();
  $.ajax({
    url: '../../sistema/painel/paginas/' + pag + "/inserir-servico.php",
    type: 'POST',
    data: formData,
    success: function (mensagem) {
      //alert(mensagem)
      //ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
      $('#toast-message').text(mensagem.trim());
      if (mensagem.trim() == "Salvo com Sucesso") {
        $('#btn-fechar-servico').click();
        $('#not_salvar').click(); // Dispara a notificação de sucesso
        setTimeout(function () {
          location.reload();
        }, 1000);
      } else {
        toast(mensagem, 'vermelha'); // Dispara a notificação de erro
      }
      $('#btn_carregando_servico').hide();
      $('#btn_salvar_servico').show();
    },
    cache: false,
    contentType: false,
    processData: false,
  });
});



 $("#form_agendamento").submit(function (event) {
  event.preventDefault();
  var formData = new FormData(this);
  $('#btn_salvar').hide();
  $('#btn_carregando').show();
  $.ajax({
    url: '../../sistema/painel/paginas/' + pag + "/inserir.php",
    type: 'POST',
    data: formData,
    success: function (mensagem) {
      //alert(mensagem)
      //ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
      $('#toast-message').text(mensagem.trim());
      if (mensagem.trim() == "Salvo com Sucesso") {
        $('#btn-fechar').click();
        $('#not_salvar').click(); // Dispara a notificação de sucesso
        setTimeout(function () {
          location.reload();
        }, 1000);
      } else {
        toast(mensagem, 'vermelha'); // Dispara a notificação de erro
      }
      $('#btn_carregando').hide();
      $('#btn_salvar').show();
    },
    cache: false,
    contentType: false,
    processData: false,
  });
});



 $("#form_dias").submit(function (event) {
  event.preventDefault();
  var formData = new FormData(this);
  $('#btn_salvar').hide();
  $('#btn_carregando').show();
  $.ajax({
    url: '../../sistema/painel/paginas/funcionarios/inserir-dias.php',
    type: 'POST',
    data: formData,
    success: function (mensagem) {
      //alert(mensagem)
      //ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
      $('#toast-message').text(mensagem.trim());
      if (mensagem.trim() == "Salvo com Sucesso") {
        $('#btn-fechar').click();
        $('#not_salvar').click(); // Dispara a notificação de sucesso
        setTimeout(function () {
          location.reload();
        }, 1000);
      } else {
        toast(mensagem, 'vermelha'); // Dispara a notificação de erro
      }
      $('#btn_carregando').hide();
      $('#btn_salvar').show();
    },
    cache: false,
    contentType: false,
    processData: false,
  });
});




 $("#form_servico").submit(function (event) {
  event.preventDefault();
  var formData = new FormData(this);
  $('#btn_salvar').hide();
  $('#btn_carregando').show();
  $.ajax({
    url: '../../sistema/painel/paginas/funcionarios/inserir-servico.php',
    type: 'POST',
    data: formData,
    success: function (mensagem) {
      //alert(mensagem)
      //ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
      $('#toast-message').text(mensagem.trim());
      if (mensagem.trim() == "Salvo com Sucesso") {
        $('#btn-fechar').click();
        $('#not_salvar').click(); // Dispara a notificação de sucesso
        setTimeout(function () {
          location.reload();
        }, 1000);
      } else {
        toast(mensagem, 'vermelha'); // Dispara a notificação de erro
      }
      $('#btn_carregando').hide();
      $('#btn_salvar').show();
    },
    cache: false,
    contentType: false,
    processData: false,
  });
});




 $("#form_dias_bloqueio").submit(function (event) {
  event.preventDefault();
  var formData = new FormData(this);
  $('#btn_salvar').hide();
  $('#btn_carregando').show();
  $.ajax({
    url: '../../sistema/painel/paginas/dias_bloqueio_func/inserir-servico.php',
    type: 'POST',
    data: formData,
    success: function (mensagem) {
      //alert(mensagem)
      //ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
      $('#toast-message').text(mensagem.trim());
      if (mensagem.trim() == "Salvo com Sucesso") {
        $('#btn-fechar').click();
        $('#not_salvar').click(); // Dispara a notificação de sucesso
        setTimeout(function () {
          location.reload();
        }, 1000);
      } else {
        toast(mensagem, 'vermelha'); // Dispara a notificação de erro
      }
      $('#btn_carregando').hide();
      $('#btn_salvar').show();
    },
    cache: false,
    contentType: false,
    processData: false,
  });
});