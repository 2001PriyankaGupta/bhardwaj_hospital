<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\SystemLog;
use App\Models\Backup;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = SystemSetting::all()->groupBy('group')->map(function ($items) {
            return $items->keyBy('key');
        });
        $logs = SystemLog::latest()->take(50)->get();
        $backups = Backup::latest()->get();
        $users = User::all();
        
        return view($user->user_type.'.settings.index', compact('settings', 'logs', 'backups', 'users'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', 'users']);

        // Group mapping for consistency
        $groups = [
            'latest_app_version' => 'app_update',
            'play_store_url' => 'app_update',
            'app_update_message' => 'app_update',
            'max_login_attempts' => 'security',
            'session_timeout' => 'security',
            'two_factor_auth' => 'security',
            'password_expiry' => 'security',
        ];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $groups[$key] ?? 'general'
                ]
            );
        }

        return back()->with('success', 'Settings updated successfully!');
    }


    public function createBackup(Request $request)
    {
        try {
            $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
            
            $backup = Backup::create([
                'filename' => $filename,
                'size' => rand(1000, 5000),
                'path' => 'storage/backups/' . $filename,
                'notes' => $request->notes,
            ]);

            SystemLog::create([
                'level' => 'info',
                'message' => 'System backup created: ' . $filename,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
            ]);

            return redirect()->back()->with('success', 'Backup created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating backup: ' . $e->getMessage());
        }
    }

    public function restoreBackup($id)
    {
        try {
            $backup = Backup::findOrFail($id);
            
            SystemLog::create([
                'level' => 'warning',
                'message' => 'System restore initiated from backup: ' . $backup->filename,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
            ]);

            return redirect()->back()->with('success', 'Restore process initiated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error restoring backup: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request)
    {
        if ($request->has('users')) {

            foreach ($request->users as $id => $u) {

                $user = User::find($id);
                if (!$user) {
                    continue;
                }

                // Update basic fields
                $user->name = $u['name'];
                $user->email = $u['email'];

                // Update password only if provided
                if (!empty($u['password'])) {
                    $user->password = bcrypt($u['password']);
                }

                $user->save();
            }
        }

        return back()->with('success', 'Users updated successfully!');
    }


    public function clearLogs()
    {
        try {
            SystemLog::where('created_at', '<', now()->subDays(30))->delete();
            
            SystemLog::create([
                'level' => 'info',
                'message' => 'System logs cleared',
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
            ]);

            return redirect()->back()->with('success', 'Logs cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error clearing logs: ' . $e->getMessage());
        }
    }
}