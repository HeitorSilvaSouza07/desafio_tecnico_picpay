CREATE DATABASE IF NOT EXISTS violeta_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE violeta_store;

CREATE TABLE categorias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE produtos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT UNSIGNED NULL,
    nome VARCHAR(160) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT UNSIGNED NOT NULL DEFAULT 0,
    imagem VARCHAR(500) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE pedidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_cliente VARCHAR(160) NOT NULL,
    email VARCHAR(180) NOT NULL,
    telefone VARCHAR(30) NOT NULL,
    cep VARCHAR(20) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    cidade VARCHAR(120) NOT NULL,
    estado CHAR(2) NOT NULL,
    pagamento VARCHAR(50) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    frete DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Recebido',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pedido_itens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NULL,
    nome_produto VARCHAR(160) NOT NULL,
    quantidade INT UNSIGNED NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO categorias (nome) VALUES ('Casa'), ('Acessórios'), ('Papelaria');

INSERT INTO produtos (categoria_id, nome, descricao, preco, estoque, imagem) VALUES
(1, 'Vela Lavanda', 'Vela aromática de lavanda em pote de vidro fosco. Perfume suave e queima aproximada de 30 horas.', 59.90, 18, 'https://images.unsplash.com/photo-1603006905003-be475563bc59?auto=format&fit=crop&w=900&q=80'),
(1, 'Manta Aurora', 'Manta macia em tom lilás para sofá ou cama. Tecido leve, confortável e fácil de lavar.', 149.90, 12, 'https://images.unsplash.com/photo-1583845112203-454c2254edce?auto=format&fit=crop&w=900&q=80'),
(2, 'Bolsa Essencial', 'Bolsa compacta para acompanhar a rotina. Possui alça regulável e fechamento seguro.', 189.90, 9, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=80'),
(2, 'Óculos Horizonte', 'Óculos de sol com armação leve e desenho atemporal para diferentes estilos.', 119.90, 15, 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?auto=format&fit=crop&w=900&q=80'),
(3, 'Caderno Ideias', 'Caderno de capa dura com 160 páginas pautadas e acabamento resistente.', 39.90, 30, 'https://images.unsplash.com/photo-1517842645767-c639042777db?auto=format&fit=crop&w=900&q=80'),
(3, 'Planner Semanal', 'Planner sem data para organizar compromissos, prioridades e hábitos da semana.', 49.90, 24, 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?auto=format&fit=crop&w=900&q=80');
