@extends('layouts.admin')

@section('content')
<div class="bg-gray-50 p-4">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-white px-6 py-4 border-b flex justify-between items-center">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <h2 class="text-xl font-medium text-gray-800">Import Data Orang Tua</h2>
            </div>
            <a href="{{ route('orangtua.index') }}" class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-700">Terjadi kesalahan:</h3>
                            <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                @if($errors->has('import_errors'))
                                    @foreach($errors->get('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                @else
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('orangtua.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="mb-6">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">File Excel/CSV</label>
                    
                    <!-- Upload Area -->
                    <div id="dropArea" class="mt-1 flex flex-col justify-center items-center px-6 pt-5 pb-6 border-2 border-blue-300 border-dashed rounded-md bg-blue-50 transition-all duration-300 hover:border-blue-400 hover:bg-blue-100">
                        <!-- Default Upload State -->
                        <div id="uploadState" class="space-y-3 text-center">
                            <svg class="mx-auto h-16 w-16 text-blue-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex flex-col items-center text-sm text-gray-600">
                                <p class="font-medium text-blue-600 mb-1">Drag and drop file di sini</p>
                                <p class="mb-2">atau</p>
                                <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-4 py-2 border border-blue-300">
                                    <span>Pilih File</span>
                                    <input id="file-upload" name="file" type="file" class="sr-only" accept=".csv, .xls, .xlsx">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">Excel atau CSV hingga 10MB</p>
                        </div>
                        
                        <!-- File Preview State -->
                        <div id="previewState" class="hidden w-full">
                            <div class="flex items-center justify-between bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 flex items-center justify-center bg-green-100 rounded-lg">
                                            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="fileName" class="text-sm font-medium text-gray-900 truncate">
                                            filename.xlsx
                                        </p>
                                        <p id="fileSize" class="text-sm text-gray-500">
                                            0.5 MB
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <span id="fileStatus" class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Siap untuk diimport
                                        </span>
                                    </div>
                                </div>
                                <button type="button" id="removeFile" class="flex-shrink-0 ml-2 bg-white rounded-md p-1 text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-3 text-center">
                                <p class="text-xs text-gray-500">File Excel telah dipilih. Klik tombol "Import Data" untuk melanjutkan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md mb-6">
                    <div class="flex items-center mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-sm font-medium text-gray-700">Format Template</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-2 ml-7">
                        File Excel/CSV harus memiliki header kolom berikut (urutan tidak harus sama):
                    </p>
                    <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm ml-7">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">id_siswa</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">nama_ayah</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">nama_ibu</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">no_telp</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">alamat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr>
                                    <td class="px-3 py-2 text-xs text-gray-500">1</td>
                                    <td class="px-3 py-2 text-xs text-gray-500">Budi Santoso</td>
                                    <td class="px-3 py-2 text-xs text-gray-500">Siti Aisyah</td>
                                    <td class="px-3 py-2 text-xs text-gray-500">08123456789</td>
                                    <td class="px-3 py-2 text-xs text-gray-500">Jl. Pemuda No. 123, Jakarta</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 ml-7">
                        <a href="{{ route('orangtua.template') }}" class="inline-flex items-center text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Template Excel
                        </a>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" id="importButton" disabled class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropArea = document.getElementById('dropArea');
        const fileUpload = document.getElementById('file-upload');
        const uploadState = document.getElementById('uploadState');
        const previewState = document.getElementById('previewState');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileStatus = document.getElementById('fileStatus');
        const removeFile = document.getElementById('removeFile');
        const importButton = document.getElementById('importButton');
        const importForm = document.getElementById('importForm');
        
        // Prevent default behavior for drag events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            dropArea.classList.add('border-green-500', 'bg-green-50');
            dropArea.classList.remove('border-blue-300', 'bg-blue-50');
        }
        
        function unhighlight() {
            dropArea.classList.remove('border-green-500', 'bg-green-50');
            dropArea.classList.add('border-blue-300', 'bg-blue-50');
        }
        
        // Handle dropped files
        dropArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                fileUpload.files = files;
                updateFileDisplay(files[0]);
            }
        }
        
        // Handle file select
        fileUpload.addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) {
                updateFileDisplay(this.files[0]);
            }
        });
        
        // Update file display
        function updateFileDisplay(file) {
            // Show file preview state
            uploadState.classList.add('hidden');
            previewState.classList.remove('hidden');
            
            // Update file details
            fileName.textContent = file.name;
            
            // Format file size
            const fileSize_KB = file.size / 1024;
            if (fileSize_KB < 1024) {
                fileSize.textContent = fileSize_KB.toFixed(2) + ' KB';
            } else {
                const fileSize_MB = fileSize_KB / 1024;
                fileSize.textContent = fileSize_MB.toFixed(2) + ' MB';
            }
            
            // Update file icon based on file type
            const fileExtension = file.name.split('.').pop().toLowerCase();
            let iconClass = 'text-blue-600';
            let bgClass = 'bg-blue-100';
            
            if (fileExtension === 'xlsx' || fileExtension === 'xls') {
                iconClass = 'text-green-600';
                bgClass = 'bg-green-100';
                fileStatus.innerHTML = '<span class="flex items-center"><svg class="h-3 w-3 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Excel siap diimport</span>';
                fileStatus.className = 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800';
            } else if (fileExtension === 'csv') {
                iconClass = 'text-blue-600';
                bgClass = 'bg-blue-100';
                fileStatus.innerHTML = '<span class="flex items-center"><svg class="h-3 w-3 mr-1 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> CSV siap diimport</span>';
                fileStatus.className = 'px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800';
            } else {
                iconClass = 'text-red-600';
                bgClass = 'bg-red-100';
                fileStatus.innerHTML = '<span class="flex items-center"><svg class="h-3 w-3 mr-1 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg> Format tidak didukung</span>';
                fileStatus.className = 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800';
            }
            
            // Update the icon
            const iconElement = previewState.querySelector('.flex-shrink-0 .w-12 svg');
            iconElement.className = `w-8 h-8 ${iconClass}`;
            iconElement.parentElement.className = `w-12 h-12 flex items-center justify-center ${bgClass} rounded-lg`;
            
            // Enable or disable import button based on file type
            if (fileExtension === 'xlsx' || fileExtension === 'xls' || fileExtension === 'csv') {
                importButton.disabled = false;
                importButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                importButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
            } else {
                importButton.disabled = true;
                importButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                importButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            }
        }
        
        // Remove file handler
        removeFile.addEventListener('click', function() {
            // Reset file input
            fileUpload.value = '';
            
            // Reset display
            uploadState.classList.remove('hidden');
            previewState.classList.add('hidden');
            
            // Disable import button
            importButton.disabled = true;
            importButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            importButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        });
        
        // Loading state on form submit
        importForm.addEventListener('submit', function() {
            if (fileUpload.files.length > 0) {
                importButton.disabled = true;
                importButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengimport Data...
                `;
                importButton.classList.add('bg-blue-500');
                importButton.classList.remove('hover:bg-blue-700');
            }
        });
    });
</script>
@endsection