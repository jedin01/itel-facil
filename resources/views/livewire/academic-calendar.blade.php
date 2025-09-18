<div>
    <h1 class="text-3xl font-bold mb-8">Calendário Acadêmico</h1>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="flex justify-between items-center mb-4">
            <button wire:click="previousMonth" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">&lt; Anterior</button>
            <h2 class="text-xl font-semibold">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</h2>
            <button wire:click="nextMonth" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">Próximo &gt;</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Evento:</label>
                <select id="type" wire:model.live="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Todos</option>
                    <option value="exam">Exame</option>
                    <option value="holiday">Feriado</option>
                    <option value="defense">Defesa</option>
                    <option value="critical_date">Data Crítica</option>
                    <option value="other">Outro</option>
                </select>
            </div>
            <div>
                <label for="course_id" class="block text-gray-700 text-sm font-bold mb-2">Curso:</label>
                <select id="course_id" wire:model.live="course_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Todos</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if ($events->count() > 0)
        <div class="space-y-6">
            @foreach ($events as $date => $dailyEvents)
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold mb-4">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</h2>
                    <ul class="space-y-3">
                        @foreach ($dailyEvents as $event)
                            <li class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50">
                                <p class="font-semibold text-lg">{{ $event->title }}</p>
                                <p class="text-gray-700 text-sm">{{ $event->description }}</p>
                                <p class="text-xs text-gray-500">Tipo: <span class="font-semibold">{{ ucfirst($event->type) }}</span></p>
                                @if ($event->course)
                                    <p class="text-xs text-gray-500">Curso: <span class="font-semibold">{{ $event->course->name }}</span></p>
                                @endif
                                <p class="text-xs text-gray-500">Horário: <span class="font-semibold">{{ $event->start_date->format('H:i') }} @if($event->end_date) - {{ $event->end_date->format('H:i') }} @endif</span></p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center text-lg">Nenhum evento encontrado para este mês.</p>
    @endif
</div>