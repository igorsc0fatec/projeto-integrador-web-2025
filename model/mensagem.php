<?php
class Mensagem
{
    private int $idMensagem;
    private int $idImgMensagem;
    private String $mensagem;
    private String $imgMensagem;
    private String $status;
    private int $idRemetente;
    private int $idDestinatario;

    public function getIdMensagem(): int
    {
        return $this->idMensagem;
    }

    public function setIdMensagem(int $idMensagem): void
    {
        $this->idMensagem = $idMensagem;
    }

    public function getIdImgMensagem(): int
    {
        return $this->idImgMensagem;
    }

    public function setIdImgMensagem(int $idImgMensagem): void
    {
        $this->idImgMensagem = $idImgMensagem;
    }

    public function getMensagem(): String
    {
        return $this->mensagem;
    }

    public function setMensagem(String $mensagem): void
    {
        $this->mensagem = $mensagem;
    }

    public function getImgMensagem(): String
    {
        return $this->imgMensagem;
    }

    public function setImgMensagem(String $imgMensagem): void
    {
        $this->imgMensagem = $imgMensagem;
    }

    public function getStatus(): String
    {
        return $this->status;
    }

    public function setStatus(String $status): void
    {
        $this->status = $status;
    }

    public function getIdRemetente(): int
    {
        return $this->idRemetente;
    }

    public function setIdRemetente(int $idRemetente): void
    {
        $this->idRemetente = $idRemetente;
    }

    public function getIdDestinatario(): int
    {
        return $this->idDestinatario;
    }

    public function setIdDestinatario(int $idDestinatario): void
    {
        $this->idDestinatario = $idDestinatario;
    }
}
?>