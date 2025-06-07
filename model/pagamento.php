<?php
class Pagamento
{
    private int $idFormaPagamento;
    private string $formaPagamento;
    private string $descricao;

    public function getIdFormaPagamento(): int
    {
        return $this->idFormaPagamento;
    }

    public function setIdFormaPagamento(int $idFormaPagamento): void
    {
        $this->idFormaPagamento = $idFormaPagamento;
    }

    public function getFormaPagamento(): string
    {
        return $this->formaPagamento;
    }

    public function setFormaPagamento(string $formaPagamento): void
    {
        $this->formaPagamento = $formaPagamento;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }
}
?>