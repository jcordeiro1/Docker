<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);

include("./config.php");
require("../sistema/conexao.php");

$id_conta = filter_var($_GET['id_conta'], @FILTER_SANITIZE_STRING);
$total = filter_var($_GET['total'], @FILTER_SANITIZE_STRING);

if($api_pagamento == "Asaas"){
    if($total > 0){
         echo '<script>window.location="'.$url_sistema.'pagamento_asaas/'.$id_conta.'/'.$total.'"</script>';  
    }else{
        echo '<script>window.location="'.$url_sistema.'pagar_asaas/'.$id_conta.'"</script>'; 
        
    }
    
} 

if($opcao_pagar == 'Não'){
    header("Location: {$url_sistema}pagamentos/pagamento_aprovado.php?id_agd={$id_conta}");
    exit;
}

if($pgto_api != 'Sim'){
    echo "<script>window.location='$url_sistema/pagamentos/pagamento_aprovado.php?id_agd=$id_conta'</script>";
}

$query = $pdo->prepare("SELECT * FROM agendamentos_temp where id = :id_conta");
$query->bindValue(":id_conta", "$id_conta");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$cliente = $res[0]['cliente'];
$servico = $res[0]['servico'];
$ref_pix = $res[0]['ref_pix'];
$data = $res[0]['data'];
$hora = $res[0]['hora'];
$dataF = implode('/', array_reverse(explode('-', $data)));
$horaF = date("H:i", strtotime($hora));

$query = $pdo->query("SELECT * FROM servicos where id = '$servico'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_servico = $res[0]['nome'];
$valor = $res[0]['valor'];

if($porc_servico > 0 and $total != 100){
    $valor = $valor * $porc_servico / 100;
}


$valorF = number_format($valor, 2, ',', '.');

if($ref_pix != ""){
     require('consultar_pagamento.php');
     if($status_api == 'approved'){
         echo 'Essa conta Já foi Paga';  
         exit();  
        }
}

$query = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_cliente =  $res[0]['nome'];
$cpf_cliente =  '450.417.700-50';
$email_cliente =  'cliente@hotmail.com';

$token_valor = ($valor!="")? sha1($valor) : "";
$doc = $cpf_cliente;
$doc =  str_replace(array(",", ".", "-", "/", " "), "", $doc);
$ref = $_REQUEST["ref"];
$email = $email_cliente;
$gerarDireto = $_REQUEST["gerarDireto"];
$descricao = $descricao;
$nome = $nome_cliente;
$sobrenome = $_REQUEST["sobrenome"];

?>
<html lang="pt-br">
<head>
    <title>Pagamento</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <link href="./assets/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/signin.css" rel="stylesheet">
    <script src="./assets/jquery-3.6.4.min.js"></script>
</head>
<body  class="text-center">


<form action="agendamento_confirmado" method="post" style="display:none">
    <input type="hidden" name="id" value="<?=$id_conta;?>">
     <input type="hidden" name="enviar" value="Sim">
    <button id="btn_form" type="submit"></button>
</form>



<div style="max-width: 500px; max-height: 800px; margin: 0 auto;  text-align: center; margin-bottom: 20px; word-break: break-all;" >


<div id="info_pagamento" style="text-align: center;"> 
        <p class="h3 font-weight-normal" style=" font-size: 18px; border-radius: 4px;"><span>(Serviço <?=$nome_servico;?>)</span> <span style="color:green; ">R$ <?=$valorF;?></span> </p>

        <?php if($porc_servico > 0){ ?>
         <?php if($porc_servico != 100){ ?>
         <div style="margin-top: -8px; margin-bottom: 8px; font-size: 13px"><a href="<?php echo $id_conta ?>/<?php echo $porc_servico ?>">Pagar <?=$porc_servico;?>% </a> / <a href="<?php echo $id_conta ?>/100"> Pagar 100% </a> </div>
        <?php } } ?>
        
        <div style=" margin-bottom: 8px; font-size: 13px"><b>Agendado</b> Data:<?=$dataF;?> / Hora <?=$horaF;?></div>

        <?php if($porc_servico <= 0){ ?>
  <div style="margin-bottom: 8px; font-size: 13px">
    <a onclick="clique()" href="<?php echo $url_sistema ?>pagamentos/pagamento_aprovado.php?id_agd=<?php echo $id_conta ?>">
      <b>
        <br>
        <font color="red" size="4">>> CLIQUE AQUI <<</font>
        <br>
      </b>
    </a>  
    <b><br>"AGENDE JÁ E PAGUE NO LOCAL!"</b>
  </div><br>
<?php } ?>


</div>    

<div  id="paymentBrick_container">
        </div>
        <div id="statusScreenBrick_container">
        </div>
        <div class="form-signin" id="form-pago" style="display:none;text-align: center;">
            <h1 class="h3 mb-3 font-weight-normal">Obrigado!</h1>
            <img class="mb-4"  src="<?=$url_sistema;?>pagamentos/assets/check_ok.png" alt="" width="120" height="120">
            <br>
            <h5><?=$MSG_APOS_PAGAMENTO;?></h5>
            <br>
            Código do pagamento: <?php echo $_GET["id"]; ?>
        </div>

          <?php if($porc_servico > 0){ ?>   
            <?php if($porc_servico == 100){ ?>   
         <div style="margin-bottom: 8px; font-size: 13px">Efetue o pagamento para confirmar seu agendamento!</div>
         <?php }else{ ?>
             <div style="margin-bottom: 8px; font-size: 13px">Efetue o pagamento de pelo menos <?php echo $porc_servico ?>% do valor para confirmar seu agendamento!</div>
        <?php } } ?>

    </div>
    <style>
        body{font-family:arial}
    </style>
    <script>
        var payment_check;
        const mp = new MercadoPago('<?=$TOKEN_MERCADO_PAGO_PUBLICO;?>', {
            locale: 'pt-BR'
        });
        const bricksBuilder = mp.bricks();
        const renderPaymentBrick = async (bricksBuilder) => {
            const settings = {
                initialization: {
                    amount: '<?=$valor;?>',
                    payer: {
                        firstName: "<?=$nome;?>",
                        lastName: "<?=$sobrenome;?>",
                        email: "<?=$email;?>",
                        identification: {
                            type: '<?=(strlen($doc)>11? "CNPJ" : "CPF");?>',
                            number: '<?=$doc;?>',
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
                    visual: {
                        style: {
                            theme: "dark",
                        },
                    },
                    paymentMethods: {
                        <?php if($ATIVAR_CARTAO_CREDITO=="1"){?>creditCard: "all",<?php } ?>
                        <?php if($ATIVAR_CARTAO_DEBIDO=="1"){?>debitCard: "all",<?php } ?>
                        <?php if($ATIVAR_BOLETO=="1"){?>ticket: "all",<?php } ?>
                        <?php if($ATIVAR_PIX=="1"){?>bankTransfer: "all",<?php } ?>
                        maxInstallments: 12
                    },
                },
                callbacks: {
                    onReady: () => {
                    },
                    onSubmit: ({ selectedPaymentMethod, formData }) => {

                        formData.external_reference = '<?=$ref;?>';
                        formData.description = '<?=$descricao;?>';
                        var id_conta = '<?=$id_conta;?>';

                        return new Promise((resolve, reject) => {
                            fetch("<?=$url_sistema;?>pagamentos/process_payment.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                },
                                body: JSON.stringify(formData),
                            })
                            .then((response) => response.json())
                            .then((response) => {
                // receber o resultado do pagamento
                                if(response.status==true){
                                    window.location.href = "<?=$url_sistema;?>pagamentos/index.php?id="+response.id+'&id_conta='+id_conta;
                                }
                                if(response.status!=true){
                                    alert(response.message);
                                }
                                resolve();
                            })
                            .catch((error) => {
                                reject();
                            });
                        });
                    },
                    onError: (error) => {
                        console.error(error);
                    },
                },
            };
            window.paymentBrickController = await bricksBuilder.create(
                "payment",
                "paymentBrick_container",
                settings
                );
        };

        const renderStatusScreenBrick = async (bricksBuilder) => {
            const settings = {
                initialization: {
                    paymentId: '<?=$_GET["id"];?>',
                },
                customization: {
                    visual: {
                        hideStatusDetails: false,
                        hideTransactionDate: false,
                        style: {
            theme: 'dark', // 'default' | 'dark' | 'bootstrap' | 'flat'
        }
    },
    backUrls: {
        //'error': '<http://<your domain>/error>',
        //'return': '<http://<your domain>/homepage>'
    }
},
callbacks: {
    onReady: () => {
        check("<?=$_GET["id"];?>", "<?=$_GET["id_conta"];?>");
    },
    onError: (error) => {
    },
},
};
window.statusScreenBrickController = await bricksBuilder.create('statusScreen', 'statusScreenBrick_container', settings);
};

<?php if($_GET["id"]!=""){ ?>
    renderStatusScreenBrick(bricksBuilder);
<?php } else { ?>
    <?php if($valor==""){?>
        alert("O valor do pagamento está vazio.");
    <?php } ?>
    renderPaymentBrick(bricksBuilder);
<?php } ?>
var redi = "<?=$URL_REDIRECIONAR;?>";
function check(id, id_conta) {
    var settings = {
        "url": "<?=$url_sistema;?>pagamentos/process_payment.php?acc=check&id=" + id + "&id_conta=" + id_conta,
        "method": "GET",
        "timeout": 0
    };
    $.ajax(settings).done(function(response) {
        try {
            if (response.status == "pago") {
                $("#statusScreenBrick_container").slideUp("fast");
                $("#form-pago").slideDown("fast");
                if (redi.trim() == "Sim") {
                    setTimeout(() => {
                        window.location = "../meus-agendamentos.php";
                        //$("#btn_form").click();
                    }, 6000);
                }
            } else {
                setTimeout(() => {
                    check(id)
                }, 3000);
            }
        } catch (error) {
            alert("Erro ao localizar o pagamento, contacte com o suporte");
        }
    });
}
</script>

<script type="text/javascript">
    function clique(){       
        document.getElementById("clique_aqui").style.display = 'none';
        }
</script>

</body>
</html>