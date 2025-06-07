<?php
require_once '../model/dao.php';
require_once '../model/adm.php';

class ControllerAdm
{
    private $dao;
    private $adm;

    public function __construct()
    {
        $this->dao = new DAO();
        $this->adm = new Adm();
    }

    public function addAdm()
    {
        try {
            $this->adm->setNomeAdm($this->dao->escape_string($_POST['nomeAdm']));
            $this->adm->setFuncaoAdm($this->dao->escape_string($_POST['funcaoAdm']));
            $this->adm->setCpfAdm($this->dao->escape_string($_POST['cpfCliente']));

            // CORRIGIDO: pegar só o ID (assumindo que getData retorna array associativo)
            $result = $this->dao->getData("SELECT id_usuario FROM tb_usuario ORDER BY id_usuario DESC LIMIT 1");

            if (!$result || count($result) == 0) {
                throw new Exception("Nenhum usuário encontrado para vincular.");
            }

            $idUsuario = $result[0]['id_usuario']; // <- aqui está o valor correto

            $sql = "INSERT INTO tb_adm (nome_adm, funcao_adm, cpf_adm, id_usuario) 
            VALUES ('{$this->adm->getNomeAdm()}','{$this->adm->getFuncaoAdm()}','{$this->adm->getCpfAdm()}','$idUsuario')";

            $exec = $this->dao->execute($sql);

            if ($exec) {
                return true;
            } else {
                throw new Exception("Erro ao inserir no banco de dados.");
            }

        } catch (Exception $e) {
            throw new Exception("Erro ao adicionar o adm: " . $e->getMessage());
        }
    }
    
    // No ControllerAdm.php
    // Adicione este método à classe ControllerAdm para suportar filtro
    public function listarAdms($filtro = '') {
        try {
            $sql = "SELECT a.id_adm, a.nome_adm, a.funcao_adm, a.cpf_adm, 
                        u.email_usuario, u.conta_ativa as ativo
                    FROM tb_adm a
                    JOIN tb_usuario u ON a.id_usuario = u.id_usuario";
            
            if (!empty($filtro)) {
                $filtro = $this->escape_string($filtro);
                $sql .= " WHERE a.nome_adm LIKE '%$filtro%' 
                        OR a.funcao_adm LIKE '%$filtro%' 
                        OR a.cpf_adm LIKE '%$filtro%' 
                        OR u.email_usuario LIKE '%$filtro%'";
            }
            
            $result = $this->dao->getData($sql);
            return $result ? $result : [];
            
        } catch (Exception $e) {
            error_log("Erro ao listar administradores: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Erro ao carregar dados do servidor'
            ];
        }
    }

    // Mantenha o método deleteAdm como já existente
    public function deleteAdm($id) {
        try {
            $sql = "DELETE FROM tb_adm WHERE id_adm = " . $this->dao->escape_string($id);
            $exec = $this->dao->execute($sql);
            
            return ['success' => $exec, 'message' => $exec ? '' : 'Erro desconhecido'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getIdUsuarioByAdm($id) {
        try {
            $sql = "SELECT id_usuario FROM tb_adm WHERE id_adm = " . $this->dao->escape_string($id);
            $result = $this->dao->getData($sql);
    
            if ($result && count($result) > 0) {
                return ['success' => true, 'id_usuario' => $result[0]['id_usuario']];
            } else {
                return ['success' => false, 'message' => 'ID não encontrado'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    

    public function deleteUsuario($id) {
        try {
            $sql = "DELETE FROM usuario WHERE id_usuario = " . $this->dao->escape_string($id);
            $exec = $this->dao->execute($sql);
    
            return ['success' => $exec, 'message' => $exec ? '' : 'Erro ao excluir o usuário'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteAdmAndUsuario($id_adm) {
        try {
            // Iniciar transação para garantir que ambas as exclusões sejam bem-sucedidas
            $this->dao->execute("START TRANSACTION");
            
            // 1. Obter o id_usuario associado ao adm
            $idUsuarioResult = $this->getIdUsuarioByAdm($id_adm);
            
            if (!$idUsuarioResult['success']) {
                throw new Exception($idUsuarioResult['message']);
            }
            
            $id_usuario = $idUsuarioResult['id_usuario'];
            
            // 2. Excluir o administrador
            $sqlAdm = "DELETE FROM tb_adm WHERE id_adm = " . $this->dao->escape_string($id_adm);
            $execAdm = $this->dao->execute($sqlAdm);
            
            if (!$execAdm) {
                throw new Exception("Falha ao excluir o administrador.");
            }
            
            // 3. Excluir o usuário
            $sqlUsuario = "DELETE FROM tb_usuario WHERE id_usuario = " . $this->dao->escape_string($id_usuario);
            $execUsuario = $this->dao->execute($sqlUsuario);
            
            if (!$execUsuario) {
                throw new Exception("Falha ao excluir o usuário associado.");
            }
            
            // Confirmar transação se tudo ocorrer bem
            $this->dao->execute("COMMIT");
            
            return ['success' => true, 'message' => 'Exclusão realizada com sucesso'];
            
        } catch (Exception $e) {
            // Reverter transação em caso de erro
            $this->dao->execute("ROLLBACK");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function cadastrarCliente($dados) {
        try {
            // Validações básicas
            $this->validarDadosCliente($dados);
            
            // Verificar se email já existe
            $email = $this->dao->escape_string($dados['emailUsuario']);
            if ($this->emailExiste($email)) {
                throw new Exception("E-mail já cadastrado no sistema.");
            }
            
            // Verificar se CPF já existe
            $cpf = $this->formatarCPF($this->dao->escape_string($dados['cpfCliente']));
            if ($this->cpfExiste($cpf)) {
                throw new Exception("CPF já cadastrado no sistema.");
            }
            
            // 1. Cadastrar usuário primeiro
            $senhaHash = password_hash($dados['senhaUsuario'], PASSWORD_DEFAULT);
            
            $sqlUsuario = sprintf(
                "INSERT INTO tb_usuario 
                (email_usuario, senha_usuario, id_tipo_usuario, email_verificado, conta_ativa) 
                VALUES ('%s', '%s', 2, 1, 1)", // 2 = ID para tipo Cliente
                $this->dao->escape_string($dados['emailUsuario']),
                $this->dao->escape_string($senhaHash)
            );
            
            if (!$this->dao->execute($sqlUsuario)) {
                throw new Exception("Erro ao cadastrar usuário.");
            }
            
            // 2. Pegar ID do usuário recém-criado
            $result = $this->dao->getData("SELECT id_usuario FROM tb_usuario ORDER BY id_usuario DESC LIMIT 1");
            if (empty($result)) {
                throw new Exception("Erro ao obter ID do usuário.");
            }
            $idUsuario = $result[0]['id_usuario'];
            
            // 3. Cadastrar cliente (sem endereço)
            $sqlCliente = sprintf(
                "INSERT INTO tb_cliente (
                    nome_cliente, cpf_cliente, data_nasc, id_usuario
                ) VALUES (
                    '%s', '%s', '%s', %d
                )",
                $this->dao->escape_string($dados['nomeCliente']),
                $cpf,
                $this->dao->escape_string($dados['nascCliente']),
                $idUsuario
            );
            
            if (!$this->dao->execute($sqlCliente)) {
                throw new Exception("Erro ao cadastrar cliente.");
            }
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Erro no cadastro: " . $e->getMessage());
        }
    }
    
    private function validarDadosCliente($dados) {
        // Verificar campos obrigatórios (removi os campos de endereço)
        $camposObrigatorios = [
            'nomeCliente', 'cpfCliente', 'nascCliente', 
            'emailUsuario', 'senhaUsuario', 'confirmaSenha'
        ];
        
        foreach ($camposObrigatorios as $campo) {
            if (empty($dados[$campo])) {
                throw new Exception("O campo " . str_replace('_', ' ', $campo) . " é obrigatório.");
            }
        }
        
        // Validar senhas
        if ($dados['senhaUsuario'] !== $dados['confirmaSenha']) {
            throw new Exception("As senhas não coincidem.");
        }
        
        if (strlen($dados['senhaUsuario']) < 8) {
            throw new Exception("A senha deve ter no mínimo 8 caracteres.");
        }
        
        // Validar CPF
        $cpf = preg_replace('/[^0-9]/', '', $dados['cpfCliente']);
        if (!$this->validarCPF($cpf)) {
            throw new Exception("CPF inválido.");
        }
        
        // Validar data de nascimento (maior de 18 anos)
        $nascimento = new DateTime($dados['nascCliente']);
        $hoje = new DateTime();
        $idade = $hoje->diff($nascimento)->y;
        
        if ($idade < 18) {
            throw new Exception("O cliente deve ter pelo menos 18 anos.");
        }
        
        // Validar e-mail
        if (!filter_var($dados['emailUsuario'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("E-mail inválido.");
        }
    }
    
    public function validarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    public function cpfExiste($cpf) {
        $sql = "SELECT id_cliente FROM tb_cliente WHERE cpf_cliente = '" . $this->dao->escape_string($cpf) . "'";
        $result = $this->dao->getData($sql);
        return !empty($result);
    }
    
    public function emailExiste($email) {
        $sql = "SELECT id_usuario FROM tb_usuario WHERE email_usuario = '" . $this->dao->escape_string($email) . "'";
        $result = $this->dao->getData($sql);
        return !empty($result);
    }
    
    private function formatarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . 
               substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    public function cadastrarConfeitaria($dados) {
        try {
            // Validações básicas
            $this->validarDadosConfeitaria($dados);
            
            // Verificar se email já existe
            $email = $this->dao->escape_string($dados['emailUsuario']);
            if ($this->emailExisteConfeitaria($email)) {
                throw new Exception("E-mail já cadastrado no sistema.");
            }
            
            // Verificar se CNPJ já existe
            $cnpj = $this->formatarCNPJ($this->dao->escape_string($dados['cnpjConfeitaria']));
            if ($this->cnpjExiste($cnpj)) {
                throw new Exception("CNPJ já cadastrado no sistema.");
            }

            // 1. Cadastrar usuário primeiro
            $senhaHash = password_hash($dados['senhaUsuario'], PASSWORD_DEFAULT);
            
            $sqlUsuario = sprintf(
                "INSERT INTO tb_usuario (
                    email_usuario, 
                    senha_usuario, 
                    id_tipo_usuario, 
                    email_verificado, 
                    conta_ativa
                ) VALUES (
                    '%s', '%s', 3, 1, 1
                )", // 3 = ID para tipo Confeitaria
                $this->dao->escape_string($dados['emailUsuario']),
                $this->dao->escape_string($senhaHash)
            );
            
            if (!$this->dao->execute($sqlUsuario)) {
                throw new Exception("Erro ao cadastrar usuário.");
            }
            
            // 2. Pegar ID do usuário recém-criado
            $result = $this->dao->getData("SELECT id_usuario FROM tb_usuario ORDER BY id_usuario DESC LIMIT 1");
            if (empty($result)) {
                throw new Exception("Erro ao obter ID do usuário.");
            }
            $idUsuario = $result[0]['id_usuario'];

            // 3. Cadastrar confeitaria
            $sqlConfeitaria = sprintf(
                "INSERT INTO tb_confeitaria (
                    nome_confeitaria, 
                    cnpj_confeitaria, 
                    cep_confeitaria, 
                    log_confeitaria, 
                    num_local, 
                    bairro_confeitaria, 
                    cidade_confeitaria, 
                    uf_confeitaria, 
                    id_usuario
                ) VALUES (
                    '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d
                )",
                $this->dao->escape_string($dados['nomeConfeitaria']),
                $cnpj,
                $this->dao->escape_string($dados['cep']),
                $this->dao->escape_string($dados['logradouro']),
                $this->dao->escape_string($dados['numLocal']),
                $this->dao->escape_string($dados['bairro']),
                $this->dao->escape_string($dados['cidade'] ?? 'Não informada'),
                $this->dao->escape_string($dados['uf']),
                $idUsuario
            );
            
            if (!$this->dao->execute($sqlConfeitaria)) {
                // Rollback em caso de erro
                $this->dao->execute("DELETE FROM tb_usuario WHERE id_usuario = ".$idUsuario);
                throw new Exception("Erro ao cadastrar confeitaria.");
            }
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Erro ao cadastrar confeitaria: " . $e->getMessage());
        }
    }

    private function validarDadosConfeitaria($dados) {
        // Campos obrigatórios
        $camposObrigatorios = [
            'nomeConfeitaria', 'cnpjConfeitaria', 'emailUsuario',
            'senhaUsuario', 'confirmaSenha', 'cep', 'logradouro',
            'numLocal', 'bairro', 'uf'
        ];
        
        foreach ($camposObrigatorios as $campo) {
            if (empty($dados[$campo])) {
                throw new Exception("O campo " . str_replace('_', ' ', $campo) . " é obrigatório.");
            }
        }
        
        // Validar senhas
        if ($dados['senhaUsuario'] !== $dados['confirmaSenha']) {
            throw new Exception("As senhas não coincidem.");
        }
        
        if (strlen($dados['senhaUsuario']) < 8) {
            throw new Exception("A senha deve ter no mínimo 8 caracteres.");
        }
        
        // Validar CNPJ
        if (!$this->validarCNPJ($dados['cnpjConfeitaria'])) {
            throw new Exception("CNPJ inválido.");
        }
        
        // Validar e-mail
        if (!filter_var($dados['emailUsuario'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("E-mail inválido.");
        }
    }

    public function validarCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais (inválido)
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        
        // Validação do primeiro dígito verificador
        $soma = 0;
        $peso = 5;
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $peso;
            $peso = ($peso == 2) ? 9 : $peso - 1;
        }
        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;
        
        if ($cnpj[12] != $digito1) {
            return false;
        }
        
        // Validação do segundo dígito verificador
        $soma = 0;
        $peso = 6;
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $peso;
            $peso = ($peso == 2) ? 9 : $peso - 1;
        }
        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;
        
        if ($cnpj[13] != $digito2) {
            return false;
        }
        
        return true;
    }

    public function cnpjExiste($cnpj) {
        $sql = "SELECT id_confeitaria FROM tb_confeitaria WHERE cnpj_confeitaria = '".$this->dao->escape_string($cnpj)."'";
        $result = $this->dao->getData($sql);
        return !empty($result);
    }

    public function emailExisteConfeitaria($email) {
        $sql = "SELECT id_usuario FROM tb_usuario WHERE email_usuario = '".$this->dao->escape_string($email)."'";
        $result = $this->dao->getData($sql);
        return !empty($result);
    }

    private function formatarCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . 
               substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }

}