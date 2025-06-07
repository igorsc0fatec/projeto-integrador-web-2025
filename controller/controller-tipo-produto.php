<?php
include_once '../model/dao.php';
include_once '../model/tipo-produto.php';

class ControllerTipoProduto
{
    private $dao;
    private $tipoProduto;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->tipoProduto = new TipoProduto();
    }

    public function addTipoProduto()
    {
        try {
            if (isset($_POST['submit'])) {
                $this->tipoProduto->setDescTipoProduto($this->dao->escape_string($_POST['descricao']));

                $result = $this->dao->execute("INSERT INTO tb_tipo_produto (desc_tipo_produto, id_confeitaria)
                                                VALUES ('{$this->tipoProduto->getDescTipoProduto()}', '{$_SESSION['idConfeitaria']}')");

                if ($result) {
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar tipo de produto: " . $e->getMessage());
        }
    }

    public function verificaTipoProduto()
    {
        try {
            $this->tipoProduto->setDescTipoProduto($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_tipo_produto WHERE desc_tipo_produto = '{$this->tipoProduto->getDescTipoProduto()}'
                                            AND id_confeitaria = '{$_SESSION['idConfeitaria']}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar tipo de produto: " . $e->getMessage());
        }
    }

    public function verificaEditTipoProduto()
    {
        try {
            $this->tipoProduto->setId($this->dao->escape_string($_POST['id']));
            $this->tipoProduto->setDescTipoProduto($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_tipo_produto WHERE desc_tipo_produto = '{$this->tipoProduto->getDescTipoProduto()}'
                                            AND id_tipo_produto = '{$this->tipoProduto->getId()}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar tipo de produto: " . $e->getMessage());
        }
    }

    public function viewTipoProduto()
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_tipo_produto WHERE id_confeitaria = '{$_SESSION['idConfeitaria']}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar tipo de produto: " . $e->getMessage());
        }
    }

    public function viewTiposProdutos()
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_tipo_produto");

            $uniqueResults = [];
            $seenNames = [];

            foreach ($result as $item) {
                $name = $item['desc_tipo_produto']; 
                if (!in_array($name, $seenNames)) {
                    $seenNames[] = $name;
                    $uniqueResults[] = $item;
                }
            }

            return $uniqueResults;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar tipo de produto: " . $e->getMessage());
        }
    }

    public function deleteTipoProduto($id)
    {
        try {
            $this->tipoProduto->setId($this->dao->escape_string($id));
            $result = $this->dao->delete("DELETE FROM tb_tipo_produto WHERE id_tipo_produto = '{$this->tipoProduto->getId()}'");
            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar tipo de produto: " . $e->getMessage());
        }
    }

    public function updateTipoProduto()
    {
        try {
            $this->tipoProduto->setId($this->dao->escape_string($_POST['id']));
            $this->tipoProduto->setDescTipoProduto($this->dao->escape_string($_POST['descricao']));

            $query = "UPDATE tb_tipo_produto SET desc_tipo_produto = '{$this->tipoProduto->getDescTipoProduto()}' WHERE id_tipo_produto = '{$this->tipoProduto->getId()}'";
            $result = $this->dao->execute($query);

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar tipo de produto: " . $e->getMessage());
        }
    }

    public function pesquisaTipoProduto()
    {
        try {
            $pesq = $this->dao->escape_string($_POST['pesq']);
            $query = "SELECT * FROM tb_tipo_produto WHERE desc_tipo_produto LIKE '%$pesq%' AND id_confeitaria = '{$_SESSION['idConfeitaria']}'";
            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao pesquisar tipo de produto: " . $e->getMessage());
        }
    }
}
?>