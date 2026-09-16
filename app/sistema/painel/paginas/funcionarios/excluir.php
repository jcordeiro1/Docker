<?php 
require_once("../../../conexao.php");
$tabela = 'usuarios';

// ✅ id seguro (int)
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
	echo 'ID inválido!';
	exit;
}

try {

	// ✅ Busca apenas a foto (mesma lógica, mais leve e seguro)
	$stmt = $pdo->prepare("SELECT foto FROM {$tabela} WHERE id = :id LIMIT 1");
	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$dados = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$dados) {
		echo 'Registro não encontrado!';
		exit;
	}

	$foto = $dados['foto'] ?? 'sem-foto.jpg';

	// ✅ Apaga a foto se não for padrão (mesma lógica)
	if ($foto && $foto !== "sem-foto.jpg") {

		// Evita path traversal (não muda lógica, só protege)
		$foto = basename($foto);

		// Mantém o mesmo caminho relativo do seu script original
		$dirPerfil = realpath(__DIR__ . '/../../img/perfil');
		if ($dirPerfil !== false) {
			$arquivo = $dirPerfil . DIRECTORY_SEPARATOR . $foto;

			// Só apaga se for arquivo mesmo e existir
			if (is_file($arquivo)) {
				@unlink($arquivo);
			}
		}
	}

	// ✅ DELETE do usuário (mesma lógica, seguro)
	$stmtDel = $pdo->prepare("DELETE FROM {$tabela} WHERE id = :id");
	$stmtDel->bindValue(':id', $id, PDO::PARAM_INT);
	$stmtDel->execute();

	// ✅ DELETE dos serviços vinculados (mesma lógica, seguro)
	$stmtServ = $pdo->prepare("DELETE FROM servicos_func WHERE funcionario = :id");
	$stmtServ->bindValue(':id', $id, PDO::PARAM_INT);
	$stmtServ->execute();

	echo 'Excluído com Sucesso';

} catch (Throwable $e) {
	// Não expõe erro interno em produção
	echo 'Erro ao Excluir!';
}
?>
