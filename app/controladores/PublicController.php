<?php

declare(strict_types=1);

namespace App\Controladores;

final class PublicController
{
    public function home(): void
    {
        render_view('public/home', [
            'title' => 'Capital Humano que impulsa resultados',
        ]);
    }
}
