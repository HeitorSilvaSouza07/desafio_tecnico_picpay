<?php
require 'config.php';

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = (float) str_replace(',', '.', $_POST['preco'] ?? 0);
    $estoque = max(0, (int) ($_POST['estoque'] ?? 0));
    $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
    $imagem = trim($_POST['imagem'] ?? '');

    if ($nome === '' || $descricao === '' || $preco <= 0 || $categoriaId <= 0 || !filter_var($imagem, FILTER_VALIDATE_URL)) {
        $erro = 'Preencha todos os campos com dados válidos.';
    } else {
        $consulta = $conexao->prepare("INSERT INTO produtos (categoria_id, nome, descricao, preco, estoque, imagem, ativo) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $consulta->bind_param('issdis', $categoriaId, $nome, $descricao, $preco, $estoque, $imagem);
        $consulta->execute();
        $mensagem = 'Produto cadastrado com sucesso.';
        $_POST = [];
    }
}

$categorias = $conexao->query("SELECT * FROM categorias ORDER BY nome");
$titulo = 'Cadastrar produto | Shelby Story';
require 'header.php';
?>
<section class="pagina limite admin-pagina">
    <div class="cabecalho-pagina">
        <span class="etiqueta">Painel simples</span>
        <h1>Cadastrar produto</h1>
        <p>Adicione um item ao catálogo da loja.</p>
    </div>
    <?php if ($mensagem): ?><div class="alerta sucesso"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?>
    <?php if ($erro): ?><div class="alerta erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <form class="formulario admin-form" method="post">
        <div class="campo largura-dupla"><label>Nome do produto<input name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"></label></div>
        <div class="campo"><label>Preço<input type="number" step="0.01" min="0.01" name="preco" required value="<?= htmlspecialchars($_POST['preco'] ?? '') ?>"></label></div>
        <div class="campo"><label>Estoque<input type="number" min="0" name="estoque" required value="<?= htmlspecialchars($_POST['estoque'] ?? '') ?>"></label></div>
        <div class="campo largura-dupla"><label>Categoria<select name="categoria_id" required><option value="">Selecione</option><?php while ($categoria = $categorias->fetch_assoc()): ?><option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option><?php endwhile; ?></select></label></div>
        <div class="campo largura-dupla"><label>URL da imagem<input type="url" name="imagem" required value="<?= htmlspecialchars($_POST['imagem'] ?? '') ?>" placeholder="https://..."></label></div>
        <div class="campo largura-dupla"><label>Descrição<textarea name="descricao" rows="5" required><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea></label></div>
        <button class="botao primario largura-dupla" type="submit">Cadastrar produto</button>
    </form>
</section>
<?php require 'footer.php'; ?>
