<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.html");
    exit();
}

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $plano_id = $_POST["plano_id"];
    $valor_plano = $_POST["valor_plano"];
    $valor_deposito = $_POST["valor"];

    // Verifica se o usuário existe
    $sql = "SELECT id FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("Usuário não encontrado. Por favor, faça login novamente.");
    }
    $stmt->close();

    // Verifica se o valor do depósito é válido
    if ($valor_deposito < $valor_plano) {
        die("O valor do depósito deve ser igual ou maior que o valor do plano.");
    }

    // Processamento do arquivo de comprovante
    $comprovante = null;
    if (isset($_FILES['comprovante']) && $_FILES['comprovante']['error'] == 0) {
        $extensao = pathinfo($_FILES['comprovante']['name'], PATHINFO_EXTENSION);
        $extensoes_validas = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($extensao, $extensoes_validas)) {
            $nome_arquivo = uniqid() . '.' . $extensao;
            $diretorio = 'comprovantes/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }

            $caminho_completo = $diretorio . $nome_arquivo;
            if (move_uploaded_file($_FILES['comprovante']['tmp_name'], $caminho_completo)) {
                $comprovante = $caminho_completo;
            } else {
                die("Erro ao fazer upload do arquivo.");
            }
        } else {
            die("Formato de arquivo inválido. Apenas JPG, PNG, e PDF são permitidos.");
        }
    }

    // Inicia a transação
    $conn->begin_transaction();

    try {
        // Insere o depósito
        $sql = "INSERT INTO depositos (usuario_id, plano_id, valor, comprovante, status, data_deposito) VALUES (?, ?, ?, ?, 'pendente', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iids", $usuario_id, $plano_id, $valor_deposito, $comprovante);
        $stmt->execute();
        $deposito_id = $stmt->insert_id;
        $stmt->close();

        // Busca informações do plano
        $sql = "SELECT nome, taxa FROM planos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $plano_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $plano = $result->fetch_assoc();
        $stmt->close();

        // Atualiza ou insere o plano do usuário
        $sql = "INSERT INTO planos_adquiridos (usuario_id, plano, valor_investido, taxa, status, data_inicio) 
                VALUES (?, ?, ?, ?, 'pendente', NOW())
                ON DUPLICATE KEY UPDATE 
                plano = VALUES(plano),
                valor_investido = VALUES(valor_investido),
                taxa = VALUES(taxa),
                status = VALUES(status),
                data_inicio = NOW()";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdd", $usuario_id, $plano['nome'], $valor_deposito, $plano['taxa']);
        $stmt->execute();
        $stmt->close();

        // Confirma a transação
        $conn->commit();

        // Redireciona para a página de sucesso
        header("Location: plano.php?success=1");
        exit();

    } catch (Exception $e) {
        // Desfaz a transação em caso de erro
        $conn->rollback();
        die("Erro ao processar o depósito: " . $e->getMessage());
    }
} else {
    header("Location: plataforma.php");
    exit();
}
?> 