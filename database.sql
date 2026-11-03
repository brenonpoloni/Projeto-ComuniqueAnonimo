CREATE DATABASE IF NOT EXISTS comunique_anonimo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE comunique_anonimo;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    empresa VARCHAR(120) NOT NULL,
    cargo VARCHAR(120) NOT NULL,
    setor VARCHAR(120) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_conta ENUM('usuario', 'tecnico') NOT NULL DEFAULT 'usuario',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    ultimo_acesso DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE denuncias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    protocolo VARCHAR(30) NOT NULL UNIQUE,
    tipo_ocorrencia VARCHAR(50) NOT NULL,
    empresa_setor VARCHAR(200) NOT NULL,
    endereco_local VARCHAR(200) NOT NULL,
    descricao TEXT NOT NULL,
    anexo VARCHAR(255) NULL,
    status ENUM('pendente', 'em_andamento', 'concluida') NOT NULL DEFAULT 'pendente',
    nivel_gravidade ENUM('grave', 'intermediario', 'estavel') NOT NULL DEFAULT 'intermediario',
    responsavel_nome VARCHAR(150) NULL,
    responsavel_email VARCHAR(150) NULL,
    responsavel_setor VARCHAR(120) NULL,
    mensagem_encaminhamento TEXT NULL,
    encaminhado_em DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE suporte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assunto VARCHAR(50) NOT NULL,
    mensagem TEXT NOT NULL,
    email VARCHAR(150) NULL,
    status ENUM('aberto', 'respondido') NOT NULL DEFAULT 'aberto',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_denuncias_status ON denuncias(status);
CREATE INDEX idx_suporte_status ON suporte(status);
