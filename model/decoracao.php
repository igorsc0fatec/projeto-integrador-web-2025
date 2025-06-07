<?php
class Decoracao
{
    private string $descDecoracao;
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

    public function getDescDecoracao(): string
    {
        return $this->descDecoracao;
    }

    public function setDescDecoracao(string $descDecoracao): void
    {
        $this->descDecoracao = $descDecoracao;
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