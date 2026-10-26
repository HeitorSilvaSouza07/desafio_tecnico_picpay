<?php
require 'config.php';

$id = (int) ($_GET['id'] ?? 0);
$consulta = $conexao->prepare("SELECT produtos.*, categorias.nome AS categoria_nome FROM produtos LEFT JOIN categorias ON categorias.id = produtos.categoria_id WHERE produtos.id = ? AND produtos.ativo = 1");
$consulta->bind_param('i', $id);
$consulta->execute();
$produto = $consulta->get_result()->fetch_assoc();

if (!$produto) {
    http_response_code(404);
    $titulo = 'Produto não encontrado';
    require 'header.php';
    echo '<section class="pagina-centro limite"><h1>Produto não encontrado</h1><p>O item que você tentou acessar não está disponível.</p><a class="botao primario" href="index.php">Voltar para a loja</a></section>';
    require 'footer.php';
    exit;
}

$titulo = $produto['nome'] . ' | Shelby Story';
require 'header.php';
?>
<section class="detalhe limite">
    <div class="detalhe-imagem"><img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>"></div>
    <div class="detalhe-info">
        <a class="voltar" href="index.php">Voltar para a loja</a>
        <span class="etiqueta"><?= htmlspecialchars($produto['categoria_nome'] ?? 'Geral') ?></span>
        <h1><?= htmlspecialchars($produto['nome']) ?></h1>
        <strong class="preco-grande"><?= dinheiro($produto['preco']) ?></strong>
        <p><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
        <p class="estoque"><?= $produto['estoque'] > 0 ? $produto['estoque'] . ' unidades em estoque' : 'Produto esgotado' ?></p>
        <?php if ($produto['estoque'] > 0): ?>
            <form class="comprar" action="carrinho.php" method="post">
                <input type="hidden" name="acao" value="adicionar">
                <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                <label>Quantidade<input type="number" name="quantidade" value="1" min="1" max="<?= $produto['estoque'] ?>"></label>
                <button class="botao primario" type="submit">Adicionar ao carrinho</button>
            </form>
        <?php endif; ?>
        <div class="detalhe-vantagens">
            <div><strong>Pagamento</strong><span>Pix ou cartão na entrega</span></div>
            <div><strong>Entrega</strong><span>Calculada após o pedido</span></div>
        </div>
    </div>
</section>
<?php require 'footer.php'; ?>
