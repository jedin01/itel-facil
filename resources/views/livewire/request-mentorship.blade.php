<div>
    <h1 class="text-3xl font-bold mb-8">Solicitar Mentoria</h1>

    <form wire:submit.prevent="requestSession" class="bg-white p-6 rounded-lg shadow-md">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <div class="mb-4">
            <label for="mentor_id" class="block text-gray-700 text-sm font-bold mb-2">Mentor:</label>
            <select id="mentor_id" wire:model="mentor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Selecione um mentor</option>
                @foreach ($mentors as $mentor)
                    <option value="{{ $mentor->id }}">{{ $mentor->name }} ({{ $mentor->course->name ?? 'N/A' }})</option>
                @endforeach
            </select>
            @error('mentor_id') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
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
            <label for="scheduled_at" class="block text-gray-700 text-sm font-bold mb-2">Data e Hora Sugerida:</label>
            <input type="datetime-local" id="scheduled_at" wire:model="scheduled_at" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('scheduled_at') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notas (Opcional):</label>
            <textarea id="notes" wire:model="notes" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            @error('notes') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Solicitar Mentoria
            </button>
        </div>
    </form>
</div>