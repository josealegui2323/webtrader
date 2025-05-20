<?php
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Usuário não está logado'
    ]);
    exit();
}

try {
    // Verificar se os dados foram enviados
    if (!isset($_POST['valor']) || !isset($_FILES['comprovante'])) {
        throw new Exception('Dados incompletos');
    }

    $valor = floatval($_POST['valor']);
    
    // Validar valor do depósito
    if ($valor <= 0) {
        throw new Exception('O valor do depósito deve ser maior que zero');
    }

    // Processar upload do comprovante
    $comprovante = null;
    if ($_FILES['comprovante']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/comprovantes/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['comprovante']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('Tipo de arquivo não permitido. Use apenas JPG, PNG ou PDF');
        }

        $file_name = uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['comprovante']['tmp_name'], $file_path)) {
            throw new Exception('Erro ao fazer upload do comprovante');
        }

        $comprovante = $file_path;
    } else {
        throw new Exception('Erro no upload do comprovante');
    }

    // Inserir novo depósito no banco de dados
    $stmt = $pdo->prepare("
        INSERT INTO depositos 
        (usuario_id, valor, comprovante, status, data_deposito, rede) 
        VALUES (?, ?, ?, 'pendente', NOW(), 'TRC20')
    ");
    
    $stmt->execute([
        $_SESSION['usuario_id'],
        $valor,
        $comprovante
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Depósito registrado com sucesso! Aguarde a aprovação.'
    ]);

} catch(Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 