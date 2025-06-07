<?php
include_once '../model/dao.php';
include_once '../controller/controller-usuario.php';
$dao = new DAO();
$usuario = new Usuario();

date_default_timezone_set('America/Sao_Paulo');

session_start();
if (isset($_POST['id'])) {
    $usuario->setOnline(date('Y-m-d H:i:s'));
    $dao->execute("UPDATE tb_usuario SET online = '{$usuario->getOnline()}' WHERE id_usuario = {$_POST['id']}");
    session_destroy();
}
echo "<script language='javascript' type='text/javascript'> window.location.href='index.php'</script>";
?>