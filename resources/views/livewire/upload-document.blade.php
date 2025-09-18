<div>
    <h1 class="text-3xl font-bold mb-8">Upload de Documento</h1>

    <form wire:submit.prevent="store" class="bg-white p-6 rounded-lg shadow-md">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Título:</label>
            <input type="text" id="title" wire:model="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('title') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Descrição (Opcional):</label>
            <textarea id="description" wire:model="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            @error('description') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Tipo:</label>
            <select id="type" wire:model="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="material">Material Didático</option>
                <option value="exam">Enunciado de Exame</option>
            </select>
            @error('type') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="subject_id" class="block text-gray-700 text-sm font-bold mb-2">Disciplina:</label>
            <select id="subject_id" wire:model="subject_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Selecione uma disciplina</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->course->name }})</option>
                @endforeach
            </select>
            @error('subject_id') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="file" class="block text-gray-700 text-sm font-bold mb-2">Arquivo (PDF, DOC, PPT):</label>
            <input type="file" id="file" wire:model="file" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('file') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Upload Documento
            </button>
        </div>
    </form>
</div>