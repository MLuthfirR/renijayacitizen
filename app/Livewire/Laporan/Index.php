<?php

namespace App\Livewire\Laporan;

use App\Support\LaporanBuilder;
use App\Support\PeriodeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Laporan')]
class Index extends Component
{
    public function render()
    {
        $periode = PeriodeContext::current();

        $data = ['periode' => $periode];

        if ($periode) {
            $builder = new LaporanBuilder($periode);
            $data += [
                'pengaturan' => $builder->pengaturan(),
                'ringkasan' => $builder->ringkasan(),
                'bukuBesar' => $builder->bukuBesar(),
            ];
        }

        return view('livewire.laporan.index', $data);
    }
}
