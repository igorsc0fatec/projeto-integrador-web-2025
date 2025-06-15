<?php
include_once '../model/dao.php';
include_once '../model/pedido-personalizado.php';

class ControllerPedidoPersonalizado
{
    private $dao;
    private $pedidoPersonalizado;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->pedidoPersonalizado = new PedidoPersonalizado();
    }

    public function addPedidoPersonalizado()
    {
        try {
            // Para todos os campos, verificamos se o valor foi enviado e se não está vazio
            $idMassa = isset($_POST['descMassa']) && !empty($_POST['descMassa']) ? (int) $_POST['descMassa'] : null;
            $idRecheio = isset($_POST['descRecheio']) && !empty($_POST['descRecheio']) ? (int) $_POST['descRecheio'] : null;
            $idCobertura = isset($_POST['descCobertura']) && !empty($_POST['descCobertura']) ? (int) $_POST['descCobertura'] : null;
            $idFormato = isset($_POST['descFormato']) && !empty($_POST['descFormato']) ? (int) $_POST['descFormato'] : null;
            $idDecoracao = isset($_POST['descDecoracao']) && !empty($_POST['descDecoracao']) ? (int) $_POST['descDecoracao'] : null;

            $this->pedidoPersonalizado->setIdPersonalizado($_POST['idPersonalizado']);
            $this->pedidoPersonalizado->setIdCliente($_SESSION['idCliente']);

            $valorTotal = $_POST['totalPrice'];
            $desconto = 0.00;
            $frete = 0.00;
            $peso_gramas = $_POST['peso'];
            $peso_kg = $peso_gramas / 1000; // Converte gramas para quilogramas

            // Montando a consulta SQL para inserção
            $sql = "INSERT INTO tb_pedido_personalizado (valor_total, desconto, peso, frete, id_forma_pagamento, 
            id_massa, id_recheio, id_cobertura, id_formato, id_decoracao, id_personalizado, id_cliente) 
            VALUES (
                '$valorTotal', '$desconto',  $peso_kg, '$frete', NULL, 
                " . ($idMassa !== null ? $idMassa : 'NULL') . ", 
                " . ($idRecheio !== null ? $idRecheio : 'NULL') . ", 
                " . ($idCobertura !== null ? $idCobertura : 'NULL') . ", 
                " . ($idFormato !== null ? $idFormato : 'NULL') . ", 
                " . ($idDecoracao !== null ? $idDecoracao : 'NULL') . ", 
                '{$this->pedidoPersonalizado->getIdPersonalizado()}', 
                '{$this->pedidoPersonalizado->getIdCliente()}'
            )";

            $result = $this->dao->execute($sql);

            if ($result) {
                $idPedidoPersonalizado = $this->dao->getData("SELECT id_pedido_personalizado FROM tb_pedido_personalizado 
                ORDER BY id_pedido_personalizado DESC LIMIT 1");
                $idPedidoPersonalizado = $idPedidoPersonalizado[0]['id_pedido_personalizado'];
                return $idPedidoPersonalizado;
            } else {
                echo "<script language='javascript' type='text/javascript'> 
                window.location.href='../view-cliente/pedir-personalizado.php'</script>";
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar pedido personalizado: " . $e->getMessage());
        }
    }

    public function viewPedidoPersonalizado($id)
    {
        try {
            $query = "SELECT 
                    tp.id_pedido_personalizado, tp.valor_total, tp.desconto, tp.data_pedido, tp.peso, tp.frete, tp.id_status,
                    ts.tipo_status, tp.id_endereco_cliente, tp.id_forma_pagamento, tp.id_cobertura, tp.id_decoracao, 
                    tp.id_formato, tp.id_massa, tp.id_recheio, tp.id_personalizado AS tp_id_personalizado, tp.id_cliente,
                    
                    tper.id_personalizado AS tper_id_personalizado, tper.nome_personalizado, tper.desc_personalizado,
                    tper.img_personalizado, tper.cobertura_ativa, tper.decoracao_ativa, tper.formato_ativa, 
                    tper.massa_ativa, tper.recheio_ativa, tper.personalizado_ativo, tper.limite_entrega, 
                    tper.id_tipo_produto, tper.id_confeitaria AS tper_id_confeitaria,
                    
                    tc.id_confeitaria AS tc_id_confeitaria, tc.nome_confeitaria, tc.cnpj_confeitaria,
                    tc.cep_confeitaria, tc.log_confeitaria, tc.num_local, tc.complemento,
                    tc.bairro_confeitaria, tc.cidade_confeitaria, tc.uf_confeitaria, tc.latitude, 
                    tc.longitude, tc.img_confeitaria, tc.id_usuario,

                    tf.forma_pagamento,
                    tec.*
                
                FROM tb_pedido_personalizado tp
                INNER JOIN tb_status ts ON tp.id_status = ts.id_status
                INNER JOIN tb_personalizado tper ON tp.id_personalizado = tper.id_personalizado
                INNER JOIN tb_confeitaria tc ON tper.id_confeitaria = tc.id_confeitaria
                LEFT JOIN tb_forma_pagamento tf ON tp.id_forma_pagamento = tf.id_forma_pagamento
                LEFT JOIN tb_endereco_cliente tec ON tp.id_endereco_cliente = tec.id_endereco_cliente
                WHERE tp.id_pedido_personalizado = $id";

            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar pedido personalizado: " . $e->getMessage());
        }
    }

    public function getPedidosPersonalizadosByCliente($idCliente)
    {
        try {
            $query = "SELECT 
            pp.*, 
            tper.*, 
            tc.*, 
            tr.*, 
            tf.*, 
            tm.*, 
            td.*, 
            ts.tipo_status 
          FROM tb_pedido_personalizado pp
          LEFT JOIN tb_personalizado tper ON pp.id_personalizado = tper.id_personalizado 
          LEFT JOIN tb_cobertura tc ON pp.id_cobertura = tc.id_cobertura
          LEFT JOIN tb_recheio tr ON pp.id_recheio = tr.id_recheio 
          LEFT JOIN tb_formato tf ON pp.id_formato = tf.id_formato
          LEFT JOIN tb_massa tm ON pp.id_massa = tm.id_massa 
          LEFT JOIN tb_decoracao td ON pp.id_decoracao = td.id_decoracao
          LEFT JOIN tb_status ts ON pp.id_status = ts.id_status
          WHERE pp.id_cliente = $idCliente";
            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar pedido personalizado do cliente: " . $e->getMessage());
        }
    }

    public function status()
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_status WHERE tipo_status != 'Cancelado pelo Cliente!'");
            return $result;
        } catch (Exception $e) {
            echo "Erro ao visualizar status: " . $e->getMessage();
        }
    }

    public function getPersonalizadosByConfeitaria()
    {
        try {
            $idConfeitaria = $this->dao->escape_string($_SESSION['idConfeitaria']);

            $query = "SELECT pp.*, tper.*, tc.*, tr.*, tf.*, tm.*, td.*, ts.tipo_status, tcli.* FROM tb_pedido_personalizado pp
            LEFT JOIN tb_personalizado tper ON pp.id_personalizado = tper.id_personalizado LEFT JOIN tb_cobertura tc ON pp.id_cobertura = tc.id_cobertura
            LEFT JOIN tb_recheio tr ON pp.id_recheio = tr.id_recheio LEFT JOIN tb_formato tf ON pp.id_formato = tf.id_formato
            LEFT JOIN tb_massa tm ON pp.id_massa = tm.id_massa LEFT JOIN tb_decoracao td ON pp.id_decoracao = td.id_decoracao
            LEFT JOIN tb_status ts ON pp.id_status = ts.id_status
            LEFT JOIN tb_cliente tcli ON pp.id_cliente = tcli.id_cliente WHERE tper.id_confeitaria = $idConfeitaria";

            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar pedido personalizado da confeitaria: " . $e->getMessage());
        }
    }

    public function mudarStatusPedidos()
    {
        if ((isset($_POST['alterar_status']) || isset($_POST['cancelar'])) && isset($_POST['idPedido'])) {
            $idPedido = $this->dao->escape_string($_POST['idPedido']);
            $novoStatus = $this->dao->escape_string($_POST['novo_status']);

            try {
                if ($novoStatus != 5) {
                    // 1. Verifica os dados do pedido
                    $pedido = $this->viewPedidoPersonalizado($idPedido);

                    // 2. Checa se está incompleto
                    if (empty($pedido[0]['id_endereco_cliente']) || empty($pedido[0]['id_forma_pagamento'])) {
                        return false;
                    }
                }

                // 3. Atualiza o status se os dados estão completos
                $sql = "UPDATE tb_pedido_personalizado SET id_status = {$novoStatus} WHERE id_pedido_personalizado = {$idPedido}";
                $stmt = $this->dao->execute($sql);

                if ($stmt) {
                    return true;
                }
            } catch (Exception $e) {
                throw new Exception("Erro ao alterar status pedido: " . $e->getMessage());
            }
        }

        return false;
    }

    public function buscarPedidosPersonalizados($idConfeitaria, $termoPesquisa)
    {
        try {
            $idConfeitariaEscaped = $this->dao->escape_string($idConfeitaria);
            $termoPesquisaEscaped = $this->dao->escape_string($termoPesquisa);
            $termoPesquisaLike = '%' . $termoPesquisaEscaped . '%';

            $query = "SELECT pp.*, tper.*, tc.*, tr.*, tf.*, tm.*, td.*, tcli.* FROM tb_pedido_personalizado pp
            LEFT JOIN tb_personalizado tper ON pp.id_personalizado = tper.id_personalizado LEFT JOIN tb_cobertura tc ON pp.id_cobertura = tc.id_cobertura
            LEFT JOIN tb_recheio tr ON pp.id_recheio = tr.id_recheio LEFT JOIN tb_formato tf ON pp.id_formato = tf.id_formato
            LEFT JOIN tb_massa tm ON pp.id_massa = tm.id_massa LEFT JOIN tb_decoracao td ON pp.id_decoracao = td.id_decoracao
            LEFT JOIN tb_cliente tcli ON pp.id_cliente = tcli.id_cliente WHERE tper.id_confeitaria = '$idConfeitariaEscaped'
            AND (pp.id_pedido_personalizado LIKE '$termoPesquisaLike' OR pp.status LIKE '$termoPesquisaLike')";

            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar pedido personalizado: " . $e->getMessage());
        }
    }

    public function updatePedidoPersonalizado()
    {
        try {
            $this->pedidoPersonalizado->setValorTotal($this->dao->escape_string($_POST['valor']));
            $this->pedidoPersonalizado->setDesconto($this->dao->escape_string($_POST['desconto']));
            $this->pedidoPersonalizado->setFrete($this->dao->escape_string($_POST['frete']));
            $this->pedidoPersonalizado->setIdFormaPagamento($_POST['idForma']);
            $this->pedidoPersonalizado->setIdPedidoPersonalizado($this->dao->escape_string($_POST['id']));
            $endereco = $this->dao->escape_string($_POST['endereco']);

            $query = "UPDATE tb_pedido_personalizado SET valor_total = {$this->pedidoPersonalizado->getValorTotal()}, 
            desconto = {$this->pedidoPersonalizado->getDesconto()}, frete = {$this->pedidoPersonalizado->getFrete()},
            id_endereco_cliente = {$endereco}, id_forma_pagamento = {$this->pedidoPersonalizado->getIdFormaPagamento()}
            WHERE id_pedido_personalizado = {$this->pedidoPersonalizado->getIdPedidoPersonalizado()}";

            $result = $this->dao->execute($query);

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar pedido personalizado: " . $e->getMessage());
        }
    }
}
