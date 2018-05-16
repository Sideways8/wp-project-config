<?php
namespace Sideways8\WP_Project_Config\Setup;

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
        $targets = [
            $config_dev_stub => $config_directory . 'wp-config-dev.php',
            $config_production_stub => $config_directory . 'wp-config-production.php',
            $config_local_stub => $www_directory . 'wp-config-local.php'
        ];

        foreach( $targets as $key => $target ) {
            if ( ! file_exists( $target ) ) {
                copy( $key, $target );
            }
        }

        // Create content directories.
        $targets = [
            $www_directory . 'content',
            $www_directory . 'content/plugins',
            $www_directory . 'content/themes',
        ];

        foreach( $targets as $target ) {
            if ( ! file_exists( $target ) ) {
                mkdir( $target );
            }
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