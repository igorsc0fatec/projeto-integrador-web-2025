<?php
include_once '../model/dao.php';
include_once '../model/decoracao.php';

class ControllerDecoracao
{
    private $dao;
    private $decoracao;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->decoracao = new Decoracao();
    }

    public function addDecoracao()
    {
        try {
            if (isset($_POST['submit'])) {
                $this->decoracao->setDescDecoracao($this->dao->escape_string($_POST['descricao']));
                $this->decoracao->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

                $result = $this->dao->execute("INSERT INTO tb_decoracao (desc_decoracao, valor_por_peso, id_confeitaria)
  VALUES ('{$this->decoracao->getDescDecoracao()}', '{$this->decoracao->getValorPeso()}', '{$_SESSION['idConfeitaria']}')");

                if ($result) {
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar decoração: " . $e->getMessage());
        }
    }

    public function verificaDecoracao()
    {
        try {
            $this->decoracao->setDescDecoracao($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_decoracao WHERE desc_decoracao = '{$this->decoracao->getDescDecoracao()}'
  AND id_confeitaria = '{$_SESSION['idConfeitaria']}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar decoração: " . $e->getMessage());
        }
    }

    public function verificaEditDecoracao()
    {
        try {
            $this->decoracao->setId($this->dao->escape_string($_POST['id']));
            $this->decoracao->setDescDecoracao($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_decoracao WHERE desc_decoracao = '{$this->decoracao->getDescDecoracao()}'
  AND id_decoracao = '{$this->decoracao->getId()}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar decoração: " . $e->getMessage());
        }
    }

    public function viewDecoracao($id)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_decoracao WHERE id_confeitaria = '{$id}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar decoração: " . $e->getMessage());
        }
    }

    public function deleteDecoracao($id)
    {
        try {
            $this->decoracao->setId($this->dao->escape_string($id));
            $result = $this->dao->delete("DELETE FROM tb_decoracao WHERE id_decoracao = '{$this->decoracao->getId()}'");
            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar decoração: " . $e->getMessage());
        }
    }

    public function updateDecoracao()
    {
        try {
            $this->decoracao->setId($this->dao->escape_string($_POST['id']));
            $this->decoracao->setDescDecoracao($this->dao->escape_string($_POST['descricao']));
            $this->decoracao->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

            $query = "UPDATE tb_decoracao SET desc_decoracao = '{$this->decoracao->getDescDecoracao()}', valor_por_peso = '{$this->decoracao->getValorPeso()}' WHERE id_decoracao = '{$this->decoracao->getId()}'";
            $result = $this->dao->execute($query);

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar decoração: " . $e->getMessage());
        }
    }

    public function pesquisaDecoracao()
    {
        try {
            $pesq = $this->dao->escape_string($_POST['pesq']);
            $query = "SELECT * FROM tb_decoracao WHERE desc_decoracao LIKE '%$pesq%' AND id_confeitaria = '{$_SESSION['idConfeitaria']}'";
            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao pesquisar decoração: " . $e->getMessage());
        }
    }
}
?>