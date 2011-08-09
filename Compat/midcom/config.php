<?php
class midcom_config implements arrayaccess
{
    private $_default_config = array
    (
        // Authentication configuration
        'auth_type' => 'Plaintext',
        'auth_backend' => 'simple',
        'auth_backend_simple_cookie_id' => '',
        'auth_login_session_timeout' => 3600,
        'auth_login_session_update_interval' => 300,
        'auth_frontend' => 'form',
        'auth_sitegroup_mode' => 'auto',
        'auth_check_client_ip' => true,
        'auth_allow_sudo' => true,
        'auth_login_form_httpcode' => 403,
        'auth_openid_enable' => false,
        'auth_save_prev_login' => false,
        'auth_success_callback' => null,
        'auth_failure_callback' => null,
        'auth_allow_trusted' => false,
        'person_class' => 'midgard_person',

        'auth_backend_simple_cookie_path' => 'auto',
        'auth_backend_simple_cookie_domain' => null,
        'auth_backend_simple_cookie_secure' => true,

        'cache_base_directory' => '/tmp/',
        'cache_autoload_queue' => Array('content', 'nap', 'phpscripts', 'memcache'),

        'cache_module_content_name' => 'auto',

        'cache_module_content_backend' => array('driver' => 'flatfile'),
        'cache_module_memcache_backend' => 'flatfile',
        'cache_module_memcache_backend_config' => Array(),
        'cache_module_memcache_data_groups' => Array('ACL', 'PARENT', 'L10N'/*, 'jscss_merged'*/),

        'cache_module_content_uncached' => true,
        'cache_module_content_headers_strategy' => 'revalidate',
        'cache_module_content_headers_strategy_authenticated' => 'private',

        'cache_module_content_default_lifetime' => 900,

        'cache_module_content_default_lifetime_authenticated' => 0,

        'cache_module_content_caching_strategy' => 'user',

        'cache_module_nap_backend' => Array() /* Auto-Detect */,
        'cache_module_nap_metadata_cachesize' => 75,

        'cache_module_phpscripts_directory' => 'phpscripts/',

        'cron_day_hours' => 0,
        'cron_day_minutes' => 0,
        'cron_hour_minutes' => 30,

        'i18n_language_db_path' => 'file:/midcom/config/language_db.inc',
        'i18n_available_languages' => null,
        'i18n_fallback_language' => 'en',

        'indexer_backend' => false,
        'indexer_index_name' => 'auto',
        'indexer_reindex_memorylimit' => 250,
        'indexer_reindex_allowed_ips' => Array('127.0.0.1'),

        'indexer_xmltcp_host' => "127.0.0.1",
        'indexer_xmltcp_port' => 8983,

        'log_filename' => '/tmp/midcom.log',
        'log_level' => MIDCOM_LOG_ERROR,
        'log_firephp' => false,
        'enable_included_list' => false,
        'error_actions' => array(),

        'midcom_root_topic_guid' => '',
        'midcom_sgconfig_basedir' => '/sitegroup-config',
        'midcom_site_url' => '/',
        'midcom_site_title' => '',
        'midcom_tempdir' => '/tmp',
        'midcom_temporary_resource_timeout' => 86400,

        'show_hidden_objects' => true,
        'show_unapproved_objects' => true,

        'styleengine_relative_paths' => false,
        'styleengine_default_styles' => Array(),

        'toolbars_host_style_class' => 'midcom_toolbar host_toolbar',
        'toolbars_host_style_id' => null,
        'toolbars_node_style_class' => 'midcom_toolbar node_toolbar',
        'toolbars_node_style_id' => null,
        'toolbars_view_style_class' => 'midcom_toolbar view_toolbar',
        'toolbars_view_style_id' => null,
        'toolbars_help_style_class' => 'midcom_toolbar help_toolbar',
        'toolbars_help_style_id' => null,
        'toolbars_simple_css_path' => '',
        'toolbars_enable_centralized' => true,
        'toolbars_type' => 'palette', // Either 'menu' or 'palette'
        'toolbars_position_storagemode' => 'cookie', // Either 'session', 'cookie' or 'parameter'

        'service_midcom_core_service_urlparser' => 'midcom_core_service_implementation_urlparsertopic',
        'service_midcom_core_service_urlgenerator' => 'midcom_core_service_implementation_urlgeneratori18n',

        'attachment_cache_enabled' => false,
        'attachment_cache_root' => '/var/lib/midgard/vhosts/example.net/80/midcom-static/blobs',
        'attachment_cache_url' => '/midcom-static/blobs',

        'attachment_xsendfile_enable' => false,

        'utility_imagemagick_base' => '',
        'utility_jpegtran' => 'jpegtran',
        'utility_unzip' => 'unzip',
        'utility_gzip' => 'gzip',
        'utility_tar' => 'tar',
        'utility_find' => 'find',
        'utility_file' => 'file',
        'utility_catdoc' => 'catdoc',
        'utility_pdftotext' => 'pdftotext',
        'utility_unrtf' => 'unrtf',
        'utility_diff' => 'diff',
        'utility_rcs' => 'rcs',

        'midcom_services_rcs_bin_dir' => '/usr/bin',
        'midcom_services_rcs_root' => '',
        'midcom_services_rcs_enable' => true,

        'metadata_approval' => false,
        'metadata_scheduling' => false,
        'metadata_lock_timeout' => 60,    // Time in minutes
        'staging2live_staging' => false,

        'metadata_schema' => 'file:/midcom/config/metadata_default.inc',

        'metadata_head_elements' => array
        (
            'published'   => 'DC.date',
        ),

        'metadata_opengraph' => false,

        'component_listing_allowed' => null,
        'component_listing_excluded' => null,

        'positioning_enable' => false,

        'page_class_include_component' => true,

        'wrap_style_show_with_name' => false,

        'jquery_version' => '1.5.2.min',
        'jquery_ui_version' => '1.8.11',
        'jquery_ui_theme' => null,
        'jquery_load_from_google' => false,
        'enable_ajax_editing' => false,

        'sessioning_service_enable' => true,
        'sessioning_service_always_enable_for_users' => true,

        'cron_purge_deleted_after' => 25,

        'symlinks' => false,

        'theme' => '',
    );

    private $_merged_config = array();

    public function __construct()
    {
        $this->_complete_defaults();

        if (! array_key_exists('midcom_config_site', $GLOBALS))
        {
            $GLOBALS['midcom_config_site'] = Array();
        }
        if (! array_key_exists('midcom_config_local', $GLOBALS))
        {
            $GLOBALS['midcom_config_local'] = Array();
        }
        $this->_merged_config = array_merge
        (
            $this->_default_config,
            $GLOBALS['midcom_config_site'],
            $GLOBALS['midcom_config_local']
        );
    }

    private function _complete_defaults()
    {
        if (isset($_MIDGARD['config']['auth_cookie_id']))
        {
            $auth_cookie_id = $_MIDGARD['config']['auth_cookie_id'];
        }
        else
        {
            // Generate host identifier from Midgard host
            $auth_cookie_id = "host{$_MIDGARD['host']}";
        }
        $this->_default_config['auth_backend_simple_cookie_id'] = $auth_cookie_id;

        if (class_exists('Memcache'))
        {
            $this->_default_config['cache_module_content_backend'] = array('driver' => 'memcached');
            $this->_default_config['cache_module_memcache_backend'] = 'memcached';
        }
        if (isset($_SERVER['SERVER_ADDR']))
        {
            $this->_default_config['indexer_reindex_allowed_ips'][] = $_SERVER['SERVER_ADDR'];
        }
        $this->_default_config['midcom_site_title'] = $_SERVER['SERVER_NAME'];
        $this->_default_config['toolbars_simple_css_path'] = MIDCOM_STATIC_URL . "/midcom.services.toolbars/simple.css";

        // TODO: Would be good to include DB name into the path
        if ($_MIDGARD['config']['prefix'] == '/usr')
        {
            $this->_default_config['midcom_services_rcs_root'] = '/var/lib/midgard/rcs';
        }
        else if ($_MIDGARD['config']['prefix'] == '/usr/local')
        {
            $this->_default_config['midcom_services_rcs_root'] = '/var/local/lib/midgard/rcs';
        }
        else
        {
            $this->_default_config['midcom_services_rcs_root'] =  "{$_MIDGARD['config']['prefix']}/var/lib/midgard/rcs";
        }
    }

    public function offsetSet($offset, $value)
    {
        $this->_merged_config[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->_merged_config[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_merged_config[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_merged_config[$offset]) ? $this->_merged_config[$offset] : null;
    }
}
?>
