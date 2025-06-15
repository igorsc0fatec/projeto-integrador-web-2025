<?php
include_once '../model/dao.php';
include_once '../model/usuario.php';
include_once '../model/email.php';
include_once '../model/suporte.php';
class ControllerUsuario
{
    private $dao;
    private $usuario;
    private $email;
    private $suporte;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->usuario = new Usuario();
        $this->email = new Email();
        $this->suporte = new Suporte();
        date_default_timezone_set('America/Sao_Paulo');
    }

    public function addUsuario()
    {
        try {
            $this->usuario->setEmailUsuario($this->dao->escape_string($_POST['emailUsuario']));
            $this->usuario->setSenhaUsuario($this->dao->escape_string($_POST['senhaUsuario']));
            $this->usuario->setIdTipoUsuario($this->dao->escape_string($_POST['msg']));

            if ($this->usuario->getIdTipoUsuario() == 1) {
                $this->usuario->setEmailVerificado(1);
            } else {
                $this->usuario->setEmailVerificado(0);
            }

            $verificado = $this->usuario->isEmailVerificado() ? 1 : 0;
            $criptoSenha = password_hash($this->usuario->getSenhaUsuario(), PASSWORD_DEFAULT);

            $result = $this->dao->execute("INSERT INTO tb_usuario(email_usuario, email_verificado, senha_usuario, id_tipo_usuario) 
            VALUES('{$this->usuario->getEmailUsuario()}','$verificado','$criptoSenha',{$this->usuario->getIdTipoUsuario()})");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar o usuario: " . $e->getMessage());
        }
    }

    public function verificaEmail($excludeCurrentUser = false, $idUsuario = null)
    {
        try {
            $this->usuario->setEmailUsuario($this->dao->escape_string($_POST['emailUsuario']));
            $sql = "SELECT COUNT(*) as total FROM tb_usuario WHERE email_usuario = '{$this->usuario->getEmailUsuario()}'";

            if ($excludeCurrentUser && isset($idUsuario)) {
                $sql .= " AND id_usuario != {$idUsuario}";
            }

            $result = $this->dao->getData($sql);
            foreach ($result as $row) {
                return $row['total'] > 0;
            }

            return false;
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar o email: " . $e->getMessage());
        }
    }

    public function buscarUltimoUsuario()
    {
        try {
            $idUsuario = $this->dao->getData("SELECT id_usuario FROM tb_usuario ORDER BY id_usuario DESC LIMIT 1");
            return $idUsuario = $idUsuario[0]['id_usuario'];
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar ultimo usuario: " . $e->getMessage());
        }
    }

    public function enviaEmail($email, $codigo)
    {
        try {
            $this->usuario->setEmailUsuario($this->dao->escape_string($email));
            $this->email->enviarEmail($this->usuario->getEmailUsuario(), $codigo);

        } catch (Exception $e) {
            throw new Exception("Erro ao enviar o email: " . $e->getMessage());
        }
    }

    public function validaEmail($email)
    {
        try {
            $this->usuario->setEmailUsuario($this->dao->escape_string($email));
            $this->usuario->setEmailVerificado(1);
            $verificado = $this->usuario->isEmailVerificado() ? 1 : 0;

            $result = $this->dao->execute("UPDATE tb_usuario SET email_verificado=$verificado WHERE email_usuario='{$this->usuario->getEmailUsuario()}'");

            if ($result) {
                return true;
            }

        } catch (Exception $e) {
            throw new Exception("Erro ao validar o email: " . $e->getMessage());
        }
    }

    public function verificaLogin()
    {
        try {
            $this->usuario->setEmailUsuario($this->dao->escape_string($_POST['emailUsuario']));
            $this->usuario->setSenhaUsuario($this->dao->escape_string($_POST['senhaUsuario']));

            $sql = $this->dao->getData("SELECT * FROM tb_usuario WHERE email_usuario = '{$this->usuario->getEmailUsuario()}'");

            if (empty($sql)) {
                return 5;
            }

            $this->usuario->setIdTipoUsuario($sql[0]['id_tipo_usuario']);

            if ($sql[0]['conta_ativa'] === "0") {
                echo "<script language='javascript' type='text/javascript'> window.location.href='../view/conta-inativa.php?e={$this->usuario->getEmailUsuario()}&t={$this->usuario->getIdTipoUsuario()}'</script>";
                return 0;
            } else if ($sql[0]['email_verificado'] === "0") {
                echo "<script language='javascript' type='text/javascript'> window.location.href='../view/usuario-nao-verificado.php?e={$this->usuario->getEmailUsuario()}&t={$this->usuario->getIdTipoUsuario()}'</script>";
                return 0;
            } else if (password_verify($this->usuario->getSenhaUsuario(), $sql[0]['senha_usuario'])) {
                $this->usuario->setOnline(date('Y-m-d H:i:s', time())); // Use time() para garantir a hora atual
                $this->dao->execute("UPDATE tb_usuario SET online = '{$this->usuario->getOnline()}' WHERE email_usuario = '{$this->usuario->getEmailUsuario()}'");
                return $this->usuario->getIdTipoUsuario();
            } else {
                return 4;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar o login: " . $e->getMessage());
        }
    }

    public function armazenaSessao($tipoUsuario, $emailUsuario)
    {
        $this->usuario->setEmailUsuario($this->dao->escape_string($emailUsuario));
        $this->usuario->setIdTipoUsuario($this->dao->escape_string($tipoUsuario));

        $tipoUsuario = $this->usuario->getIdTipoUsuario();

        if ($tipoUsuario === 2) {
            $sql = $this->dao->getData("SELECT * FROM tb_usuario INNER JOIN tb_cliente ON tb_usuario.id_usuario = tb_cliente.id_usuario WHERE tb_usuario.email_usuario = '{$this->usuario->getEmailUsuario()}'");
            foreach ($sql as $dados) {
                $_SESSION['idUsuario'] = $dados['id_usuario'];
                $_SESSION['idCliente'] = $dados['id_cliente'];
                $_SESSION['idTipoUsuario'] = $dados['id_tipo_usuario'];
                $nomes = preg_split('/[^a-zA-Z]+/', $dados['nome_cliente']);
                if (!empty($nomes)) {
                    $_SESSION['nome'] = $nomes[0];
                } else {
                    // Caso o nome esteja vazio ou não contenha letras
                    $_SESSION['nome'] = '';
                }
            }
        } else if ($tipoUsuario === 3) {
            $sql = $this->dao->getData("SELECT * FROM tb_usuario INNER JOIN tb_confeitaria ON tb_usuario.id_usuario = tb_confeitaria.id_usuario WHERE tb_usuario.email_usuario = '{$this->usuario->getEmailUsuario()}'");
            foreach ($sql as $dados) {
                $_SESSION['idUsuario'] = $dados['id_usuario'];
                $_SESSION['idConfeitaria'] = $dados['id_confeitaria'];
                $_SESSION['idTipoUsuario'] = $dados['id_tipo_usuario'];
                $_SESSION['nome'] = $dados['nome_confeitaria'];
            }
        } else if ($tipoUsuario === 1) {
            $sql = $this->dao->getData("SELECT * FROM tb_usuario INNER JOIN tb_adm ON tb_usuario.id_usuario = tb_adm.id_usuario WHERE tb_usuario.email_usuario = '{$this->usuario->getEmailUsuario()}'");
            foreach ($sql as $dados) {
                $_SESSION['idUsuario'] = $dados['id_usuario'];
                $_SESSION['idAdm'] = $dados['id_adm'];
                $_SESSION['idTipoUsuario'] = $dados['id_tipo_usuario'];
                $_SESSION['nome'] = $dados['nome_adm'];
            }
        }
    }

    public function viewUsuario($idUsuario)
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_usuario where id_usuario={$idUsuario}");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao visualizar o usuario: " . $e->getMessage());
        }
    }

    public function updateEmail($emailUsuario)
    {
        try {
            $this->usuario->setEmailUsuario($this->dao->escape_string($emailUsuario));
            $this->dao->execute("UPDATE tb_usuario SET email_usuario='{$this->usuario->getEmailUsuario()}' WHERE id_usuario=$_SESSION[idUsuario]");
            return true;

        } catch (Exception $e) {
            throw new Exception("Erro ao editar o email: " . $e->getMessage());
        }
    }

    public function updateSenha($senhaUsuario, $idUsuario)
    {
        try {
            $this->usuario->setIdUsuario($this->dao->escape_string($idUsuario));
            $this->usuario->setSenhaUsuario($this->dao->escape_string($senhaUsuario));

            $criptoSenha = password_hash($this->usuario->getSenhaUsuario(), PASSWORD_DEFAULT);

            $result = $this->dao->execute("UPDATE tb_usuario SET senha_usuario='$criptoSenha' WHERE id_usuario='{$this->usuario->getIdUsuario()}'");
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar nova senha: " . $e->getMessage());
        }
    }

    public function ativaUsuario($valor, $email)
    {
        try {
            $this->usuario->setEmailUsuario($this->dao->escape_string($email));
            $this->usuario->setContaAtiva($valor);
            $ativo = $this->usuario->isContaAtiva() ? 1 : 0;

            $sql = $this->dao->execute("UPDATE tb_usuario SET conta_ativa='{$ativo}' WHERE email_usuario='{$this->usuario->getEmailUsuario()}'");
            if ($sql) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao ativar usuario: " . $e->getMessage());
        }
    }

    public function pedirSuporte()
    {
        try {
            $this->suporte->setTitulo($this->dao->escape_string($_POST['titulo']));
            $this->suporte->setDescSuporte($this->dao->escape_string($_POST['descSuporte']));
            $this->suporte->setIdTipoSuporte($this->dao->escape_string($_POST['tipoSuporte']));
            $this->suporte->setIdUsuario($_SESSION['idUsuario']);

            $result = $this->dao->execute("INSERT INTO tb_suporte(titulo_suporte, desc_suporte, id_tipo_suporte, id_usuario) 
            VALUES('{$this->suporte->getTitulo()}','{$this->suporte->getDescSuporte()}', {$this->suporte->getIdTipoSuporte()}, 
            {$this->suporte->getIdUsuario()})");

            if ($result) {
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erro ao pedir suporte: " . $e->getMessage());
        }
    }

    public function viewTipoSuporte()
    {
        try {
            $result = $this->dao->getData("SELECT * FROM tb_tipo_suporte WHERE id_tipo_usuario=$_SESSION[idTipoUsuario]");
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao visualizar tipo suporte: " . $e->getMessage());
        }
    }

    public function buscaUsuario()
    {
        try {
            $this->usuario->setIdUsuario($this->dao->escape_string($_POST['pesq']));

            $sql = $this->dao->getData("SELECT * FROM tb_usuario WHERE id_usuario = {$this->usuario->getIdUsuario()}");
            if (!empty($sql)) {
                $this->armazenaSessao($sql[0]['id_tipo_suporte'], $sql[0]['emailUsuario']);
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            throw new Exception("Erro ao armazenar usuario: " . $e->getMessage());
        }
    }

    function gerarCodigo($idUsuario)
    {
        try {
            // Gera um código aleatório de 6 dígitos
            $codigo = '';
            for ($i = 0; $i < 6; $i++) {
                $codigo .= mt_rand(0, 9);  // Gera um número aleatório entre 0 e 9 e concatena no código
            }

            $this->dao->execute("INSERT INTO tb_codigo (id_usuario, codigo, status)
                VALUES ('{$idUsuario}', '{$codigo}', 0)"); // Assumindo 0 para 'pendente'

            return $codigo;

        } catch (Exception $e) {
            throw new Exception("Erro ao gerar codigo: " . $e->getMessage());
        }
    }

    function validarCodigo($codigoDigitado, $idUsuario)
    {
        try {
            // Consulta o código no banco de dados
            $sql = "SELECT * FROM tb_codigo
                    WHERE id_usuario = '{$idUsuario}'
                    AND codigo = '{$codigoDigitado}'
                    AND status = 0"; // Assumindo 0 para 'pendente'

            $result = $this->dao->getData($sql);  // Usando o método getData

            // Verifica se há resultados
            if (!empty($result)) {
                $row = $result[0];  // Pega a primeira linha do resultado
                $dataCriacao = $row['data_criacao'];

                // Calcula a diferença de tempo entre a criação e agora
                $dataAtual = new DateTime();
                $dataCriacaoObj = new DateTime($dataCriacao);
                $intervalo = $dataAtual->diff($dataCriacaoObj);

                if ($intervalo->h < 6) {  // Verifica se a diferença é menor que 6 horas
                    // Atualiza o status para 'verificado'
                    $updateSql = "UPDATE tb_codigo
                                  SET status = 1
                                  WHERE id_usuario = '{$idUsuario}'
                                  AND codigo = '{$codigoDigitado}'";
                    $this->dao->execute($updateSql);  // Executa a atualização

                    return true;  // Código válido
                } else {
                    // Atualiza o status para 'expirado'
                    $updateSql = "UPDATE tb_codigo
                                  SET status = 2
                                  WHERE id_usuario = '{$idUsuario}'
                                  AND codigo = '{$codigoDigitado}'";
                    $this->dao->execute($updateSql);  // Executa a atualização

                    return false;  // Código expirado
                }
            } else {
                return false;  // Código não encontrado ou já foi usado
            }

        } catch (Exception $e) {
            throw new Exception("Erro ao validar codigo: " . $e->getMessage());
        }
    }

    function identificaEmail($email)
    {
        try {
            $id = $this->dao->getData("SELECT id_usuario FROM tb_usuario WHERE email_usuario = '{$email}'");
            return $id;
        } catch (Exception $e) {
            throw new Exception("Erro ao identificar email: " . $e->getMessage());
        }
    }
}
