<?php
class Conversa
{
    private string $idConversa;
    private string $dataCriacao;
    private int $idRemetente;
    private int $idDestinatario;

    public function getIdConversa(): string
    {
        return $this->idConversa;
    }

    public function setIdConversa(string $idConversa): self
    {
        $this->idConversa = $idConversa;
        return $this;
    }

    public function getDataCriacao(): string
    {
        return $this->dataCriacao;
    }

    public function setDataCriacao(string $dataCriacao): self
    {
        $this->dataCriacao = $dataCriacao;
        return $this;
    }

    public function getIdRemetente(): int
    {
        return $this->idRemetente;
    }

    public function setIdRemetente(int $idRemetente): self
    {
        $this->idRemetente = $idRemetente;
        return $this;
    }

    public function getIdDestinatario(): int
    {
        return $this->idDestinatario;
    }

    public function setIdDestinatario(int $idDestinatario): self
    {
        $this->idDestinatario = $idDestinatario;
        return $this;
    }
}
?>