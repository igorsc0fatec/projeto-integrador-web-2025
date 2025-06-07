<?php
class PedidoPersonalizado
{
    private int $idPedidoPersonalizado;
    private float $valorTotal;
    private float $desconto;
    private string $dataPedido;
    private string $horaPedido;
    private float $frete;
    private int $idFormaPagamento;
    private string $status;
    private int $idMassa;
    private int $idRecheio;
    private int $idCobertura;
    private int $idFormato;
    private int $idDecoracao;
    private int $idPersonalizado;
    private int $idCliente;

    public function getIdPedidoPersonalizado(): int
    {
        return $this->idPedidoPersonalizado;
    }

    public function setIdPedidoPersonalizado(int $idPedidoPersonalizado): void
    {
        $this->idPedidoPersonalizado = $idPedidoPersonalizado;
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

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
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

    public function getIdFormaPagamento(): int
    {
        return $this->idFormaPagamento;
    }

    public function setIdFormaPagamento(int $idFormaPagamento): void
    {
        $this->idFormaPagamento = $idFormaPagamento;
    }

    public function getIdMassa(): int
    {
        return $this->idMassa;
    }

    public function setIdMassa(int $idMassa): void
    {
        $this->idMassa = $idMassa;
    }

    public function getIdRecheio(): int
    {
        return $this->idRecheio;
    }

    public function setIdRecheio(int $idRecheio): void
    {
        $this->idRecheio = $idRecheio;
    }

    public function getIdCobertura(): int
    {
        return $this->idCobertura;
    }

    public function setIdCobertura(int $idCobertura): void
    {
        $this->idCobertura = $idCobertura;
    }

    public function getIdFormato(): int
    {
        return $this->idFormato;
    }

    public function setIdFormato(int $idFormato): void
    {
        $this->idFormato = $idFormato;
    }

    public function getIdDecoracao(): int
    {
        return $this->idDecoracao;
    }

    public function setIdDecoracao(int $idDecoracao): void
    {
        $this->idDecoracao = $idDecoracao;
    }

    public function getIdPersonalizado(): int
    {
        return $this->idPersonalizado;
    }

    public function setIdPersonalizado(int $idPersonalizado): void
    {
        $this->idPersonalizado = $idPersonalizado;
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

}
?>