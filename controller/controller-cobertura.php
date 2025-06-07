<?php
include_once '../model/dao.php';
include_once '../model/cobertura.php';

class ControllerCobertura
{
    private $dao;
    private $cobertura;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->cobertura = new Cobertura();
    }

    public function addCobertura()
    {
        try {
            if (isset($_POST['submit'])) {
                $this->cobertura->setDescCobertura($this->dao->escape_string($_POST['descricao']));
                $this->cobertura->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

                $result = $this->dao->execute("INSERT INTO tb_cobertura (desc_cobertura, valor_por_peso, id_confeitaria) 
                VALUES ('{$this->cobertura->getDescCobertura()}', '{$this->cobertura->getValorPeso()}', '{$_SESSION['idConfeitaria']}')");

                if ($result) {
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar cobertura: " . $e->getMessage());
        }
    }

    public function verificaCobertura()
    {
        try {
            $this->cobertura->setDescCobertura($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_cobertura WHERE desc_cobertura = '{$this->cobertura->getDescCobertura()}' 
            AND id_confeitaria = '{$_SESSION['idConfeitaria']}'");

            foreach ($sql as $row) { // Changed variable name from 'produto' to 'row'
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar cobertura: " . $e->getMessage());
        }
    }

    public function verificaEditCobertura()
    {
        try {
            $this->cobertura->setId($this->dao->escape_string($_POST['id']));
            $this->cobertura->setDescCobertura($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_cobertura WHERE desc_cobertura = '{$this->cobertura->getDescCobertura()}' 
            AND id_cobertura = '{$this->cobertura->getId()}'");

            foreach ($sql as $row) { // Changed variable name from 'produto' to 'row'
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar cobertura: " . $e->getMessage());
        }
    }

    public function viewCobertura($id)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_cobertura WHERE id_confeitaria = '{$id}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar cobertura: " . $e->getMessage());
        }
    }

    public function deleteCobertura($id)
    {
        try {
            $this->cobertura->setId($this->dao->escape_string($id));
            $result = $this->dao->delete("DELETE FROM tb_cobertura WHERE id_cobertura = '{$this->cobertura->getId()}'");
            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar cobertura: " . $e->getMessage());
        }
    }

    public function updateCobertura()
    {
        try {
            $this->cobertura->setId($this->dao->escape_string($_POST['id']));
            $this->cobertura->setDescCobertura($this->dao->escape_string($_POST['descricao']));
            $this->cobertura->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

            $query = "UPDATE tb_cobertura SET desc_cobertura = '{$this->cobertura->getDescCobertura()}', valor_por_peso = '{$this->cobertura->getValorPeso()}' WHERE id_cobertura = '{$this->cobertura->getId()}'";
            $result = $this->dao->execute($query);

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar cobertura: " . $e->getMessage());
        }
    }

    public function pesquisaCobertura()
    {
        try {
            $pesq = $this->dao->escape_string($_POST['pesq']);
            $query = "SELECT * FROM tb_cobertura WHERE desc_cobertura LIKE '%$pesq%' AND id_confeitaria = '{$_SESSION['idConfeitaria']}'";
            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao pesquisar cobertura: " . $e->getMessage());
        }
    }
}
?>