<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\DocumentationService;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function __construct(private DocumentationService $documentation)
    {
        $this->checkGuard();
    }

    public function index(Request $request)
    {
        if ($request->has('lang')) {
            $locale = $this->documentation->locale($request->query('lang'));
        } else {
            $locale = $this->documentation->locale(app()->getLocale());
        }

        return view('backend.pages.help.index', [
            'locale' => $locale,
            'modules' => $this->documentation->moduleList($locale),
            'navGroups' => $this->documentation->navGroups($locale),
            'meta' => $this->documentation->meta($locale),
        ]);
    }

    public function show(string $locale, string $module)
    {
        $locale = $this->documentation->locale($locale);
        $data = $this->documentation->module($locale, $module);

        if (!$data) {
            abort(404);
        }

        $moduleData = array_merge($data, ['key' => $module]);

        return view('backend.pages.help.module', [
            'locale' => $locale,
            'module' => $moduleData,
            'modules' => $this->documentation->moduleList($locale),
            'navGroups' => $this->documentation->navGroups($locale),
            'meta' => $this->documentation->meta($locale),
        ]);
    }
}
