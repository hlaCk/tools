<?php

function attrLocaleName($col = "name"){

    $locale = current(explode("-", app()->getLocale()));
    return "{$col}_$locale";
}

function currentLocale($full = false) : string {
    if($full)
        return app()->getLocale();

    $locale = current(explode("-", app()->getLocale()));
    return $locale?:"";
}

if (!function_exists('appDispatch')) {
    /**
     * Send the given command to the dispatcher for execution.
     *
     * @param object $command
     *
     * @return void
     */
    function appDispatch($command)
    {
        return app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch($command);
    }
}

if (!function_exists('globalCompacts')) {
    /**
     * get global vars to compact array.
     *
     * @return array
     */
    function globalCompacts() {
        global $auth_user, $date_format;

        // Share user logged in
        $auth_user = AuthUser();

        return compact('auth_user');
    }
}

if (!function_exists('appendGlobalCompacts')) {
    /**
     * add global vars to compact array.
     *
     * @param array $compactValues
     *
     * @return array
     */
    function appendGlobalCompacts(array $compactValues)
    {
        return collect($compactValues?:[])->merge(globalCompacts())->all();
    }
}


if (!function_exists('AuthUser')) {
    /**
     * get Auth::User, current logged in user
     *
     * @return \App\Models\Auth\User|null
     */
    function AuthUser()
    {
        return Auth::user();
    }
}


if (!function_exists('Support')) {
    /**
     * get if Auth::User is support user
     *
     * @return bool
     */
    function Support() {
        $auth_user = User();
        return ($auth_user != null && $auth_user->email == "support@4myth.com");
    }
}

if (!function_exists('User')) {
    /**
     * get Auth::User, current logged in user
     *
     * @return \App\Models\Auth\User
     */
    function User() {
        return app('auth')->user() ? : new \App\Models\Auth\User();
    }
}

if (!function_exists('CurrentRoute')) {
    /**
     * get current route
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Route|mixed
     */
    function CurrentRoute() {
        return app(\Illuminate\Routing\Route::class);
    }
}

if (!function_exists('ViewMode')) {
    /**
     * get current route
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Route|mixed
     */
    function ViewMode() {
        try {
            return @end(explode('.', CurrentRoute()->getName()));
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('isViewMode')) {
    /**
     * get current route
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Route|mixed
     */
    function isViewMode($mode) {
        return strtolower(trim($mode)) == strtolower(trim(ViewMode()));
    }
}

if (!function_exists('UserCan')) {
    /**
     * Check if user has a permission by its name.
     *
     * @param  string|array  $permission Permission string or array of permissions.
     * @param  string|bool  $team      Team name or requiredAll roles.
     * @param  bool  $requireAll All permissions in the array are required.
     * @return bool
     */
    function UserCan($permission, $team = null, $requireAll = false)
    {
//        return app('auth')->user()->hasPermission($permission, $team, $requireAll);
        return User()->can($permission, $team, $requireAll);
//        return Support() ? true :   User()->can($permission, $team, $requireAll);
    }
}

if (!function_exists('UserCant')) {
    /**
     * Check if user doesn't has a permission by its name.
     *
     * @param  string|array  $permission Permission string or array of permissions.
     * @param  string|bool  $team      Team name or requiredAll roles.
     * @param  bool  $requireAll All permissions in the array are required.
     * @return bool
     */
    function UserCant($permission, $team = null, $requireAll = false)
    {
        return !!!UserCan($permission, $team, $requireAll);
    }
}

if (!function_exists('AuthPermissions')) {
    /**
     * get all user permissions
     *
     * @param \App\Models\Auth\User|null $user
     *
     * @return array
     */
    function AuthPermissions(\App\Models\Auth\User $user = null) : array
    {
        $user = is_null($user) ? AuthUser() : $user;

        return $user ? collect($user->allPermissions())
                ->pluck('name')
                ->unique()
                ->toArray() : [];
    }
}

if (!function_exists('grtNamespacePermission')) {
    /**
     * return perm name from controller full namespace
     *
     * @return string
     */
    function grtNamespacePermission() : string
    {
        $route = app(\Illuminate\Routing\Route::class);

        // Get the controller array
        $arr = array_reverse(explode('\\', explode('@', $route->getAction()['uses'])[0]));

        $controller = '';

        // Add folder
        if (strtolower($arr[1]) != 'controllers') {
            $controller .= kebab_case($arr[1]) . '-';
        }

        // Add module
        if (isset($arr[3]) && isset($arr[4]) && (strtolower($arr[4]) == 'modules')) {
            $controller .= kebab_case($arr[3]) . '-';
        }

        // Add file
        $controller .= kebab_case($arr[0]);

        return $controller;
    }
}

if (! function_exists('crudTrans')) {
    /**
     * Translate curd syste,
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function crudTrans($key = null)
    {
        if(!$key) return "";

        return trans(
            str_plural(
                snake_case(
                    collect(
                        explode(
                            "\\",
                            str_before(Route::current()->getActionName(), "@")
                        )
                    )
                        ->last()
                )
            ) . ".{$key}"
        );
//        $locale = 'en';
//        return trans($key, $replace, $locale);
    }
}

if (! function_exists('transEN')) {
    /**
     * Translate the given message. To en
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function transEN($key = null, $replace = [])
    {
        $locale = 'en';
        return trans($key, $replace, $locale);
    }
}

if (! function_exists('trans_choiceEN')) {
    /**
     * Translates the given message based on a count. to en
     *
     * @param  string  $key
     * @param  int|array|\Countable  $number
     * @param  array   $replace
     * @param  string  $locale
     * @return string
     */
    function trans_choiceEN($key, $number, array $replace = [])
    {
        $locale = 'en';
        return trans_choice($key, $number, $replace, $locale);
    }
}

if (! function_exists('transAR')) {
    /**
     * Translate the given message. To ar
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function transAR($key = null, $replace = [])
    {
        $locale = 'ar';
        return trans($key, $replace, $locale);
    }
}

if (! function_exists('trans_choiceAR')) {
    /**
     * Translates the given message based on a count. to ar
     *
     * @param  string  $key
     * @param  int|array|\Countable  $number
     * @param  array   $replace
     * @param  string  $locale
     * @return string
     */
    function trans_choiceAR($key, $number, array $replace = [])
    {
        $locale = 'ar';
        return trans_choice($key, $number, $replace, $locale);
    }
}

if (! function_exists('trans2')) {
    /**
     * Translate the given message. To the other language.
     * if your lang is ar it will return en
     * if your lang is en it will return ar
     *
     * @param  string  $key
     * @param  array   $replace
     * @param  string  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function trans2($key = null, $replace = [])
    {
        $locale = app()->getLocale() == 'ar' ? 'en' : 'ar';
        return trans($key, $replace, $locale);
    }
}

if (! function_exists('trans_choice2')) {
    /**
     * Translates the given message based on a count. To the other language.
     * if your lang is ar it will return en
     * if your lang is en it will return ar
     *
     * @param  string  $key
     * @param  int|array|\Countable  $number
     * @param  array   $replace
     * @param  string  $locale
     * @return string
     */
    function trans_choice2($key, $number, array $replace = [])
    {
        $locale = app()->getLocale() == 'ar' ? 'en' : 'ar';
        return trans_choice($key, $number, $replace, $locale);
    }
}

if (! function_exists('is_collection')) {
    function is_collection(&$var): bool
    {
        return $var instanceof \Illuminate\Support\Collection;
    }
}

if (! function_exists('du')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed  $args
     * @return void
     */
    function du(...$args)
    {
        try {
            $debug = @debug_backtrace();
            if(!empty($debug) AND is_array($debug))
            {
                $_nl = $debugFiles = "<br>\r\n";
                $debugFiles = "";

                $call = @current($debug);
                foreach ($debug as $issues)
                {
                    $_line = isset($issues['line']) ? $issues['line'] : 0;
//                    $debugFiles .= (!empty($debugFiles)?$_nl:"");
//                    $debugFiles .= "[" . @basename($issues['file']) . "] Line ({$_line})";// . $_nl;
                }
            } else throw new \Exception(__LINE__ . " function debug_backtrace() returned: {$debug}");
            $line = (isset($call['line'])?$call['line']:__LINE__);
            $file = (isset($call['file'])?$call['file']:__FILE__);
            $file = @basename($file);

//            dump( $debugFiles );
            if(App::runningInConsole()) {
                echo(
                "\n\n[{$file}] Line ({$line}): \n"
                );
            } else  $args = \Illuminate\Support\Arr::prepend($args, "{$file}:{$line}");

            collect($args)->each(function ($e) {
                dump($e);
            });

            if(App::runningInConsole()) {
                echo(
                        "\n\n\n :" . __LINE__ . ""
                );
            } else echo(
                    "<small>By: \n" . __FILE__ . ":" . __LINE__ . "  \n" . __CLASS__ . "@" . __METHOD__ . "</small>"
            );
//            exit;
        }
        catch(\Exception $e) {
            if (App::runningInConsole()) {
                echo $msg = (__LINE__ . " ERROR: Function (".__FUNCTION__."), File (".__FILE__."), Line (".__LINE__."): ".$e->getMessage());
            } else
                echo $msg = (__LINE__ . " ERROR: Function (".__FUNCTION__."), File (".__FILE__."), Line (".__LINE__."): ".$e->getMessage());
            dump(
                    $e->getMessage(),
                    $msg,
                    debug_backtrace()
            );
//            exit;
        }

//        die(1);
    }
}
if (! function_exists('d')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed  $args
     * @return void
     */
    function d(...$args)
    {
        try {
            $debug = @debug_backtrace();
            if(!empty($debug) AND is_array($debug))
            {
                $_nl = $debugFiles = "<br>\r\n";
                $debugFiles = "";

                $call = @current($debug);
                foreach ($debug as $issues)
                {
                    $_line = isset($issues['line']) ? $issues['line'] : 0;
//                    $debugFiles .= (!empty($debugFiles)?$_nl:"");
//                    $debugFiles .= "[" . @basename($issues['file']) . "] Line ({$_line})";// . $_nl;
                }
            } else throw new \Exception(__LINE__ . " function debug_backtrace() returned: {$debug}");
            $line = (isset($call['line'])?$call['line']:__LINE__);
            $file = (isset($call['file'])?$call['file']:__FILE__);
            $file = @basename($file);

//            dump( $debugFiles );
            if(App::runningInConsole()) {
                echo(
                "\n\n[{$file}] Line ({$line}): \n"
                );
            } else echo( "[{$file}] Line ({$line}): <br>" );

            collect($args)->each(function ($e) {
                dump($e);
            });

            if(App::runningInConsole()) {
                echo(
                        "\n\n\n :" . __LINE__ . ""
                );
            } else echo(
                    "<small>By: \n" . __FILE__ . ":" . __LINE__ . "  \n" . __CLASS__ . "@" . __METHOD__ . "</small>"
            );
            exit;
        }
        catch(\Exception $e) {
            if (App::runningInConsole()) {
                echo $msg = (__LINE__ . " ERROR: Function (".__FUNCTION__."), File (".__FILE__."), Line (".__LINE__."): ".$e->getMessage());
            } else
                echo $msg = (__LINE__ . " ERROR: Function (".__FUNCTION__."), File (".__FILE__."), Line (".__LINE__."): ".$e->getMessage());
            dd(
                    $e->getMessage(),
                    $msg,
                    debug_backtrace()
            );
            exit;
        }

        die(1);
    }
}

if (! function_exists('dx')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed  $args
     * @return void
     */
    function dx(...$args)
    {
        try {
            $debug = @debug_backtrace();
            if(!empty($debug) AND is_array($debug))
            {
                $_nl = $debugFiles = "<br>\r\n";
                $debugFiles = "";

                $call = @current($debug);
                foreach ($debug as $issues)
                {
                    $_line = isset($issues['line']) ? $issues['line'] : 0;
//                    $debugFiles .= (!empty($debugFiles)?$_nl:"");
//                    $debugFiles .= "[" . @basename($issues['file']) . "] Line ({$_line})";// . $_nl;
                }
            } else throw new \Exception(__LINE__ . " function debug_backtrace() returned: {$debug}");
            $line = (isset($call['line'])?$call['line']:__LINE__);
            $file = (isset($call['file'])?$call['file']:__FILE__);
            $file = @basename($file);

//            dump( $debugFiles );
            echo( "[{$file}] Line ({$line}): <br>" );

            collect($args)->each(function ($e) {
                dump($e);
            });

            echo(
                    "<small>By: \n" . __FILE__ . ":" . __LINE__ . "  \n" . __CLASS__ . "@" . __METHOD__ . "</small>"
            );
        }
        catch(\Exception $e)
        {
            echo $msg = (__LINE__ . " ERROR: Function (".__FUNCTION__."), File (".__FILE__."), Line (".__LINE__."): ".$e->getMessage());
            dd(
                    $e->getMessage(),
                    $msg,
                    debug_backtrace()
            );
            exit;
        }

    }
}

if (! function_exists('dxx')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed  $args
     * @return void
     */
    function dxx(...$args) { }
}

if (! function_exists('autoModel')) {
    /**
     * config storage for autoModel
     * @param null $key
     * @param null $default
     *
     * @return \Illuminate\Foundation\Application|mixed|autoModel
     */
    function autoModel($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('autoModel');
        }

        if (is_array($key)) {
            return app('autoModel')->put($key);
        }

        return app('autoModel')->get($key, $default);
    }
}



// statics
// overrides
config([
    'language.middleware' => 'App\Overrides\Akaunting\SetLocale'
]);

