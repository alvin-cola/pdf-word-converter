<?php

namespace App\Livewire;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Converter extends Component
{
    use WithFileUploads;

    public string $mode = 'word-to-pdf'; // or 'pdf-to-word'
    public $file;
    public ?string $error = null;

    public function updatedMode(): void
    {
        $this->reset('file', 'error');
    }

    public function convert()
    {
        $this->error = null;

        $this->validate([
            'file' => [
                'required', 'file', 'max:10240',
                $this->mode === 'pdf-to-word' ? 'mimes:pdf' : 'mimes:doc,docx',
            ],
        ]);

        // Private working folder for this conversion
        $work = storage_path('app/conversions/' . Str::uuid());
        File::ensureDirectoryExists($work . '/out');
        register_shutdown_function(fn () => File::deleteDirectory($work));

        $ext   = strtolower($this->file->getClientOriginalExtension());
        $base  = Str::slug(pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'document';
        $input = "{$work}/{$base}.{$ext}";
        copy($this->file->getRealPath(), $input);

        $cmd = ['soffice', '--headless', '--norestore', "-env:UserInstallation=file://{$work}/profile"];

        if ($this->mode === 'pdf-to-word') {
            $target = 'docx';
            array_push($cmd, '--infilter=writer_pdf_import', '--convert-to', 'docx:MS Word 2007 XML');
        } else {
            $target = 'pdf';
            array_push($cmd, '--convert-to', 'pdf');
        }
        array_push($cmd, '--outdir', "{$work}/out", $input);

        $result = Process::timeout(120)->env(['HOME' => $work])->run($cmd);
        $output = "{$work}/out/{$base}.{$target}";

        if (! $result->successful() || ! file_exists($output)) {
            $this->error = 'Conversion failed. Please try a different file.';
            return;
        }

        $this->reset('file');

        return response()->download($output, "{$base}.{$target}");
    }

    public function render()
    {
        return view('livewire.converter');
    }
}
