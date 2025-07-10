<?php

// Apenas processa requisições POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- COLETA E LIMPEZA DOS DADOS ---
    // Use o operador de coalescência nula (??) para campos opcionais
    // e htmlspecialchars para segurança básica contra XSS.
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : ''; // Opcional
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : ''; // Opcional
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // --- VALIDAÇÃO DOS DADOS OBRIGATÓRIOS ---
    // Verifique apenas os campos que são realmente obrigatórios (required no HTML).
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Define o código de resposta 400 (bad request) e encerra.
        http_response_code(400);
        echo "Por favor, preencha os campos obrigatórios (Nome, Email, Mensagem) e tente novamente.";
        exit;
    }

    // --- PREPARAÇÃO DO EMAIL ---
    
    // ATUALIZE AQUI: Coloque o email que receberá a mensagem.
    $recipient = "contatoinstitutoibratec@gmail.com";

    // Assunto do email que você receberá.
    $sender_subject = "Novo contato do site: $subject";

    // Construção do corpo do email.
    $email_content = "Você recebeu uma nova mensagem do formulário de contato do seu site:\n\n";
    $email_content .= "Nome: $name\n";
    $email_content .= "Email: $email\n";
    
    if (!empty($phone)) {
        $email_content .= "Telefone: $phone\n";
    }

    $email_content .= "Assunto: $subject\n\n";
    $email_content .= "Mensagem:\n$message\n";

    // Cabeçalhos do email. (ALTERAÇÃO 1: Adicionados cabeçalhos para UTF-8)
    $email_headers = "MIME-Version: 1.0" . "\r\n";
    $email_headers .= "Content-Type: text/plain; charset=UTF-8" . "\r\n";
    $email_headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
    $email_headers .= "From: $name <$email>" . "\r\n";

    // ALTERAÇÃO 2: Codificação do assunto para aceitar acentos
    $encoded_subject = '=?UTF-8?B?' . base64_encode($sender_subject) . '?=';

    // --- ENVIO DO EMAIL ---
    // ALTERAÇÃO 3: Usando a variável do assunto codificada
    if (mail($recipient, $encoded_subject, $email_content, $email_headers)) {
        // Define o código de resposta 200 (ok).
        http_response_code(200);
        echo "Obrigado! Sua mensagem foi enviada com sucesso.";
    } else {
        // Define o código de resposta 500 (internal server error).
        http_response_code(500);
        echo "Oops! Algo deu errado e não foi possível enviar sua mensagem.";
    }

} else {
    // Não é uma requisição POST, define o código de resposta 403 (forbidden).
    http_response_code(403);
    echo "Houve um problema com seu envio, por favor, tente novamente.";
}

?>