<div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 w-full max-w-lg">
    <h1 class="text-2xl font-bold text-slate-800">PDF &harr; Word Converter</h1>
    <p class="text-slate-500 text-sm mt-1 mb-6">Upload a file, pick a direction, download the result.</p>

    <div class="grid grid-cols-2 gap-2 mb-6">
        <label class="cursor-pointer text-center rounded-lg border px-3 py-2 text-sm font-medium
            {{ $mode === 'word-to-pdf' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-300' }}">
            <input type="radio" class="hidden" value="word-to-pdf" wire:model.live="mode"> Word &rarr; PDF
        </label>
        <label class="cursor-pointer text-center rounded-lg border px-3 py-2 text-sm font-medium
            {{ $mode === 'pdf-to-word' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-300' }}">
            <input type="radio" class="hidden" value="pdf-to-word" wire:model.live="mode"> PDF &rarr; Word
        </label>
    </div>

    <form wire:submit="convert" class="space-y-4">
        <div>
            <input type="file" wire:model="file" wire:key="{{ $mode }}"
                   accept="{{ $mode === 'pdf-to-word' ? '.pdf' : '.doc,.docx' }}"
                   class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-indigo-700 hover:file:bg-indigo-100">
            <div wire:loading wire:target="file" class="text-xs text-slate-500 mt-1">Uploading...</div>
            @error('file') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled" wire:target="convert"
                class="w-full rounded-lg bg-indigo-600 py-2.5 font-semibold text-white hover:bg-indigo-700 disabled:opacity-60">
            <span wire:loading.remove wire:target="convert">Convert &amp; Download</span>
            <span wire:loading wire:target="convert">Converting... please wait</span>
        </button>
    </form>

    @if ($error)
        <p class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $error }}</p>
    @endif

    <p class="mt-6 text-xs text-slate-400">Max 10 MB. Files are deleted right after conversion.</p>
</div>
