<?php
require 'config.php';

$carrinho = carrinhoAtual();

if (!$carrinho) {
    header('Location: carrinho.php');
    exit;
}

$ids = array_map('intval', array_keys($carrinho));
$lista = implode(',', $ids);
$resultado = $conexao->query("SELECT * FROM produtos WHERE id IN ($lista) AND ativo = 1");
$itens = [];
$subtotal = 0;

while ($produto = $resultado->fetch_assoc()) {
    $produto['quantidade'] = min($carrinho[$produto['id']], $produto['estoque']);
    $produto['total'] = $produto['preco'] * $produto['quantidade'];
    $subtotal += $produto['total'];
    $itens[] = $produto;
}

$frete = $subtotal >= 250 ? 0 : 19.90;
$total = $subtotal + $frete;
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $pagamento = $_POST['pagamento'] ?? 'Pix';

    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $telefone === '' || $cep === '' || $endereco === '' || $cidade === '' || $estado === '') {
        $erro = 'Preencha todos os campos corretamente.';
    } elseif (!$itens) {
        $erro = 'Não há produtos disponíveis no carrinho.';
    } else {
        $conexao->begin_transaction();

        try {
            $pedido = $conexao->prepare("INSERT INTO pedidos (nome_cliente, email, telefone, cep, endereco, cidade, estado, pagamento, subtotal, frete, total, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Recebido')");
            $pedido->bind_param('ssssssssddd', $nome, $email, $telefone, $cep, $endereco, $cidade, $estado, $pagamento, $subtotal, $frete, $total);
            $pedido->execute();
            $pedidoId = $conexao->insert_id;

            foreach ($itens as $item) {
                if ($item['quantidade'] < 1) {
                    throw new Exception('Produto sem estoque.');
                }

                $pedidoItem = $conexao->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, nome_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?, ?)");
                $pedidoItem->bind_param('iisid', $pedidoId, $item['id'], $item['nome'], $item['quantidade'], $item['preco']);
                $pedidoItem->execute();
                $baixa = $conexao->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ? AND estoque >= ?");
                $baixa->bind_param('iii', $item['quantidade'], $item['id'], $item['quantidade']);
                $baixa->execute();

                if ($baixa->affected_rows === 0) {
                    throw new Exception('Estoque insuficiente para ' . $item['nome'] . '.');
                }
            }

            $conexao->commit();
            salvarCarrinho([]);
            header('Location: pedido.php?id=' . $pedidoId . '&email=' . urlencode($email));
            exit;
        } catch (Throwable $e) {
            $conexao->rollback();
            $erro = $e->getMessage();
        }
    }
}

$titulo = 'Finalizar compra | Shelby Story';
require 'header.php';
?>
<section class="pagina limite">
    <div class="cabecalho-pagina">
        <span class="etiqueta">Última etapa</span>
        <h1>Finalizar compra</h1>
        <p>Informe seus dados para registrar o pedido.</p>
    </div>
    <?php if ($erro): ?><div class="alerta erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <div class="checkout-layout">
        <form class="formulario" method="post">
            <h2>Dados pessoais</h2>
            <div class="campo largura-dupla"><label>Nome completo<input name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"></label></div>
            <div class="campo"><label>E-mail<input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></label></div>
            <div class="campo"><label>Telefone<input name="telefone" required value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>"></label></div>
            <h2 class="largura-dupla">Endereço de entrega</h2>
            <div class="campo"><label>CEP<input name="cep" required value="<?= htmlspecialchars($_POST['cep'] ?? '') ?>"></label></div>
            <div class="campo"><label>Estado<input name="estado" maxlength="2" required value="<?= htmlspecialchars($_POST['estado'] ?? '') ?>"></label></div>
            <div class="campo largura-dupla"><label>Endereço e número<input name="endereco" required value="<?= htmlspecialchars($_POST['endereco'] ?? '') ?>"></label></div>
            <div class="campo largura-dupla"><label>Cidade<input name="cidade" required value="<?= htmlspecialchars($_POST['cidade'] ?? '') ?>"></label></div>
            <h2 class="largura-dupla">Pagamento</h2>
            <div class="campo largura-dupla">
                <label>Forma de pagamento
                    <select name="pagamento"><option>Pix</option><option>Cartão na entrega</option><option>Dinheiro na entrega</option></select>
                </label>
            </div>
            <button class="botao primario largura-dupla" type="submit">Confirmar pedido</button>
        </form>
        <aside class="resumo">
            <h2>Seu pedido</h2>
            <?php foreach ($itens as $item): ?><div><span><?= $item['quantidade'] ?> × <?= htmlspecialchars($item['nome']) ?></span><strong><?= dinheiro($item['total']) ?></strong></div><?php endforeach; ?>
            <div><span>Frete</span><strong><?= $frete ? dinheiro($frete) : 'Grátis' ?></strong></div>
            <div class="resumo-total"><span>Total</span><strong><?= dinheiro($total) ?></strong></div>
        </aside>
    </div>
</section>
<?php require 'footer.php'; ?>
