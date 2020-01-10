<?php

namespace Clockwork\Base\Audits\Https\Controllers;

use Carbon\Carbon;
use Clockwork\Http\Controllers\Controller;
use Clockwork\Http\Resources\CustomerResource;
use Clockwork\Models\Customer;
use Clockwork\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Clockwork\Services\Upload;

use Clockwork\Base\Audits\Models\Audit;

class AuditsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return Audit::with(['user', 'auditable'])
            ->whereNotIn('auditable_type', [
                'Clockwork\Timesheets\Models\TimesheetEntry',
                'Clockwork\Timesheets\Models\TimesheetLog'
            ])
            ->scopes(['byMonth', 'fromManager'])
            ->get();
    }
}
