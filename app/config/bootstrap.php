<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2012, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(__DIR__)));

/**
 * This is the primary bootstrap file of your application, and is loaded immediately after the front
 * controller (`webroot/index.php`) is invoked. It includes references to other feature-specific
 * bootstrap files that you can turn on and off to configure the services needed for your
 * application.
 *
 * Besides global configuration of external application resources, these files also include
 * configuration for various classes to interact with one another, usually through _filters_.
 * Filters are Lithium's system of creating interactions between classes without tight coupling. See
 * the `Filters` class for more information.
 *
 * If you have other services that must be configured globally for the entire application, create a
 * new bootstrap file and `require` it here.
 *
 * @see lithium\util\collection\Filters
 */

/**
 * The libraries file contains the loading instructions for all plugins, frameworks and other class
 * libraries used in the application, including the Lithium core, and the application itself. These
 * instructions include library names, paths to files, and any applicable class-loading rules. This
 * file also statically loads common classes to improve bootstrap performance.
 */
require __DIR__ . '/bootstrap/libraries.php';

/**
 * Setting a custom matcher for the Environment so that it can be controlled via an environment
 * variable named "LI3_ENV".
 */
use lithium\core\Environment;

Environment::is(function($request){
    if ( isset($request->params['env']) ) return $request->params['env'];

    if ( preg_match('|^test/|', $request->url) ) return 'test';

    return $request->env('LI3_ENV') ?: get_cfg_var('aws.li3_env') ?: 'production';
});
if (!Environment::get())
{
    if (!Environment::get(getenv('LI3_ENV')))
    {
        Environment::set(getenv('LI3_ENV'), array());
    }
    Environment::set(getenv('LI3_ENV'));
}

/**
 * Your own development config here (in .gitignore)
 */
if (!(Environment::is('production') || Environment::is('demo')) && file_exists(__DIR__.'/bootstrap/development.php'))
{
    require __DIR__.'/bootstrap/development.php';
}

/**
 * The error configuration allows you to use the filter system along with the advanced matching
 * rules of the `ErrorHandler` class to provide a high level of control over managing exceptions in
 * your application, with no impact on framework or application code.
 */
require __DIR__ . '/bootstrap/errors.php';

/**
 * This file contains configurations for loggers
 */
require __DIR__ . '/bootstrap/loggers.php';

/**
 * This file contains configurations for connecting to external caching resources, as well as
 * default caching rules for various systems within your application
 */
require __DIR__ . '/bootstrap/cache.php';

/**
 * Include this file if your application uses one or more database connections.
 */
require __DIR__ . '/bootstrap/connections.php';

/**
 * This file defines bindings between classes which are triggered during the request cycle, and
 * allow the framework to automatically configure its environmental settings. You can add your own
 * behavior and modify the dispatch cycle to suit your needs.
 */
require __DIR__ . '/bootstrap/action.php';

/**
 * This file contains configuration for session (and/or cookie) storage, and user or web service
 * authentication.
 */
require __DIR__ . '/bootstrap/session.php';

/**
 * This file contains your application's globalization rules, including inflections,
 * transliterations, localized validation, and how localized text should be loaded. Uncomment this
 * line if you plan to globalize your site.
 */
// require __DIR__ . '/bootstrap/g11n.php';

/**
 * This file contains configurations for handling different content types within the framework,
 * including converting data to and from different formats, and handling static media assets.
 */
require __DIR__ . '/bootstrap/media.php';

/**
 * This file configures console filters and settings, specifically output behavior and coloring.
 */
if (PHP_SAPI === 'cli') {
	require __DIR__ . '/bootstrap/console.php';
}

define('DOTCOM_PORTAL_URL', getenv('DOTCOM_PORTAL_URL'));
define('COMMUNITY_PORTAL_URL', getenv('COMMUNITY_PORTAL_URL'));
define('BASECAMP_URL', getenv('BASECAMP_URL') . '/login');
define('DOCUMENTATION_URL', getenv('DOCUMENTATION_URL'));
define('MANAGE_PORTAL_URL', (getenv('TESTING_URL_HOSTNAME') == 'accounts.localhost') ? 'http://'.getenv('TESTING_URL_HOSTNAME'): 'https://'.getenv('TESTING_URL_HOSTNAME'));
define('MBAAS_PORTAL_URL', getenv('MBAAS_PORTAL_URL'));
define('MF_CONSOLE_URL', 'https://'.getenv('TESTING_URL_HOSTNAME').'/'.getenv('MBAAS_PORTAL_URL'));
define('TERMS_AND_CONDITIONS_URL', getenv('DOTCOM_PORTAL_URL').'/terms-and-conditions');
define('PRIVACY_STATEMENT_URL', 'https://www.hcltechsw.com/wps/portal/legal/privacy');
define('ENV_PRODUCTION', 'production');
define('ENV_STG', 'stg');
define('ENV_QA', 'qa');
define('ENV_SIT', 'sit');
define('ENV_DEV', 'dev');
define('ENV_DEMO', 'demo');
?>
