{{-- ═══════════════════════════════════════════════════════════════
     Contingent Management — Livewire-компонент

     Управление контингентом: сравнение ЛК ↔ Moodle, генерация SQL.
     Используется компонент App\Livewire\Employee\ContingentManagement.
     ═══════════════════════════════════════════════════════════════ --}}
<div class="space-y-6">

    {{-- ─── Заголовок страницы ─── --}}
    <div>
        <h2 class="text-xl font-bold text-slate-800">Формирование запроса на синхронизацию контингента</h2>
        <p class="text-sm text-slate-500 mt-1">
            Сравнение данных ЛК и Moodle и генерация SQL-запросов для приведения базы Moodle в соответствие с ЛК.
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         СЕКЦИЯ 1: SQL-запрос для Moodle

         Пользователь копирует запрос, выполняет в Moodle DB,
         выгружает результат в CSV для загрузки в Section 2.
         ═══════════════════════════════════════════════════════════ --}}
    <section class="bg-white rounded-xl shadow-sm ring-1 ring-slate-200 overflow-hidden">
        <header class="flex items-center gap-3 px-5 py-3 bg-slate-50 border-b border-slate-200">
            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-blue-600 text-white text-sm font-bold shadow-sm">1</span>
            <span class="font-semibold text-slate-700">Скопировать запрос мудл</span>
        </header>
        <div class="p-5 space-y-3">
            <p class="text-sm text-slate-500">
                Выполните SQL-запрос в базе данных Moodle, затем выгрузите результат в CSV-файл для загрузки в разделе 2.
            </p>
            <div class="flex justify-end">
                <button type="button" onclick="copyMoodleQuery(this)"
                    class="inline-flex items-center gap-2 text-sm px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition">
                    <i class="fa-solid fa-copy"></i> Копировать
                </button>
            </div>
            <pre id="moodle-query" class="text-xs whitespace-pre overflow-x-auto bg-slate-50 text-slate-800 rounded-lg p-4 ring-1 ring-slate-200">{{ $moodleQuery }}</pre>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         СЕКЦИЯ 2: Загрузка CSV-файла Moodle

         Загрузка CSV → клик «Обработать» → сравнение с ЛК.
         ═══════════════════════════════════════════════════════════ --}}
    <section class="bg-white rounded-xl shadow-sm ring-1 ring-slate-200 overflow-hidden">
        <header class="flex items-center gap-3 px-5 py-3 bg-slate-50 border-b border-slate-200">
            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-blue-600 text-white text-sm font-bold shadow-sm">2</span>
            <span class="font-semibold text-slate-700">Загрузка файла</span>
        </header>
        <div class="p-5 flex flex-wrap items-end gap-5">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Файл Мудл <span class="text-slate-400">(обязательно)</span></label>
                <input type="file" wire:model="csvFile" accept=".csv,.txt"
                    class="block w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                @error('csvFile') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                <p wire:loading wire:target="csvFile" class="text-xs text-blue-600 mt-1">
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i>Файл загружается на сервер…
                </p>
            </div>
            <div class="sm:col-span-2 flex justify-end">
                <button type="button" wire:click="process" @disabled(! $csvFile) wire:loading.attr="disabled" wire:target="csvFile, process"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 disabled:opacity-50 shadow-sm transition">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span wire:loading.remove wire:target="csvFile, process">Обработать</span>
                    <span wire:loading wire:target="process">Обработка…</span>
                    <span wire:loading wire:target="csvFile">Загрузка файла…</span>
                </button>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════
         Предупреждения парсинга CSV

         Отображаются при дублях логинов или пропущенных строках.
         ═══════════════════════════════════════════════════════════ --}}
    @if (!empty($csvWarnings))
    <div class="rounded-lg bg-amber-50 ring-1 ring-amber-200 p-4 space-y-1">
        @foreach ($csvWarnings as $warning)
            <p class="text-sm text-amber-700"><i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $warning }}</p>
        @endforeach
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         СЕКЦИЯ 3: Результаты сравнения

         Статистика → вкладки (ФИО / Зачисления / Перевод / Отчисления)
         → таблицы с чекбоксами → предпросмотр SQL.
         ═══════════════════════════════════════════════════════════ --}}
    @if ($parsed)
    <section class="bg-white rounded-xl shadow-sm ring-1 ring-slate-200 overflow-hidden">
        <header class="flex items-center gap-3 px-5 py-3 bg-slate-50 border-b border-slate-200">
            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-blue-600 text-white text-sm font-bold shadow-sm">3</span>
            <span class="font-semibold text-slate-700">Списки изменений</span>
            <button type="button" wire:click="downloadSql" wire:loading.attr="disabled" @disabled($lkUnavailable)
                class="ml-auto inline-flex items-center gap-2 text-sm px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm transition disabled:opacity-50">
                <i class="fa-solid fa-download"></i>
                <span wire:loading.remove wire:target="downloadSql">Скачать запрос в txt</span>
                <span wire:loading wire:target="downloadSql"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Готовим файл…</span>
            </button>
            <button type="button" wire:click="copySql" wire:loading.attr="disabled" @disabled($lkUnavailable)
                class="inline-flex items-center gap-2 text-sm px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition disabled:opacity-50">
                <i class="fa-solid fa-copy"></i>
                <span wire:loading.remove wire:target="copySql">Копировать запрос</span>
                <span wire:loading wire:target="copySql"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Копируем…</span>
            </button>
        </header>

        <div class="p-5 space-y-4">

            {{-- ─── Блокировка генерации SQL: данные ЛК недоступны ─── --}}
            @if ($lkUnavailable)
            <div class="rounded-lg bg-rose-50 ring-1 ring-rose-200 p-4 text-sm text-rose-700">
                <p><i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    <b>Данные ЛК из БД недоступны.</b> Генерация SQL и скачивание заблокированы, чтобы случайно не
                    отчислить весь контингент Мудл. Здесь показана разница «по файлу Мудл» только для справки.
                </p>
            </div>
            @endif

            {{-- ─── Предупреждения по данным ЛК (многогрупповые студенты) ─── --}}
            @if (! empty($lkWarnings))
            <div class="rounded-lg bg-amber-50 ring-1 ring-amber-200 p-4 text-sm text-amber-800 space-y-1">
                @foreach ($lkWarnings as $warning)
                <p><i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $warning }}</p>
                @endforeach
            </div>
            @endif

            {{-- ─── Статистика ─── --}}
            <div class="flex flex-wrap gap-2 text-sm">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-medium bg-blue-50 text-blue-700 ring-1 ring-blue-200">
                    <i class="fa-solid fa-user-pen"></i> ФИО: <b class="ml-0.5">{{ $stats['fio'] }}</b>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                    <i class="fa-solid fa-user-plus"></i> Зачисления: <b class="ml-0.5">{{ $stats['enroll'] }}</b>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-medium bg-amber-50 text-amber-700 ring-1 ring-amber-200">
                    <i class="fa-solid fa-right-left"></i> Перевод: <b class="ml-0.5">{{ $stats['transfer'] }}</b>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-medium bg-rose-50 text-rose-700 ring-1 ring-rose-200">
                    <i class="fa-solid fa-user-minus"></i> Отчисления: <b class="ml-0.5">{{ $stats['expel'] }}</b>
                </span>
            </div>

            @if ($stats['fio'] === 0 && $stats['enroll'] === 0 && $stats['transfer'] === 0 && $stats['expel'] === 0)
                <p class="text-sm text-slate-500">Расхождений не обнаружено. Контингент Мудл соответствует ЛК.</p>
            @endif

            {{-- ─── Вкладки (серверное состояние — не сбрасываются после обновлений) ─── --}}
            <div class="flex flex-wrap gap-1 border-b border-slate-200" role="tablist" aria-label="Типы изменений">
                <button type="button" wire:click="setTab('fio')"
                    role="tab" aria-selected="{{ $activeTab === 'fio' ? 'true' : 'false' }}" aria-controls="tab-fio" id="tab-btn-fio"
                    class="tab-btn px-4 py-2 text-sm font-medium border-b-2 {{ $activeTab === 'fio' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500' }}">ФИО ({{ $stats['fio'] }})</button>
                <button type="button" wire:click="setTab('enroll')"
                    role="tab" aria-selected="{{ $activeTab === 'enroll' ? 'true' : 'false' }}" aria-controls="tab-enroll" id="tab-btn-enroll"
                    class="tab-btn px-4 py-2 text-sm font-medium border-b-2 {{ $activeTab === 'enroll' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500' }}">Зачисления ({{ $stats['enroll'] }})</button>
                <button type="button" wire:click="setTab('transfer')"
                    role="tab" aria-selected="{{ $activeTab === 'transfer' ? 'true' : 'false' }}" aria-controls="tab-transfer" id="tab-btn-transfer"
                    class="tab-btn px-4 py-2 text-sm font-medium border-b-2 {{ $activeTab === 'transfer' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500' }}">Перевод ({{ $stats['transfer'] }})</button>
                <button type="button" wire:click="setTab('expel')"
                    role="tab" aria-selected="{{ $activeTab === 'expel' ? 'true' : 'false' }}" aria-controls="tab-expel" id="tab-btn-expel"
                    class="tab-btn px-4 py-2 text-sm font-medium border-b-2 {{ $activeTab === 'expel' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500' }}">Отчисления ({{ $stats['expel'] }})</button>
            </div>

            {{-- ─── Таблица: ФИО (рендерится только на своей вкладке) ─── --}}
            @if ($activeTab === 'fio')
            <div id="tab-fio" role="tabpanel" aria-labelledby="tab-btn-fio" tabindex="0">
                <div class="flex flex-wrap items-center gap-2 mb-2 text-xs">
                    <button type="button" wire:click="toggleType('fio', true)" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Выбрать все</button>
                    <button type="button" wire:click="toggleType('fio', false)" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Снять все</button>
                    <span class="ml-auto text-slate-500">Стр. {{ $this->fioChanges['page'] }} из {{ $this->fioChanges['pages'] }} (всего {{ $this->fioChanges['total'] }})</span>
                    <button type="button" wire:click="gotoPage('fio', {{ $this->fioChanges['page'] - 1 }})" @disabled($this->fioChanges['page'] <= 1) class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 disabled:opacity-40">←</button>
                    <button type="button" wire:click="gotoPage('fio', {{ $this->fioChanges['page'] + 1 }})" @disabled($this->fioChanges['page'] >= $this->fioChanges['pages']) class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 disabled:opacity-40">→</button>
                </div>
                <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Логин</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">ФИО</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Группа</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Было</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Станет</th>
                            <th class="text-center p-2.5 w-16 border border-slate-200 text-slate-600 font-semibold">✓</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->fioChanges['items'] as $change)
                            <tr class="border-b border-slate-200 hover:bg-slate-50">
                                <td class="p-2.5 font-mono border border-slate-200 bg-white">{{ $change['login'] }}</td>
                                <td class="p-2.5 border border-slate-200 bg-white">{{ $change['surnameNew'] }} {{ $change['nameNew'] }}</td>
                                <td class="p-2.5 border border-slate-200 bg-white">{{ $change['groupNew'] }}</td>
                                <td class="p-2.5 line-through text-red-500 border border-slate-200 bg-white">{{ $change['surnameOld'] }} {{ $change['nameOld'] }}</td>
                                <td class="p-2.5 font-semibold text-emerald-600 border border-slate-200 bg-white">{{ $change['surnameNew'] }} {{ $change['nameNew'] }}</td>
                                <td class="p-2.5 text-center border border-slate-200 bg-white">
                                    <input type="checkbox" wire:model="selected.{{ $change['id'] }}" class="w-4 h-4 text-blue-600 rounded">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

            {{-- ─── Таблица: Зачисления (рендерится только на своей вкладке) ─── --}}
            @if ($activeTab === 'enroll')
            <div id="tab-enroll" role="tabpanel" aria-labelledby="tab-btn-enroll" tabindex="0">
                <div class="flex flex-wrap items-center gap-2 mb-2 text-xs">
                    <button type="button" wire:click="toggleType('enroll', true)" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Выбрать все</button>
                    <button type="button" wire:click="toggleType('enroll', false)" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Снять все</button>
                    <span class="ml-auto text-slate-500">Стр. {{ $this->enrollChanges['page'] }} из {{ $this->enrollChanges['pages'] }} (всего {{ $this->enrollChanges['total'] }})</span>
                    <button type="button" wire:click="gotoPage('enroll', {{ $this->enrollChanges['page'] - 1 }})" @disabled($this->enrollChanges['page'] <= 1) class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 disabled:opacity-40">←</button>
                    <button type="button" wire:click="gotoPage('enroll', {{ $this->enrollChanges['page'] + 1 }})" @disabled($this->enrollChanges['page'] >= $this->enrollChanges['pages']) class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 disabled:opacity-40">→</button>
                </div>
                <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Логин</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">ФИО</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Группа</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Статус</th>
                            <th class="text-center p-2.5 w-16 border border-slate-200 text-slate-600 font-semibold">✓</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->enrollChanges['items'] as $change)
                            <tr class="border-b border-slate-200 hover:bg-slate-50">
                                <td class="p-2.5 font-mono border border-slate-200 bg-white">{{ $change['login'] }}</td>
                                <td class="p-2.5 font-semibold text-emerald-600 border border-slate-200 bg-white">{{ $change['surnameNew'] }} {{ $change['nameNew'] }}</td>
                                <td class="p-2.5 font-semibold text-emerald-600 border border-slate-200 bg-white">{{ $change['groupNew'] }}</td>
                                <td class="p-2.5 text-slate-400 border border-slate-200 bg-white">— (нет в Мудл)</td>
                                <td class="p-2.5 text-center border border-slate-200 bg-white">
                                    <input type="checkbox" wire:model="selected.{{ $change['id'] }}" class="w-4 h-4 text-blue-600 rounded">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

            {{-- ─── Таблица: Перевод (рендерится только на своей вкладке) ─── --}}
            @if ($activeTab === 'transfer')
            <div id="tab-transfer" role="tabpanel" aria-labelledby="tab-btn-transfer" tabindex="0">
                <div class="flex flex-wrap items-center gap-2 mb-2 text-xs">
                    <button type="button" wire:click="toggleType('transfer', true)" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Выбрать все</button>
                    <button type="button" wire:click="toggleType('transfer', false)" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Снять все</button>
                    <span class="ml-auto text-slate-500">Стр. {{ $this->transferChanges['page'] }} из {{ $this->transferChanges['pages'] }} (всего {{ $this->transferChanges['total'] }})</span>
                    <button type="button" wire:click="gotoPage('transfer', {{ $this->transferChanges['page'] - 1 }})" @disabled($this->transferChanges['page'] <= 1) class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 disabled:opacity-40">←</button>
                    <button type="button" wire:click="gotoPage('transfer', {{ $this->transferChanges['page'] + 1 }})" @disabled($this->transferChanges['page'] >= $this->transferChanges['pages']) class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 disabled:opacity-40">→</button>
                </div>
                <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Логин</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">ФИО</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Было (группа)</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Станет (группа)</th>
                            <th class="text-center p-2.5 w-16 border border-slate-200 text-slate-600 font-semibold">✓</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->transferChanges['items'] as $change)
                            <tr class="border-b border-slate-200 hover:bg-slate-50">
                                <td class="p-2.5 font-mono border border-slate-200 bg-white">{{ $change['login'] }}</td>
                                <td class="p-2.5 border border-slate-200 bg-white">{{ $change['surnameNew'] }} {{ $change['nameNew'] }}</td>
                                <td class="p-2.5 line-through text-red-500 border border-slate-200 bg-white">{{ $change['groupOld'] }}</td>
                                <td class="p-2.5 font-semibold text-emerald-600 border border-slate-200 bg-white">{{ $change['groupNew'] }}</td>
                                <td class="p-2.5 text-center border border-slate-200 bg-white">
                                    <input type="checkbox" wire:model="selected.{{ $change['id'] }}" class="w-4 h-4 text-blue-600 rounded">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

            {{-- ─── Таблица: Отчисления (рендерится только на своей вкладке) ─── --}}
            @if ($activeTab === 'expel')
            <div id="tab-expel" role="tabpanel" aria-labelledby="tab-btn-expel" tabindex="0">
                <div class="flex flex-wrap items-center gap-2 mb-2 text-xs">
                    <button type="button" wire:click="toggleType('expel', true)" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Выбрать все</button>
                    <button type="button" wire:click="toggleType('expel', false)" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">Снять все</button>
                    <span class="ml-auto text-slate-500">Стр. {{ $this->expelChanges['page'] }} из {{ $this->expelChanges['pages'] }} (всего {{ $this->expelChanges['total'] }})</span>
                    <button type="button" wire:click="gotoPage('expel', {{ $this->expelChanges['page'] - 1 }})" @disabled($this->expelChanges['page'] <= 1) class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 disabled:opacity-40">←</button>
                    <button type="button" wire:click="gotoPage('expel', {{ $this->expelChanges['page'] + 1 }})" @disabled($this->expelChanges['page'] >= $this->expelChanges['pages']) class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 disabled:opacity-40">→</button>
                </div>
                <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Логин</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">ФИО</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Группа</th>
                            <th class="text-left p-2.5 border border-slate-200 text-slate-600 font-semibold">Статус</th>
                            <th class="text-center p-2.5 w-16 border border-slate-200 text-slate-600 font-semibold">✓</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->expelChanges['items'] as $change)
                            <tr class="border-b border-slate-200 hover:bg-slate-50">
                                <td class="p-2.5 font-mono border border-slate-200 bg-white">{{ $change['login'] }}</td>
                                <td class="p-2.5 border border-slate-200 bg-white">{{ $change['surnameOld'] }} {{ $change['nameOld'] }}</td>
                                <td class="p-2.5 line-through text-red-500 border border-slate-200 bg-white">{{ $change['groupOld'] }}</td>
                                <td class="p-2.5 font-semibold text-rose-500 border border-slate-200 bg-white">— (отчислить)</td>
                                <td class="p-2.5 text-center border border-slate-200 bg-white">
                                    <input type="checkbox" wire:model="selected.{{ $change['id'] }}" class="w-4 h-4 text-blue-600 rounded">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

            {{-- ─── Предпросмотр SQL (строится только по кнопке) ─── --}}
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="buildPreview" @disabled($lkUnavailable) wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 text-sm px-4 py-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 disabled:opacity-50 shadow-sm transition">
                    <i class="fa-solid fa-eye"></i>
                    <span wire:loading.remove wire:target="buildPreview">Показать SQL</span>
                    <span wire:loading wire:target="buildPreview">Строим…</span>
                </button>
                @if ($sqlBuilt)
                    <span class="text-xs text-slate-500">Построен по выбранным чекбоксам ({{ mb_strlen($this->sql) }} симв.). Скачивание и копирование — в шапке раздела.</span>
                @else
                    <span class="text-xs text-slate-400">Предпросмотр устарел или не построен — нажмите «Показать SQL».</span>
                @endif
            </div>
            @if ($sqlBuilt)
                <details class="text-sm" open>
                    <summary class="cursor-pointer text-slate-500 hover:text-slate-700 select-none">Предпросмотр SQL (первые 20 000 символов)</summary>
                    <pre class="mt-2 whitespace-pre overflow-x-auto bg-slate-50 text-slate-800 rounded-lg p-4 ring-1 ring-slate-200 max-h-96 overflow-y-auto">{{ \Illuminate\Support\Str::limit($this->sql, 20000) }}</pre>
                </details>
            @endif
        </div>
    </section>
    @endif

    <script>
        // ── Копирование в буфер обмена (Clipboard API + фолбэк) ──────────
        window.copyToClipboard = function (text) {
            const ok = () => { if (window.toastr) toastr.success('Скопировано в буфер обмена'); };
            const fail = () => { if (window.toastr) toastr.error('Не удалось скопировать — выделите текст вручную'); };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(ok, fail);
                return;
            }
            // Фолбэк: скрытый textarea + execCommand (http / старые браузеры)
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.cssText = 'position:fixed;left:-9999px;top:-9999px';
            document.body.appendChild(ta);
            ta.select();
            let result = false;
            try { result = document.execCommand('copy'); } catch (_) { result = false; }
            document.body.removeChild(ta);
            result ? ok() : fail();
        };

        window.copyMoodleQuery = function () {
            const el = document.getElementById('moodle-query');
            if (!el) return;
            window.copyToClipboard(el.textContent.trim());
        };

// ── Копирование запроса: сервер сам строит SQL и отдаёт его событием ──
        // Livewire подключается ниже тела страницы, поэтому регистрируем
        // обработчик по livewire:init. Скрипт перевыполняется при каждом
        // обновлении Livewire — гарантируем единичную регистрацию.
        if (! window.__moodleCopySqlBound) {
            const onCopySql = ({ sql }) => {
                if (! sql || ! sql.length) {
                    if (window.toastr) toastr.warning('Запрос пустой — сначала обработайте файл.');
                    return;
                }
                window.copyToClipboard(sql);
            };
            if (window.Livewire) {
                window.Livewire.on('copy-sql', onCopySql);
            } else {
                document.addEventListener('livewire:init', () => window.Livewire.on('copy-sql', onCopySql), { once: true });
            }
            window.__moodleCopySqlBound = true;
        }
    </script>
</div>
