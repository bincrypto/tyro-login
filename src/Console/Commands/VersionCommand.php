<?php

namespace HasinHayder\TyroLogin\Console\Commands;

use Illuminate\Console\Command;

class VersionCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tyro-login:version';

    /**
     * The console command description.
     */
    protected $description = 'Display the current Tyro Login version';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $version = config('tyro-login.version', '1.0.0');
        
        $this->info('');
        $this->info('  ╔════════════════════════════════════════╗');
        $this->info('  ║                                        ║');
        $this->info('  ║     🔐 Tyro Login                      ║');
        $this->info('  ║                                        ║');
        $this->info('  ╚════════════════════════════════════════╝');
        $this->info('');
        $this->info("  Version: <comment>{$version}</comment>");
        $this->info('  Laravel: <comment>' . app()->version() . '</comment>');
        $this->info('  PHP: <comment>' . PHP_VERSION . '</comment>');
        $this->info('');
        $this->info('  📖 Documentation: <comment>https://hasinhayder.github.io/tyro/tyro-login/doc.html</comment>');
        $this->info('  📦 GitHub: <comment>https://github.com/hasinhayder/tyro-login</comment>');
        $this->info('');

        return self::SUCCESS;
    }
}
