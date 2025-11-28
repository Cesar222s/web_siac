<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateAdminCommand extends Command
{
    protected $signature = 'admin:update';
    protected $description = 'Actualizar usuario admin con permisos de administrador';

    public function handle()
    {
        $admin = User::where('email', 'admin@siac.com')->first();
        
        if ($admin) {
            $admin->is_admin = true;
            $admin->save();
            $this->info('✅ Usuario admin actualizado con is_admin = true');
        } else {
            $this->error('❌ Usuario admin no encontrado');
        }
    }
}
