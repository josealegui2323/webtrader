<?php
function verificarDepositoAprovado($usuario_id) {
    global $conn;
    try {
        $sql = "SELECT status FROM depositos WHERE usuario_id = ? AND status = 'aprovado' ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() !== false;
    } catch(Exception $e) {
        error_log("Erro ao verificar depósito: " . $e->getMessage());
        return false;
    }
}

function ativarBot($usuario_id, $plano_id) {
    global $conn;
    try {
        $sql = "INSERT INTO bots_ativos (usuario_id, plano_id, status) VALUES (?, ?, 'ativo')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $plano_id);
        return $stmt->execute();
    } catch(Exception $e) {
        error_log("Erro ao ativar bot: " . $e->getMessage());
        return false;
    }
}
?>
