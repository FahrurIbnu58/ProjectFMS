<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first() ?? User::first();
        if (! $admin) {
            return;
        }

        $mk = function (string $name, ?Folder $parent = null) use ($admin) {
            return Folder::firstOrCreate(
                ['name' => $name, 'parent_id' => $parent?->id],
                ['created_by' => $admin->id]
            );
        };

        $perusahaan = $mk('Perusahaan');
        $hr = $mk('HR');
        $keuangan = $mk('Keuangan');

        $mk('SOP', $perusahaan);
        $mk('Kebijakan', $perusahaan);
        $mk('Rekrutmen', $hr);
        $it = $mk('IT', $perusahaan);
        $mk('Infrastruktur', $it);
    }
}
