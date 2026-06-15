<?php

namespace App\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // On Windows, PHP's rename() can briefly fail with "Access is denied (code: 5)"
        // when the OS (file indexer, AV real-time scan) holds a handle on the just-created
        // .tmp file. This override retries up to 5 times with an exponential back-off
        // before falling back to a direct write, eliminating the intermittent 500 on
        // POST /livewire/update caused by Blade's view compiler.
        if (PHP_OS_FAMILY === 'Windows') {
            $this->app->singleton('files', function () {
                return new class extends Filesystem {
                    public function replace($path, $content, $lock = false): void
                    {
                        $tmpFile = tempnam(dirname($path), basename($path));

                        if ($tmpFile === false) {
                            file_put_contents($path, $content, $lock ? LOCK_EX : 0);
                            return;
                        }

                        file_put_contents($tmpFile, $content, $lock ? LOCK_EX : 0);

                        // Retry rename with exponential back-off (10ms → 20ms → 40ms → 80ms → 160ms)
                        $delay = 10000; // microseconds
                        for ($i = 0; $i < 5; $i++) {
                            if (@rename($tmpFile, $path)) {
                                return;
                            }
                            usleep($delay);
                            $delay *= 2;
                        }

                        // Final fallback: direct write + clean up temp file
                        file_put_contents($path, $content, $lock ? LOCK_EX : 0);
                        @unlink($tmpFile);
                    }
                };
            });
        }
    }

    public function boot(): void
    {
        //
    }
}
