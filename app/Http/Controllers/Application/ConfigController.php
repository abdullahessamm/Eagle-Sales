<?php

namespace App\Http\Controllers\Application;

use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationError;
use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    public function update(Request $request)
    {
        $admin = auth()->user()->userData->userInfo;
        $appConfigAccess = (bool) $admin->permissions->app_config_access;

        if (! $appConfigAccess)
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'configs'           => 'required|array|min:1|max:50',
            'config.*.key'      => 'required|string|min:1|max:255',
            'config.*.value'    => 'required|numeric|min:0'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $confs = $request->get('configs');
        foreach ($confs as $conf) {
            $confRow = AppConfig::where('key', $conf['key'])->first();
            if (! $confRow)
                throw new ValidationError(['Invalid key']);

            $confRow->value = $conf['value'];
            $confRow->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Configuration updated successfully'
        ]);
    }

    public function index()
    {
        $admin = auth()->user()->userData->userInfo;
        $appConfigAccess = (bool) $admin->permissions->app_config_access;

        if (! $appConfigAccess)
            throw new ForbiddenException;

        return response()->json([
            'success' => true,
            'configs' => AppConfig::get()
        ]);
    }
}
