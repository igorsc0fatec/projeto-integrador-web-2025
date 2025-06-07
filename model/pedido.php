<?php
class Pedido
{
    private int $id;
    private float $valorTotal;
    private float $desconto;
    private string $dataPedido;
    private string $horaPedido;
    private float $frete;
    private string $status;
    private int $idCliente;
    private int $idFormaPagamento;
    private string $idEnderecoCliente;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    public function setValorTotal(float $valorTotal): void
    {
        if ($valorTotal <= 0) {
            throw new Exception("Valor total do pedido deve ser maior que zero.");
        }

        $this->valorTotal = $valorTotal;
    }

    public function getDesconto(): float
    {
        return $this->desconto;
    }

    public function setDesconto(float $desconto): void
    {
        if ($desconto < 0 || $desconto > 1) {
            throw new Exception("Desconto deve ser um valor entre 0 e 1.");
        }

        $this->desconto = $desconto;
    }

    public function getDataPedido(): string
    {
        return $this->dataPedido;
    }

    public function setDataPedido(string $dataPedido): void
    {
        $this->dataPedido = $dataPedido;
    }

    public function getHoraPedido(): string
    {
        return $this->horaPedido;
    }

    public function setHoraPedido(string $horaPedido): void
    {
        $this->horaPedido = $horaPedido;
    }

    public function getFrete(): float
    {
        return $this->frete;
    }

    public function setFrete(float $frete): void
    {
        if ($frete < 0) {
            throw new Exception("Valor do frete deve ser maior ou igual a zero.");
        }

        $this->frete = $frete;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getIdCliente(): int
    {
        return $this->idCliente;
    }

    public function setIdCliente(int $idCliente): void
    {
        if ($idCliente <= 0) {
            throw new Exception("ID do cliente deve ser maior que zero.");
        }
        $this->idCliente = $idCliente;
    }

    public function getIdFormaPagamento(): int
    {
        return $this->idFormaPagamento;
    }

    public function setIdFormaPagamento(int $idFormaPagamento): void
    {
        $this->idFormaPagamento = $idFormaPagamento;
    }

    public function getIdEnderecoCliente()
    {
        return $this->idEnderecoCliente;
    }

    public function setIdEnderecoCliente($idEnderecoCliente)
    {
        $this->idEnderecoCliente = $idEnderecoCliente;
    }

}
?>