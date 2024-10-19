<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 12:18 PM
 */

if (config('webarq.system.query-log')) {
    DB::enableQueryLog();
}

/**
 * @param string $class
 * @param string $string
 * @return string
 */
function webarqMakeControllerMethod($class, $string)
{
    $method = config('webarq.system.action-prefix');
    if (Request::ajax()) {
        $method .= 'Ajax';
    }
    $method .= studly_case(strtolower(Request::method() . ' ' . $string));

    if (method_exists($class, $method)) {
        return $method;
    }
}

/**
 * @param $namespace
 * @param $directory
 * @param $file
 * @return string
 */
function webarqMakeControllerClass($namespace, $directory, $file)
{
    if (is_file($directory . $file . 'Controller.php')) {
        return $namespace . str_replace(DIRECTORY_SEPARATOR, '\\', $file) . 'Controller';
    }
}

function webarqMakePattern($paramLength)
{
    $pattern = '{module?}/{panel?}/{action?}';

    if ($paramLength > 0) {
        for ($i = 1; $i <= $paramLength; $i++) {
            $pattern .= '/{param' . $i . '?}';
        }
    }

    return $pattern;
}

function webarqMakeRoutePattern($param)
{
    $pattern = '{lang?}/{module?}/{controller?}/{action?}';

    for ($i = 1; $i <= $param; $i++) {
        $pattern .= '/{param' . $i . '?}';
    }

    return $pattern;
}

/**
 * @param string $group Group directory
 * @param string $a Default module
 * @param string $b Default panel
 * @param string $c Default action
 * @param int $len Param length
 */
function webarqAutoRoute($group, $a, $b = null, $c = null, $len = 4)
{
    $pattern = webarqMakeRoutePattern($len);
    Route::match(['get', 'post'], $pattern, function ($lang = null, $mod = null, $pnl = null, $act = null)
    use ($group, $a, $b, $c, $len) {
        /*
         * Set the Wl default lang with config system lang, when in Site route group, and class Wl exist.
         */
        if ('Site' === $group && class_exists('Wl') && Wa::config('system.lang')) {
            Wl::setDefault(Wa::config('system.lang'));
        }

        /*
         * Take out $lang parameter when in panel board, or when class Wl does not exist,
         * or given $lang does not exist in class Wl allowed codes.
         * There for we will use Wl default code when exists or "en" by de facto.
         */
        if ('Panel' === $group || !class_exists('Wl') || !in_array($lang, Wl::getCodes())) {
            $act = $pnl;
            $pnl = $mod;
            $mod = $lang;
            $lang = !class_exists('Wl') ? 'en' : Wl::getDefault();
        }

        function insider($dir, $lang, $mod, $pnl, $act, $group, $a, $b, $c, $len)
        {
// Set app locale
            App::setLocale($lang);

// Default method
            $method = null;
// Set module, controller, and action
            if (null === $mod) {
                $mod = $a;
            }
            $mod = strtolower($mod);
            $scMod = studly_case($mod);

            if (null === $pnl) {
                $pnl = $b;
            }
            $pnl = strtolower($pnl);
            $scPnl = studly_case($pnl);

            if (null === $act) {
                $act = $c;
            }

            if (null !== $act) {
                $act = strtolower($act);
            }
            $scAct = studly_case($act);

// Params
            $params = [];
            $i = 4;
            $t = 3 + $len;

            if ('Panel' === $group) {
                $i += 1;
                $t += 1;
            } else {
                $menu = Wa::menu();
            }

            for ($i; $i <= $t; $i++) {
                if (Request::segment($i)) {
                    $params[] = Request::segment($i);
                }
            }

// Server directory separator
            $sep = DIRECTORY_SEPARATOR;
            if ('app' === $dir) {
                $ns = 'App\Http\Controllers\\' . $group . '\\';
                $rt = app_path() . $sep . 'Http' . $sep . 'Controllers' . $sep . $group . $sep;
            } else {
                $ns = 'Webarq\Http\Controllers\\' . $group . '\\';
                $rt = __DIR__ . $sep . '..' . $sep . 'src' . $sep . 'Http' . $sep . 'Controllers' . $sep . $group . $sep;
            }
            $md = null;

// Looking out for controller by given url path
// Panel is a directory and action is a file controller
            if (is_dir($rt . $scMod . $sep . $scPnl)
                    && null !== ($class = webarqMakeControllerClass($ns, $rt, $scMod . $sep . $scPnl . $sep . $scAct))
            ) {
                $con = $act;
                $act = array_get($params, 0);
                if (null !== ($method = webarqMakeControllerMethod($class, array_get($params, 0)))) {
                    array_pull($params, 0);
                } else {
                    $act = $con;
                }
// Module is a directory, and panel is a controller
            } elseif (is_dir($rt . $scMod)
                    && null !== ($class = webarqMakeControllerClass($ns, $rt, $scMod . $sep . $scPnl))
            ) {
                $con = $pnl;
                if (null !== $act && null === ($method = webarqMakeControllerMethod($class, $act))) {
                    array_unshift($params, $act);
                    $act = null;
                }
// Instead of a directory, this time module is a controller file
            } elseif (null !== ($class = webarqMakeControllerClass($ns, $rt, $scMod))) {
                $con = $mod;
                if (null !== ($method = webarqMakeControllerMethod($class, $pnl))) {
                    if (null !== $act) {
                        array_unshift($params, $act);
                    }
                    $act = $pnl;
                } else {
                    if (null !== $act) {
                        array_unshift($params, $act);
                    }
                    if (null !== $pnl) {
                        array_unshift($params, $pnl);
                    }
                }
            } elseif (isset($menu)) {
// Check site template controller
                if (null !== $menu->getActive() && [] !== $menu->getActive()) {
                    $tpl = studly_case($menu->getActive()->template);
                    if (null !== ($tpl = webarqMakeControllerClass(
                                    $ns . 'Templates\\', $rt . 'Templates' . $sep, $tpl))
                    ) {
                        $class = $tpl;
                        $con = $mod;

// Check if module is a method
                        if (null !== ($tpl = webarqMakeControllerMethod($class, $mod))) {
                            $method = $tpl;
                            if (null !== $act) {
                                array_unshift($params, $pnl, $act);
                            } elseif ('' !== $pnl) {
                                array_unshift($params, $pnl);
                            }

                            $act = $mod;
// Check if panel is a method
                        } elseif (null !== ($tpl = webarqMakeControllerMethod($class, $pnl))) {
                            $method = $tpl;
                            if (null !== $act) {
                                array_unshift($params, $act);
                            }

                            $act = $pnl;
                        }
                    }
                }
            }

// Last option should be base controller
            if (is_null($class) && 'vendor' === $dir && null !== ($con = config('webarq.system.default-controller'))) {
// Application level
                $class = webarqMakeControllerClass(
                        'App\Http\Controllers\\' . $group . '\\',
                        app_path() . $sep . 'Http' . $sep . 'Controllers' . $sep . $group . $sep,
                        studly_case($con));
// Vendor level
                if (is_null($class)) {
                    $class = webarqMakeControllerClass($ns, $rt, studly_case($con));
                }

                if (!is_null($class)) {
                    if (null !== ($method = webarqMakeControllerMethod($class, $mod))) {
                        if (null !== $act) {
                            array_unshift($params, $pnl, $act);
                        } elseif ('' !== $pnl) {
                            array_unshift($params, $pnl);
                        }
                        $act = $mod;
                    }
                }
            }

// Yay, found a class
            if (isset($class)) {
                if ('Panel' === $group && 'helper' === $mod) {
                    $mod = array_pull($params, 0);
                    $pnl = array_pull($params, 1);
                }
// Reindex params keys
                if ([] !== $params) {
                    $params = array_combine(range(1, count($params)), array_values($params));
                }
// Params menu
                if ('Site' === $group && isset($menu)) {
                    $params['menu'] = $menu;
                }
// Use default method when it is not set
                if (null === $method) {
                    $act = config('webarq.system.default-action');
                    $method = config('webarq.system.action-prefix') . ucfirst(strtolower(Request::method()))
                            . studly_case($act);
                }
// Route parameters
                $params['module'] = $mod;
                $params['panel'] = $pnl;
                $params['controller'] = $con;
                $params['action'] = $act;
// Resolving class
                $class = resolve($class, ['params' => $params]);
// Execute class "escape" method if any
                if (method_exists($class, 'escape')) {
                    if (null !== ($escape = $class->escape())) {
                        return $escape;
                    }
                }

// Execute class "before" method if any
                if (method_exists($class, 'before')) {
                    $class->before($params);
                }
// Execute class "escape{$method}" if any
                if (method_exists($class, 'escape' . $scAct) && null !== ($escape = $class->{'escape' . $scAct}())) {
                    return $escape;
                }
// Call method (do not forget about method injection)
                $call = App::call([$class, $method], $params);

                if (!is_null($call)) {
                    return $call;
                } elseif (method_exists($class, 'after')) {
                    return $class->after();
                } else {
                    return view('webarq.errors.204');
                }
            } else {
                if ('app' === $dir) {
                    return insider('vendor', $lang, $mod, $pnl, $act, $group, $a, $b, $c, $len);
                } else {
                    abort(404, 'Route not matched');
                }
            }
        }

        return insider('app', $lang, $mod, $pnl, $act, $group, $a, $b, $c, $len);
    });
}