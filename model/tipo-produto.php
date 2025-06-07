<?php
class TipoProduto
{
    private string $descTipoProduto;
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDescTipoProduto(): string
    {
        return $this->descTipoProduto;
    }

    public function setDescTipoProduto(string $descTipoProduto): void
    {
        $this->descTipoProduto = $descTipoProduto;
    }
}

?>