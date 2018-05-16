<?php
namespace Sideways8\Setup\WP_Project_Config;

class Install
{
    /**
     * @var
     */
    protected $root_path;

    /**
     * Install constructor.
     *
     * @param string $project_root Absolute path to project root.
     *
     * @throws \Exception
     */
    public function __construct( $project_root = null ) {
        // Verify the root was supplied.
        if ( null === $project_root ) {
            throw new \Exception( 'Please specify absolute path to project root.' );
        }
        // Verify the root exists.
        if ( ! is_dir( $project_root ) ) {
            throw new \Exception( 'Invalid project root directory specified.' );
        }
        // Set the project root.
        $this->root_path = $project_root;
    }

    public function run() {
        $this->create_configuration_directory();
        $this->copy_stubs();
        $this->install_auth_keys();
    }

    public function create_configuration_directory() {
        $path = $this->root_path . '/config';
        if ( ! is_dir( $path ) ) {
            mkdir( $path );
        }
    }

    public function copy_stubs() {
        // Project configuration directory.
        $config_directory = $this->root_path . '/config/';
        $www_directory    = $this->root_path . '/www/';
        // Paths to subs.
        $config_dev_stub        = __DIR__ . '/../../stubs/wp-config-dev.php.stub';
        $config_local_stub      = __DIR__ . '/../../stubs/wp-config-local.php.stub';
        $config_production_stub = __DIR__ . '/../../stubs/wp-config-production.php.stub';
        // Copy stubs.
        $target = $config_directory . 'wp-config-dev.php';
        if ( ! file_exists( $target ) ) {
            copy( $config_dev_stub, $target );
        }
        $target = $config_directory . 'wp-config-production.php';
        if ( ! file_exists( $target ) ) {
            copy( $config_production_stub, $target );
        }
        $target = $www_directory . 'wp-config-local.php';
        if ( ! file_exists( $target ) ) {
            copy( $config_local_stub, $target );
        }
    }

    public function install_auth_keys() {
        $url    = 'https://api.wordpress.org/secret-key/1.1/salt/';
        $data   = "<?php\n" . file_get_contents( $url );
        $target = $this->root_path . '/config/auth-keys.php';
        if ( ! file_exists( $target ) ) {
            file_put_contents( $target, $data );
        }
    }
}