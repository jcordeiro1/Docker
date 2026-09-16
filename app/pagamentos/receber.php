<?php
// Cabeçalho HTML correto (antes de qualquer saída)
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);

include("./config.php");
require("../sistema/conexao.php");

// id da conta vindo da rota /pagamentos/receber.php?id_conta={id}
$id_conta = isset($_GET['id_conta']) ? trim($_GET['id_conta']) : '';

if ($id_conta === '') {
    exit('Conta inválida.');
}

/*
 * Se a forma de pagamento configurada for Asaas,
 * redireciona para a rota própria e NÃO continua neste arquivo.
 * (segue a mesma lógica do exemplo antigo)
 */
if (isset($api_pagamento) && $api_pagamento === "Asaas") {
    echo '<script>window.location="' . $url_sistema . 'conta_asaas/' . htmlspecialchars($id_conta, ENT_QUOTES, "UTF-8") . '"</script>';
    exit;
}

// ===================== BUSCA DA CONTA A RECEBER =====================

$query = $pdo->prepare("SELECT * FROM receber WHERE id = :id_conta LIMIT 1");
$query->bindValue(":id_conta", $id_conta, PDO::PARAM_INT);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($res)) {
    exit('Conta não encontrada.');
}

$linha       = $res[0];
$pago        = $linha['pago'];
$valor       = $linha['valor'];
$ref_pix     = $linha['ref_pix'];
$frequencia  = $linha['frequencia'];
$data        = $linha['data_venc'];
$descricao   = $linha['descricao'];

// Em alguns pontos do sistema a coluna do cliente é "pessoa", noutros "cliente"
$cliente_id  = isset($linha['pessoa'])
    ? $linha['pessoa']
    : (isset($linha['cliente']) ? $linha['cliente'] : null);

if (!$cliente_id) {
    exit('Cliente não associado à conta.');
}

$dataF  = implode('/', array_reverse(explode('-', $data)));
$valorF = number_format($valor, 2, ',', '.');

// Se já existe referência, consulta status na API e bloqueia se já pago
if ($ref_pix != "") {
    require('consultar_pagamento.php'); // deve preencher $status_api
    if (!empty($status_api) && $status_api == 'approved') {
        echo 'Essa assinatura já foi paga.';
        exit();
    }
}

// ===================== DADOS DO CLIENTE =====================

$qCli = $pdo->query("SELECT * FROM clientes WHERE id = '$cliente_id' LIMIT 1");
$rCli = $qCli->fetchAll(PDO::FETCH_ASSOC);

$nome_cliente  = !empty($rCli) ? $rCli[0]['nome'] : 'Cliente';
$cpf_cliente   = '450.417.700-50';        // ajuste se tiver CPF no banco
$email_cliente = 'cliente@hotmail.com';   // ajuste se tiver e-mail no banco

// Montagem para o Brick
$doc        = str_replace(array(",", ".", "-", "/", " "), "", $cpf_cliente);
$ref        = isset($_REQUEST["ref"]) ? $_REQUEST["ref"] : '';
$email      = $email_cliente;
$gerarDireto= isset($_REQUEST["gerarDireto"]) ? $_REQUEST["gerarDireto"] : '';
$nome       = $nome_cliente;
$sobrenome  = isset($_REQUEST["sobrenome"]) ? $_REQUEST["sobrenome"] : '';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pagamento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <link href="./assets/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/signin.css" rel="stylesheet">
    <script src="./assets/jquery-3.6.4.min.js"></script>
</head>
<body class="text-center">

<form action="agendamento_confirmado" method="post" style="display:none">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id_conta, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="enviar" value="Sim">
    <button id="btn_form" type="submit"></button>
</form>

<div style="max-width: 500px; max-height: 800px; margin: 0 auto; text-align: center; margin-bottom: 20px; word-break: break-all;">

    <div id="info_pagamento" style="text-align: center;">
        <p class="h3 font-weight-normal" style="font-size: 18px; border-radius: 4px;">
            <span>(<?= htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8'); ?>)</span>
            <span style="color:green;">R$ <?= $valorF; ?></span>
        </p>
    </div>

    <div id="paymentBrick_container"></div>
    <div id="statusScreenBrick_container"></div>

    <div class="form-signin" id="form-pago" style="display:none;text-align: center;">
        <h1 class="h3 mb-3 font-weight-normal">Obrigado!</h1>
        <img class="mb-4" src="<?= $url_sistema; ?>pagamentos/assets/check_ok.png" alt="" width="120" height="120">
        <br>
        <h5><?= $MSG_APOS_PAGAMENTO; ?></h5>
        <br>
        Código do pagamento:
        <?= isset($_GET["id"]) ? htmlspecialchars($_GET["id"], ENT_QUOTES, 'UTF-8') : ''; ?>
    </div>

    <div style="margin-bottom: 8px; font-size: 13px">
        Efetue o pagamento para confirmar a compra do seu plano!
    </div>

</div>

<style>body{font-family:arial}</style>

<script>
    const mp = new MercadoPago('<?= $TOKEN_MERCADO_PAGO_PUBLICO; ?>', { locale: 'pt-BR' });
    const bricksBuilder = mp.bricks();

    const renderPaymentBrick = async (bricksBuilder) => {
        const settings = {
            initialization: {
                amount: '<?= $valor; ?>',
                payer: {
                    firstName: "<?= addslashes($nome); ?>",
                    lastName: "<?= addslashes($sobrenome); ?>",
                    email: "<?= addslashes($email); ?>",
                    identification: {
                        type: '<?= (strlen($doc) > 11 ? "CNPJ" : "CPF"); ?>',
                        number: '<?= $doc; ?>',
                    },
                    address: {
                        zipCode: '',
                        federalUnit: '',
                        city: '',
                        neighborhood: '',
                        streetName: '',
                        streetNumber: '',
                        complement: '',
                    }
                },
            },
            customization: {
                visual: { style: { theme: "dark" } },
                paymentMethods: {
                    <?php if($ATIVAR_CARTAO_CREDITO=="1"){?>creditCard: "all",<?php } ?>
                    <?php if($ATIVAR_CARTAO_DEBIDO=="1"){?>debitCard: "all",<?php } ?>
                    <?php if($ATIVAR_BOLETO=="1"){?>ticket: "all",<?php } ?>
                    <?php if($ATIVAR_PIX=="1"){?>bankTransfer: "all",<?php } ?>
                    maxInstallments: 12
                },
            },
            callbacks: {
                onReady: () => {},
                onSubmit: ({ selectedPaymentMethod, formData }) => {
                    // Amarra a cobrança à conta no seu banco
                    formData.external_reference = '<?= $id_conta; ?>';
                    formData.description       = '<?= addslashes($descricao); ?>';
                    var id_conta = '<?= $id_conta; ?>';

                    return new Promise((resolve, reject) => {
                        fetch("<?= $url_sistema; ?>pagamentos/process_payment_conta.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify(formData),
                        })
                        .then((response) => response.json())
                        .then((response) => {
                            if (response.status == true) {
                                window.location.href =
                                    "<?= $url_sistema; ?>pagamentos/receber.php?id=" +
                                    response.id + "&id_conta=" + id_conta;
                            } else {
                                alert(response.message || 'Falha ao iniciar pagamento.');
                            }
                            resolve();
                        })
                        .catch((error) => { reject(); });
                    });
                },
                onError: (error) => { console.error(error); },
            },
        };
        window.paymentBrickController =
            await bricksBuilder.create("payment", "paymentBrick_container", settings);
    };

    const renderStatusScreenBrick = async (bricksBuilder) => {
        const settings = {
            initialization: {
                paymentId: '<?= isset($_GET["id"]) ? addslashes($_GET["id"]) : ''; ?>',
            },
            customization: {
                visual: {
                    hideStatusDetails: false,
                    hideTransactionDate: false,
                    style: { theme: 'dark' }
                }
            },
            callbacks: {
                onReady: () => {
                    check(
                        "<?= isset($_GET["id"]) ? addslashes($_GET["id"]) : ''; ?>",
                        "<?= isset($_GET["id_conta"]) ? addslashes($_GET["id_conta"]) : addslashes($id_conta); ?>"
                    );
                },
                onError: (error) => {},
            },
        };
        window.statusScreenBrickController =
            await bricksBuilder.create('statusScreen', 'statusScreenBrick_container', settings);
    };

    <?php if(!empty($_GET["id"])) { ?>
        renderStatusScreenBrick(bricksBuilder);
    <?php } else { ?>
        <?php if($valor==""){?> alert("O valor do pagamento está vazio."); <?php } ?>
        renderPaymentBrick(bricksBuilder);
    <?php } ?>

    var redi = "<?= $URL_REDIRECIONAR; ?>";

    function check(id, id_conta) {
        var settings = {
            url: "<?= $url_sistema; ?>pagamentos/process_payment_conta.php?acc=check&id=" +
                 id + "&id_conta=" + id_conta,
            method: "GET",
            timeout: 0
        };
        $.ajax(settings).done(function(response) {
            try {
                if (response.status == "pago") {
                    $("#statusScreenBrick_container").slideUp("fast");
                    $("#form-pago").slideDown("fast");
                    if (redi.trim() == "Sim") {
                        setTimeout(() => {
                            // aqui você pode redirecionar se quiser
                            // window.location = "../meus-agendamentos.php";
                            // $("#btn_form").click();
                        }, 6000);
                    }
                } else {
                    setTimeout(() => { check(id, id_conta); }, 3000);
                }
            } catch (error) {
                alert("Erro ao localizar o pagamento, contacte o suporte.");
            }
        });
    }
</script>

<script>
    function clique() {
        var el = document.getElementById("clique_aqui");
        if (el) { el.style.display = 'none'; }
    }
</script>

</body>
</html>
