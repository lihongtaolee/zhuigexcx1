<?php
if (!defined('ABSPATH')) {
    die;
}

require_once dirname(__FILE__) . '/classes/setup.class.php';
require_once dirname(__FILE__) . '/classes/abstract.class.php';
require_once dirname(__FILE__) . '/classes/fields.class.php';
require_once dirname(__FILE__) . '/classes/admin-options.class.php';
require_once dirname(__FILE__) . '/classes/metabox-options.class.php';
require_once dirname(__FILE__) . '/classes/taxonomy-options.class.php';

require_once dirname(__FILE__) . '/functions/actions.php';
require_once dirname(__FILE__) . '/functions/helpers.php';
require_once dirname(__FILE__) . '/functions/sanitize.php';
require_once dirname(__FILE__) . '/functions/validate.php';

if (!function_exists('csf_init') && !class_exists('CSF')) {
    function csf_init() {
        return CSF::init();
    }
}