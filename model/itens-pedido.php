<?php

class ItensPedido
{
    private int $id;
    private int $quantidade;
    private int $idProduto;
    private int $idPedido;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new Exception("ID do item do pedido deve ser maior que zero.");
        }

        $this->id = $id;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function setQuantidade(int $quantidade): void
    {
        if ($quantidade < 1) {
            throw new Exception("Quantidade de itens deve ser maior que zero.");
        }

        $this->quantidade = $quantidade;
    }

    public function getIdProduto(): int
    {
        return $this->idProduto;
    }

    public function setIdProduto(int $idProduto): void
    {
        if ($idProduto <= 0) {
            throw new Exception("ID do produto deve ser maior que zero.");
        }

        $this->idProduto = $idProduto;
    }

    public function getIdPedido(): int
    {
        return $this->idPedido;
    }

    public function setIdPedido(int $idPedido): void
    {
        if ($idPedido <= 0) {
            throw new Exception("ID do pedido deve ser maior que zero.");
        }

        $this->idPedido = $idPedido;
    }
}
?>