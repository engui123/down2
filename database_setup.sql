-- Valify - Mercado Online
-- Script de criação do banco de dados

CREATE DATABASE IF NOT EXISTS valify CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE valify;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  tipo ENUM('cliente', 'empresa', 'admin') NOT NULL DEFAULT 'cliente',
  cnpj VARCHAR(14) NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(50) NULL,
  nome VARCHAR(255) NOT NULL,
  categoria VARCHAR(100) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  estoque INT NOT NULL DEFAULT 0,
  imagem TEXT NULL,
  descricao TEXT NULL,
  usuario_id INT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_categoria (categoria),
  INDEX idx_nome (nome),
  INDEX idx_usuario (usuario_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir usuário admin padrão (senha: admin123)
INSERT INTO usuarios (nome, email, senha, tipo) VALUES 
('Administrador', 'admin@valify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE nome=nome;

-- Inserir produtos de exemplo
INSERT INTO produtos (codigo, nome, categoria, preco, estoque, imagem) VALUES
('7891234567890', 'Banana Nanica 1kg', 'Hortifruti', 7.99, 120, 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?q=80&w=400&auto=format&fit=crop'),
('7891234567891', 'Leite Integral 1L', 'Laticínios', 4.89, 85, 'https://images.unsplash.com/photo-1580910051074-3eb694886505?q=80&w=400&auto=format&fit=crop'),
('7891234567892', 'Arroz Tipo 1 5kg', 'Mercearia', 24.90, 60, 'https://images.unsplash.com/photo-1615486363876-cc0ced6b2d3a?q=80&w=400&auto=format&fit=crop'),
('7891234567893', 'Refrigerante Cola 2L', 'Bebidas', 7.49, 200, 'https://images.unsplash.com/photo-1554866585-cd94860890b7?q=80&w=400&auto=format&fit=crop'),
('7891234567894', 'Queijo Mussarela 500g', 'Laticínios', 22.50, 45, 'https://images.unsplash.com/photo-1604908176997-43163f7f2a8a?q=80&w=400&auto=format&fit=crop'),
('7891234567895', 'Tomate Italiano 500g', 'Hortifruti', 5.99, 140, 'https://images.unsplash.com/photo-1546470427-eae79b8f4275?q=80&w=400&auto=format&fit=crop'),
('7891234567896', 'Café Torrado e Moído 500g', 'Mercearia', 17.90, 100, 'https://images.unsplash.com/photo-1509043759401-136742328bb3?q=80&w=400&auto=format&fit=crop'),
('7891234567897', 'Água Mineral 1.5L', 'Bebidas', 2.99, 300, 'https://images.unsplash.com/photo-1587202372775-98927b5f2ee9?q=80&w=400&auto=format&fit=crop')
ON DUPLICATE KEY UPDATE nome=nome;

SELECT 'Banco de dados criado com sucesso!' as status;
