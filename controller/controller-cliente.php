<?php
require_once '../model/dao.php';
require_once '../model/cliente.php';
require_once '../model/endereco.php';
require_once 'controller-usuario.php';

class ControllerCliente
{
    private $dao;
    private $cliente;
    private $endereco;
    private $usuario;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->cliente = new Cliente();
        $this->endereco = new Endereco();
        $this->usuario = new ControllerUsuario();
    }

    public function addCliente()
    {
        try {
            $this->cliente->setNomeCliente($this->dao->escape_string($_POST['nomeCliente']));
            $this->cliente->setCpfCliente($this->dao->escape_string($_POST['cpfCliente']));
            $this->cliente->setNascCliente($this->dao->escape_string($_POST['nascCliente']));

            $idUsuario = $this->usuario->buscarUltimoUsuario();

            $this->dao->execute("INSERT INTO tb_cliente (nome_cliente, cpf_cliente, data_nasc, id_usuario) 
            VALUES ('{$this->cliente->getNomeCliente()}', '{$this->cliente->getCpfCliente()}', '{$this->cliente->getNascCliente()}', 
            '$idUsuario')");

            $idCliente = $this->buscarUltimoCliente();
            $result = $this->addEndereco($idCliente);

            if ($result) {
                return true;
            }

        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar o cliente: " . $e->getMessage());
        }
    }

    public function verificaCPF()
    {
        try {
            $this->cliente->setCpfCliente($this->dao->escape_string($_POST['cpfCliente']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_cliente 
            WHERE cpf_cliente = '{$this->cliente->getCpfCliente()}'");
            foreach ($sql as $row) {
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar cpf: " . $e->getMessage());
        }
    }

    public function buscarUltimoCliente()
    {
        try {
            $idCliente = $this->dao->getData("SELECT id_cliente FROM tb_cliente ORDER BY id_cliente DESC LIMIT 1");
            return $idCliente = $idCliente[0]['id_cliente'];
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar ultimo cliente: " . $e->getMessage());
        }
    }

    public function updateCliente()
    {
        try {
            $this->cliente->setNomeCliente($this->dao->escape_string($_POST['nomeCliente']));
            $this->cliente->setNascCliente($this->dao->escape_string($_POST['nascCliente']));
            $this->cliente->setCpfCliente($this->dao->escape_string($_POST['cpfCliente']));

            $result = $this->dao->execute("UPDATE tb_cliente SET nome_cliente = '{$this->cliente->getNomeCliente()}', 
            data_nasc = '{$this->cliente->getNascCliente()}', cpf_cliente = '{$this->cliente->getCpfCliente()}' WHERE id_cliente = '{$_SESSION['idCliente']}'");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar o cliente: " . $e->getMessage());
        }
    }

    public function viewCliente($id)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_cliente WHERE id_cliente = '{$id}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao mostrar dados do cliente: " . $e->getMessage());
        }
    }

    public function addEndereco($idCliente)
    {
        try {
            $this->endereco->setCep($this->dao->escape_string($_POST['cep']));
            $this->endereco->setLogradouro($this->dao->escape_string($_POST['logradouro']));
            $this->endereco->setNumLocal($this->dao->escape_string($_POST['numLocal']));
            $this->endereco->setBairro($this->dao->escape_string($_POST['bairro']));
            $this->endereco->setCidade($this->dao->escape_string($_POST['cidade']));
            $this->endereco->setUf($this->dao->escape_string($_POST['uf']));
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];

            $result = $this->dao->execute("INSERT INTO tb_endereco_cliente(cep_cliente, log_cliente, num_local, bairro_cliente, cidade_cliente, uf_cliente, latitude, longitude, id_cliente) 
            VALUES('{$this->endereco->getCep()}','{$this->endereco->getLogradouro()}','{$this->endereco->getNumLocal()}',
            '{$this->endereco->getBairro()}','{$this->endereco->getCidade()}','{$this->endereco->getUf()}', '{$latitude}', '{$longitude}', {$idCliente})");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar endereço: " . $e->getMessage());
        }
    }

    public function verificaEndereco()
    {
        try {
            $this->endereco->setNumLocal($this->dao->escape_string($_POST['numLocal']));
            $this->endereco->setCep($this->dao->escape_string($_POST['cep']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_endereco_cliente WHERE num_local = '{$this->endereco->getNumLocal()}' 
            AND cep_cliente = '{$this->endereco->getCep()}' AND id_cliente = '{$_SESSION['idCliente']}'");

            foreach ($sql as $row) { // Changed variable name from 'endereco' to 'row'
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar endereço: " . $e->getMessage());
        }
    }

    public function verificaEditEndereco()
    {
        try {
            $this->endereco->setNumLocal($this->dao->escape_string($_POST['numLocal']));
            $this->endereco->setCep($this->dao->escape_string($_POST['cep']));
            $this->endereco->setId($this->dao->escape_string($_POST['id']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_endereco_cliente WHERE num_local = '{$this->endereco->getNumLocal()}' 
            AND cep_cliente = '{$this->endereco->getCep()}' AND id_endereco_cliente = '{$this->endereco->getId()}'");

            foreach ($sql as $row) {  // Changed variable name from 'endereco' to 'row'
                if ($row['total'] > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar edição endereço: " . $e->getMessage());
        }
    }

    public function countEndereco()
    {
        try {
            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_endereco_cliente WHERE id_cliente = '{$_SESSION['idCliente']}'");

            foreach ($sql as $row) { // Changed variable name from 'total' to 'row'
                if ($row['total'] >= 3) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao contar endereço: " . $e->getMessage());
        }
    }

    public function deleteEndereco($id)
    {
        try {
            $result = $this->dao->delete("DELETE FROM tb_endereco_cliente WHERE id_endereco_cliente = $id");
            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao excluir o endereço: " . $e->getMessage());
        }
    }

    public function updateEndereco()
    {
        try {
            $this->endereco->setId($this->dao->escape_string($_POST['id']));
            $this->endereco->setCep($this->dao->escape_string($_POST['cep']));
            $this->endereco->setLogradouro($this->dao->escape_string($_POST['logradouro']));
            $this->endereco->setNumLocal($this->dao->escape_string($_POST['numLocal']));
            $this->endereco->setBairro($this->dao->escape_string($_POST['bairro']));
            $this->endereco->setCidade($this->dao->escape_string($_POST['cidade']));
            $this->endereco->setUf($this->dao->escape_string($_POST['uf']));
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];

            $result = $this->dao->execute("UPDATE tb_endereco_cliente SET cep_cliente='{$this->endereco->getCep()}', log_cliente='{$this->endereco->getLogradouro()}', num_local='{$this->endereco->getNumLocal()}', bairro_cliente='{$this->endereco->getBairro()}'
            , cidade_cliente='{$this->endereco->getCidade()}', uf_cliente='{$this->endereco->getUf()}', latitude='{$latitude}', longitude='{$longitude}' WHERE id_endereco_cliente={$this->endereco->getId()}");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar o endereço: " . $e->getMessage());
        }
    }

    public function viewEndereco($idCliente)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_endereco_cliente WHERE id_cliente = '{$idCliente}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao visualizar endereços: " . $e->getMessage());
        }
    }

}