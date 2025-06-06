<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        try {
            $userLevel = session('user_level');
            
            if ($userLevel !== 'orang_tua') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $parentId = session('parent_id');
            $siswaId = session('siswa_id');
            
            // AUTO-FIX: Jika parent_id tidak ada, cari dan set otomatis
            if (!$parentId && $siswaId) {
                $orangTua = \App\Models\OrangTua::where('id_siswa', $siswaId)->first();
                if ($orangTua) {
                    $parentId = $orangTua->id_orang_tua;
                    session(['parent_id' => $parentId]);
                    Log::info("Auto-fixed parent_id: {$parentId} for siswa_id: {$siswaId}");
                }
            }

            if (!$parentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data orang tua tidak ditemukan di sistem'
                ], 400);
            }

            $limit = $request->get('limit', 10);
            $notifications = Notification::with(['siswa'])
                ->where('id_orang_tua', $parentId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            $formattedNotifications = $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'siswa_nama' => $notification->siswa->nama_siswa ?? 'Unknown',
                    'is_read' => $notification->is_read,
                    'time_ago' => $notification->time_ago,
                    'icon' => $notification->type_icon,
                    'color' => $notification->type_color,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'data' => $notification->data
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedNotifications,
                'unread_count' => NotificationService::getUnreadCount($parentId)
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to get notifications: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications'
            ], 500);
        }
    }

    public function getUnreadCount()
    {
        try {
            $userLevel = session('user_level');
            
            if ($userLevel !== 'orang_tua') {
                return response()->json(['count' => 0]);
            }

            $parentId = session('parent_id');
            $siswaId = session('siswa_id');
            
            // AUTO-FIX untuk unread count juga
            if (!$parentId && $siswaId) {
                $orangTua = \App\Models\OrangTua::where('id_siswa', $siswaId)->first();
                if ($orangTua) {
                    $parentId = $orangTua->id_orang_tua;
                    session(['parent_id' => $parentId]);
                }
            }

            if (!$parentId) {
                return response()->json(['count' => 0]);
            }

            $count = NotificationService::getUnreadCount($parentId);

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to get unread count: " . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    public function markAsRead(Request $request, $id)
    {
        try {
            $userLevel = session('user_level');
            
            if ($userLevel !== 'orang_tua') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $parentId = session('parent_id');
            $siswaId = session('siswa_id');
            
            // AUTO-FIX juga untuk mark as read
            if (!$parentId && $siswaId) {
                $orangTua = \App\Models\OrangTua::where('id_siswa', $siswaId)->first();
                if ($orangTua) {
                    $parentId = $orangTua->id_orang_tua;
                    session(['parent_id' => $parentId]);
                }
            }

            if (!$parentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent ID not found'
                ], 400);
            }

            $notification = Notification::where('id', $id)
                ->where('id_orang_tua', $parentId)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to mark notification as read: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification'
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            $userLevel = session('user_level');
            
            if ($userLevel !== 'orang_tua') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $parentId = session('parent_id');
            $siswaId = session('siswa_id');
            
            // AUTO-FIX untuk mark all as read
            if (!$parentId && $siswaId) {
                $orangTua = \App\Models\OrangTua::where('id_siswa', $siswaId)->first();
                if ($orangTua) {
                    $parentId = $orangTua->id_orang_tua;
                    session(['parent_id' => $parentId]);
                }
            }

            if (!$parentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent ID not found'
                ], 400);
            }

            $updated = NotificationService::markAllAsRead($parentId);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'updated_count' => $updated
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to mark all notifications as read: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notifications'
            ], 500);
        }
    }

    public function index()
    {
        $userLevel = session('user_level');
        
        if ($userLevel !== 'orang_tua') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $parentId = session('parent_id');
        $siswaId = session('siswa_id');
        
        // AUTO-FIX untuk halaman index juga
        if (!$parentId && $siswaId) {
            $orangTua = \App\Models\OrangTua::where('id_siswa', $siswaId)->first();
            if ($orangTua) {
                $parentId = $orangTua->id_orang_tua;
                session(['parent_id' => $parentId]);
            }
        }

        if (!$parentId) {
            return redirect()->route('dashboard.orangtua')->with('error', 'Data orang tua tidak ditemukan di sistem');
        }

        $notifications = NotificationService::getRecentNotifications($parentId, 50);
        $unreadCount = NotificationService::getUnreadCount($parentId);

        return view('orangtua.notifications.index', compact('notifications', 'unreadCount'));
    }
}