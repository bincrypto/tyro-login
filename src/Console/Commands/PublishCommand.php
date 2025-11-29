<?php

namespace HasinHayder\TyroLogin\Console\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tyro-login:publish 
                            {--config : Publish only configuration}
                            {--views : Publish only views}
                            {--assets : Publish only assets}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     */
    protected $description = 'Publish Tyro Login package resources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $publishConfig = $this->option('config');
        $publishViews = $this->option('views');
        $publishAssets = $this->option('assets');
        $publishAll = !$publishConfig && !$publishViews && !$publishAssets;

        $this->info('');

        if ($publishConfig || $publishAll) {
            $this->info('📄 Publishing configuration...');
            $this->callSilently('vendor:publish', [
                '--tag' => 'tyro-login-config',
                '--force' => $this->option('force'),
            ]);
            $this->info('   ✓ Configuration published to config/tyro-login.php');
        }

        if ($publishViews || $publishAll) {
            $this->info('📄 Publishing views...');
            $this->callSilently('vendor:publish', [
                '--tag' => 'tyro-login-views',
                '--force' => $this->option('force'),
            ]);
            $this->info('   ✓ Views published to resources/views/vendor/tyro-login/');
        }

        if ($publishAssets || $publishAll) {
            $this->info('📄 Publishing assets...');
            $this->callSilently('vendor:publish', [
                '--tag' => 'tyro-login-assets',
                '--force' => $this->option('force'),
            ]);
            $this->info('   ✓ Assets published to public/vendor/tyro-login/');
        }

        $this->info('');
        $this->info('✨ Resources published successfully!');
        $this->info('');

        return self::SUCCESS;
    }
}
