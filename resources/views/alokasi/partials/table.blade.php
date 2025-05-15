@forelse($siswas as $siswa)
    <tr>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="checkbox" name="selected_siswa[]" value="{{ $siswa->id_siswa }}" class="rounded text-indigo-600 focus:ring-indigo-500">
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
            {{ $siswa->id_siswa }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            {{ $siswa->nama_siswa }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $siswa->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                {{ $siswa->jenis_kelamin }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            @if($siswa->detailSiswa && $siswa->detailSiswa->jurusan)
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    {{ $siswa->detailSiswa->jurusan->Nama_Jurusan }}
                </span>
            @else
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Belum Dialokasikan
                </span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            @if($siswa->detailSiswa && $siswa->detailSiswa->kelas)
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ $siswa->detailSiswa->kelas->Nama_Kelas }}
                </span>
            @else
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Belum Dialokasikan
                </span>
            @endif
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <button type="button" 
                    class="btn-alokasi text-indigo-600 hover:text-indigo-900"
                    data-id="{{ $siswa->id_siswa }}" 
                    data-nama="{{ $siswa->nama_siswa }}"
                    data-jurusan="{{ $siswa->detailSiswa->kode_jurusan ?? '' }}"
                    data-kelas="{{ $siswa->detailSiswa->kode_kelas ?? '' }}">
                <i class="fas fa-user-cog"></i> Alokasi
            </button>
            <a href="{{ route('siswa.show', $siswa->id_siswa) }}" class="text-blue-600 hover:text-blue-900 ml-3">
                <i class="fas fa-eye"></i> Detail
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
            Tidak ada data siswa yang sesuai dengan filter
        </td>
    </tr>
@endforelse