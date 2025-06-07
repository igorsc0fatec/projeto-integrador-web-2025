<?php
include_once '../model/dao.php';
include_once '../model/formato.php';

class ControllerFormato
{
    private $dao;
    private $formato;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->formato = new Formato();
    }

    public function addFormato()
    {
        try {
            if (isset($_POST['submit'])) {
                $this->formato->setDescFormato($this->dao->escape_string($_POST['descricao']));
                $this->formato->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

                $result = $this->dao->execute("INSERT INTO tb_formato (desc_formato, valor_por_peso, id_confeitaria)
                    VALUES ('{$this->formato->getDescFormato()}', '{$this->formato->getValorPeso()}', '{$_SESSION['idConfeitaria']}')");

                if ($result) {
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar formato: " . $e->getMessage());
        }
    }

    public function verificaFormato()
    {
        try {
            $this->formato->setDescFormato($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_formato WHERE desc_formato = '{$this->formato->getDescFormato()}'
                AND id_confeitaria = '{$_SESSION['idConfeitaria']}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar formato: " . $e->getMessage());
        }
    }

    public function verificaEditFormato()
    {
        try {
            $this->formato->setId($this->dao->escape_string($_POST['id']));
            $this->formato->setDescFormato($this->dao->escape_string($_POST['descricao']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_formato WHERE desc_formato = '{$this->formato->getDescFormato()}'
                AND id_formato = '{$this->formato->getId()}'");

            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar formato: " . $e->getMessage());
        }
    }

    public function viewFormato($id)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_formato WHERE id_confeitaria = '{$id}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar formato: " . $e->getMessage());
        }
    }

    public function deleteFormato($id)
    {
        try {
            $this->formato->setId($this->dao->escape_string($id));
            $result = $this->dao->delete("DELETE FROM tb_formato WHERE id_formato = '{$this->formato->getId()}'");
            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar formato: " . $e->getMessage());
        }
    }

    public function updateFormato()
    {
        try {
            $this->formato->setId($this->dao->escape_string($_POST['id']));
            $this->formato->setDescFormato($this->dao->escape_string($_POST['descricao']));
            $this->formato->setValorPeso($this->dao->escape_string(str_replace(['R$', '.', ','], ['', '', '.'], $_POST['ValorGrama'])));

            $query = "UPDATE tb_formato SET desc_formato = '{$this->formato->getDescFormato()}', valor_por_peso = '{$this->formato->getValorPeso()}' WHERE id_formato = '{$this->formato->getId()}'";
            $result = $this->dao->execute($query);

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar formato: " . $e->getMessage());
        }
    }

    public function pesquisaFormato()
    {
        try {
            $pesq = $this->dao->escape_string($_POST['pesq']);
            $query = "SELECT * FROM tb_formato WHERE desc_formato LIKE '%$pesq%' AND id_confeitaria = '{$_SESSION['idConfeitaria']}'";
            $result = $this->dao->getData($query);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao pesquisar formato: " . $e->getMessage());
        }
    }
}
?>