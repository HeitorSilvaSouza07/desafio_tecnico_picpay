<?php
require 'config.php';

$id = (int) ($_GET['id'] ?? 0);
$email = trim($_GET['email'] ?? '');
$consulta = $conexao->prepare("SELECT * FROM pedidos WHERE id = ? AND email = ?");
$consulta->bind_param('is', $id, $email);
$consulta->execute();
$pedido = $consulta->get_result()->fetch_assoc();
$titulo = 'Pedido confirmado | Shelby Story';
require 'header.php';
?>
<section class="pagina-centro limite">
    <?php if ($pedido): ?>
        <span class="etiqueta">Pedido recebido</span>
        <h1>Obrigado, <?= htmlspecialchars($pedido['nome_cliente']) ?>.</h1>
        <p>Seu pedido número <strong>#<?= str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) ?></strong> foi registrado com sucesso.</p>
        <div class="confirmacao">
            <div><span>Total</span><strong><?= dinheiro($pedido['total']) ?></strong></div>
            <div><span>Pagamento</span><strong><?= htmlspecialchars($pedido['pagamento']) ?></strong></div>
            <div><span>Status</span><strong><?= htmlspecialchars($pedido['status']) ?></strong></div>
        </div>
        <a class="botao primario" href="index.php">Voltar para a loja</a>
    <?php else: ?>
        <h1>Pedido não encontrado</h1>
        <p>Confira os dados informados e tente novamente.</p>
        <a class="botao primario" href="index.php">Voltar para a loja</a>
    <?php endif; ?>
</section>
<?php require 'footer.php'; ?>
