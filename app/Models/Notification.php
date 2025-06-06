<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_orang_tua',
        'id_siswa',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'created_by',
        'created_by_role',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'id_orang_tua', 'id_orang_tua');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForParent($query, $parentId)
    {
        return $query->where('id_orang_tua', $parentId);
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getTypeIconAttribute()
    {
        $icons = [
            'rekam_medis' => 'fa-file-medical',
            'pemeriksaan_awal' => 'fa-stethoscope',
            'pemeriksaan_fisik' => 'fa-heartbeat',
            'pemeriksaan_harian' => 'fa-calendar-check',
            'resep' => 'fa-prescription-bottle-alt'
        ];

        return $icons[$this->type] ?? 'fa-bell';
    }

    public function getTypeColorAttribute()
    {
        $colors = [
            'rekam_medis' => 'text-blue-500',
            'pemeriksaan_awal' => 'text-green-500',
            'pemeriksaan_fisik' => 'text-red-500',
            'pemeriksaan_harian' => 'text-purple-500',
            'resep' => 'text-orange-500'
        ];

        return $colors[$this->type] ?? 'text-gray-500';
    }
}