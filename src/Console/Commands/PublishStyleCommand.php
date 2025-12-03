<?php

namespace HasinHayder\TyroLogin\Console\Commands;

use Illuminate\Console\Command;

class PublishStyleCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tyro-login:publish-style 
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     */
    protected $description = 'Publish Tyro Login styles to customize shadcn variables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('Publishing Tyro Login styles...');
        
        $this->callSilently('vendor:publish', [
            '--tag' => 'tyro-login-styles',
            '--force' => $this->option('force'),
        ]);
        
        $this->info('   ✓ Styles published to resources/views/vendor/tyro-login/partials/styles.blade.php');
        $this->info('');
        $this->info('You can now customize the shadcn variables in the styles file:');
        $this->info('   resources/views/vendor/tyro-login/partials/styles.blade.php');
        $this->info('');

        return self::SUCCESS;
    }
}
