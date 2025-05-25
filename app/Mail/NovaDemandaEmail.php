<?php

namespace App\Mail;

use App\Models\Demanda;
use App\Models\Doador;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NovaDemandaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $demanda;
    public $doador;

    public function __construct(Demanda $demanda, Doador $doador)
    {
        $this->demanda = $demanda;
        $this->doador = $doador;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova Demanda de Sangue - Seu Tipo é Necessário!'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nova_demanda',
            with: [
                'demanda' => $this->demanda,
                'doador' => $this->doador,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
