<?php
require_once 'verificar_super_admin.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Super Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Painel do Super Administrador</h1>
        
        <!-- Navegação -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="#depositos" data-bs-toggle="tab">Depósitos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#planos" data-bs-toggle="tab">Planos</a>
            </li>
        </ul>

        <!-- Conteúdo -->
        <div class="tab-content">
            <!-- Seção de Depósitos -->
            <div class="tab-pane fade show active" id="depositos">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Gerenciar Depósitos</h5>
                    </div>
                    <div class="card-body">
                        <table id="tabela_depositos" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuário</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once 'conexao.php';
                                $sql = "SELECT d.id, u.nome, d.valor, d.status, d.data_deposito 
                                        FROM depositos d 
                                        JOIN usuarios u ON d.id_usuario = u.id 
                                        ORDER BY d.data_deposito DESC";
                                $result = $conn->query($sql);
                                
                                while ($deposito = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $deposito['id'] . "</td>";
                                    echo "<td>" . $deposito['nome'] . "</td>";
                                    echo "<td>R$ " . number_format($deposito['valor'], 2, ',', '.') . "</td>";
                                    echo "<td><span class='badge bg-" . ($deposito['status'] == 'pendente' ? 'warning' : ($deposito['status'] == 'aprovado' ? 'success' : 'danger')) . "'>" . ucfirst($deposito['status']) . "</span></td>";
                                    echo "<td>" . date('d/m/Y H:i', strtotime($deposito['data_deposito'])) . "</td>";
                                    echo "<td>";
                                    if ($deposito['status'] == 'pendente') {
                                        echo "<button class='btn btn-sm btn-success' onclick='aprovarDeposito(" . $deposito['id'] . ")'>Aprovar</button>";
                                        echo " <button class='btn btn-sm btn-danger' onclick='reprovarDeposito(" . $deposito['id'] . ")'>Reprovar</button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Seção de Planos -->
            <div class="tab-pane fade" id="planos">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Gerenciar Planos</h5>
                    </div>
                    <div class="card-body">
                        <table id="tabela_planos" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuário</th>
                                    <th>Plano</th>
                                    <th>Status</th>
                                    <th>Data Início</th>
                                    <th>Data Fim</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT p.id, u.nome, pl.nome as nome_plano, p.status, p.data_inicio, p.data_fim 
                                        FROM planos p 
                                        JOIN usuarios u ON p.id_usuario = u.id 
                                        JOIN planos_disponiveis pl ON p.id_plano = pl.id 
                                        ORDER BY p.data_inicio DESC";
                                $result = $conn->query($sql);
                                
                                while ($plano = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $plano['id'] . "</td>";
                                    echo "<td>" . $plano['nome'] . "</td>";
                                    echo "<td>" . $plano['nome_plano'] . "</td>";
                                    echo "<td><span class='badge bg-" . ($plano['status'] == 'ativo' ? 'success' : 'secondary') . "'>" . ucfirst($plano['status']) . "</span></td>";
                                    echo "<td>" . date('d/m/Y', strtotime($plano['data_inicio'])) . "</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($plano['data_fim'])) . "</td>";
                                    echo "<td>";
                                    if ($plano['status'] == 'inativo') {
                                        echo "<button class='btn btn-sm btn-success' onclick='ativarPlano(" . $plano['id'] . ")'>Ativar</button>";
                                    } else {
                                        echo "<button class='btn btn-sm btn-danger' onclick='desativarPlano(" . $plano['id'] . ")'>Desativar</button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabela_depositos').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                }
            });
            $('#tabela_planos').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                }
            });
        });

        function aprovarDeposito(id) {
            if (confirm('Tem certeza que deseja aprovar este depósito?')) {
                window.location.href = 'aprovar_deposito.php?id=' + id;
            }
        }

        function reprovarDeposito(id) {
            if (confirm('Tem certeza que deseja reprovar este depósito?')) {
                window.location.href = 'reprovar_deposito.php?id=' + id;
            }
        }

        function ativarPlano(id) {
            if (confirm('Tem certeza que deseja ativar este plano?')) {
                window.location.href = 'ativar_plano.php?id=' + id;
            }
        }

        function desativarPlano(id) {
            if (confirm('Tem certeza que deseja desativar este plano?')) {
                window.location.href = 'desativar_plano.php?id=' + id;
            }
        }
    </script>
</body>
</html>
