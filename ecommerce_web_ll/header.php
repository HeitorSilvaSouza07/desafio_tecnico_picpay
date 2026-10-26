<?php
if (!isset($titulo)) {
    $titulo = 'Shelby Story';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topo">
        <div class="limite barra">
            <a class="marca" href="index.php">Shelby Story</a>
            <nav>
                <a href="index.php">Loja</a>
                <a href="admin.php">Cadastrar produto</a>
                <a class="botao-carrinho" href="carrinho.php">Carrinho <span><?= quantidadeCarrinho() ?></span></a>
            </nav>
        </div>
    </header>
    <main>
