<?php
class Produto
{
    private int $id;
    private string $nomeProduto;
    private string $descProduto;
    private bool $ativoProduto;
    private float $valorProduto;
    private float $frete;
    private float $distanciaMaxima;
    private int $idTipoProduto;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getIdTipoProduto(): int
    {
        return $this->idTipoProduto;
    }

    public function setIdTipoProduto(int $idTipoProduto): void
    {
        $this->idTipoProduto = $idTipoProduto;
    }
    public function getNomeProduto(): string
    {
        return $this->nomeProduto;
    }

    public function setNomeProduto(string $nomeProduto): void
    {
        $this->nomeProduto = $nomeProduto;
    }

    public function getDescProduto(): string
    {
        return $this->descProduto;
    }

    public function setDescProduto(string $descProduto): void
    {
        $this->descProduto = $descProduto;
    }

    public function getAtivoProduto(): bool
    {
        return $this->ativoProduto;
    }

    public function setAtivoProduto(bool $ativoProduto): void
    {
        $this->ativoProduto = $ativoProduto;
    }

    public function getValorProduto(): float
    {
        return $this->valorProduto;
    }

    public function setValorProduto(float $valorProduto): void
    {
        $this->valorProduto = $valorProduto;
    }
    
    public function getFrete(): float {
        return $this->frete;
    }
    
    public function setFrete(float $frete): void {
        $this->frete = $frete;
    }

    public function getDistanciaMaxima(): float {
        return $this->distanciaMaxima;
    }
    
    public function setDistanciaMaxima(float $distanciaMaxima): void {
        $this->distanciaMaxima = $distanciaMaxima;
    }
}

?>