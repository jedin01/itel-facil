<div>
    <h1 class="text-3xl font-bold mb-8">Documentos Disponíveis</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Buscar:</label>
                <input type="text" id="search" wire:model.live="search" placeholder="Título ou descrição..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Tipo:</label>
                <select id="type" wire:model.live="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Todos</option>
                    <option value="material">Material Didático</option>
                    <option value="exam">Enunciado de Exame</option>
                </select>
            </div>
            <div>
                <label for="subject_id" class="block text-gray-700 text-sm font-bold mb-2">Disciplina:</label>
                <select id="subject_id" wire:model.live="subject_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Todas</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->course->name }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if ($documents->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($documents as $document)
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                    <div>
                        <h2 class="text-xl font-bold mb-2">{{ $document->title }}</h2>
                        <p class="text-gray-700 text-sm mb-3">{{ Str::limit($document->description, 100) }}</p>
                        <p class="text-xs text-gray-500 mb-1">Tipo: <span class="font-semibold">{{ $document->type == 'exam' ? 'Enunciado de Exame' : 'Material Didático' }}</span></p>
                        <p class="text-xs text-gray-500 mb-1">Disciplina: <span class="font-semibold">{{ $document->subject->name }} ({{ $document->subject->course->name }})</span></p>
                        <p class="text-xs text-gray-500 mb-3">Uploader: <span class="font-semibold">{{ $document->user->name }}</span></p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Ver Documento
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $documents->links() }}
        </div>
    @else
        <p class="text-gray-500 text-center text-lg">Nenhum documento encontrado com os critérios selecionados.</p>
    @endif
</div>