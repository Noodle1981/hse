<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\EvaluarVencimientosEppJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Motor de Triggers EPP Exchange
Schedule::job(new EvaluarVencimientosEppJob)
    ->daily()
    ->name('epp:evaluar-vencimientos')
    ->withoutOverlapping();
