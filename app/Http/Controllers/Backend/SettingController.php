<?php

namespace App\Http\Controllers\Backend;

use App\Helper\CustomHelper;
use App\Helper\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Pagination\Paginator;

class SettingController extends Controller
{
    public $pageHeader;
    public $index_route = "admin.settings.index";
    public $create_route = "admin.settings.create";
    public $store_route = "admin.settings.store";
    public $edit_route = "admin.settings.edit";
    public $update_route = "admin.settings.update";

    public function __construct()
    {
        $this->checkGuard();
        Paginator::useBootstrapFive();
        $this->pageHeader = [
            'title' => "Settings",
            'sub_title' => "",
            'plural_name' => "settings",
            'singular_name' => "Setting",
            'index_route' => $this->edit_route,
            'create_route' => $this->edit_route,
            'store_route' => $this->store_route,
            'edit_route' => $this->edit_route,
            'update_route' => $this->update_route,
            'base_url' => url('admin/settings'),

        ];
    }


    public function edit(Request $request)
    {
        $this->checkOwnPermission('settings.edit');
        $data['pageHeader'] = $this->pageHeader;
        $data['edited'] = Setting::where('branch_id', auth()->user()->branch_id)
            ->pluck('value', 'key');
        return view('backend.pages.settings.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): RedirectResponse
    {
        $this->checkOwnPermission('settings.edit');
        try {
            $row = $request->except('_token');
            foreach ($row as $key => $value) {
                Setting::set($key, $value);
            }
            if ($request->hasFile('logo')) {
                $logoPath = CustomHelper::imageUpload($request->file('logo'), 'settings');
                Setting::set('logo', $logoPath); // Save the logo path to settings
            }

                return RedirectHelper::back();


        } catch (QueryException $e) {
            return $e;
            return RedirectHelper::backWithInputFromException();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        $this->checkOwnPermission('settings.delete');
        $deleteData = Setting::where('branch_id', auth()->user()->branch_id)
            ->find($id);

        if (!is_null($deleteData)) {
            if ($deleteData->delete()) {
                return response()->json(['status' => 200]);
            } else {
                return response()->json(['status' => 422]);
            }
        }
    }
}
