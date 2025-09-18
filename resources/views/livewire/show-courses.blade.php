<div>
    <h1 class="text-3xl font-bold mb-8">Cursos e Disciplinas</h1>

    <div class="space-y-8">
        @foreach ($courses as $course)
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-2">{{ $course->name }}</h2>
                <p class="text-gray-700 mb-4">{{ $course->description }}</p>

                @if ($course->subjects->count() > 0)
                    <h3 class="text-xl font-semibold mb-3">Disciplinas</h3>
                    <ul class="space-y-2">
                        @foreach ($course->subjects as $subject)
                            <li class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50">
                                <p class="font-semibold">{{ $subject->name }}</p>
                                <p class="text-sm text-gray-600">{{ $subject->description }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">Nenhuma disciplina cadastrada para este curso ainda.</p>
                @endif
            </div>
        @endforeach
    </div>
</div>