<?php
include_once '../model/dao.php';
include_once '../model/produto.php';

class ControllerProduto
{
    private $dao;
    private $produto;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->produto = new Produto();
    }

    public function addProduto()
    {
        try {
            $this->produto->setNomeProduto($this->dao->escape_string($_POST['nomeProduto']));
            $this->produto->setDescProduto($this->dao->escape_string($_POST['descProduto']));
            $this->produto->setValorProduto($this->dao->escape_string($_POST['valorProduto']));
            $this->produto->setFrete($this->dao->escape_string($_POST['frete']));
            $this->produto->setIdTipoProduto($this->dao->escape_string($_POST['tiposProduto']));
            $this->produto->setDistanciaMaxima($this->dao->escape_string($_POST['distanciaMaxima']));

            $uploadDir = 'img/img-produto/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imagem_url = null;
            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $imagem_tmp = $_FILES['img']['tmp_name'];
                $imagem_nome = basename($_FILES['img']['name']);
                $imagem_path = $uploadDir . $imagem_nome;

                if (move_uploaded_file($imagem_tmp, $imagem_path)) {
                    $imagem_url = $this->dao->escape_string($imagem_path);
                } else {
                    throw new Exception("Erro ao mover o arquivo de imagem.");
                }
            }

            $result = $this->dao->execute("INSERT INTO tb_produto (nome_produto, desc_produto, valor_produto, frete, limite_entrega, img_produto, id_tipo_produto, id_confeitaria) 
            VALUES ('{$this->produto->getNomeProduto()}', '{$this->produto->getDescProduto()}','{$this->produto->getValorProduto()}', '{$this->produto->getFrete()}', 
            {$this->produto->getDistanciaMaxima()}, '$imagem_url', {$this->produto->getIdTipoProduto()}, {$_SESSION['idConfeitaria']})");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar o produto: " . $e->getMessage());
        }
    }

    public function verificaProduto()
    {
        try {
            $this->produto->setNomeProduto($this->dao->escape_string($_POST['nomeProduto']));
            $this->produto->setDescProduto($this->dao->escape_string($_POST['descProduto']));

            $uploadDir = 'img/img-produto/';
            $imagem_path = null;
            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $imagem_nome = basename($_FILES['img']['name']);
                $imagem_path = $uploadDir . $imagem_nome;
            }

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_produto WHERE nome_produto = '{$this->produto->getNomeProduto()}' 
            AND desc_produto = '{$this->produto->getDescProduto()}' AND img_produto = '{$imagem_path}' AND id_confeitaria = {$_SESSION['idConfeitaria']}");

            foreach ($sql as $produto) {
                if ($produto['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar o produto: " . $e->getMessage());
        }
    }

    public function verificaEditProduto()
    {
        try {
            $this->produto->setNomeProduto($this->dao->escape_string($_POST['nomeProduto']));
            $this->produto->setDescProduto($this->dao->escape_string($_POST['descProduto']));
            $this->produto->setId($this->dao->escape_string($_POST['id']));

            $uploadDir = 'img/img-produto/';
            $imagem_path = null;
            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $imagem_nome = basename($_FILES['img']['name']);
                $imagem_path = $uploadDir . $imagem_nome;
            }

            $sql = $this->dao->getData("
            SELECT COUNT(*) as total 
            FROM tb_produto 
            WHERE nome_produto = '{$this->produto->getNomeProduto()}' 
            AND desc_produto = '{$this->produto->getDescProduto()}' 
            AND img_produto = '{$imagem_path}' 
            AND id_produto = {$this->produto->getId()}
        ");

            foreach ($sql as $produto) {
                if ($produto['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            echo "Erro ao verificar produto: " . $e->getMessage();
        }
    }

    public function viewProduto()
    {
        try {
            if (isset($_SESSION['idConfeitaria'])) {
                $result = $this->dao->getData("SELECT p.*, tp.desc_tipo_produto FROM tb_produto AS p 
                INNER JOIN tb_tipo_produto AS tp ON p.id_tipo_produto = tp.id_tipo_produto WHERE p.id_confeitaria = {$_SESSION['idConfeitaria']}");
                return $result;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao visualizar o produto: " . $e->getMessage());
        }
    }

    public function viewTipoProduto()
    {
        try {
            $query = "SELECT * FROM tb_tipo_produto";
            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            echo "Erro ao visualizar o tipo produto: " . $e->getMessage();
            return null;
        }
    }

    public function updateProduto()
    {
        try {
            $this->produto->setId($this->dao->escape_string($_POST['id']));
            $this->produto->setNomeProduto($this->dao->escape_string($_POST['nomeProduto']));
            $this->produto->setDescProduto($this->dao->escape_string($_POST['descProduto']));
            $this->produto->setIdTipoProduto($this->dao->escape_string($_POST['tiposProduto']));
            $this->produto->setValorProduto($this->dao->escape_string($_POST['valorProduto']));
            $this->produto->setFrete($this->dao->escape_string($_POST['frete']));
            $this->produto->setAtivoProduto($_POST['ativo']);
            $this->produto->setDistanciaMaxima($_POST['distanciaMaxima']);

            $uploadDir = 'img/img-produto/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imagem_url = null;
            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $imagem_tmp = $_FILES['img']['tmp_name'];
                $imagem_nome = basename($_FILES['img']['name']);
                $imagem_path = $uploadDir . $imagem_nome;

                // Movendo a imagem para o diretório desejado
                if (move_uploaded_file($imagem_tmp, $imagem_path)) {
                    $imagem_url = $uploadDir . $imagem_nome;
                } else {
                    throw new Exception("Erro ao mover o arquivo de imagem.");
                }
            }

            $ativo = $this->produto->getAtivoProduto() ? 1 : 0;

            $result = $this->dao->execute("UPDATE tb_produto SET nome_produto = '{$this->produto->getNomeProduto()}', 
            desc_produto = '{$this->produto->getDescProduto()}', valor_produto = '{$this->produto->getValorProduto()}', frete = '{$this->produto->getFrete()}',
            produto_ativo = {$ativo}, limite_entrega = '{$this->produto->getDistanciaMaxima()}', img_produto = '{$imagem_url}', 
            id_tipo_produto = {$this->produto->getIdTipoProduto()} WHERE id_produto = {$this->produto->getId()}");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar o produto: " . $e->getMessage());
        }
    }

    public function pesquisaProduto()
    {
        try {
            $pesq = $this->dao->escape_string($_POST['pesq']);
            $query = "
            SELECT p.*, tp.desc_tipo_produto 
            FROM tb_produto AS p 
            INNER JOIN tb_tipo_produto AS tp ON p.id_tipo_produto = tp.id_tipo_produto
            WHERE p.nome_produto LIKE '%$pesq%' AND p.id_confeitaria = '{$_SESSION['idConfeitaria']}'
        ";

            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            echo "Erro ao pesquisar: " . $e->getMessage();
            return [];
        }
    }

    public function viewProdutos($desc, $idCliente)
    {
        try {
            if ($idCliente == 0) {
                $result = $this->dao->getData("
                SELECT p.* 
                FROM tb_produto p
                INNER JOIN tb_tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto
                WHERE tp.desc_tipo_produto = '$desc' 
                AND p.produto_ativo = 1
            ");
                shuffle($result);
            } else {
                $query = "
                SELECT 
                    p.*, 
                    c.latitude as conf_latitude, 
                    c.longitude as conf_longitude, 
                    p.limite_entrega
                FROM tb_produto p
                INNER JOIN tb_confeitaria c ON p.id_confeitaria = c.id_confeitaria
                INNER JOIN tb_tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto
                WHERE tp.desc_tipo_produto = '$desc' 
                AND p.produto_ativo = 1
            ";

                $produtos = $this->dao->getData($query);

                if (empty($produtos) || empty($idCliente)) {
                    return [];
                }

                // 2. Busca TODOS os endereços do cliente (não apenas um)
                $queryEnderecos = "SELECT latitude, longitude 
                         FROM tb_endereco_cliente 
                         WHERE id_cliente = $idCliente";
                $enderecosCliente = $this->dao->getData($queryEnderecos);

                if (empty($enderecosCliente)) {
                    return [];
                }

                $result = [];

                foreach ($produtos as $produto) {
                    $confLat = $produto['conf_latitude'];
                    $confLon = $produto['conf_longitude'];
                    $limiteEntrega = $produto['limite_entrega'];

                    // 3. Verifica se PELO MENOS UM endereço está dentro do limite
                    foreach ($enderecosCliente as $endereco) {
                        $clienteLat = $endereco['latitude'];
                        $clienteLon = $endereco['longitude'];

                        $distancia = $this->dao->calcularDistancia($clienteLat, $clienteLon, $confLat, $confLon);

                        // Se algum endereço estiver no alcance, inclui o produto e passa para o próximo
                        if ($distancia <= $limiteEntrega) {
                            // Remove campos temporários antes de adicionar ao resultado
                            unset($produto['conf_latitude']);
                            unset($produto['conf_longitude']);
                            $result[] = $produto;
                            break; // Sai do loop de endereços, pois já encontrou um válido
                        }
                    }
                }

                return $result;
            }
            return $result;
        } catch (Exception $e) {
            echo "Erro ao visualizar produtos: " . $e->getMessage();
        }
    }

    public function viewProdutosConfeitaria($tipoProduto, $idCliente, $idConfeitaria)
    {
        try {
            if ($idCliente == 0) {
                $result = $this->dao->getData("
                SELECT * 
                FROM tb_produto 
                WHERE id_tipo_produto = $tipoProduto 
                AND produto_ativo = 1 
                AND id_confeitaria = $idConfeitaria
            ");
                shuffle($result);
            } else {
                $query = "
                SELECT p.*, c.latitude as conf_latitude, c.longitude as conf_longitude, p.limite_entrega
                FROM tb_produto p
                INNER JOIN tb_confeitaria c ON p.id_confeitaria = c.id_confeitaria
                WHERE p.id_tipo_produto = $tipoProduto 
                AND p.produto_ativo = 1 
                AND p.id_confeitaria = $idConfeitaria
            ";

                $produtos = $this->dao->getData($query);

                if (empty($produtos)) {
                    return [];
                }

                $queryEnderecos = "
                SELECT latitude, longitude 
                FROM tb_endereco_cliente 
                WHERE id_cliente = $idCliente
            ";
                $enderecosCliente = $this->dao->getData($queryEnderecos);

                if (empty($enderecosCliente)) {
                    return [];
                }

                $result = [];

                foreach ($produtos as $produto) {
                    $confLat = $produto['conf_latitude'];
                    $confLon = $produto['conf_longitude'];
                    $limiteEntrega = $produto['limite_entrega'];

                    foreach ($enderecosCliente as $endereco) {
                        $clienteLat = $endereco['latitude'];
                        $clienteLon = $endereco['longitude'];

                        $distancia = $this->dao->calcularDistancia($clienteLat, $clienteLon, $confLat, $confLon);

                        if ($distancia <= $limiteEntrega) {
                            unset($produto['conf_latitude']);
                            unset($produto['conf_longitude']);
                            $result[] = $produto;
                            break;
                        }
                    }
                }
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao visualizar produtos: " . $e->getMessage());
        }
    }

    public function viewMostraTipoProdutos($idConfeitaria)
    {
        try {
            $result = $this->dao->getData("
            SELECT 
                MIN(p.id_tipo_produto) as id_tipo_produto,
                tp.desc_tipo_produto
            FROM tb_produto p
            JOIN tb_tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto
            WHERE p.id_confeitaria = {$idConfeitaria}
            GROUP BY tp.desc_tipo_produto
            ORDER BY tp.desc_tipo_produto
        ");

            return !empty($result) ? $result : [];
        } catch (Exception $e) {
            echo "Erro ao visualizar id_tipo_produto: " . $e->getMessage();
            return [];
        }
    }

    public function viewMostraTipoGlobal()
    {
        try {
            // Esta query retorna o menor id_tipo_produto E a desc_tipo_produto para cada tipo
            $result = $this->dao->getData("
            SELECT 
                MIN(p.id_tipo_produto) as id_tipo_produto,
                tp.desc_tipo_produto
            FROM tb_produto p
            JOIN tb_tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto
            GROUP BY tp.desc_tipo_produto
            ORDER BY tp.desc_tipo_produto
        ");

            return $result ?: [];
        } catch (Exception $e) {
            echo "Erro ao visualizar tipos de produto: " . $e->getMessage();
            return [];
        }
    }
}
