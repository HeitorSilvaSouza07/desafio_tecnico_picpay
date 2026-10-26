<?php
require 'config.php';

$carrinho = carrinhoAtual();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $produtoId = (int) ($_POST['produto_id'] ?? 0);
    $quantidade = max(1, (int) ($_POST['quantidade'] ?? 1));

    if ($acao === 'adicionar' && $produtoId > 0) {
        $carrinho[$produtoId] = ($carrinho[$produtoId] ?? 0) + $quantidade;
    }

    if ($acao === 'atualizar' && $produtoId > 0) {
        $carrinho[$produtoId] = $quantidade;
    }

    if ($acao === 'remover' && $produtoId > 0) {
        unset($carrinho[$produtoId]);
    }

    if ($acao === 'limpar') {
        $carrinho = [];
    }

    salvarCarrinho($carrinho);
    header('Location: carrinho.php');
    exit;
}

$itens = [];
$subtotal = 0;

if ($carrinho) {
    $ids = array_map('intval', array_keys($carrinho));
    $lista = implode(',', $ids);
    $resultado = $conexao->query("SELECT * FROM produtos WHERE id IN ($lista) AND ativo = 1");

    while ($produto = $resultado->fetch_assoc()) {
        $produto['quantidade'] = $carrinho[$produto['id']];
        $produto['total'] = $produto['preco'] * $produto['quantidade'];
        $subtotal += $produto['total'];
        $itens[] = $produto;
    }
}

$frete = $subtotal >= 250 || $subtotal === 0 ? 0 : 19.90;
$total = $subtotal + $frete;
$titulo = 'Seu carrinho | Shelby Story';
require 'header.php';
?>
<section class="pagina limite">
    <div class="cabecalho-pagina">
        <span class="etiqueta">Sua seleção</span>
        <h1>Carrinho de compras</h1>
        <p>Revise os itens antes de finalizar.</p>
    </div>

    <?php if (!$itens): ?>
        <div class="vazio">
            <h2>Seu carrinho está vazio</h2>
            <p>Explore a loja e encontre algo especial para você.</p>
            <a class="botao primario" href="index.php">Conhecer produtos</a>
        </div>
    <?php else: ?>
        <div class="carrinho-layout">
            <div class="lista-carrinho">
                <?php foreach ($itens as $item): ?>
                    <div class="item-carrinho">
                        <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>">
                        <div class="item-dados">
                            <h3><a href="produto.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['nome']) ?></a></h3>
                            <span><?= dinheiro($item['preco']) ?> por unidade</span>
                            <div class="acoes-item">
                                <form method="post">
                                    <input type="hidden" name="acao" value="atualizar">
                                    <input type="hidden" name="produto_id" value="<?= $item['id'] ?>">
                                    <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" min="1" max="<?= $item['estoque'] ?>">
                                    <button class="link-botao" type="submit">Atualizar</button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="acao" value="remover">
                                    <input type="hidden" name="produto_id" value="<?= $item['id'] ?>">
                                    <button class="link-botao remover" type="submit">Remover</button>
                                </form>
                            </div>
                        </div>
                        <strong><?= dinheiro($item['total']) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <aside class="resumo">
                <h2>Resumo</h2>
                <div><span>Subtotal</span><strong><?= dinheiro($subtotal) ?></strong></div>
                <div><span>Frete</span><strong><?= $frete > 0 ? dinheiro($frete) : 'Grátis' ?></strong></div>
                <div class="resumo-total"><span>Total</span><strong><?= dinheiro($total) ?></strong></div>
                <?php if ($subtotal < 250): ?><p>Faltam <?= dinheiro(250 - $subtotal) ?> para o frete grátis.</p><?php endif; ?>
                <a class="botao primario largura-total" href="checkout.php">Finalizar compra</a>
                <a class="continuar" href="index.php">Continuar comprando</a>
            </aside>
        </div>
    <?php endif; ?>
</section>
<?php require 'footer.php'; ?>
