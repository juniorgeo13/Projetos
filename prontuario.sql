CREATE TABLE prontuario2025 (
    id INT PRIMARY KEY,
    Nomepaciente VARCHAR(255),
    cpf VARCHAR(20),
    data_nascimento DATE,
    peso DECIMAL(5,2),
    altura DECIMAL(4,2),
    vacinas_aplicadas TEXT,
    data_proxima_vacina DATE,
    data_proxima_consulta DATE,
    receita TEXT,
    observacoes TEXT,
    FOREIGN KEY (id) REFERENCES pacientecadastro(id) ON DELETE CASCADE
);
