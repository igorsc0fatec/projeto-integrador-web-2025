<?php
require_once '../model/dao.php';
require_once '../model/confeitaria.php';
require_once 'controller-usuario.php';

class ControllerConfeitaria
{
    private $dao;
    private $confeitaria;
    private $usuario;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->confeitaria = new Confeitaria();
        $this->usuario = new ControllerUsuario();
    }

    public function addConfeitaria()
    {
        try {
            $this->confeitaria->setNomeConfeitaria($this->dao->escape_string($_POST['nomeConfeitaria']));
            $this->confeitaria->setCnpjConfeitaria($this->dao->escape_string($_POST['cnpjConfeitaria']));
            $this->confeitaria->setCepConfeitaria($this->dao->escape_string($_POST['cep']));
            $this->confeitaria->setLogConfeitaria($this->dao->escape_string($_POST['logradouro']));
            $this->confeitaria->setNumLocal($this->dao->escape_string($_POST['numLocal']));
            $this->confeitaria->setBairroConfeitaria($this->dao->escape_string($_POST['bairro']));
            $this->confeitaria->setCidadeConfeitaria($this->dao->escape_string($_POST['cidade']));
            $this->confeitaria->setUfConfeitaria($this->dao->escape_string($_POST['uf']));
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];
            $inicio = $_POST['hora_entrada'];
            $termino = $_POST['hora_saida'];

            $uploadDir = 'img/img-confeitaria/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $imagem_tmp = $_FILES['img']['tmp_name'];
                $imagem_nome = basename($_FILES['img']['name']);
                $imagem_path = $uploadDir . $imagem_nome;

                if (move_uploaded_file($imagem_tmp, $imagem_path)) {
                    $imagem_url = $this->dao->escape_string($imagem_path);
                } else {
                    throw new Exception("Erro ao mover o arquivo de imagem.");
                }
            } else {
                $imagem_url = null;
            }

            $idUsuario = $this->usuario->buscarUltimoUsuario();

            $result = $this->dao->execute("INSERT INTO tb_confeitaria(nome_confeitaria, cnpj_confeitaria, cep_confeitaria, hora_entrada, hora_saida, log_confeitaria, num_local, bairro_confeitaria, cidade_confeitaria, uf_confeitaria, latitude, longitude, img_confeitaria, id_usuario) 
            VALUES('{$this->confeitaria->getNomeConfeitaria()}','{$this->confeitaria->getCnpjConfeitaria()}','{$this->confeitaria->getCepConfeitaria()}', '$inicio', '$termino',
            '{$this->confeitaria->getLogConfeitaria()}','{$this->confeitaria->getNumLocal()}','{$this->confeitaria->getBairroConfeitaria()}','{$this->confeitaria->getCidadeConfeitaria()}',
            '{$this->confeitaria->getUfConfeitaria()}', '{$latitude}', '{$longitude}', '$imagem_url','$idUsuario')");

            if ($result) {
                return true;
            }

        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar a confeitaria: " . $e->getMessage());
        }
    }

    public function verificaCNPJ()
    {
        try {
            $this->confeitaria->setCnpjConfeitaria($this->dao->escape_string($_POST['cnpjConfeitaria']));

            $sql = $this->dao->getData("SELECT COUNT(*) as total FROM tb_confeitaria WHERE cnpj_confeitaria = '{$this->confeitaria->getCnpjConfeitaria()}'");
            foreach ($sql as $row) {
                $total = $row['total'];
                return $total > 0;
            }

        } catch (Exception $e) {
            throw new Exception("Erro ao verificar CNPJ: " . $e->getMessage());
        }
    }

    public function updateConfeitaria()
    {
        try {
            $this->confeitaria->setNomeConfeitaria($this->dao->escape_string($_POST['nomeConfeitaria']));
            $this->confeitaria->setCnpjConfeitaria($this->dao->escape_string($_POST['cnpjConfeitaria']));
            $this->confeitaria->setCepConfeitaria($this->dao->escape_string($_POST['cep']));
            $this->confeitaria->setLogConfeitaria($this->dao->escape_string($_POST['logradouro']));
            $this->confeitaria->setNumLocal($this->dao->escape_string($_POST['numLocal']));
            $this->confeitaria->setBairroConfeitaria($this->dao->escape_string($_POST['bairro']));
            $this->confeitaria->setCidadeConfeitaria($this->dao->escape_string($_POST['cidade']));
            $this->confeitaria->setUfConfeitaria($this->dao->escape_string($_POST['uf']));
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];
            $inicio = $_POST['hora_entrada'];
            $termino = $_POST['hora_saida'];

            $uploadDir = 'img/img-confeitaria/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if ($_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $imagem_tmp = $_FILES['img']['tmp_name'];
                $imagem_nome = basename($_FILES['img']['name']);
                $imagem_path = $uploadDir . $imagem_nome;

                if (move_uploaded_file($imagem_tmp, $imagem_path)) {
                    $imagem_url = $this->dao->escape_string($imagem_path);
                } else {
                    throw new Exception("Erro ao mover o arquivo de imagem.");
                }
            } else {
                $imagem_url = null;
            }

            $result = $this->dao->execute("UPDATE tb_confeitaria SET nome_confeitaria='{$this->confeitaria->getNomeConfeitaria()}', cnpj_confeitaria = '{$this->confeitaria->getCnpjConfeitaria()}', 
            cep_confeitaria='{$this->confeitaria->getCepConfeitaria()}', hora_entrada='{$inicio}', hora_saida='{$termino}',
            log_confeitaria='{$this->confeitaria->getLogConfeitaria()}', num_local='{$this->confeitaria->getNumLocal()}', bairro_confeitaria='{$this->confeitaria->getBairroConfeitaria()}', 
            cidade_confeitaria='{$this->confeitaria->getCidadeConfeitaria()}', uf_confeitaria='{$this->confeitaria->getUfConfeitaria()}', latitude='{$latitude}', 
            longitude='{$longitude}', img_confeitaria='{$imagem_url}' WHERE id_confeitaria=$_SESSION[idConfeitaria]");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao editar confitaria: " . $e->getMessage());
        }
    }

    public function viewConfeitaria($id)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_confeitaria WHERE id_confeitaria = '{$id}'");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao mostrar dados da confitaria: " . $e->getMessage());
        }
    }

    public function viewConfeitarias()
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_confeitaria");
            shuffle($result);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao visualizar confeitarias: " . $e->getMessage());
        }
    }

    public function viewPerfilConfeitaria($id)
    {
        try {
            $result = $this->dao->getData("SELECT c.id_confeitaria, c.nome_confeitaria, c.cep_confeitaria, c.log_confeitaria, c.num_local, c.bairro_confeitaria, c.cidade_confeitaria,
            c.uf_confeitaria, c.img_confeitaria, u.email_usuario, u.id_usuario FROM tb_confeitaria c JOIN tb_usuario u ON c.id_usuario = u.id_usuario
            WHERE c.id_confeitaria = $id ");

            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar confeitaria: " . $e->getMessage());
        }
    }

}