<?php
class Telefone
{
    private $idTelefone;
    private string $numTelelefone;
    private int $tipoTelefone;

    public function getIdTelefone(): int
    {
        return $this->idTelefone;
    }

    public function setIdTelefone(int $idTelefone): void
    {
        $this->idTelefone = $idTelefone;
    }
    public function getNumTelefone(): string
    {
        return $this->numTelelefone;
    }

    public function setNumTelefone(string $numTelelefone): void
    {
        $this->numTelelefone = $numTelelefone;
    }

    public function getTipoTelefone(): int
    {
        return $this->tipoTelefone;
    }

    public function setTipoTelefone(int $tipoTelefone): void
    {
        $this->tipoTelefone = $tipoTelefone;
    }
}
?>