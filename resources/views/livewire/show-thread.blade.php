<div>
    <h1 class="text-3xl font-bold mb-8">{{ $thread->title }}</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <p class="text-gray-700 text-lg mb-4">{{ $thread->content }}</p>
        <p class="text-sm text-gray-500">
            Criado por <span class="font-semibold">{{ $thread->user->name }}</span> em {{ $thread->created_at->format('d/m/Y H:i') }}
        </p>
        @if ($thread->subject)
            <p class="text-sm text-gray-500">
                Disciplina: <span class="font-semibold">{{ $thread->subject->name }} ({{ $thread->subject->course->name }})</span>
            </p>
        @endif
    </div>

    <h2 class="text-2xl font-bold mb-4">Respostas</h2>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @if ($thread->posts->count() > 0)
        <div class="space-y-4 mb-6">
            @foreach ($thread->posts as $post)
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-400">
                    <p class="text-gray-800 mb-2">{{ $post->content }}</p>
                    <p class="text-xs text-gray-500">
                        Por <span class="font-semibold">{{ $post->user->name }}</span> em {{ $post->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 mb-6">Nenhuma resposta ainda. Seja o primeiro a responder!</p>
    @endif

    @auth
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Adicionar Resposta</h2>
            <form wire:submit.prevent="addPost">
                <div class="mb-4">
                    <label for="newPostContent" class="sr-only">Sua Resposta:</label>
                    <textarea id="newPostContent" wire:model="newPostContent" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Escreva sua resposta aqui..."></textarea>
                    @error('newPostContent') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Responder
                </button>
            </form>
        </div>
    @else
        <p class="text-gray-600 text-center mt-8">Faça login para adicionar uma resposta.</p>
    @endauth
</div>