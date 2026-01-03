<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FingerprintTemplate;

class FingerprintController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'finger_id' => 'required|integer',
            'template' => 'required|string',
        ]);

        $tpl = FingerprintTemplate::updateOrCreate(
            ['finger_id' => $data['finger_id']],
            ['template' => $data['template']]
        );

        return response()->json(['success' => true, 'id' => $tpl->id]);
    }

    public function list()
    {
        return response()->json(FingerprintTemplate::all());
    }
}
