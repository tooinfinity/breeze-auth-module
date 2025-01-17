<?php

namespace Tooinfinity\AuthModule\Console;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait InstallsBladeStack
{
    /**
     * Install the Blade module-auth stack.
     *
     * @return int|null
     */
    protected function installBladeStack(): ?int
    {
        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                '@tailwindcss/forms' => '^0.5.2',
                'alpinejs' => '^3.4.2',
                'autoprefixer' => '^10.4.2',
                'postcss' => '^8.4.31',
                'tailwindcss' => '^3.1.0',
            ] + $packages;
        });

        // Controllers...
        (new Filesystem)->ensureDirectoryExists(base_path('Modules/Auth/app/Http/Controllers'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Controllers', base_path('Modules/Auth/app/Http/Controllers'));

        // Requests...
        (new Filesystem)->ensureDirectoryExists(base_path('Modules/Auth/app/Http/Requests'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Requests', base_path('Modules/Auth/app/Http/Requests'));

        // Views...
        (new Filesystem)->ensureDirectoryExists(base_path('Modules/Auth/resources/views'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/resources/views', base_path('Modules/Auth/resources/views'));

        if (! $this->option('dark')) {
            $this->removeDarkClasses((new Finder)
                ->in(base_path('Modules/Auth/resources/views'))
                ->name('*.blade.php')
                ->notPath('livewire/welcome/navigation.blade.php')
                ->notName('welcome.blade.php')
            );
        }

        // Components...
        (new Filesystem)->ensureDirectoryExists(base_path('Modules/Auth/app/View/Components'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/View/Components', base_path('Modules/Auth/app/View/Components'));

        // Tests...
        if (! $this->installTests()) {
            return 1;
        }

        // Routes...
        copy(__DIR__.'/../../stubs/default/routes/web.php', base_path('Modules/Auth/routes/web.php'));
        copy(__DIR__.'/../../stubs/default/routes/auth.php', base_path('Modules/Auth/routes/auth.php'));

        // "Dashboard" Route...
        $this->replaceInFile('/home', '/dashboard', base_path('Modules/Auth/resources/views/welcome.blade.php'));
        $this->replaceInFile('Home', 'Dashboard', base_path('Modules/Auth/resources/views/welcome.blade.php'));

        // Tailwind / Vite...
        copy(__DIR__.'/../../stubs/default/tailwind.config.js', base_path('Modules/Auth/tailwind.config.js'));
        copy(__DIR__.'/../../stubs/default/postcss.config.js', base_path('Modules/Auth/postcss.config.js'));
        copy(__DIR__.'/../../stubs/default/vite.config.js', base_path('Modules/Auth/vite.config.js'));
        copy(__DIR__.'/../../stubs/default/resources/css/app.css', base_path('Modules/Auth/resources/assets/css/app.css'));
        copy(__DIR__.'/../../stubs/default/resources/js/app.js', base_path('Modules/Auth/resources/assets/js/app.js'));

        $this->components->info('Installing and building Node dependencies.');

        if (file_exists(base_path('pnpm-lock.yaml'))) {
            $this->runCommands(['pnpm install', 'pnpm run build']);
        } elseif (file_exists(base_path('yarn.lock'))) {
            $this->runCommands(['yarn install', 'yarn run build']);
        } else {
            $this->runCommands(['npm install', 'npm run build']);
        }

        $this->line('');
        $this->components->info('Auth Module scaffolding installed successfully.');
    }
}
