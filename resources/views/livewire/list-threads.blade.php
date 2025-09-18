<div>
    <h1 class="text-3xl font-bold mb-8">Fórum Estudantil</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Buscar Tópico:</label>
                <input type="text" id="search" wire:model.live="search" placeholder="Título ou conteúdo..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="subject_id" class="block text-gray-700 text-sm font-bold mb-2">Filtrar por Disciplina:</label>
                <select id="subject_id" wire:model.live="subject_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Todas as Disciplinas</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->course->name }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('forum.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Criar Novo Tópico
            </a>
        </div>
    </div>

    @if ($threads->count() > 0)
        <div class="space-y-6">
            @foreach ($threads as $thread)
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold mb-2">
                        <a href="{{ route('forum.show', $thread->id) }}" class="text-blue-600 hover:underline">{{ $thread->title }}</a>
                    </h2>
                    <p class="text-gray-700 text-sm mb-3">{{ Str::limit($thread->content, 150) }}</p>
                    <p class="text-xs text-gray-500 mb-1">
                        Criado por <span class="font-semibold">{{ $thread->user->name }}</span> em {{ $thread->created_at->format('d/m/Y H:i') }}
                    </p>
                    @if ($thread->subject)
                        <p class="text-xs text-gray-500 mb-1">
                            Disciplina: <span class="font-semibold">{{ $thread->subject->name }} ({{ $thread->subject->course->name }})</span>
                        </p>
                    @endif
                    <p class="text-xs text-gray-500">
                        Respostas: <span class="font-semibold">{{ $thread->posts->count() }}</span>
                    </p>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $threads->links() }}
        </div>
    @else
        <p class="text-gray-500 text-center text-lg">Nenhum tópico encontrado.</p>
    @endif
</div>