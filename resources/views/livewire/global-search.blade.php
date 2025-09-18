<div>
    <h1 class="text-3xl font-bold mb-8">Busca Inteligente</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Pesquisar em todo o Nexus:</label>
        <input type="text" id="search" wire:model.live.debounce.300ms="search" placeholder="Digite sua busca..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    </div>

    @if (strlen($search) >= 3)
        @if (empty(array_filter($results)))
            <p class="text-gray-500 text-center text-lg">Nenhum resultado encontrado para "{{ $search }}".</p>
        @else
            <div class="space-y-8">
                @if (!empty($results['documents']))
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-bold mb-4">Documentos</h2>
                        <ul class="space-y-3">
                            @foreach ($results['documents'] as $document)
                                <li class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50">
                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-blue-600 hover:underline font-semibold">{{ $document->title }}</a>
                                    <p class="text-sm text-gray-700">{{ Str::limit($document->description, 100) }}</p>
                                    <p class="text-xs text-gray-500">Disciplina: {{ $document->subject->name }}</p>
                                </li>
                            @endforeach
                        </ul>
                        @if (count($results['documents']) >= 5)
                            <p class="text-right mt-2 text-sm"><a href="{{ route('documents.index', ['search' => $search]) }}" class="text-blue-500 hover:underline">Ver todos os documentos</a></p>
                        @endif
                    </div>
                @endif

                @if (!empty($results['forum_threads']))
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-bold mb-4">Tópicos do Fórum</h2>
                        <ul class="space-y-3">
                            @foreach ($results['forum_threads'] as $thread)
                                <li class="border-l-4 border-green-500 pl-4 py-2 bg-gray-50">
                                    <a href="{{ route('forum.show', $thread->id) }}" class="text-green-600 hover:underline font-semibold">{{ $thread->title }}</a>
                                    <p class="text-sm text-gray-700">{{ Str::limit($thread->content, 100) }}</p>
                                    <p class="text-xs text-gray-500">Por: {{ $thread->user->name }}</p>
                                </li>
                            @endforeach
                        </ul>
                        @if (count($results['forum_threads']) >= 5)
                            <p class="text-right mt-2 text-sm"><a href="{{ route('forum.index', ['search' => $search]) }}" class="text-green-500 hover:underline">Ver todos os tópicos</a></p>
                        @endif
                    </div>
                @endif

                @if (!empty($results['courses']))
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-bold mb-4">Cursos</h2>
                        <ul class="space-y-3">
                            @foreach ($results['courses'] as $course)
                                <li class="border-l-4 border-purple-500 pl-4 py-2 bg-gray-50">
                                    <a href="{{ route('home') }}" class="text-purple-600 hover:underline font-semibold">{{ $course->name }}</a>
                                    <p class="text-sm text-gray-700">{{ Str::limit($course->description, 100) }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!empty($results['subjects']))
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-bold mb-4">Disciplinas</h2>
                        <ul class="space-y-3">
                            @foreach ($results['subjects'] as $subject)
                                <li class="border-l-4 border-orange-500 pl-4 py-2 bg-gray-50">
                                    <a href="{{ route('home') }}" class="text-orange-600 hover:underline font-semibold">{{ $subject->name }}</a>
                                    <p class="text-sm text-gray-700">{{ Str::limit($subject->description, 100) }}</p>
                                    <p class="text-xs text-gray-500">Curso: {{ $subject->course->name }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!empty($results['helpers']))
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-bold mb-4">Ajudantes (Garimpo)</h2>
                        <ul class="space-y-3">
                            @foreach ($results['helpers'] as $helper)
                                <li class="border-l-4 border-red-500 pl-4 py-2 bg-gray-50">
                                    <a href="{{ route('garimpo.index', ['search' => $search]) }}" class="text-red-600 hover:underline font-semibold">{{ $helper->name }}</a>
                                    <p class="text-sm text-gray-700">{{ $helper->email }}</p>
                                    <p class="text-xs text-gray-500">Curso: {{ $helper->course->name ?? 'N/A' }}</p>
                                </li>
                            @endforeach
                        </ul>
                        @if (count($results['helpers']) >= 5)
                            <p class="text-right mt-2 text-sm"><a href="{{ route('garimpo.index', ['search' => $search]) }}" class="text-red-500 hover:underline">Ver todos os ajudantes</a></p>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    @else
        <p class="text-gray-500 text-center text-lg">Digite pelo menos 3 caracteres para iniciar a busca.</p>
    @endif
</div>