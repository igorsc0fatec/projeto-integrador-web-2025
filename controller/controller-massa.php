<?php
include_once '../model/dao.php';
include_once '../model/massa.php';

class ControllerMassa
{
    private $dao;
    private $massa;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->massa = new Massa();
    }

    public function addMassa()
    {
        try {
            if (isset($_POST['submit'])) {
                $this->massa->setDescMassa($this->dao->escape_string($_POST['descricao']));
                $this->massa->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

                $result = $this->dao->execute("INSERT INTO tb_massa (desc_massa, valor_por_peso, id_confeitaria)
                VALUES ('{$this->massa->getDescMassa()}', '{$this->massa->getValorPeso()}', '{$_SESSION['idConfeitaria']}')");

                if ($result) {
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar massa: " . $e->getMessage());
        }
    }

    public function verificaMassa()
    {
        try {
            $this->massa->setDescMassa($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_massa WHERE desc_massa = '{$this->massa->getDescMassa()}'
            AND id_confeitaria = '{$_SESSION['idConfeitaria']}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar massa: " . $e->getMessage());
        }
    }

    public function verificaEditMassa()
    {
        try {
            $this->massa->setId($this->dao->escape_string($_POST['id']));
            $this->massa->setDescMassa($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_massa WHERE desc_massa = '{$this->massa->getDescMassa()}'
            AND id_massa = '{$this->massa->getId()}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar massa: " . $e->getMessage());
        }
    }

    public function viewMassa($id)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_massa WHERE id_confeitaria = '{$id}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar massa: " . $e->getMessage());
        }
    }

    public function deleteMassa($id)
    {
        try {
            $this->massa->setId($this->dao->escape_string($id));
            $result = $this->dao->delete("DELETE FROM tb_massa WHERE id_massa = '{$this->massa->getId()}'");
            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar massa: " . $e->getMessage());
        }
    }

    public function updateMassa()
    {
        try {
            $this->massa->setId($this->dao->escape_string($_POST['id']));
            $this->massa->setDescMassa($this->dao->escape_string($_POST['descricao']));
            $this->massa->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

            $query = "UPDATE tb_massa SET desc_massa = '{$this->massa->getDescMassa()}', valor_por_peso = '{$this->massa->getValorPeso()}' WHERE id_massa = '{$this->massa->getId()}'";
            $result = $this->dao->execute($query);

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar massa: " . $e->getMessage());
        }
    }

    public function pesquisaMassa()
    {
        try {
            $pesq = $this->dao->escape_string($_POST['pesq']);
            $query = "SELECT * FROM tb_massa WHERE desc_massa LIKE '%$pesq%' AND id_confeitaria = '{$_SESSION['idConfeitaria']}'";
            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao pesquisar massa: " . $e->getMessage());
        }
    }
}
?>