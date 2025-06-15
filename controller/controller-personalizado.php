<?php
include_once '../model/dao.php';
include_once '../model/personalizado.php';

class ControllerPersonalizado
{
    private $dao;
    private $personalizado;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->personalizado = new Personalizado();
    }

    public function addPersonalizado()
    {
        try {
            $this->personalizado->setNomePersonalizado($this->dao->escape_string($_POST['nomeProduto']));
            $this->personalizado->setDescPersonalizado($this->dao->escape_string($_POST['descProduto']));
            $this->personalizado->setIdTipoProduto($this->dao->escape_string($_POST['tiposProduto']));
            $this->personalizado->setDistanciaMaxima($this->dao->escape_string($_POST['distanciaMaxima']));
            $campos = ['Cobertura', 'Decoracao', 'Formato', 'Massa', 'Recheio'];
            foreach ($campos as $campo) {
                $valor = isset($_POST["ativo$campo"]) ? $_POST["ativo$campo"] : 0;
                $this->personalizado->{"set$campo"}($valor);
            }
            $uploadDir = 'img/img-personalizado/';
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
            $cobertura = $this->personalizado->getCobertura() ? 1 : 0;
            $decoracao = $this->personalizado->getDecoracao() ? 1 : 0;
            $formato = $this->personalizado->getFormato() ? 1 : 0;
            $massa = $this->personalizado->getMassa() ? 1 : 0;
            $recheio = $this->personalizado->getRecheio() ? 1 : 0;
            $result = $this->dao->execute("INSERT INTO tb_personalizado (nome_personalizado, desc_personalizado, img_personalizado, 
            cobertura_ativa, decoracao_ativa, formato_ativa,
            massa_ativa, recheio_ativa, limite_entrega, id_tipo_produto, id_confeitaria) 
            VALUES ('{$this->personalizado->getNomePersonalizado()}', '{$this->personalizado->getDescPersonalizado()}','$imagem_url', 
            '{$cobertura}','{$decoracao}','{$formato}','{$massa}','{$recheio}', 
            {$this->personalizado->getDistanciaMaxima()}, {$this->personalizado->getIdTipoProduto()}, {$_SESSION['idConfeitaria']})");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao cadastrar personalizado: " . $e->getMessage());
        }
    }

    public function verificaPersonalizado()
    {
        try {
            $this->personalizado->setNomePersonalizado($this->dao->escape_string($_POST['nomeProduto']));
            $this->personalizado->setDescPersonalizado($this->dao->escape_string($_POST['descProduto']));

            $uploadDir = 'img/img-produto/';
            $imagem_path = null;
            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $imagem_nome = basename($_FILES['img']['name']);
                $imagem_path = $uploadDir . $imagem_nome;
            }

            $sql = $this->dao->getData("
            SELECT COUNT(*) as total 
            FROM tb_personalizado 
            WHERE nome_personalizado = '{$this->personalizado->getNomePersonalizado()}' 
            AND desc_personalizado = '{$this->personalizado->getDescPersonalizado()}' 
            AND img_personalizado = '{$imagem_path}' 
            AND id_confeitaria = {$_SESSION['idConfeitaria']}
        ");
            foreach ($sql as $produto) {
                if ($produto['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            echo "Erro ao verificar personalizado: " . $e->getMessage();
        }
    }

    public function verificaEditPersonalizado()
    {
        try {
            $this->personalizado->setNomePersonalizado($this->dao->escape_string($_POST['nomeProduto']));
            $this->personalizado->setDescPersonalizado($this->dao->escape_string($_POST['descProduto']));
            $this->personalizado->setIdPersonalizado($this->dao->escape_string($_POST['id']));

            $uploadDir = 'img/img-produto/';
            $imagem_path = null;
            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $imagem_nome = basename($_FILES['img']['name']);
                $imagem_path = $uploadDir . $imagem_nome;
            }

            $sql = $this->dao->getData("
            SELECT COUNT(*) as total 
            FROM tb_personalizado 
            WHERE nome_personalizado = '{$this->personalizado->getNomePersonalizado()}' 
            AND desc_personalizado = '{$this->personalizado->getDescPersonalizado()}' 
            AND img_personalizado = '{$imagem_path}' 
            AND id_personalizado = {$this->personalizado->getIdPersonalizado()}
        ");
            foreach ($sql as $produto) {
                if ($produto['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            echo "Erro ao verificar personalizado: " . $e->getMessage();
        }
    }

    public function viewPersonalizado()
    {
        try {
            if (isset($_SESSION['idConfeitaria'])) {
                $result = $this->dao->getData("SELECT pp.*, tp.desc_tipo_produto FROM tb_personalizado AS pp INNER JOIN tb_tipo_produto 
                AS tp ON pp.id_tipo_produto = tp.id_tipo_produto WHERE pp.id_confeitaria = {$_SESSION['idConfeitaria']}");
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao visualizar produto: " . $e->getMessage());
        }
    }

    public function viewTipoProduto()
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_tipo_produto");
            return $result;
        } catch (Exception $e) {
            echo "Ocorreu um erro: " . $e->getMessage();
            return null;
        }
    }

    public function updatePersonalizado()
    {
        try {
            $this->personalizado->setNomePersonalizado($this->dao->escape_string($_POST['nomeProduto']));
            $this->personalizado->setDescPersonalizado($this->dao->escape_string($_POST['descProduto']));
            $this->personalizado->setIdTipoProduto($this->dao->escape_string($_POST['tiposProduto']));
            $this->personalizado->setDistanciaMaxima($this->dao->escape_string($_POST['distanciaMaxima']));
            $this->personalizado->setAtivoPersonalizado($_POST['ativo']);
            $campos = ['Cobertura', 'Decoracao', 'Formato', 'Massa', 'Recheio'];
            foreach ($campos as $campo) {
                $valor = isset($_POST["ativo$campo"]) ? $_POST["ativo$campo"] : 0;
                $this->personalizado->{"set$campo"}($valor);
            }
            $uploadDir = 'img/img-personalizado/';
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
            $cobertura = $this->personalizado->getCobertura() ? 1 : 0;
            $decoracao = $this->personalizado->getDecoracao() ? 1 : 0;
            $formato = $this->personalizado->getFormato() ? 1 : 0;
            $massa = $this->personalizado->getMassa() ? 1 : 0;
            $recheio = $this->personalizado->getRecheio() ? 1 : 0;
            $ativo = $this->personalizado->getAtivoPersonalizado() ? 1 : 0;

            $query = "UPDATE tb_personalizado SET 
                    nome_personalizado = '{$this->personalizado->getNomePersonalizado()}',
                    desc_personalizado = '{$this->personalizado->getDescPersonalizado()}',
                    id_tipo_produto = '{$this->personalizado->getIdTipoProduto()}',
                    limite_entrega = '{$this->personalizado->getDistanciaMaxima()}',
                    cobertura_ativa = {$cobertura},
                    decoracao_ativa = {$decoracao},
                    formato_ativa = {$formato},
                    massa_ativa = {$massa},
                    recheio_ativa = {$recheio},
                    img_personalizado = '{$imagem_url}',
                    personalizado_ativo = {$ativo} WHERE id_personalizado = '{$this->personalizado->getIdPersonalizado()}'";

            $result = $this->dao->execute($query);

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar personalizado: " . $e->getMessage());
        }
    }

    public function pesquisaPersonalizado()
    {
        try {
            $pesq = $this->dao->escape_string($_POST['pesq']);

            $result = $this->dao->getData("
            SELECT pp.*, tp.desc_tipo_produto
            FROM tb_personalizado AS pp 
            INNER JOIN tb_tipo_produto AS tp ON pp.id_tipo_produto = tp.id_tipo_produto
            WHERE pp.nome_personalizado LIKE '%$pesq%' AND pp.id_confeitaria = {$_SESSION['idConfeitaria']}
        ");

            return $result;
        } catch (Exception $e) {
            echo "Erro ao pesquisar personalizado: " . $e->getMessage();
            return [];
        }
    }

    public function viewPersonalizados($desc, $idCliente, $idConfeitaria)
    {
        try {
            if ($idCliente == 0) {
                $result = $this->dao->getData("
                SELECT p.* 
                FROM tb_personalizado p
                INNER JOIN tb_tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto
                WHERE tp.desc_tipo_produto = '$desc' 
                AND p.personalizado_ativo = 1 AND p.id_confeitaria = {$idConfeitaria}
            ");
                shuffle($result);
            } else {
                $query = "
                SELECT 
                    p.*, 
                    c.latitude as conf_latitude, 
                    c.longitude as conf_longitude, 
                    p.limite_entrega
                FROM tb_personalizado p
                INNER JOIN tb_confeitaria c ON p.id_confeitaria = c.id_confeitaria
                INNER JOIN tb_tipo_produto tp ON p.id_tipo_produto = tp.id_tipo_produto
                WHERE tp.desc_tipo_produto = '$desc' 
                AND p.personalizado_ativo = 1 AND p.id_confeitaria = {$idConfeitaria}
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
            FROM tb_personalizado p
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

    public function viewDadosPersonalizado($idPersonalizado)
    {
        try {
            $result = $this->dao->getData("
            SELECT * FROM tb_personalizado 
            WHERE id_personalizado = {$idPersonalizado}
        ");
            return $result;
        } catch (Exception $e) {
            echo "Erro ao visualizar personalizado: " . $e->getMessage();
        }
    }
}
