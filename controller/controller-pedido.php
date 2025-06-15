<?php
include_once '../model/dao.php';
include_once '../model/pedido.php';
include_once '../model/itens-pedido.php';
include_once '../model/pagamento.php';

class ControllerPedido
{
    private $dao;
    private $pedido;
    private $itensPedido;
    private $pagamento;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->pedido = new Pedido();
        $this->itensPedido = new ItensPedido();
        $this->pagamento = new Pagamento();
    }

    public function addPedido()
    {
        try {
            date_default_timezone_set('America/Sao_Paulo');

            $carrinho = $_SESSION['carrinho'];
            $valorTotal = 0;
            $frete = 0;

            foreach ($carrinho as $produto) {
                if (is_array($produto) && isset($produto['frete']) && isset($produto['valorProduto']) && isset($produto['quantidade'])) {
                    $frete = $produto['frete'];
                    $valorTotal += $produto['valorProduto'] * $produto['quantidade'];
                    $valorTotal = $valorTotal + $frete;
                }
            }

            $this->pedido->setValorTotal($valorTotal);
            $this->pedido->setDesconto(0);
            $this->pedido->setFrete($frete);
            $this->pedido->setIdCliente($this->dao->escape_string($_SESSION['idCliente']));
            $this->pedido->setIdFormaPagamento($this->dao->escape_string($_POST['pagamento']));
            $this->pedido->setIdEnderecoCliente($this->dao->escape_string($_POST['endereco']));

            $result = $this->dao->execute("INSERT INTO tb_pedido (valor_total, desconto, frete, id_endereco_cliente, 
            id_forma_pagamento, id_cliente) 
            VALUES ( {$this->pedido->getValorTotal()}, {$this->pedido->getDesconto()}, {$this->pedido->getFrete()},
            {$this->pedido->getIdEnderecoCliente()}, {$this->pedido->getIdFormaPagamento()}, {$this->pedido->getIdCliente()} )");

            if ($result) {
                $idPedido = $this->dao->getData("SELECT id_pedido FROM tb_pedido ORDER BY id_pedido DESC LIMIT 1");
                $idPedido = $idPedido[0]['id_pedido'];
                return $idPedido;
            }
        } catch (Exception $e) {
            echo "Erro ao fazer o pedido: " . $e->getMessage();
        }
    }

    public function addItensPedido($idPedido)
    {
        try {
            $carrinho = $_SESSION['carrinho'];
            $totalItens = count($carrinho);
            $count = 0;
            $cupomUtilizado = false;

            // Obtém o id_cupom dos dados do pedido se existir
            $idCupom = isset($_SESSION['dados_pedido']['id_cupom']) && $_SESSION['dados_pedido']['id_cupom'] !== null
                ? $this->dao->escape_string($_SESSION['dados_pedido']['id_cupom'])
                : 'NULL';

            foreach ($carrinho as $produto) {
                if (!isset($produto['idProduto'], $produto['quantidade'])) {
                    continue;
                }

                $this->itensPedido->setIdProduto($this->dao->escape_string($produto['idProduto']));
                $this->itensPedido->setIdPedido($this->dao->escape_string($idPedido));
                $this->itensPedido->setQuantidade($this->dao->escape_string($produto['quantidade']));

                $query = "INSERT INTO tb_itens_pedido (quantidade, id_produto, id_pedido, id_cupom) 
                  VALUES (
                      {$this->itensPedido->getQuantidade()},
                      {$this->itensPedido->getIdProduto()},
                      {$this->itensPedido->getIdPedido()},
                      {$idCupom})";

                $result = $this->dao->execute($query);

                if ($result) {
                    $count++;
                }
            }

            // Atualiza o cupom como usado se existir
            if ($idCupom !== 'NULL') {
                $this->marcarCupomComoUsado($idCupom);
                $cupomUtilizado = true;
            }

            if ($count == $totalItens) {
                unset($_SESSION['carrinho']);
                unset($_SESSION['dados_pedido']);
                unset($_SESSION['cupom_aplicado']);
                return true;
            } else {
                // Se falhou, desfaz a marcação do cupom (se tiver sido marcado)
                if ($cupomUtilizado) {
                    $this->desmarcarCupomComoUsado($idCupom);
                }
                return false;
            }
        } catch (Exception $e) {
            error_log("Erro ao adicionar item ao pedido: " . $e->getMessage());
            return false;
        }
    }

    private function marcarCupomComoUsado($idCupom)
    {
        $query = "UPDATE tb_cupom SET cupom_usado = TRUE WHERE id_cupom = {$idCupom}";
        return $this->dao->execute($query);
    }

    private function desmarcarCupomComoUsado($idCupom)
    {
        $query = "UPDATE tb_cupom SET cupom_usado = FALSE WHERE id_cupom = {$idCupom}";
        return $this->dao->execute($query);
    }

    public function formaPagamento()
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_forma_pagamento");
            return $result;
        } catch (Exception $e) {
            echo "Erro ao visualizar produto: " . $e->getMessage();
        }
    }

    public function viewPedido($idPedido)
    {
        try {
            // Query principal do pedido
            $pedidoQuery = "SELECT 
                p.id_pedido,
                p.valor_total,
                p.desconto,
                p.data_pedido,
                p.id_status,
                ts.tipo_status, -- <-- aqui pega o nome do status
                p.frete as valor_frete,
                f.forma_pagamento,
                f.descricao as descricao_pagamento,
                ec.cep_cliente as cep,
                ec.log_cliente as logradouro,
                ec.num_local as numero,
                ec.complemento,
                ec.bairro_cliente as bairro,
                ec.cidade_cliente as cidade,
                ec.uf_cliente as estado
            FROM tb_pedido p 
            INNER JOIN tb_forma_pagamento f ON p.id_forma_pagamento = f.id_forma_pagamento
            INNER JOIN tb_endereco_cliente ec ON p.id_endereco_cliente = ec.id_endereco_cliente
            INNER JOIN tb_status ts ON p.id_status = ts.id_status
            WHERE p.id_pedido = {$this->dao->escape_string($idPedido)}";

            $pedidoResult = $this->dao->getData($pedidoQuery);

            // Query dos itens do pedido com informações do produto e confeitaria
            $itensQuery = "SELECT 
                i.id_itens_pedido,
                i.quantidade,
                pr.id_produto,
                pr.nome_produto,
                pr.valor_produto as preco_unitario,
                pr.img_produto,
                cf.nome_confeitaria,
                cf.id_confeitaria
            FROM tb_itens_pedido i 
            INNER JOIN tb_produto pr ON i.id_produto = pr.id_produto
            INNER JOIN tb_confeitaria cf ON pr.id_confeitaria = cf.id_confeitaria
            WHERE i.id_pedido = {$this->dao->escape_string($idPedido)}";

            $itensResult = $this->dao->getData($itensQuery);

            return [
                'pedido' => $pedidoResult,
                'itens' => $itensResult
            ];
        } catch (Exception $e) {
            throw new Exception("Erro ao visualizar pedido: " . $e->getMessage());
        }
    }

    public function getPedidosByCliente($idCliente)
    {
        try {
            // Consulta principal para buscar pedidos do cliente
            $pedidoQuery = "
            SELECT 
                p.id_pedido,
                p.valor_total,
                p.desconto,
                p.data_pedido,
                ts.tipo_status,
                p.frete,
                p.id_endereco_cliente,
                p.id_forma_pagamento,
                p.id_cliente,
                c.nome_cliente,
                c.cpf_cliente,
                f.forma_pagamento,
                f.descricao AS descricao_pagamento,
                ec.*
            FROM tb_pedido p
            INNER JOIN tb_cliente c ON p.id_cliente = c.id_cliente
            INNER JOIN tb_forma_pagamento f ON p.id_forma_pagamento = f.id_forma_pagamento
            INNER JOIN tb_endereco_cliente ec ON p.id_endereco_cliente = ec.id_endereco_cliente
            INNER JOIN tb_status ts ON p.id_status = ts.id_status
            WHERE p.id_cliente = {$this->dao->escape_string($idCliente)}
            ORDER BY p.id_pedido DESC
        ";

            $pedidosResult = $this->dao->getData($pedidoQuery);

            $pedidos = [];

            foreach ($pedidosResult as $pedido) {
                // Consulta para buscar os itens do pedido atual
                $itensQuery = "
                SELECT 
                    i.*, 
                    pr.nome_produto, 
                    pr.img_produto
                FROM tb_itens_pedido i
                INNER JOIN tb_produto pr ON i.id_produto = pr.id_produto
                WHERE i.id_pedido = {$this->dao->escape_string($pedido['id_pedido'])}
            ";

                $itensResult = $this->dao->getData($itensQuery);

                $pedidos[] = [
                    'pedido' => $pedido,
                    'itens' => $itensResult
                ];
            }

            return $pedidos;
        } catch (Exception $e) {
            echo "Erro ao visualizar pedidos: " . $e->getMessage();
            return [];
        }
    }

    public function mudarStatusPedidos()
    {
        if ((isset($_POST['alterar_status']) || isset($_POST['cancelar'])) && isset($_POST['idPedido'])) {
            $idPedido = $this->dao->escape_string($_POST['idPedido']);
            $novoStatus = $this->dao->escape_string($_POST['novo_status']);

            try {
                $sql = "UPDATE tb_pedido SET id_status = {$novoStatus} WHERE id_pedido = {$idPedido}";
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

    public function getPedidosConfeitaria($idConfeitaria)
    {
        try {
            $idConfeitariaEscaped = $this->dao->escape_string($idConfeitaria);

            $query = "
            SELECT DISTINCT
        tp.*,
        tfp.*,
        tc.*,
        tec.*,
        tip.quantidade,
        tip.id_itens_pedido,
        tip.id_produto,
        tip.id_cupom,
        tpr.*,
        tcu.*,
        ts.tipo_status
    FROM 
        tb_pedido tp
    JOIN 
        tb_forma_pagamento tfp ON tp.id_forma_pagamento = tfp.id_forma_pagamento
    JOIN 
        tb_cliente tc ON tp.id_cliente = tc.id_cliente
    JOIN 
        tb_endereco_cliente tec ON tp.id_endereco_cliente = tec.id_endereco_cliente
    JOIN 
        tb_itens_pedido tip ON tp.id_pedido = tip.id_pedido
    JOIN 
        tb_produto tpr ON tip.id_produto = tpr.id_produto
    LEFT JOIN 
        tb_cupom tcu ON tip.id_cupom = tcu.id_cupom
    JOIN 
        tb_status ts ON tp.id_status = ts.id_status
    WHERE 
        tpr.id_confeitaria = '$idConfeitariaEscaped'
            ";

            $result = $this->dao->getData($query);

            return $result;
        } catch (Exception $e) {
            echo "Erro ao obter pedidos da confeitaria: " . $e->getMessage();
            return [];
        }
    }

    public function buscarPedidosConfeitaria($idConfeitaria, $termo)
    {
        try {
            $idConfeitariaEscaped = $this->dao->escape_string($idConfeitaria);
            $termoEscaped = $this->dao->escape_string($termo);

            $query = "
            SELECT DISTINCT
                tp.*,
                tfp.*,
                tc.*,
                tec.*,
                tip.quantidade,
                tip.id_itens_pedido,
                tip.id_produto,
                tip.id_cupom,
                tpr.*,
                tcu.*,
                ts.tipo_status
            FROM 
                tb_pedido tp
            JOIN 
                tb_forma_pagamento tfp ON tp.id_forma_pagamento = tfp.id_forma_pagamento
            JOIN 
                tb_cliente tc ON tp.id_cliente = tc.id_cliente
            JOIN 
                tb_endereco_cliente tec ON tp.id_endereco_cliente = tec.id_endereco_cliente
            JOIN 
                tb_itens_pedido tip ON tp.id_pedido = tip.id_pedido
            JOIN 
                tb_produto tpr ON tip.id_produto = tpr.id_produto
            LEFT JOIN 
                tb_cupom tcu ON tip.id_cupom = tcu.id_cupom
                JOIN 
        tb_status ts ON tp.id_status = ts.id_status
            WHERE 
                tpr.id_confeitaria = '$idConfeitariaEscaped' AND ( tp.id_pedido LIKE '%$termoEscaped%' OR tp.status LIKE '%$termoEscaped%' ) 
            ";

            $result = $this->dao->getData($query);

            return $result;
        } catch (Exception $e) {
            echo "Erro ao buscar pedidos da confeitaria: " . $e->getMessage();
            return [];
        }
    }

    public function addCarrinho()
    {
        try {
            $id = $_POST['id'];
            $sql = $this->dao->getData("SELECT p.*, c.id_usuario FROM tb_produto p INNER JOIN 
            tb_confeitaria c ON p.id_confeitaria = c.id_confeitaria WHERE p.id_produto = $id");

            if (!isset($_SESSION['carrinho'])) {
                $_SESSION['carrinho'] = [];
            } else if (!empty($_SESSION['carrinho'])) {
                $confeitariaAtual = $_SESSION['carrinho'][0]['idConfeitaria'];
                if ($confeitariaAtual != $sql[0]['id_confeitaria']) {
                    return 'id_diferente';
                }
            }

            $produtoExistente = false;
            foreach ($_SESSION['carrinho'] as &$item) {
                if ($item['idProduto'] == $id) {
                    $item['quantidade'] += 1;
                    return 'add';
                    if ($item['quantidade'] > 5) {
                        $item['quantidade'] = 5; // Limita a quantidade máxima a 5
                    }
                    $produtoExistente = true;
                    break;
                }
            }

            if (!$produtoExistente) {
                $produto = [
                    'idProduto' => $sql[0]['id_produto'],
                    'nomeProduto' => $sql[0]['nome_produto'],
                    'descProduto' => $sql[0]['desc_produto'],
                    'imgProduto' => $sql[0]['img_produto'],
                    'valorProduto' => $sql[0]['valor_produto'],
                    'frete' => $sql[0]['frete'],
                    'quantidade' => 1,
                    'idConfeitaria' => $sql[0]['id_confeitaria'],
                    'idUsuario' => $sql[0]['id_usuario']
                ];
                $_SESSION['carrinho'][] = $produto;
                return 'add';
            } else {
                return 'no add';
            }
        } catch (Exception $e) {
            echo "Erro ao adicionar dados ao carrinho: " . $e->getMessage();
            return false;
        }
    }

    public function gerarCupomSeNecessario($idUsuario)
    {
        try {
            // Obtém a data atual
            $dataAtual = new DateTime();
            $anoAtual = $dataAtual->format('Y');
            $dataFormatada = $dataAtual->format('Y-m-d');

            // Obtém o nascimento do cliente
            $query = "SELECT data_nasc FROM tb_cliente WHERE id_usuario = '$idUsuario'";
            $resultado = $this->dao->getData($query);
            if (empty($resultado)) {
                return false;
            }
            $nascCliente = new DateTime($resultado[0]['data_nasc']);
            $nascCliente->setDate($anoAtual, $nascCliente->format('m'), $nascCliente->format('d'));

            // Obtém os feriados nacionais
            $feriadosJson = file_get_contents("https://brasilapi.com.br/api/feriados/v1/{$anoAtual}");
            $feriados = json_decode($feriadosJson, true);

            // Verifica se hoje é aniversário do cliente ou feriado
            $tituloCupom = null;
            $mensagem = null;
            $descCupom = null;
            if ($dataFormatada == $nascCliente->format('Y-m-d')) {
                $tituloCupom = "Cupom de Aniversário";
                $mensagem = "Feliz Aniversário!";
                $descCupom = "Aniversario5";
            } else {
                foreach ($feriados as $feriado) {
                    if ($dataFormatada == $feriado['date']) {
                        $tituloCupom = "Cupom de " . $feriado['name'];
                        $mensagem = "Feliz Dia de " . $feriado['name'] . "!";
                        $descCupom = $feriado['name'] . "5";
                        break;
                    }
                }
            }

            // Verifica se o cupom já foi gerado
            $queryVerifica = "SELECT id_cupom FROM tb_cupom WHERE id_usuario = '$idUsuario' AND desc_cupom = '$descCupom'";
            $existeCupom = $this->dao->getData($queryVerifica);
            if (!empty($existeCupom)) {
                return false; // Já existe um cupom com essas características
            }

            // Define validade para 7 dias após a criação
            $dataValidade = (clone $dataAtual)->modify('+7 days')->format('Y-m-d H:i:s'); // Including time for more precision
            // Insere o cupom na base de dados
            $queryInsercao = "INSERT INTO tb_cupom (titulo_cupom, mensagem, desc_cupom, porcen_desconto, data_validade, id_usuario) 
            VALUES ('$tituloCupom', '$mensagem', '$descCupom', 5, '$dataValidade', '$idUsuario')";
            return $this->dao->execute($queryInsercao);
        } catch (Exception $e) {
            error_log("Erro ao gerar cupom: " . $e->getMessage());
            return false;
        }
    }

    public function listarCupons($idUsuario)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_cupom WHERE id_usuario = '$idUsuario'");
            return $result;
        } catch (Exception $e) {
            echo "Erro ao visualizar cupons: " . $e->getMessage();
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

    public function validarCupom($codigoCupom)
    {
        try {
            if (isset($_SESSION['dados_pedido']['id_cupom'])) {
                return json_encode([
                    "status" => "error",
                    "message" => "Este cupom já foi utilizado em um pedido"
                ]);
            }

            if (empty($codigoCupom)) {
                return json_encode(["status" => "error", "message" => "Código do cupom não pode estar vazio."]);
            }

            // Verifica se já existe um cupom aplicado na sessão
            if (isset($_SESSION['cupom_aplicado'])) {
                return json_encode(["status" => "error", "message" => "Você já aplicou um cupom."]);
            }

            $codigoCupom = $this->dao->escape_string($codigoCupom);

            // Consulta simplificada - verifique os campos reais da sua tabela tb_cupom
            $query = "SELECT id_cupom, desc_cupom, data_validade, porcen_desconto 
                      FROM tb_cupom 
                      WHERE desc_cupom = '$codigoCupom' AND cupom_usado != 1";

            $result = $this->dao->getData($query);

            if (empty($result)) {
                return json_encode(["status" => "error", "message" => "Cupom inválido."]);
            }

            $cupom = $result[0];
            $dataAtual = date('Y-m-d H:i:s');

            if ($cupom['data_validade'] < $dataAtual) {
                return json_encode(["status" => "error", "message" => "Cupom expirado."]);
            }

            // Calcula o total dos produtos no carrinho
            $totalProdutos = 0;
            if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
                foreach ($_SESSION['carrinho'] as $item) {
                    if (isset($item['valorProduto'], $item['quantidade'])) {
                        $totalProdutos += $item['valorProduto'] * $item['quantidade'];
                    }
                }
            }

            // Calcula o desconto (assumindo que todos os cupons são percentuais)
            $desconto = $totalProdutos * ($cupom['porcen_desconto'] / 100);
            $mensagem = "Cupom aplicado! Desconto de " . $cupom['porcen_desconto'] . "%";

            // Armazena na sessão
            $_SESSION['cupom_aplicado'] = [
                'codigo' => $codigoCupom,
                'desconto' => $desconto,
                'id_cupom' => $cupom['id_cupom'],
                'porcen_desconto' => $cupom['porcen_desconto']
            ];

            return json_encode([
                "status" => "success",
                "message" => $mensagem,
                "desconto" => number_format($desconto, 2, '.', ''),
                "porcen_desconto" => $cupom['porcen_desconto'],
                "id_cupom" => $cupom['id_cupom']
            ]);
        } catch (Exception $e) {
            error_log("Erro ao validar cupom: " . $e->getMessage());
            return json_encode([
                "status" => "error",
                "message" => "Erro ao processar cupom. Detalhes: " . $e->getMessage()
            ]);
        }
    }
}
