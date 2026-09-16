<?php 
require_once("../../../conexao.php");
$tabela = 'manutencoes_protese';

$id = @$_POST['id'];
$cliente = @$_POST['cliente'];
$id_protese = @$_POST['id_protese'];
$data_manutencao = @$_POST['data_manutencao'];
$tipo = @$_POST['tipo'];
$produtos_utilizados = @$_POST['produtos_utilizados'];
$observacoes = @$_POST['observacoes'];
$proxima_manutencao = @$_POST['proxima_manutencao'];

$tipo_pele = @$_POST['tipo_pele'];
$nivel_sudorese = @$_POST['nivel_sudorese'];
$clima = @$_POST['clima'];
$tipo_adesivo = @$_POST['tipo_adesivo'];

if($cliente == ""){
	echo 'Selecione um Cliente';
	exit();
}

if($id_protese == ""){
	echo 'Selecione uma Prótese';
	exit();
}

if($data_manutencao == ""){
	echo 'Informe a Data da Manutenção';
	exit();
}

/*
	Se a próxima manutenção vier em branco,
	o sistema calcula automaticamente com base no perfil técnico
*/
if($proxima_manutencao == "" && $data_manutencao != ""){
	
	$dias_manutencao = 15;

	// Tipo de pele
	if($tipo_pele == 'Oleosa'){
		$dias_manutencao -= 3;
	}else if($tipo_pele == 'Seca'){
		$dias_manutencao += 2;
	}

	// Nível de sudorese
	if($nivel_sudorese == '1'){
		$dias_manutencao += 2;
	}else if($nivel_sudorese == '2'){
		$dias_manutencao += 1;
	}else if($nivel_sudorese == '4'){
		$dias_manutencao -= 2;
	}else if($nivel_sudorese == '5'){
		$dias_manutencao -= 4;
	}

	// Clima
	if($clima == 'Frio'){
		$dias_manutencao += 2;
	}else if($clima == 'Quente'){
		$dias_manutencao -= 2;
	}

	// Tipo de adesivo
	if($tipo_adesivo == 'Ultra Hold'){
		$dias_manutencao += 4;
	}else if($tipo_adesivo == 'Gold / Amarela'){
		$dias_manutencao += 2;
	}else if($tipo_adesivo == 'No-Shine'){
		$dias_manutencao += 1;
	}else if($tipo_adesivo == 'Fita Branca'){
		$dias_manutencao += 0;
	}else if($tipo_adesivo == 'Cola Acrílica'){
		$dias_manutencao += 3;
	}

	// Limite mínimo para não gerar data muito curta
	if($dias_manutencao < 5){
		$dias_manutencao = 5;
	}

	$proxima_manutencao = date('Y-m-d', strtotime("+$dias_manutencao days", strtotime($data_manutencao)));
}

$query = $pdo->prepare("SELECT * FROM $tabela WHERE cliente = :cliente AND id_protese = :id_protese AND data_manutencao = :data_manutencao");
$query->bindValue(":cliente", $cliente);
$query->bindValue(":id_protese", $id_protese);
$query->bindValue(":data_manutencao", $data_manutencao);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if(@count($res) > 0 and $id != $res[0]['id']){
	echo 'Registro já Cadastrado, escolha outro!!';
	exit();
}

if($id == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET 
		cliente = :cliente, 
		id_protese = :id_protese, 
		data_manutencao = :data_manutencao, 
		tipo = :tipo, 
		produtos_utilizados = :produtos_utilizados, 
		observacoes = :observacoes, 
		proxima_manutencao = :proxima_manutencao,
		tipo_pele = :tipo_pele,
		nivel_sudorese = :nivel_sudorese,
		clima = :clima,
		tipo_adesivo = :tipo_adesivo,
		alerta_manutencao_enviado = 0,
		data_alerta_manutencao = NULL");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET 
		cliente = :cliente, 
		id_protese = :id_protese, 
		data_manutencao = :data_manutencao, 
		tipo = :tipo, 
		produtos_utilizados = :produtos_utilizados, 
		observacoes = :observacoes, 
		proxima_manutencao = :proxima_manutencao,
		tipo_pele = :tipo_pele,
		nivel_sudorese = :nivel_sudorese,
		clima = :clima,
		tipo_adesivo = :tipo_adesivo,
		alerta_manutencao_enviado = 0,
		data_alerta_manutencao = NULL
		WHERE id = :id");
	$query->bindValue(":id", $id);
}

$query->bindValue(":cliente", $cliente);
$query->bindValue(":id_protese", $id_protese);
$query->bindValue(":data_manutencao", $data_manutencao);
$query->bindValue(":tipo", $tipo);
$query->bindValue(":produtos_utilizados", $produtos_utilizados);
$query->bindValue(":observacoes", $observacoes);
$query->bindValue(":proxima_manutencao", $proxima_manutencao);
$query->bindValue(":tipo_pele", $tipo_pele);
$query->bindValue(":nivel_sudorese", $nivel_sudorese);
$query->bindValue(":clima", $clima);
$query->bindValue(":tipo_adesivo", $tipo_adesivo);
$query->execute();

echo 'Salvo com Sucesso';
?>