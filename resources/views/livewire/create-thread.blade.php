<div>
    <h1 class="text-3xl font-bold mb-8">Criar Novo Tópico no Fórum</h1>

    <form wire:submit.prevent="createThread" class="bg-white p-6 rounded-lg shadow-md">
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
            <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Conteúdo:</label>
            <textarea id="content" wire:model="content" rows="8" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            @error('content') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="subject_id" class="block text-gray-700 text-sm font-bold mb-2">Disciplina (Opcional):</label>
            <select id="subject_id" wire:model="subject_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Nenhuma Disciplina</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->course->name }})</option>
                @endforeach
            </select>
            @error('subject_id') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Criar Tópico
            </button>
        </div>
    </form>
</div>