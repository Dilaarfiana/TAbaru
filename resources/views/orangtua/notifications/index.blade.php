{{-- resources/views/orangtua/notifications/index.blade.php - SIMPLE VERSION --}}
@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-bell text-blue-600 mr-2"></i>
                Notifikasi
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Total: {{ $notifications->count() }} | Belum dibaca: {{ $unreadCount }}
            </p>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <div class="p-4 {{ !$notification->is_read ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 mb-1">
                                {{ $notification->title }}
                                @if(!$notification->is_read)
                                    <span class="w-2 h-2 bg-red-500 rounded-full inline-block ml-2"></span>
                                @endif
                            </h3>
                            <p class="text-gray-700 mb-2">{{ $notification->message }}</p>
                            <div class="text-xs text-gray-500">
                                <span>{{ $notification->siswa->nama_siswa ?? 'Siswa' }}</span> • 
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        
                        @if(!$notification->is_read)
                            <button onclick="markAsRead({{ $notification->id }})" 
                                    class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                Tandai Dibaca
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <i class="fas fa-bell-slash text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada notifikasi</h3>
                    <p class="text-gray-500">Notifikasi akan muncul di sini</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
async function markAsRead(id) {
    try {
        const response = await fetch(`/api/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            window.location.reload();
        }
    } catch (error) {
        alert('Gagal menandai sebagai dibaca');
    }
}
</script>
@endsection