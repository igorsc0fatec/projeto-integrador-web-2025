<?php
class Recheio
{
    private string $descRecheio;
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

    public function getDescRecheio(): string
    {
        return $this->descRecheio;
    }

    public function setDescRecheio(string $descRecheio): void
    {
        $this->descRecheio = $descRecheio;
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