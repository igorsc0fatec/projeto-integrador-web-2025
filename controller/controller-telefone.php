<?php
require_once '../model/dao.php';
require_once '../model/telefone.php';

class ControllerTelefone
{
    private $dao;
    private $telefone;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->telefone = new Telefone();
    }

    public function addTelefone($idUsuario)
    {
        try {
            $this->telefone->setNumTelefone($this->dao->escape_string($_POST['telefone']));
            $this->telefone->setTipoTelefone($this->dao->escape_string($_POST['tipoTelefone']));

            $telefone = $this->telefone->getNumTelefone();

            $ddd_pattern = '/\((\d{2})\)/'; // More descriptive name
            if (preg_match($ddd_pattern, $telefone, $matches)) {
                $ddd = $matches[1];
            } else {
                throw new Exception("DDD not found in telefone number"); // Handle this case explicitly
            }

            $idDDDResult = $this->dao->getData("SELECT id_ddd FROM tb_ddd WHERE ddd = '$ddd'");
            if ($idDDDResult && isset($idDDDResult[0]['id_ddd'])) {
                $idDDD = $idDDDResult[0]['id_ddd'];
            } else {
                throw new Exception("DDD not found in database"); // Handle this case
            }

            $result = $this->dao->execute("INSERT INTO tb_telefone (num_telefone, id_usuario, id_ddd, id_tipo_telefone) 
            VALUES ('$telefone', $idUsuario, $idDDD, '{$this->telefone->getTipoTelefone()}')");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar o telefone: " . $e->getMessage());
        }
    }

    public function verificaTelefone()
    {
        try {
            $this->telefone->setNumTelefone($this->dao->escape_string($_POST['telefone']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_telefone WHERE num_telefone = '{$this->telefone->getNumTelefone()}'");
            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }

        } catch (Exception $e) {
            throw new Exception("Erro ao verificar telefone: " . $e->getMessage());
        }
    }

    public function verificaDDD()
    {
        try {
            $this->telefone->setNumTelefone($this->dao->escape_string($_POST['telefone']));
            $telefone = $this->telefone->getNumTelefone();

            $ddd_pattern = '/\((\d{2})\)/'; // Changed variable name for clarity
            if (preg_match($ddd_pattern, $telefone, $matches)) {
                $ddd = $matches[1];
            } else {
                return false; // Or handle the case where DDD isn't found as needed
            }

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_ddd WHERE ddd = '$ddd'");
            foreach ($sql as $row) { // Changed variable name for consistency
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar ddd: " . $e->getMessage());
        }
    }

    public function countTelefone($idUsuario)
    {
        try {
            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_telefone WHERE id_usuario = '{$idUsuario}'");

            foreach ($sql as $row) { // Changed variable name for consistency
                if ($row['total'] >= 3) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao contar telefone: " . $e->getMessage());
        }
    }

    public function viewTipoTelefone()
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_tipo_telefone");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao mostrar tipo de telefone: " . $e->getMessage());
        }
    }

    public function deleteTelefone($id)
    {
        try {
            $query = "DELETE FROM tb_telefone WHERE id_telefone = $id";
            $result = $this->dao->delete($query);
            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar telefone: " . $e->getMessage());
        }
    }

    public function viewTelefone($idUsuario)
    {
        try {
            $result = $this->dao->getData("SELECT tc.*, tt.* FROM tb_telefone tc INNER JOIN tb_tipo_telefone tt 
            ON tc.id_tipo_telefone = tt.id_tipo_telefone WHERE tc.id_usuario = '{$idUsuario}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao mostrar dados do telefone: " . $e->getMessage());
        }
    }
}