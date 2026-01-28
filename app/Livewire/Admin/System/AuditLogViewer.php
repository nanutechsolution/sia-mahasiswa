<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class AuditLogViewer extends Component
{
    use WithPagination;

    public $filterSubject;

    public function render()
    {
        $logs = Activity::with('causer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.admin.system.audit-log-viewer', ['logs' => $logs]);
    }
}