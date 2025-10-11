<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Importar jugadores (CSV / Excel)
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6">
        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white shadow rounded p-6 space-y-6">
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm text-gray-600">
                    ¿No tenés el archivo? Descargá la plantilla:
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.players.import.template', app()->getLocale()) }}"
                    class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Descargar plantilla XLSX
                    </a>
                    <a href="{{ route('admin.players.import.template_csv', app()->getLocale()) }}"
                    class="px-3 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">
                    Descargar plantilla CSV
                    </a>
                </div>
</div>

<p class="text-xs text-gray-500">
    <strong>Campos:</strong> full_name, known_as, <u>position</u> (GK/DF/MF/FW), nationality (2 letras), birthdate (YYYY-MM-DD),
    height_cm, weight_kg, photo_url, is_active (1/0).
</p>

            <form method="POST" action="{{ route('admin.players.import.store', app()->getLocale()) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Archivo</label>
                    <input type="file" name="file" accept=".csv,.txt,.xlsx" required class="mt-1 block w-full border rounded p-2">
                    @error('file') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Modo de importación</label>
                    <select name="mode" class="mt-1 block w-full border rounded p-2">
                        <option value="upsert" selected>Upsert (actualiza si existe por clave y crea si no)</option>
                        <option value="create">Solo crear</option>
                        <option value="update">Solo actualizar (requiere clave)</option>
                    </select>
                    @error('mode') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Columna clave (para update/upsert)</label>
                    <input type="text" name="id_column" value="id" class="mt-1 block w-full border rounded p-2" placeholder="id, external_id, code, etc.">
                    <p class="text-xs text-gray-500 mt-1">Debe existir en el CSV y en tu tabla (o estar en fillable).</p>
                    @error('id_column') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Subir e importar
                </button>
            </form>

            @if (session('summary'))
                @php($s = session('summary'))
                <div class="mt-4 p-4 rounded border bg-green-50 text-green-800">
                    <p class="font-semibold mb-2">Resultado de la importación:</p>
                    <ul class="text-sm space-y-1">
                        <li>✅ Creados: <strong>{{ $s['created'] ?? 0 }}</strong></li>
                        <li>♻️ Actualizados: <strong>{{ $s['updated'] ?? 0 }}</strong></li>
                        <li>⏭️ Omitidos: <strong>{{ $s['skipped'] ?? 0 }}</strong></li>
                    </ul>
                </div>
            @endif


            <div class="prose max-w-none">
                <h3 class="font-semibold">Formato CSV recomendado</h3>
                <p>Primera fila = encabezados. Se importan solo campos existentes en <code>players</code>.</p>
                <pre class="bg-gray-50 p-3 rounded border overflow-auto text-xs">
                id,name,position,team,overall,age,nationality,price,external_id
                1,Lionel Messi,FW,Inter Miami,90,37,Argentina,12000000,LM10
                </pre>
                <p class="text-sm text-gray-600">
                  Para XLSX, instalá luego: <code>composer require maatwebsite/excel</code> y activamos lectura de Excel.
                </p>
            </div>
        </div>
    </div>
</x-admin-layout>
