<?php
class Conexao
{
	private $_host = 'localhost';
	private $_username = 'root';
	private $_password = '';
	private $_database = 'db_delicias_online';
	protected $conn;

	public function __construct()
	{
		try {
			$this->conn = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);
		} catch (Exception $e) {
			throw new Exception("Erro de conexão: " . $e->getMessage());
		}
	}
}