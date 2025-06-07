<?php
include_once 'conexao.php';

class DAO extends Conexao
{
	public function __construct()
	{
		parent::__construct();
	}

	public function escape_string($value)
	{
		return $this->conn->real_escape_string($value);
	}

	public function execute($query)
	{
		$result = $this->conn->query($query);

		if ($result === false) {
			throw new Exception('Error: ' . $this->conn->error);
		}

		return true;
	}

	public function getData($query)
	{
		$result = $this->conn->query($query);

		if ($result === false) {
			throw new Exception('Error: ' . $this->conn->error);
		}

		$rows = array();

		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}

		return $rows;
	}

	public function delete($query)
	{
		$result = $this->conn->query($query);

		if ($result === false) {
			throw new Exception('Error: ' . $this->conn->error);
		}
		return true;
	}

	public function getLastInsertedId($table, $id_column)
	{
		$query = "SELECT MAX($id_column) as last_id FROM $table";
		$result = $this->conn->query($query);

		if ($result === false) {
			throw new Exception('Error: ' . $this->conn->error);
		}

		$row = $result->fetch_assoc();
		return $row['last_id'] ?? null;
	}

	function calcularDistancia($lat1, $lon1, $lat2, $lon2)
	{
		$raioTerra = 6371; // Raio da Terra em km

		$dLat = deg2rad($lat2 - $lat1);
		$dLon = deg2rad($lon2 - $lon1);

		$a = sin($dLat / 2) * sin($dLat / 2) +
			cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
			sin($dLon / 2) * sin($dLon / 2);

		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

		return $raioTerra * $c;
	}
}
