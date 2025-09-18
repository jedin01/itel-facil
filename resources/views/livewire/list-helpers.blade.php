<div>
    <h1 class="text-3xl font-bold mb-8">Garimpo (Ajudantes)</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Buscar Ajudante:</label>
                <input type="text" id="search" wire:model.live="search" placeholder="Nome ou email..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>
    </div>

    @if ($helpers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($helpers as $helper)
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold mb-2">{{ $helper->name }}</h2>
                    <p class="text-gray-700 text-sm mb-1">{{ $helper->email }}</p>
                    @if ($helper->course)
                        <p class="text-xs text-gray-500 mb-3">Curso: <span class="font-semibold">{{ $helper->course->name }}</span></p>
                    @else
                        <p class="text-xs text-gray-500 mb-3">Curso: <span class="font-semibold">Não especificado</span></p>
                    @endif
                    <p class="text-xs text-gray-500">Status: <span class="font-semibold">Disponível para ajudar</span></p>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $helpers->links() }}
        </div>
    @else
        <p class="text-gray-500 text-center text-lg">Nenhum ajudante encontrado.</p>
    @endif
</div>