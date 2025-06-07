<?php
require_once '../model/dao.php';

class ControllerSuporte
{
    private $dao;

    public function __construct()
    {
        $this->dao = new DAO();
    }

    public function listarSuportes($filtro = '')
    {
        try {
            $sql = "SELECT s.id_suporte, s.titulo_suporte, s.desc_suporte, s.resolvido,
                        ts.tipo_suporte, 
                        u.email_usuario, 
                        tu.tipo_usuario,
                        COALESCE(c.nome_cliente, cf.nome_confeitaria, a.nome_adm, 'Usuário não encontrado') AS nome_usuario
                    FROM tb_suporte s
                    JOIN tb_tipo_suporte ts ON s.id_tipo_suporte = ts.id_tipo_suporte
                    JOIN tb_usuario u ON s.id_usuario = u.id_usuario
                    JOIN tb_tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
                    LEFT JOIN tb_cliente c ON u.id_usuario = c.id_usuario AND tu.id_tipo_usuario = 2
                    LEFT JOIN tb_confeitaria cf ON u.id_usuario = cf.id_usuario AND tu.id_tipo_usuario = 3
                    LEFT JOIN tb_adm a ON u.id_usuario = a.id_usuario AND tu.id_tipo_usuario = 1";
            
            if (!empty($filtro)) {
                $filtro = $this->dao->escape_string($filtro);
                $sql .= " WHERE s.titulo_suporte LIKE '%$filtro%' 
                        OR s.desc_suporte LIKE '%$filtro%' 
                        OR ts.tipo_suporte LIKE '%$filtro%' 
                        OR u.email_usuario LIKE '%$filtro%'
                        OR c.nome_cliente LIKE '%$filtro%'
                        OR cf.nome_confeitaria LIKE '%$filtro%'
                        OR a.nome_adm LIKE '%$filtro%'";
            }
            
            $sql .= " ";
            
            $result = $this->dao->getData($sql);
            return $result ? $result : [];
            
        } catch (Exception $e) {
            error_log("Erro ao listar suportes: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Erro ao carregar dados do servidor'
            ];
        }
    }

    public function buscarSuportePorId($id_suporte) {
        try {
            $sql = "SELECT s.id_suporte, s.titulo_suporte, s.desc_suporte, s.resolvido,
                        ts.tipo_suporte, 
                        u.email_usuario, 
                        tu.tipo_usuario,
                        COALESCE(c.nome_cliente, cf.nome_confeitaria, a.nome_adm, 'Usuário não encontrado') AS nome_usuario,
                        COALESCE(c.cpf_cliente, cf.cnpj_confeitaria, a.cpf_adm, 'Não informado') AS documento_usuario
                    FROM tb_suporte s
                    JOIN tb_tipo_suporte ts ON s.id_tipo_suporte = ts.id_tipo_suporte
                    JOIN tb_usuario u ON s.id_usuario = u.id_usuario
                    JOIN tb_tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
                    LEFT JOIN tb_cliente c ON u.id_usuario = c.id_usuario AND tu.id_tipo_usuario = 2
                    LEFT JOIN tb_confeitaria cf ON u.id_usuario = cf.id_usuario AND tu.id_tipo_usuario = 3
                    LEFT JOIN tb_adm a ON u.id_usuario = a.id_usuario AND tu.id_tipo_usuario = 1
                    WHERE s.id_suporte = " . $this->dao->escape_string($id_suporte);
            
            $result = $this->dao->getData($sql);
            return $result ? $result[0] : null;
            
        } catch (Exception $e) {
            error_log("Erro ao buscar suporte por ID: " . $e->getMessage());
            return null;
        }
    }

    public function listarSuportesClientes($filtro = '')
    {
        try {
            $sql = "SELECT s.id_suporte, s.titulo_suporte, s.desc_suporte, s.resolvido,
                        ts.tipo_suporte, 
                        u.email_usuario, 
                        tu.tipo_usuario,
                        c.nome_cliente AS nome_usuario
                    FROM tb_suporte s
                    JOIN tb_tipo_suporte ts ON s.id_tipo_suporte = ts.id_tipo_suporte
                    JOIN tb_usuario u ON s.id_usuario = u.id_usuario
                    JOIN tb_tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
                    JOIN tb_cliente c ON u.id_usuario = c.id_usuario
                    WHERE tu.id_tipo_usuario = 2";

            if (!empty($filtro)) {
                $filtro = $this->dao->escape_string($filtro);
                $sql .= " AND (
                            s.titulo_suporte LIKE '%$filtro%' 
                            OR s.desc_suporte LIKE '%$filtro%' 
                            OR ts.tipo_suporte LIKE '%$filtro%' 
                            OR u.email_usuario LIKE '%$filtro%' 
                            OR c.nome_cliente LIKE '%$filtro%'
                        )";
            }

            $result = $this->dao->getData($sql);
            return $result ? $result : [];

        } catch (Exception $e) {
            error_log("Erro ao listar suportes de clientes: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Erro ao carregar dados dos clientes'
            ];
        }
    }

    public function listarSuportesClientesCadastro($filtro = '')
    {
        try {
            $sql = "SELECT s.id_suporte, s.titulo_suporte, s.desc_suporte, s.resolvido,
                        ts.tipo_suporte, 
                        u.email_usuario, 
                        tu.tipo_usuario,
                        c.nome_cliente AS nome_usuario
                    FROM tb_suporte s
                    JOIN tb_tipo_suporte ts ON s.id_tipo_suporte = ts.id_tipo_suporte
                    JOIN tb_usuario u ON s.id_usuario = u.id_usuario
                    JOIN tb_tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
                    JOIN tb_cliente c ON u.id_usuario = c.id_usuario
                    WHERE tu.id_tipo_usuario = 2
                    AND ts.id_tipo_suporte IN (1, 5)";  // Agora filtra pelos tipos 1 E 5
    
            if (!empty($filtro)) {
                $filtro = $this->dao->escape_string($filtro);
                $sql .= " AND (
                            s.titulo_suporte LIKE '%$filtro%' 
                            OR s.desc_suporte LIKE '%$filtro%' 
                            OR u.email_usuario LIKE '%$filtro%' 
                            OR c.nome_cliente LIKE '%$filtro%'
                        )";
            }
    
            $result = $this->dao->getData($sql);
            return $result ? $result : [];
    
        } catch (Exception $e) {
            error_log("Erro ao listar suportes tipos 1 e 5: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Erro ao carregar suportes dos tipos 1 e 5'
            ];
        }
    }

    public function listarSuportesExcetoCadastraisCliente($filtro = '')
    {
        try {
            $sql = "SELECT s.id_suporte, s.titulo_suporte, s.desc_suporte, s.resolvido,
                        ts.tipo_suporte, 
                        u.email_usuario, 
                        tu.tipo_usuario,
                        c.nome_cliente AS nome_usuario
                    FROM tb_suporte s
                    JOIN tb_tipo_suporte ts ON s.id_tipo_suporte = ts.id_tipo_suporte
                    JOIN tb_usuario u ON s.id_usuario = u.id_usuario
                    JOIN tb_tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
                    JOIN tb_cliente c ON u.id_usuario = c.id_usuario
                    WHERE tu.id_tipo_usuario = 2
                    AND ts.id_tipo_suporte NOT IN (5)";  // Exclui os tipos 5 e 10

            if (!empty($filtro)) {
                $filtro = $this->dao->escape_string($filtro);
                $sql .= " AND (
                            s.titulo_suporte LIKE '%$filtro%' 
                            OR s.desc_suporte LIKE '%$filtro%' 
                            OR u.email_usuario LIKE '%$filtro%' 
                            OR c.nome_cliente LIKE '%$filtro%'
                        )";
            }

            $result = $this->dao->getData($sql);
            return $result ? $result : [];

        } catch (Exception $e) {
            error_log("Erro ao listar suportes (exceto tipos 5 e 10): " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Erro ao carregar suportes (exceto tipos 5 e 10)'
            ];
        }
    }

    public function listarSuportesConfeitarias($filtro = '')
    {
        try {
            $sql = "SELECT s.id_suporte, s.titulo_suporte, s.desc_suporte, s.resolvido,
                        ts.tipo_suporte, 
                        u.email_usuario, 
                        tu.tipo_usuario,
                        cf.nome_confeitaria AS nome_usuario
                    FROM tb_suporte s
                    JOIN tb_tipo_suporte ts ON s.id_tipo_suporte = ts.id_tipo_suporte
                    JOIN tb_usuario u ON s.id_usuario = u.id_usuario
                    JOIN tb_tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
                    JOIN tb_confeitaria cf ON u.id_usuario = cf.id_usuario
                    WHERE tu.id_tipo_usuario = 3";

            if (!empty($filtro)) {
                $filtro = $this->dao->escape_string($filtro);
                $sql .= " AND (
                            s.titulo_suporte LIKE '%$filtro%' 
                            OR s.desc_suporte LIKE '%$filtro%' 
                            OR ts.tipo_suporte LIKE '%$filtro%' 
                            OR u.email_usuario LIKE '%$filtro%' 
                            OR cf.nome_confeitaria LIKE '%$filtro%'
                        )";
            }

            $result = $this->dao->getData($sql);
            return $result ? $result : [];

        } catch (Exception $e) {
            error_log("Erro ao listar suportes de confeitarias: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Erro ao carregar dados das confeitarias'
            ];
        }
    }

    public function listarSuportesConfeitariasCadastro($filtro = '')
    {
        try {
            $sql = "SELECT s.id_suporte, s.titulo_suporte, s.desc_suporte, s.resolvido,
                        ts.tipo_suporte, 
                        u.email_usuario, 
                        tu.tipo_usuario,
                        cf.nome_confeitaria AS nome_usuario
                    FROM tb_suporte s
                    JOIN tb_tipo_suporte ts ON s.id_tipo_suporte = ts.id_tipo_suporte
                    JOIN tb_usuario u ON s.id_usuario = u.id_usuario
                    JOIN tb_tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
                    JOIN tb_confeitaria cf ON u.id_usuario = cf.id_usuario
                    WHERE tu.id_tipo_usuario = 3
                    AND s.id_tipo_suporte = 10";

            if (!empty($filtro)) {
                $filtro = $this->dao->escape_string($filtro);
                $sql .= " AND (
                            s.titulo_suporte LIKE '%$filtro%' 
                            OR s.desc_suporte LIKE '%$filtro%' 
                            OR ts.tipo_suporte LIKE '%$filtro%' 
                            OR u.email_usuario LIKE '%$filtro%' 
                            OR cf.nome_confeitaria LIKE '%$filtro%'
                        )";
            }

            $result = $this->dao->getData($sql);
            return $result ? $result : [];

        } catch (Exception $e) {
            error_log("Erro ao listar suportes de confeitarias (tipo 10): " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Erro ao carregar dados das confeitarias (tipo 10)'
            ];
        }
    }

    public function listarSuportesConfeitariasExcetoCadastrao($filtro = '')
    {
        try {
            $sql = "SELECT s.id_suporte, s.titulo_suporte, s.desc_suporte, s.resolvido,
                        ts.tipo_suporte, 
                        u.email_usuario, 
                        tu.tipo_usuario,
                        cf.nome_confeitaria AS nome_usuario
                    FROM tb_suporte s
                    JOIN tb_tipo_suporte ts ON s.id_tipo_suporte = ts.id_tipo_suporte
                    JOIN tb_usuario u ON s.id_usuario = u.id_usuario
                    JOIN tb_tipo_usuario tu ON u.id_tipo_usuario = tu.id_tipo_usuario
                    JOIN tb_confeitaria cf ON u.id_usuario = cf.id_usuario
                    WHERE tu.id_tipo_usuario = 3
                    AND s.id_tipo_suporte != 10";

            if (!empty($filtro)) {
                $filtro = $this->dao->escape_string($filtro);
                $sql .= " AND (
                            s.titulo_suporte LIKE '%$filtro%' 
                            OR s.desc_suporte LIKE '%$filtro%' 
                            OR ts.tipo_suporte LIKE '%$filtro%' 
                            OR u.email_usuario LIKE '%$filtro%' 
                            OR cf.nome_confeitaria LIKE '%$filtro%'
                        )";
            }

            $result = $this->dao->getData($sql);
            return $result ? $result : [];

        } catch (Exception $e) {
            error_log("Erro ao listar suportes de confeitarias (exceto tipo 10): " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'Erro ao carregar dados das confeitarias (exceto tipo 10)'
            ];
        }
    }

    public function marcarComoResolvido($id_suporte)
    {
        try {
            $sql = "UPDATE tb_suporte SET resolvido = 1 WHERE id_suporte = " . $this->dao->escape_string($id_suporte);
            $exec = $this->dao->execute($sql);
            
            return ['success' => $exec, 'message' => $exec ? '' : 'Erro desconhecido'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deletarSuporte($id_suporte)
    {
        try {
            $sql = "DELETE FROM tb_suporte WHERE id_suporte = " . $this->dao->escape_string($id_suporte);
            $exec = $this->dao->execute($sql);
            
            return ['success' => $exec, 'message' => $exec ? '' : 'Erro desconhecido'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}