<?php
require_once('src/PHPMailer.php');
require_once('src/SMTP.php');
require_once('src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email
{
    public function enviarEmail($email, $codigo)
    {
        $mail = new PHPMailer(true);

        try {
            // Configurações do SMTP
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'emulador.igor2@gmail.com';
            $mail->Password = 'pvlt vtfu mcxg brhs';
            $mail->Port = 587;

            // Remetente e destinatário
            $mail->setFrom('emulador.igor2@gmail.com', 'Equipe FoxBitSystem');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Endereço de email inválido.');
            }
            $mail->addAddress($email);

            // Conteúdo do email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Email de Verificação';

            // Corpo do email (HTML estilizado)
            $mail->Body = '
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Email de Verificação</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .email-container {
                    background-color: #ffffff;
                    width: 100%;
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                }
                .email-header {
                    text-align: center;
                    color: #333333;
                }
                .email-header h1 {
                    font-size: 24px;
                    margin-bottom: 10px;
                }
                .email-body {
                    font-size: 16px;
                    line-height: 1.5;
                    color: #555555;
                    text-align: center;
                    margin-bottom: 20px;
                }
                .code-box {
                    display: inline-block;
                    font-size: 24px;
                    font-weight: bold;
                    color: #ffffff;
                    background-color: #6c63ff;
                    padding: 15px 30px;
                    border-radius: 5px;
                    margin-top: 20px;
                }
                .email-footer {
                    text-align: center;
                    font-size: 14px;
                    color: #888888;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h1>Verificação de Conta</h1>
                </div>
                <div class="email-body">
                    <p>Olá!</p>
                    <p>Para completar seu processo de verificação, por favor, use o código abaixo:</p>
                    <div class="code-box">
                        ' . $codigo . '
                    </div>
                    <p>Este código é válido por 6 horas.</p>
                </div>
                <div class="email-footer">
                    <p>Se você não solicitou este código, por favor, ignore este email.</p>
                    <p>Obrigado, sua equipe de suporte.</p>
                </div>
            </div>
        </body>
        </html>';

            $mail->send();

        } catch (Exception $e) {
            error_log("Erro ao enviar mensagem: {$mail->ErrorInfo}");
            return false;
        }
    }
}
