<?php
require_once 'controller-chat.php';
require_once 'controller-cliente.php';
require_once 'controller-cobertura.php';
require_once 'controller-confeitaria.php';
require_once 'controller-decoracao.php';
require_once 'controller-formato.php';
require_once 'controller-massa.php';
require_once 'controller-pedido-personalizado.php';
require_once 'controller-pedido.php';
require_once 'controller-personalizado.php';
require_once 'controller-produto.php';
require_once 'controller-recheio.php';
require_once 'controller-suporte.php';
require_once 'controller-telefone.php';
require_once 'controller-tipo-produto.php';
require_once 'controller-usuario.php';

class ControllerConfig {
    public $chat;
    public $cliente;
    public $cobertura;
    public $confeitaria;
    public $decoracao;
    public $formato;
    public $massa;
    public $pedidoPersonalizado;
    public $pedido;
    public $personalizado;
    public $produto;
    public $recheio;
    public $suporte;
    public $telefone;
    public $tipoProduto;
    public $usuario;

    public function __construct() {
        $this->chat = new ControllerChat();
        $this->cliente = new ControllerCliente();
        $this->cobertura = new ControllerCobertura();
        $this->confeitaria = new ControllerConfeitaria();
        $this->decoracao = new ControllerDecoracao();
        $this->formato = new ControllerFormato();
        $this->massa = new ControllerMassa();
        $this->pedidoPersonalizado = new ControllerPedidoPersonalizado();
        $this->pedido = new ControllerPedido();
        $this->personalizado = new ControllerPersonalizado();
        $this->produto = new ControllerProduto();
        $this->recheio = new ControllerRecheio();
        $this->suporte = new ControllerSuporte();
        $this->telefone = new ControllerTelefone();
        $this->tipoProduto = new ControllerTipoProduto();
        $this->usuario = new ControllerUsuario();
    }
}