<?php

namespace tlopasha\cotACl;

use Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use tlopasha\cotACl\Helpers\LvHelper;

class LaravelVueAdminServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        if (LvHelper::laravel_ver() == 5.3) {

            // Call to Entrust::hasRole
            Blade::directive('role', function ($expression) {
                return "<?php if (\\Entrust::hasRole({$expression})) : ?>";
            });

            // Call to Entrust::can
            Blade::directive('permission', function ($expression) {
                return "<?php if (\\Entrust::can({$expression})) : ?>";
            });

            // Call to Entrust::ability
            Blade::directive('ability', function ($expression) {
                return "<?php if (\\Entrust::ability({$expression})) : ?>";
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/routes.php';

        /*
        |--------------------------------------------------------------------------
        | Providers
        |--------------------------------------------------------------------------
         */

        // Collective HTML & Form Helper
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);
        // For Datatables
        $this->app->register(\Yajra\Datatables\DatatablesServiceProvider::class);
        // For Gravatar
        $this->app->register(\Creativeorange\Gravatar\GravatarServiceProvider::class);
        // For Entrust
        $this->app->register(\Zizaco\Entrust\EntrustServiceProvider::class);
        // For Spatie Backup
        $this->app->register(\Spatie\Backup\BackupServiceProvider::class);

        /*
        |--------------------------------------------------------------------------
        | Register the Alias
        |--------------------------------------------------------------------------
         */

        $loader = AliasLoader::getInstance();

        // Collective HTML & Form Helper
        $loader->alias('Form', \Collective\Html\FormFacade::class);
        $loader->alias('HTML', \Collective\Html\HtmlFacade::class);

        // For Gravatar User Profile Pics
        $loader->alias('Gravatar', \Creativeorange\Gravatar\Facades\Gravatar::class);

        // For LaraAdmin Code Generation
        $loader->alias('CodeGenerator', \tlopasha\cotACl\CodeGenerator::class);

        // For LaraAdmin Form Helper
        $loader->alias('LvFormMaker', \tlopasha\cotACl\LvFormMaker::class);

        // For LaraAdmin Helper
        $loader->alias('LvHelper', \tlopasha\cotACl\Helpers\LvHelper::class);

        // LaraAdmin Module Model
        $loader->alias('Module', \tlopasha\cotACl\Models\Module::class);

        // For LaravelVueAdmin Configuration Model
        $loader->alias('LvConfigs', \tlopasha\cotACl\Models\LvConfigs::class);

        // For Entrust
        $loader->alias('Entrust', \Zizaco\Entrust\EntrustFacade::class);
        $loader->alias('role', \Zizaco\Entrust\Middleware\EntrustRole::class);
        $loader->alias('permission', \Zizaco\Entrust\Middleware\EntrustPermission::class);
        $loader->alias('ability', \Zizaco\Entrust\Middleware\EntrustAbility::class);

        /*
        |--------------------------------------------------------------------------
        | Register the Controllers
        |--------------------------------------------------------------------------
         */

        $this->app->make('tlopasha\cotACl\Controllers\ModuleController');
        $this->app->make('tlopasha\cotACl\Controllers\FieldController');
        $this->app->make('tlopasha\cotACl\Controllers\MenuController');

        /*
        |--------------------------------------------------------------------------
        | Blade Directives
        |--------------------------------------------------------------------------
         */

        // LAForm Input Maker
        Blade::directive('lv_input', function ($expression) {
            if (LvHelper::laravel_ver() == 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php echo LvFormMaker::input$expression; ?>";
        });

        // LAForm Form Maker
        Blade::directive('lv_form', function ($expression) {
            if (LvHelper::laravel_ver() == 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php echo LvFormMaker::form$expression; ?>";
        });

        // LAForm Maker - Display Values
        Blade::directive('lv_display', function ($expression) {
            if (LvHelper::laravel_ver() == 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php echo LvFormMaker::display$expression; ?>";
        });

        // LAForm Maker - Check Whether User has Module Access
        Blade::directive('lv_access', function ($expression) {
            if (LvHelper::laravel_ver() == 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php if(LvFormMaker::lv_access$expression) { ?>";
        });
        Blade::directive('endlv_access', function ($expression) {
            return "<?php } ?>";
        });

        // LAForm Maker - Check Whether User has Module Field Access
        Blade::directive('lv_field_access', function ($expression) {
            if (LvHelper::laravel_ver() == 5.3) {
                $expression = "(" . $expression . ")";
            }
            return "<?php if(LvFormMaker::lv_field_access$expression) { ?>";
        });
        Blade::directive('endlv_field_access', function ($expression) {
            return "<?php } ?>";
        });

        /*
        |--------------------------------------------------------------------------
        | Register the Commands
        |--------------------------------------------------------------------------
         */

        $commands = [
            \tlopasha\cotACl\Commands\Migration::class,
            \tlopasha\cotACl\Commands\Crud::class,
            \tlopasha\cotACl\Commands\Packaging::class,
            \tlopasha\cotACl\Commands\LvInstall::class,
        ];

        $this->commands($commands);
    }
}
