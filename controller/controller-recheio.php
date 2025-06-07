<?php
include_once '../model/dao.php';
include_once '../model/recheio.php';

class ControllerRecheio
{
    private $dao;
    private $recheio;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->recheio = new Recheio();
    }

    public function addRecheio()
    {
        try {
            if (isset($_POST['submit'])) {
                $this->recheio->setDescRecheio($this->dao->escape_string($_POST['descricao']));
                $this->recheio->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

                $result = $this->dao->execute("INSERT INTO tb_recheio (desc_recheio, valor_por_peso, id_confeitaria)
                    VALUES ('{$this->recheio->getDescRecheio()}', '{$this->recheio->getValorPeso()}', '{$_SESSION['idConfeitaria']}')");

                if ($result) {
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar recheio: " . $e->getMessage());
        }
    }

    public function verificaRecheio()
    {
        try {
            $this->recheio->setDescRecheio($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_recheio WHERE desc_recheio = '{$this->recheio->getDescRecheio()}'
                AND id_confeitaria = '{$_SESSION['idConfeitaria']}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar recheio: " . $e->getMessage());
        }
    }

    public function verificaEditRecheio()
    {
        try {
            $this->recheio->setId($this->dao->escape_string($_POST['id']));
            $this->recheio->setDescRecheio($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_recheio WHERE desc_recheio = '{$this->recheio->getDescRecheio()}'
                AND id_recheio = '{$this->recheio->getId()}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar recheio: " . $e->getMessage());
        }
    }

    public function viewRecheio($id)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_recheio WHERE id_confeitaria = '{$id}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar recheio: " . $e->getMessage());
        }
    }

    public function deleteRecheio($id)
    {
        try {
            $this->recheio->setId($this->dao->escape_string($id));
            $result = $this->dao->delete("DELETE FROM tb_recheio WHERE id_recheio = '{$this->recheio->getId()}'");
            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar recheio: " . $e->getMessage());
        }
    }

    public function updateRecheio()
    {
        try {
            $this->recheio->setId($this->dao->escape_string($_POST['id']));
            $this->recheio->setDescRecheio($this->dao->escape_string($_POST['descricao']));
            $this->recheio->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

            $query = "UPDATE tb_recheio SET desc_recheio = '{$this->recheio->getDescRecheio()}', valor_por_peso = '{$this->recheio->getValorPeso()}' WHERE id_recheio = '{$this->recheio->getId()}'";
            $result = $this->dao->execute($query);

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar recheio: " . $e->getMessage());
        }
    }

    public function pesquisaRecheio()
    {
        try {
            $pesq = $this->dao->escape_string($_POST['pesq']);
            $query = "SELECT * FROM tb_recheio WHERE desc_recheio LIKE '%$pesq%' AND id_confeitaria = '{$_SESSION['idConfeitaria']}'";
            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao pesquisar recheio: " . $e->getMessage());
        }
    }
}
?>