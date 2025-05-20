<?php
require_once 'conexao.php';

try {
    // Primeiro, limpa a tabela de planos existentes
    $pdo->exec("TRUNCATE TABLE planos");
    
    // Array com os planos disponíveis
    $planos = [
        [
            'nome' => 'Plano Starter',
            'descricao' => 'Ideal para iniciantes no mercado de trading. Inclui acesso básico às ferramentas de análise.',
            'valor' => 99.90,
            'duracao_dias' => 30
        ],
        [
            'nome' => 'Plano Professional',
            'descricao' => 'Para traders intermediários. Inclui ferramentas avançadas de análise e suporte prioritário.',
            'valor' => 199.90,
            'duracao_dias' => 30
        ],
        [
            'nome' => 'Plano Expert',
            'descricao' => 'Para traders experientes. Acesso completo a todas as ferramentas, suporte VIP e análises exclusivas.',
            'valor' => 299.90,
            'duracao_dias' => 30
        ],
        [
            'nome' => 'Plano Master',
            'descricao' => 'Nossa solução mais completa. Inclui mentoria personalizada, sinais exclusivos e acesso a todas as funcionalidades.',
            'valor' => 499.90,
            'duracao_dias' => 30
        ]
    ];
    
    // Prepara a query de inserção
    $stmt = $pdo->prepare("
        INSERT INTO planos (nome, descricao, valor, duracao_dias)
        VALUES (:nome, :descricao, :valor, :duracao_dias)
    ");
    
    // Insere cada plano
    foreach ($planos as $plano) {
        $stmt->execute($plano);
    }
    
    echo "Planos inseridos com sucesso!";
    
} catch(PDOException $e) {
    echo "Erro ao inserir planos: " . $e->getMessage();
}
?> 