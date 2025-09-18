<div>
    <h1 class="text-3xl font-bold mb-8">Gerenciar Mentorias</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="filterStatus" class="block text-gray-700 text-sm font-bold mb-2">Filtrar por Status:</label>
                <select id="filterStatus" wire:model.live="filterStatus" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Todos</option>
                    <option value="pending">Pendente</option>
                    <option value="accepted">Aceita</option>
                    <option value="rejected">Rejeitada</option>
                    <option value="completed">Concluída</option>
                    <option value="canceled">Cancelada</option>
                </select>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($mentorships->count() > 0)
        <div class="space-y-6">
            @foreach ($mentorships as $session)
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold mb-2">Sessão de Mentoria #{{ $session->id }}</h2>
                    <p class="text-gray-700 text-sm mb-1">Mentor: <span class="font-semibold">{{ $session->mentor->name }}</span></p>
                    <p class="text-gray-700 text-sm mb-1">Mentee: <span class="font-semibold">{{ $session->mentee->name }}</span></p>
                    <p class="text-gray-700 text-sm mb-1">Disciplina: <span class="font-semibold">{{ $session->subject->name }} ({{ $session->subject->course->name }})</span></p>
                    <p class="text-gray-700 text-sm mb-1">Agendado para: <span class="font-semibold">{{ $session->scheduled_at->format('d/m/Y H:i') }}</span></p>
                    <p class="text-gray-700 text-sm mb-1">Status: <span class="font-semibold">{{ ucfirst($session->status) }}</span></p>
                    @if ($session->notes)
                        <p class="text-gray-700 text-sm mb-3">Notas: {{ $session->notes }}</p>
                    @endif

                    <div class="mt-4 flex space-x-2">
                        @if (auth()->user()->isMentor() && $session->status === 'pending')
                            <button wire:click="acceptSession({{ $session->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">Aceitar</button>
                            <button wire:click="rejectSession({{ $session->id }})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">Rejeitar</button>
                        @endif
                        @if ((auth()->user()->isMentor() || auth()->user()->isMentee()) && $session->status === 'accepted')
                            <button wire:click="completeSession({{ $session->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">Concluir</button>
                            <button wire:click="cancelSession({{ $session->id }})" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">Cancelar</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $mentorships->links() }}
        </div>
    @else
        <p class="text-gray-500 text-center text-lg">Nenhuma sessão de mentoria encontrada.</p>
    @endif
</div>