<?php
include_once '../model/dao.php';
include_once '../model/mensagem.php';
include_once '../model/conversa.php';

class ControllerChat
{
    private $dao;
    private $mensagem;
    private $conversa;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->mensagem = new Mensagem();
        $this->conversa = new Conversa();
    }

    public function addMensagem()
    {
        try {
            $this->mensagem->setMensagem($this->dao->escape_string($_POST['mensagem']));
            $this->mensagem->setIdRemetente($_SESSION['idUsuario']);
            $this->mensagem->setIdDestinatario($this->dao->escape_string($_POST['id']));
            $this->conversa->setIdRemetente($_SESSION['idUsuario']);
            $this->conversa->setIdDestinatario($this->dao->escape_string($_POST['id']));

            $uploadDir = '../img-chat/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
                $imagem_tmp = $_FILES['img']['tmp_name'];
                $imagem_nome = basename($_FILES['img']['name']);
                $imagem_path = $uploadDir . $imagem_nome;

                if (move_uploaded_file($imagem_tmp, $imagem_path)) {
                    $this->mensagem->setMensagem($this->dao->escape_string($imagem_path));
                } else {
                    throw new Exception("Erro ao mover o arquivo de imagem.");
                }
            }

            $sql = $this->dao->getData(" SELECT id_conversa FROM tb_conversa WHERE id_remetente = 
            '{$this->conversa->getIdRemetente()}' AND id_destinatario = '{$this->conversa->getIdDestinatario()}'");
            $id_conversa = $sql[0]['id_conversa'];

            if (count($sql) < 1) {
                $this->dao->execute("INSERT INTO tb_conversa (id_remetente, id_destinatario) 
                VALUES ('{$this->conversa->getIdRemetente()}','{$this->conversa->getIdDestinatario()}')");
                $id_conversa = $this->dao->getLastInsertedId('tb_conversa', 'id_conversa');
            }
            $this->dao->execute("INSERT INTO tb_mensagem (desc_mensagem, id_remetente, id_destinatario) 
                VALUES ('{$this->mensagem->getMensagem()}','{$this->mensagem->getIdRemetente()}','{$this->mensagem->getIdDestinatario()}')");
            $id_mensagem = $this->dao->getLastInsertedId('tb_mensagem', 'id_mensagem');

            $this->dao->execute("INSERT INTO tb_conversa_mensagem (id_mensagem, id_conversa)
                VALUES ('$id_mensagem','$id_conversa')");

        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar mensagem: " . $e->getMessage());
        }
    }

    public function viewMensagem($idRemetente, $idDestinatario)
    {
        try {
            // Busca mensagens em ambas as direções (trocando remetente/destinatário)
            $result = $this->dao->getData("SELECT * FROM tb_mensagem WHERE (id_remetente = $idRemetente AND id_destinatario = $idDestinatario)
            OR (id_remetente = $idDestinatario AND id_destinatario = $idRemetente) ORDER BY hora_envio ASC");

            foreach ($result as &$mensagem) {
                $caminhoArquivo = '../img-chat/' . $mensagem['desc_mensagem'];

                if (preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $mensagem['desc_mensagem']) && file_exists($caminhoArquivo)) {
                    $mensagem['tipo'] = 'imagem';
                    $mensagem['desc_mensagem'] = $caminhoArquivo;
                } else {
                    $mensagem['tipo'] = 'texto';
                }
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar mensagem: " . $e->getMessage());
        }
    }

    public function viewConversa($idUsuario)
    {
        try {
            $sql = $this->dao->getData("SELECT * FROM tb_conversa WHERE id_remetente = $idUsuario OR id_destinatario = $idUsuario");
            $result = [];

            if (!empty($sql)) {
                if ($sql[0]['id_remetente'] === $idUsuario) {
                    $idTipoUsuario = $this->dao->getData("SELECT id_tipo_usuario FROM tb_usuario WHERE id_usuario = " . $sql[0]['id_destinatario']);
                    if ($idTipoUsuario[0]['id_tipo_usuario'] === '2') {
                        $result = $this->dao->getData("SELECT * FROM tb_cliente WHERE id_usuario = " . $sql[0]['id_destinatario']);
                    } else if ($idTipoUsuario[0]['id_tipo_usuario'] === '3') {
                        $result = $this->dao->getData("SELECT * FROM tb_confeitaria WHERE id_usuario = " . $sql[0]['id_destinatario']);
                    }
                } else {
                    $idTipoUsuario = $this->dao->getData("SELECT id_tipo_usuario FROM tb_usuario WHERE id_usuario = " . $sql[0]['id_remetente']);
                    if ($idTipoUsuario[0]['id_tipo_usuario'] === '2') {
                        $result = $this->dao->getData("SELECT * FROM tb_cliente WHERE id_usuario = " . $sql[0]['id_remetente']);
                    } else if ($idTipoUsuario[0]['id_tipo_usuario'] === '3') {
                        $result = $this->dao->getData("SELECT * FROM tb_confeitaria WHERE id_usuario = " . $sql[0]['id_remetente']);
                    }
                }
            } else {
                return [];
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar conversa: " . $e->getMessage());
        }
    }

    public function online($idUsuario)
    {
        try {
            // Busca os dados do usuário (incluindo o timestamp de quando esteve online)
            $dadosUsuario = $this->dao->getData("SELECT online FROM tb_usuario WHERE id_usuario = $idUsuario");

            if (empty($dadosUsuario)) {
                return "offline";
            }

            $ultimaAtividade = strtotime($dadosUsuario[0]['online']);
            $agora = time();
            $diferenca = $agora - $ultimaAtividade;

            // Consideramos "online" se a última atividade foi nos últimos 3 minutos
            if ($diferenca <= 180) { // 180 segundos = 3 minutos
                return "online";
            } else {
                return "última vez " . $this->calcularTempo($ultimaAtividade);
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar status: " . $e->getMessage());
        }
    }

    private function calcularTempo($timestamp)
    {
        $diferenca = time() - $timestamp;

        if ($diferenca < 60) {
            return "agora mesmo";
        } elseif ($diferenca < 3600) {
            $minutos = floor($diferenca / 60);
            return $minutos . " minuto" . ($minutos > 1 ? "s" : "") . " atrás";
        } elseif ($diferenca < 86400) {
            $horas = floor($diferenca / 3600);
            return $horas . " hora" . ($horas > 1 ? "s" : "") . " atrás";
        } elseif ($diferenca < 2592000) {
            $dias = floor($diferenca / 86400);
            return $dias . " dia" . ($dias > 1 ? "s" : "") . " atrás";
        } else {
            $meses = floor($diferenca / 2592000);
            return $meses . " mês" . ($meses > 1 ? "es" : "") . " atrás";
        }
    }

    public function marcarMensagensComoLidas($idRemetente, $idDestinatario)
    {
        try {
            $sql = "UPDATE tb_mensagem SET status_mensagem = 'lido' WHERE id_remetente = $idRemetente 
            AND id_destinatario = $idDestinatario AND status_mensagem = 'nao_lido'";

            return $this->dao->execute($sql);
        } catch (Exception $e) {
            throw new Exception("Erro ao mudar status: " . $e->getMessage());
        }
    }

    public function viewMensagemNaoLida($idDestinatario)
    {
        try {
            $sql = "
                SELECT 
                    m.*, 
                    c.* 
                FROM 
                    tb_mensagem m
                INNER JOIN 
                    tb_cliente c ON m.id_remetente = c.id_usuario
                WHERE 
                    m.id_destinatario = $idDestinatario 
                    AND m.status_mensagem = 'nao_lido'
                ORDER BY 
                    m.hora_envio ASC
            ";

            $result = $this->dao->getData($sql);

            foreach ($result as &$mensagem) {
                $caminhoArquivo = '../img-chat/' . $mensagem['desc_mensagem'];

                if (preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $mensagem['desc_mensagem']) && file_exists($caminhoArquivo)) {
                    $mensagem['tipo'] = 'imagem';
                    $mensagem['desc_mensagem'] = $caminhoArquivo;
                } else {
                    $mensagem['tipo'] = 'texto';
                }
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar mensagem não lida: " . $e->getMessage());
        }
    }

}