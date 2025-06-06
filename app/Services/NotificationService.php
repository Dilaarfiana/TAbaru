<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public static function createMedicalNotification($type, $siswaId, $data = [])
    {
        try {
            $orangTua = OrangTua::where('id_siswa', $siswaId)->first();
            
            if (!$orangTua) {
                Log::warning("No parent found for student ID: {$siswaId}");
                return false;
            }

            $siswa = Siswa::find($siswaId);
            if (!$siswa) {
                Log::warning("Student not found with ID: {$siswaId}");
                return false;
            }

            $currentUser = session('username', 'System');
            $currentRole = session('user_level', 'admin');

            $notificationContent = self::generateNotificationContent($type, $siswa->nama_siswa, $data);

            $notification = Notification::create([
                'id_orang_tua' => $orangTua->id_orang_tua,
                'id_siswa' => $siswaId,
                'type' => $type,
                'title' => $notificationContent['title'],
                'message' => $notificationContent['message'],
                'data' => [
                    'siswa_nama' => $siswa->nama_siswa,
                    'created_by' => $currentUser,
                    'created_by_role' => $currentRole,
                    'record_id' => $data['record_id'] ?? null,
                    'additional_info' => $data['additional_info'] ?? null
                ],
                'created_by' => $currentUser,
                'created_by_role' => $currentRole
            ]);

            Log::info("Notification created for parent {$orangTua->id_orang_tua} about {$type} for student {$siswaId}");
            
            return $notification;

        } catch (\Exception $e) {
            Log::error("Failed to create notification: " . $e->getMessage());
            return false;
        }
    }

    private static function generateNotificationContent($type, $siswaName, $data = [])
    {
        $templates = [
            'rekam_medis' => [
                'title' => 'Rekam Medis Baru',
                'message' => "Rekam medis baru telah ditambahkan untuk {$siswaName}. Silakan cek detail pemeriksaan untuk informasi lebih lanjut."
            ],
            'pemeriksaan_awal' => [
                'title' => 'Pemeriksaan Awal',
                'message' => "Pemeriksaan awal telah dilakukan untuk {$siswaName}. Data vital signs dan keluhan telah dicatat."
            ],
            'pemeriksaan_fisik' => [
                'title' => 'Pemeriksaan Fisik',
                'message' => "Pemeriksaan fisik lengkap telah dilakukan untuk {$siswaName}. Hasil pemeriksaan tersedia di sistem."
            ],
            'pemeriksaan_harian' => [
                'title' => 'Pemeriksaan Harian',
                'message' => "Pemeriksaan harian telah dilakukan untuk {$siswaName} hari ini. Kondisi kesehatan telah dicatat."
            ],
            'resep' => [
                'title' => 'Resep Obat Baru',
                'message' => "Resep obat baru telah diberikan untuk {$siswaName}. Pastikan untuk mengikuti petunjuk penggunaan yang diberikan."
            ]
        ];

        return $templates[$type] ?? [
            'title' => 'Notifikasi Kesehatan',
            'message' => "Ada pembaruan data kesehatan untuk {$siswaName}."
        ];
    }

    public static function getUnreadCount($parentId)
    {
        return Notification::where('id_orang_tua', $parentId)
            ->where('is_read', false)
            ->count();
    }

    public static function getRecentNotifications($parentId, $limit = 10)
    {
        return Notification::with(['siswa'])
            ->where('id_orang_tua', $parentId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function markAllAsRead($parentId)
    {
        return Notification::where('id_orang_tua', $parentId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }
}