<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HemocentroCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $nome;

    public function __construct($nome, $email, $password)
    {
        $this->nome = $nome;
        $this->email = $email;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo ao Tuntum - Credenciais de Acesso'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hemocentro_credentials',
            with: [
                'nome' => $this->nome,
                'email' => $this->email,
                'password' => $this->password,
                'link' => 'http://localhost:8000/login' //TODO: Alterar para o link correto
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

