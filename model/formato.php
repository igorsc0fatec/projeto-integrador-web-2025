<?php
class Formato
{
    private string $descFormato;
    private int $id;
    private float $valorPeso;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDescFormato(): string
    {
        return $this->descFormato;
    }

    public function setDescFormato(string $descFormato): void
    {
        $this->descFormato = $descFormato;
    }

    public function getValorPeso(): float
    {
        return $this->valorPeso;
    }

    public function setValorPeso(float $valorPeso): void
    {
        $this->valorPeso = $valorPeso;
    }
}

?>