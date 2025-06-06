<!-- resources/views/components/footer.blade.php -->
<footer class="bg-white border-t border-gray-200">
    <!-- Desktop Footer - Standard (not sticky) -->
    <div class="hidden md:flex bg-white border-t border-gray-200 px-4 py-3 justify-between items-center">
        <!-- Brand -->
        <div class="flex items-center">
            <img src="{{ asset('images/logosekolah.png') }}" alt="Logo Sekolah" class="w-6 h-6 mr-2">
            <span class="font-medium">Sehati</span>
            <span class="text-xs text-gray-500 ml-4">&copy; {{ date('Y') }} Sistem Kesehatan Terpadu</span>
        </div>
        
        <!-- Contact SLBN 1 Bantul -->
        <div class="text-xs text-gray-600">
            <div>SLBN 1 Bantul</div>
            <div>Jl. Wates KM 3 No. 147, Ngestiharjo, Kasihan, Bantul, Yogyakarta</div>
            <div>Telp: (0274) 371243 | Email: slbn1bantul@gmail.com</div>
        </div>
    </div>
    
    <!-- Minimal Mobile Footer - Standard (not sticky) -->
    <div class="md:hidden bg-white border-t border-gray-200 flex justify-around">
        <a href="{{ route('dashboard') }}" class="py-2 flex-1 text-center">
            <i class="fas fa-home text-blue-500"></i>
        </a>
        <a href="#" class="py-2 flex-1 text-center">
            <i class="fas fa-school text-blue-500"></i>
        </a>
        <a href="tel:(0274)371243" class="py-2 flex-1 text-center">
            <i class="fas fa-phone text-blue-500"></i>
        </a>
        <a href="mailto:slbn1bantul@gmail.com" class="py-2 flex-1 text-center">
            <i class="fas fa-envelope text-blue-500"></i>
        </a>
    </div>
</footer>