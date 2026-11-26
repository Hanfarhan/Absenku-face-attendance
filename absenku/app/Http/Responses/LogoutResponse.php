<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements Responsable
{
    /**
     * Setelah logout dari admin panel, redirect ke landing page (/)
     * bukan ke halaman login admin.
     */
    public function toResponse($request): RedirectResponse
    {
        return redirect('/');
    }
}
