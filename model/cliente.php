<?php
class Cliente
{
    private int $idCliente;
    private string $nomeCliente;
    private string $cpfCliente;
    private string $nascCliente;

    public function getIdCliente()
    {
        return $this->idCliente;
    }

    public function setIdCliente(int $idCliente)
    {
        $this->idCliente = $idCliente;
    }

    public function getNomeCliente(): string
    {
        return $this->nomeCliente;
    }

    public function setNomeCliente(string $nomeCliente): void
    {
        $this->nomeCliente = $nomeCliente;
    }

    public function getCpfCliente(): string
    {
        return $this->cpfCliente;
    }

    public function setCpfCliente(string $cpfCliente): void
    {
        $this->cpfCliente = $cpfCliente;
    }

    public function getNascCliente(): string
    {
        return $this->nascCliente;
    }

    public function setNascCliente(string $nascCliente): void
    {
        $this->nascCliente = $nascCliente;
    }
}
?>