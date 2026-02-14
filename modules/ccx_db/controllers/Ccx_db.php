<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ccx_db extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (!has_permission('settings', '', 'view')) {
            access_denied('CCX DB');
        }
        $this->load->helper('perfex_saas/perfex_saas');
    }

    public function compare_db()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $source_slug = $this->input->post('source_slug');
        $dest_slug = $this->input->post('dest_slug');

        if ($source_slug == $dest_slug) {
            echo json_encode(['error' => 'Source and Destination databases cannot be the same.']);
            return;
        }

        $source_db = $this->_get_db_connection($source_slug);
        $dest_db = $this->_get_db_connection($dest_slug);

        if (isset($source_db['error']) || isset($dest_db['error'])) {
            echo json_encode(['error' => 'Connection failed: ' . ($source_db['error'] ?? '') . ' ' . ($dest_db['error'] ?? '')]);
            return;
        }

        $source_schema = $this->_get_db_schema($source_db['conn'], $source_db['prefix']);
        $dest_schema = $this->_get_db_schema($dest_db['conn'], $dest_db['prefix']);

        // Compare Schemas
        $comparison = [
            'missing_in_dest' => [],
            'missing_in_source' => [],
            'schema_mismatch' => [],
            'row_count_mismatch' => []
        ];

        // Check tables in source
        foreach ($source_schema as $table => $info) {
            if (!isset($dest_schema[$table])) {
                $comparison['missing_in_dest'][] = $table;
                continue;
            }

            // Check row counts
            if ($info['rows'] != $dest_schema[$table]['rows']) {
                $comparison['row_count_mismatch'][] = [
                    'table' => $table,
                    'source_rows' => $info['rows'],
                    'dest_rows' => $dest_schema[$table]['rows']
                ];
            }

            // Check columns (Basic count check for now, can be expanded)
            if (count($info['columns']) != count($dest_schema[$table]['columns'])) {
                $comparison['schema_mismatch'][] = [
                    'table' => $table,
                    'issue' => 'Column count mismatch',
                    'source_cols' => count($info['columns']),
                    'dest_cols' => count($dest_schema[$table]['columns'])
                ];
            } else {
                // Deep column check
                $diff_cols = array_diff($info['columns'], $dest_schema[$table]['columns']);
                if (!empty($diff_cols)) {
                    $comparison['schema_mismatch'][] = [
                        'table' => $table,
                        'issue' => 'Column name mismatch',
                        'diff' => implode(', ', $diff_cols)
                    ];
                }
            }
        }

        // Check tables in dest
        foreach ($dest_schema as $table => $info) {
            if (!isset($source_schema[$table])) {
                $comparison['missing_in_source'][] = $table;
            }
        }

        // Close connections
        if (is_object($source_db['conn']))
            $source_db['conn']->close();
        if (is_object($dest_db['conn']))
            $dest_db['conn']->close();

        $this->load->view('ccx_db/compare_result', ['comparison' => $comparison]);
    }

    private function _get_db_connection($slug)
    {
        if ($slug == 'master') {
            return ['conn' => $this->db, 'prefix' => ''];
        }

        $companies = $this->perfex_saas_model->companies($slug); // Not efficient but works for single lookup if slug is ID, but we have slug.
        // Perfex_saas_model::companies() expects ID potentially. 
        // Let's use get_company_by_slug
        $company = $this->perfex_saas_model->get_company_by_slug($slug);

        if (!$company) {
            return ['error' => 'Company not found'];
        }

        $dsn = perfex_saas_get_company_dsn($company);
        $prefix = perfex_saas_tenant_db_prefix($company->slug);

        try {
            $temp_db = perfex_saas_load_ci_db_from_dsn($dsn, ['dbprefix' => $prefix]);

            if ($temp_db && $temp_db->conn_id) {
                return ['conn' => $temp_db, 'prefix' => $prefix];
            }
        } catch (Throwable $e) {
            return ['error' => 'Connection Exception: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => 'Connection Exception: ' . $e->getMessage()];
        }

        return ['error' => 'Connection failed'];
    }

    private function _get_db_schema($db, $prefix)
    {
        $tables = [];
        $query = $db->query("SHOW TABLES LIKE '$prefix%'");
        $rows = $query->result_array();

        foreach ($rows as $row) {
            $table_name = array_values($row)[0];
            // Normalize table name (remove prefix for comparison)
            $normalized_name = $table_name;
            if (!empty($prefix) && strpos($table_name, $prefix) === 0) {
                $normalized_name = substr($table_name, strlen($prefix));
            }

            // Get Row Count
            $count_query = $db->query("SELECT COUNT(*) as c FROM `$table_name`");
            $count = $count_query->row()->c;

            // Get Columns
            $cols_query = $db->query("SHOW COLUMNS FROM `$table_name`");
            $cols = [];
            foreach ($cols_query->result() as $col) {
                $cols[] = $col->Field;
            }

            $tables[$normalized_name] = [
                'real_name' => $table_name,
                'rows' => $count,
                'columns' => $cols
            ];
        }
        return $tables;
    }

    public function get_tables_json($slug)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $db = $this->_get_db_connection($slug);
        if (isset($db['error'])) {
            echo json_encode(['error' => $db['error']]);
            return;
        }

        $schema = $this->_get_db_schema($db['conn'], $db['prefix']);
        $tables = array_keys($schema);
        sort($tables);

        if (is_object($db['conn']))
            $db['conn']->close();

        echo json_encode($tables);
    }

    public function copy_db_action()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $source_slug = $this->input->post('source_slug');
        $dest_slug = $this->input->post('dest_slug');

        if ($source_slug == $dest_slug) {
            echo json_encode(['error' => 'Source and Destination cannot be the same.']);
            return;
        }

        try {
            $source_db = $this->_get_db_connection($source_slug);
            $dest_db = $this->_get_db_connection($dest_slug);

            if (isset($source_db['error']) || isset($dest_db['error'])) {
                throw new Exception('Connection failed: ' . ($source_db['error'] ?? '') . ' ' . ($dest_db['error'] ?? ''));
            }

            // Get all tables from source
            $schema = $this->_get_db_schema($source_db['conn'], $source_db['prefix']);
            $tables = array_keys($schema);

            // Disable FK checks in Dest
            $dest_db['conn']->query('SET FOREIGN_KEY_CHECKS=0');

            $errors = [];
            foreach ($tables as $table) {
                try {
                    $this->_copy_table($source_db, $dest_db, $table);
                } catch (Exception $e) {
                    $errors[] = "Failed to copy $table: " . $e->getMessage();
                }
            }

            // Enable FK checks
            $dest_db['conn']->query('SET FOREIGN_KEY_CHECKS=1');

            // Close connections
            if (is_object($source_db['conn']))
                $source_db['conn']->close();
            if (is_object($dest_db['conn']))
                $dest_db['conn']->close();

            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => 'Completed with errors: ' . implode(', ', $errors)]);
            } else {
                echo json_encode(['success' => true, 'message' => 'Database copied successfully!']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function copy_table_action()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $source_slug = $this->input->post('source_slug');
        $table_name = $this->input->post('table_name');
        $dest_slug = $this->input->post('dest_slug');

        if ($source_slug == $dest_slug) {
            echo json_encode(['error' => 'Source and Destination cannot be the same.']);
            return;
        }

        try {
            $source_db = $this->_get_db_connection($source_slug);
            $dest_db = $this->_get_db_connection($dest_slug);

            if (isset($source_db['error']) || isset($dest_db['error'])) {
                throw new Exception('Connection failed: ' . ($source_db['error'] ?? '') . ' ' . ($dest_db['error'] ?? ''));
            }

            $dest_db['conn']->query('SET FOREIGN_KEY_CHECKS=0');
            $this->_copy_table($source_db, $dest_db, $table_name);
            $dest_db['conn']->query('SET FOREIGN_KEY_CHECKS=1');

            if (is_object($source_db['conn']))
                $source_db['conn']->close();
            if (is_object($dest_db['conn']))
                $dest_db['conn']->close();

            echo json_encode(['success' => true, 'message' => "Table $table_name copied successfully!"]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function _copy_table($source_db, $dest_db, $table_name)
    {
        $s_conn = $source_db['conn'];
        $d_conn = $dest_db['conn'];
        $s_prefix = $source_db['prefix'];
        $d_prefix = $dest_db['prefix'];

        $source_real_table = $s_prefix . $table_name;
        $dest_real_table = $d_prefix . $table_name;

        // 1. Get Create SQL
        $query = $s_conn->query("SHOW CREATE TABLE `$source_real_table`");
        $row = $query->row_array();
        if (!$row)
            throw new Exception("Table $source_real_table not found in source.");

        $create_sql = $row['Create Table'];

        // 2. Adjust Table Name and FK references in SQL
        // Replace source table name with dest table name
        $create_sql = str_replace("`$source_real_table`", "`$dest_real_table`", $create_sql);

        // Replace prefix references (for Foreign Keys)
        // This is a bit risky but standard for this controlled environment
        if ($s_prefix !== $d_prefix) {
            // Replace `prefix_ with `dest_prefix_
            if (!empty($s_prefix)) {
                $create_sql = str_replace("`$s_prefix", "`$d_prefix", $create_sql);
            }
        }

        // 3. Drop Dest Table
        $d_conn->query("DROP TABLE IF EXISTS `$dest_real_table`");

        // 4. Create Dest Table
        if (!$d_conn->query($create_sql)) {
            throw new Exception("Failed to create table $dest_real_table. Error: " . $d_conn->error()['message']);
        }

        // 5. Compute common columns to safely insert data
        $scols_q = $s_conn->query("SHOW COLUMNS FROM `$source_real_table`");
        $scols = [];
        foreach ($scols_q->result() as $c)
            $scols[] = $c->Field;

        $dcols_q = $d_conn->query("SHOW COLUMNS FROM `$dest_real_table`");
        $dcols = [];
        foreach ($dcols_q->result() as $c)
            $dcols[] = $c->Field;

        $common_cols = array_intersect($scols, $dcols);
        if (empty($common_cols))
            return; // Nothing to copy

        // 6. Copy Data (Chunked)
        $batch_size = 1000;
        $offset = 0;

        $cols_str = '`' . implode('`, `', $common_cols) . '`';

        do {
            $data_query = $s_conn->query("SELECT $cols_str FROM `$source_real_table` LIMIT $batch_size OFFSET $offset");
            $rows = $data_query->result_array();

            if (empty($rows))
                break;

            // Batch insert
            // CodeIgniter's insert_batch is nice but we are using potentially different DB objects/drivers, 
            // and we want to be raw for speed.

            $values = [];
            foreach ($rows as $row) {
                $row_vals = [];
                foreach ($row as $val) {
                    if ($val === null) {
                        $row_vals[] = 'NULL';
                    } else {
                        $row_vals[] = "'" . $d_conn->escape_str($val) . "'";
                    }
                }
                $values[] = "(" . implode(',', $row_vals) . ")";
            }

            if (!empty($values)) {
                $in_sql = "INSERT INTO `$dest_real_table` ($cols_str) VALUES " . implode(',', $values);
                if (!$d_conn->query($in_sql)) {
                    throw new Exception("Failed to insert data into $dest_real_table. Error: " . $d_conn->error()['message']);
                }
            }

            $offset += $batch_size;

        } while (count($rows) == $batch_size);
    }


    public function index()
    {
        $data['title'] = 'CCX Database Manager';

        // Fetch Master DB Info
        $master_db_name = $this->db->database;
        $data['master_db'] = [
            'name' => 'Master DB (' . $master_db_name . ')',
            'slug' => 'master',
            'is_master' => true,
            'stats' => $this->_get_db_stats($this->db, '')
        ];

        // Fetch Tenants
        $tenants = [];
        if (table_exists('perfex_saas_companies')) {
            $this->load->model('perfex_saas/perfex_saas_model');
            $companies = $this->perfex_saas_model->companies();

            foreach ($companies as $company) {
                $tenant_stats = $this->_get_tenant_stats($company);
                $tenants[] = [
                    'name' => $company->name . ' (' . $company->slug . ')',
                    'slug' => $company->slug,
                    'is_master' => false,
                    'stats' => $tenant_stats,
                    'company' => $company
                ];
            }
        }
        $data['tenants'] = $tenants;

        // Get all system modules for the dropdown
        $data['system_modules'] = $this->app_modules->get();

        $this->load->view('ccx_db/dashboard', $data);
    }

    private function _get_tenant_stats($company)
    {
        try {
            // Get DSN and prefix for tenant
            $dsn = perfex_saas_get_company_dsn($company);
            $prefix = perfex_saas_tenant_db_prefix($company->slug);

            // Connect to tenant DB
            // Note: This might be heavy if there are many tenants. 
            // In a real production environment with hundreds of tenants, 
            // this should be cached or loaded via AJAX on demand.
            // For now, we'll try to connect dynamically.

            // We use a temporary connection
            $temp_db = perfex_saas_load_ci_db_from_dsn($dsn, ['dbprefix' => $prefix]);

            if ($temp_db && $temp_db->conn_id) {
                $stats = $this->_get_db_stats($temp_db, $prefix);
                $temp_db->close();
                return $stats;
            } else {
                return ['error' => 'Could not connect'];
            }

        } catch (Throwable $e) {
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function _get_db_stats($db_instance, $prefix)
    {
        // Get generic DB stats
        // Size query (approximation for MySQL)
        $db_name = $db_instance->database;
        $sql = "SELECT 
            SUM(data_length + index_length) / 1024 / 1024 AS size_mb, 
            COUNT(*) as table_count 
            FROM information_schema.TABLES 
            WHERE table_schema = '$db_name'";

        // If we are sharing the same DB (multitenancy), we might want to filter by prefix if possible,
        // but information_schema shows physical tables. 
        // For multitenancy (prefix-based), we should filter tables by name.

        if (!empty($prefix)) {
            $sql .= " AND table_name LIKE '$prefix%'";
        }

        $query = $db_instance->query($sql);
        $result = $query->row();

        return [
            'size_mb' => isset($result->size_mb) ? round($result->size_mb, 2) : 0,
            'table_count' => isset($result->table_count) ? $result->table_count : 0
        ];
    }

    /**
     * Redirect helper used by the backup form which posts to this endpoint
     * and opens the actual backup URL in a new tab/window.
     */
    public function backup_redirect()
    {
        $slug = $this->input->post('backup_slug');
        if (empty($slug)) {
            show_404();
        }

        // The form targets _blank, so sending a Location header will navigate the new tab to the backup URL
        $backup_url = admin_url('ccx_db/backup/' . $slug);
        redirect($backup_url);
    }

    /**
     * Generate a simple SQL dump for the requested database (master or tenant slug)
     * Streams the output to avoid large memory usage where possible.
     *
     * @param string $slug
     */
    public function backup($slug = '')
    {
        if (empty($slug)) {
            show_404();
        }

        // Increase limits for large databases
        @ini_set('memory_limit', '-1');
        @set_time_limit(0);

        $db_info = $this->_get_db_connection($slug);
        if (isset($db_info['error'])) {
            show_error('Database connection error: ' . $db_info['error']);
        }

        $db = $db_info['conn'];

        // Prepare filename
        $filename = 'ccx_db_' . $slug . '_' . date('Y-m-d_H-i-s') . '.sql';

        // Send headers for download
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Start output
        echo "-- Database dump for: {$slug}\n";
        echo "-- Generated: " . date('c') . "\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $schema = $this->_get_db_schema($db, $db_info['prefix']);

        foreach ($schema as $name => $info) {
            $real_table = $info['real_name'];

            // Get CREATE TABLE
            $q = $db->query("SHOW CREATE TABLE `" . $real_table . "`");
            $row = $q->row_array();
            $create_sql = $row['Create Table'] ?? null;
            if ($create_sql) {
                echo "DROP TABLE IF EXISTS `{$real_table}`;\n";
                echo $create_sql . ";\n\n";
            }

            // Dump data in chunks
            $batch = 1000;
            $offset = 0;
            $cols = $info['columns'];
            if (empty($cols))
                continue;
            $cols_list = '`' . implode('`, `', $cols) . '`';

            do {
                $res = $db->query("SELECT $cols_list FROM `{$real_table}` LIMIT {$batch} OFFSET {$offset}");
                $rows = $res->result_array();
                if (empty($rows))
                    break;

                $values = [];
                foreach ($rows as $r) {
                    $vals = [];
                    foreach ($r as $v) {
                        if ($v === null) {
                            $vals[] = 'NULL';
                        } else {
                            $vals[] = "'" . $db->escape_str($v) . "'";
                        }
                    }
                    $values[] = '(' . implode(',', $vals) . ')';
                }

                if (!empty($values)) {
                    echo "INSERT INTO `{$real_table}` ($cols_list) VALUES \n" . implode(",\n", $values) . ";\n\n";
                    // Flush output
                    if (function_exists('ob_flush')) {
                        @ob_flush();
                    }
                    if (function_exists('flush')) {
                        @flush();
                    }
                }

                $offset += $batch;
            } while (count($rows) == $batch);
        }

        echo "SET FOREIGN_KEY_CHECKS=1;\n";

        // Close connection if temporary
        if (is_object($db) && $slug !== 'master') {
            $db->close();
        }

        // Stop further processing
        exit;
    }

    /**
     * Handle uploaded SQL file and restore into the target database (master or tenant)
     * Expects multipart POST with 'backup_file' and 'dest_slug'
     */
    public function restore_action()
    {
        // Only allow POST
        if ($this->input->method(true) !== 'POST') {
            show_404();
        }

        // Simple permission check
        if (!has_permission('settings', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        // Check inputs
        $dest_slug = $this->input->post('dest_slug');
        if (empty($dest_slug) || !isset($_FILES['backup_file'])) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters or file']);
            return;
        }

        // Increase limits
        @ini_set('memory_limit', '-1');
        @set_time_limit(0);

        $file = $_FILES['backup_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File upload error']);
            return;
        }

        $tmp_path = $file['tmp_name'];
        if (!is_uploaded_file($tmp_path) && !file_exists($tmp_path)) {
            echo json_encode(['success' => false, 'message' => 'Uploaded file not found']);
            return;
        }

        // Get DB connection for destination
        $db_info = $this->_get_db_connection($dest_slug);
        if (isset($db_info['error'])) {
            echo json_encode(['success' => false, 'message' => 'DB connection error: ' . $db_info['error']]);
            return;
        }

        $db = $db_info['conn'];

        // Try to open file and execute statements line by line
        $handle = fopen($tmp_path, 'r');
        if ($handle === false) {
            echo json_encode(['success' => false, 'message' => 'Could not open uploaded file']);
            return;
        }

        $errors = [];
        $statement = '';

        // Disable foreign key checks while restoring
        @$db->query('SET FOREIGN_KEY_CHECKS=0');

        while (!feof($handle)) {
            $line = fgets($handle);
            if ($line === false)
                break;

            $trim = trim($line);
            // Skip comments and empty lines
            if ($trim === '' || strpos($trim, '--') === 0 || strpos($trim, '/*') === 0 || strpos($trim, '#') === 0) {
                continue;
            }

            $statement .= $line;

            // Check for end of statement - semicolon at end after trimming
            if (substr(rtrim($trim), -1) === ';') {
                // Execute statement
                try {
                    if (!@$db->query($statement)) {
                        $err = $db->error();
                        $errors[] = isset($err['message']) ? $err['message'] : 'Unknown DB error';
                    }
                } catch (Throwable $e) {
                    $errors[] = $e->getMessage();
                }

                // Reset
                $statement = '';
            }
        }

        fclose($handle);

        // Re-enable foreign key checks
        @$db->query('SET FOREIGN_KEY_CHECKS=1');

        // Close connection if temporary
        if (is_object($db) && $dest_slug !== 'master') {
            $db->close();
        }

        // Remove temp upload file if present (PHP will usually clean it up)
        if (file_exists($tmp_path)) {
            @unlink($tmp_path);
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => 'Restore completed with errors: ' . implode(' | ', $errors)]);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'Database restored successfully']);
        return;
    }
    public function get_tenant_modules_json($slug)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $db_info = $this->_get_db_connection($slug);
        if (isset($db_info['error'])) {
            echo json_encode(['error' => $db_info['error']]);
            return;
        }

        $db = $db_info['conn'];
        $prefix = $db_info['prefix']; // This is usually empty for within the tenant DB connection if we use the DSN properly, but let's check table existence.

        // We need to check if tblmodules exists
        // In DSN connection, prefix might be handled by CI, so we just use 'tblmodules' usually
        // But if we are in a shared DB setup, we might need the prefix.
        // Let's try simple 'tblmodules' first as _get_db_connection should set the prefix in the DB object.

        // Check if table exists
        if (!$db->table_exists('modules')) {
            echo json_encode(['error' => 'Table modules not found in tenant database.']);
            if (is_object($db) && $slug !== 'master')
                $db->close();
            return;
        }

        $modules = $db->get('modules')->result_array();

        if (is_object($db) && $slug !== 'master')
            $db->close();

        echo json_encode($modules);
    }

    public function update_tenant_module_action()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $slug = $this->input->post('slug');
        $id = $this->input->post('id');
        $active = $this->input->post('active');

        if (!$slug || !$id) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            return;
        }

        $db_info = $this->_get_db_connection($slug);
        if (isset($db_info['error'])) {
            echo json_encode(['success' => false, 'message' => $db_info['error']]);
            return;
        }

        $db = $db_info['conn'];

        $data = ['active' => $active];
        $db->where('id', $id);
        if ($db->update('modules', $data)) {
            echo json_encode(['success' => true, 'message' => 'Module updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update module']);
        }

        if (is_object($db) && $slug !== 'master')
            $db->close();
    }
    /**
     * Helper to get table names from a module's install.php file
     */
    private function _get_module_tables($module_name)
    {
        $module_path = APP_MODULES_PATH . $module_name . '/install.php';
        if (!file_exists($module_path)) {
            return [];
        }

        $content = file_get_contents($module_path);
        $tables = [];

        // Regex to find CREATE TABLE statements
        // Matches `db_prefix() . 'tablename'` or `tbltablename`
        // We need to be flexible.
        // Pattern 1: `CREATE TABLE [IF NOT EXISTS] `[prefix]tablename`
        // Pattern 2: `CREATE TABLE [IF NOT EXISTS] `tablename`

        // Let's look for "CREATE TABLE" and then try to extract the table name
        // PHP's regex might be tricky if quotes/concatenation are used differently.
        // A robust way often used in Perfex is scanning for specific db_prefix usage.

        // Standard Perfex pattern: db_prefix() . 'tablename'
        // Pattern: /db_prefix\(\)\s*\.\s*['"]([a-zA-Z0-9_]+)['"]/
        preg_match_all("/db_prefix\(\)\s*\.\s*['\"]([a-zA-Z0-9_]+)['\"]/", $content, $matches);
        if (!empty($matches[1])) {
            $tables = array_merge($tables, $matches[1]);
        }

        // Hardcoded 'tbl...' pattern
        // Pattern: /['`](tbl[a-zA-Z0-9_]+)['`]/
        preg_match_all("/['`](tbl[a-zA-Z0-9_]+)['`]/", $content, $matches_hardcoded);
        if (!empty($matches_hardcoded[1])) {
            // Remove 'tbl' prefix to normalize if we are going to prepend it later, 
            // OR keep it if we treat it as full name.
            // The system uses db_prefix() which defaults to 'tbl'.
            // If the regex caught 'tblsomething', we should check if db_prefix is 'tbl'.
            $tables = array_merge($tables, $matches_hardcoded[1]);
        }

        return array_unique($tables);
    }

    public function export_module_action($slug = '', $module_name = '')
    {
        // Fallback to POST/GET if not valid in arguments (CI3 URI segments)
        if (empty($slug)) {
            $slug = $this->input->post('slug');
            if (empty($slug))
                $slug = $this->input->get('slug');
        }
        if (empty($module_name)) {
            $module_name = $this->input->post('module_name');
            if (empty($module_name))
                $module_name = $this->input->get('module_name');
        }

        if (empty($slug) || empty($module_name)) {
            show_404();
        }

        // Increase limits
        @ini_set('memory_limit', '-1');
        @set_time_limit(0);

        $tables_raw = $this->_get_module_tables($module_name);
        if (empty($tables_raw)) {
            show_error('No tables found in install.php for module: ' . $module_name);
        }

        $db_info = $this->_get_db_connection($slug);
        if (isset($db_info['error'])) {
            show_error('Database connection error: ' . $db_info['error']);
        }

        $db = $db_info['conn'];
        $prefix = $db_info['prefix'];

        // Filter valid tables and map to real names
        $valid_tables = [];
        // We need to check if these tables actually exist in the DB
        // The _get_module_tables returns 'tablename' (without prefix implies db_prefix w/o 'tbl' usually, OR 'tbltablename')
        // We need to construct the real table name in the tenant DB.

        // Tenant Prefix Logic:
        // If tenant is master: db_prefix() . table (or just table if it has tbl)
        // If tenant is not master: tenant_prefix . table 

        // Let's just list all tables in the DB and fuzzy match?
        // Or construct derived names?
        // perfex_saas_tenant_db_prefix($slug) returns 'tenant_slug_'
        // The tables in install.php might be 'appointments' (implied tblappointments) or 'tblappointments'

        $schema = $this->_get_db_schema($db, $prefix); // Keys are normalized names (without prefix) if prefix is passed
        // But _get_db_schema logic:
        // $normalized_name = substr($table_name, strlen($prefix));
        // So if prefix is 'tenant_1_', table is 'tenant_1_tblappointments', normalized is 'tblappointments'

        // Our $tables_raw might contain 'appointments' or 'tblappointments'.
        // We should normalize $tables_raw to 'tbl...' format if possible, or matches schema keys.

        foreach ($tables_raw as $t) {
            // If $t is 'appointments', map to 'tblappointments' ? 
            // Perfex usually assumes 'tbl' prefix.
            $candidate = $t;
            if (strpos($t, 'tbl') !== 0) {
                $candidate = 'tbl' . $t;
            }

            // Check if $candidate exists in schema keys
            // Schema keys from _get_db_schema are stripped of 'tenant_prefix_', so they look like 'tblappointments'
            if (array_key_exists($candidate, $schema)) {
                $valid_tables[] = $schema[$candidate]['real_name'];
            } elseif (array_key_exists($t, $schema)) {
                $valid_tables[] = $schema[$t]['real_name'];
            }
        }

        if (empty($valid_tables)) {
            show_error('None of the module tables were found in the database.');
        }

        // Prepare filename
        $filename = 'ccx_module_' . $module_name . '_' . $slug . '_' . date('Y-m-d_H-i-s') . '.sql';

        // Send headers
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "-- Module Export: {$module_name}\n";
        echo "-- Tenant: {$slug}\n";
        echo "-- Generated: " . date('c') . "\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($valid_tables as $real_table) {
            // No DROP TABLE or CREATE TABLE for Module Data Export (Data Only / Update)

            // Dump data
            $batch = 1000;
            $offset = 0;

            // Get columns to ensure order
            $cols_q = $db->query("SHOW COLUMNS FROM `$real_table`");
            $cols = [];
            foreach ($cols_q->result() as $col)
                $cols[] = $col->Field;
            if (empty($cols))
                continue;

            $cols_list = '`' . implode('`, `', $cols) . '`';

            // Build ON DUPLICATE KEY UPDATE clause
            $update_clause = [];
            foreach ($cols as $col) {
                // Skip primary key updates if we want, but usually we just update everything to match source
                // verification: checked syntax, VALUES(col) is deprecated in newer MySQL (8.0.20+), use NEW.col alias or strictly VALUES function where supported.
                // CI/Perfex environment usually supports standard syntax.
                // Safe standard syntax: `col` = VALUES(`col`)
                $update_clause[] = "`$col` = VALUES(`$col`)";
            }
            $update_sql_suffix = " ON DUPLICATE KEY UPDATE " . implode(', ', $update_clause);

            do {
                $res = $db->query("SELECT $cols_list FROM `{$real_table}` LIMIT {$batch} OFFSET {$offset}");
                $rows = $res->result_array();
                if (empty($rows))
                    break;

                $values = [];
                foreach ($rows as $r) {
                    $vals = [];
                    foreach ($r as $v) {
                        if ($v === null)
                            $vals[] = 'NULL';
                        else
                            $vals[] = "'" . $db->escape_str($v) . "'";
                    }
                    $values[] = '(' . implode(',', $vals) . ')';
                }

                if (!empty($values)) {
                    echo "INSERT INTO `{$real_table}` ($cols_list) VALUES \n" . implode(",\n", $values) . "\n{$update_sql_suffix};\n\n";
                }

                $offset += $batch;
            } while (count($rows) == $batch);
        }

        echo "SET FOREIGN_KEY_CHECKS=1;\n";

        if (is_object($db) && $slug !== 'master') {
            $db->close();
        }
        exit;
    }

    public function import_module_action()
    {
        // Only allow POST
        if ($this->input->method(true) !== 'POST') {
            show_404();
        }

        $dest_slug = $this->input->post('dest_slug');
        // We need module_name for validation
        // In JS we need to append it.
        // If not sent, we can't strict validate against a module, but the user requested "of that select module".
        // Let's grab it from POST.
        $module_name = $this->input->post('module_name');

        if (empty($dest_slug) || !isset($_FILES['backup_file'])) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters or file']);
            return;
        }

        // Increase limits
        @ini_set('memory_limit', '-1');
        @set_time_limit(0);

        $file = $_FILES['backup_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File upload error']);
            return;
        }

        $tmp_path = $file['tmp_name'];
        if (!is_uploaded_file($tmp_path) && !file_exists($tmp_path)) {
            echo json_encode(['success' => false, 'message' => 'Uploaded file not found']);
            return;
        }

        // Validation: Check tables in module vs tables in SQL
        $expected_tables = [];
        if (!empty($module_name)) {
            $expected_tables = $this->_get_module_tables($module_name);
            // Normalize expected to 'tbl' prefix for comparison if not already
            $normalized_expected = [];
            foreach ($expected_tables as $t) {
                if (strpos($t, 'tbl') !== 0)
                    $normalized_expected[] = 'tbl' . $t;
                else
                    $normalized_expected[] = $t;
            }
            $expected_tables = $normalized_expected;
        }

        // Scan SQL file for tables
        $handle = fopen($tmp_path, 'r');
        if ($handle === false) {
            echo json_encode(['success' => false, 'message' => 'Could not open file']);
            return;
        }

        $tables_in_file = [];
        while (!feof($handle)) {
            $line = fgets($handle);
            // Simple regex to find INSERT INTO `table` or CREATE TABLE `table`
            if (preg_match('/(?:INSERT INTO|CREATE TABLE)\s+[`\'"]?([a-zA-Z0-9_]+)[`\'"]?/', $line, $m)) {
                $tables_in_file[] = $m[1];
            }
        }
        fclose($handle);
        $tables_in_file = array_unique($tables_in_file);

        // Verify matches
        // We want to ensure the file actually contains data for the selected module.
        // It might have other tables (if full backup), but the prompt says "cross verify tables of that select module".
        // So checking if ANY of the file's tables match the module's expected tables.

        // Also, we must handle prefix differences. The SQL dump from export uses REAL table names (e.g. tenant_1_tblappointments).
        // But expected_tables are 'tblappointments'.
        // import is usually for the SAME tenant or cross tenant.
        // If I export from Tenant A (tenant_a_tblfoo) and import to Tenant B.
        // Tenant B expects 'tenant_b_tblfoo'.
        // The SQL dump has 'tenant_a_tblfoo'.
        // If we just run the SQL, it will try to create/insert 'tenant_a_tblfoo' in Tenant B's DB.
        // THIS IS THE BUG. "It's giving success but not importing data".
        // Because it created 'tenant_a_tblfoo' in Tenant B's DB, instead of updating 'tenant_b_tblfoo'.
        // CodeIgniter/MySQL just ran the SQL.

        // WE MUST REWRITE THE TABLE NAMES IN THE SQL TO MATCH THE DESTINATION TENANT.

        // 1. Determine Source Prefix?
        // We can guess it from the dump content if we see `tenant_something_tbl...`
        // Or simply Regex replace `\w+_tbl` with `dest_prefix_tbl`?
        // Risky. source might not be tenant qualified if master.

        // Better approach:
        // We know the module tables are `tblfoo`.
        // We scan the SQL. If we see `something_tblfoo`, we map it to `dest_prefix_tblfoo`.

        $dest_db_info = $this->_get_db_connection($dest_slug);
        if (isset($dest_db_info['error'])) {
            echo json_encode(['success' => false, 'message' => $dest_db_info['error']]);
            return;
        }
        $db = $dest_db_info['conn'];
        $dest_prefix = $dest_db_info['prefix'];

        // Map Expected Tables to their Regex Pattern in SQL and their Target Name
        $maps = [];
        $found_any = false;

        foreach ($expected_tables as $tbl) {
            // $tbl is 'tblappointments'
            // We want to match `*tblappointments` in the SQL.
            // And replace with `$dest_prefix . $tbl` (where $tbl usually has tbl prefix? expected_tables usually does or we normalized it)
            // But wait, if $dest_prefix is 'tenant_1_' and $tbl is 'tblappointments', result is 'tenant_1_tblappointments'. Correct.

            // We need to be careful not to match partials like `othertblappointments`.
            // The dump usually uses backticks. `some_prefix_tblappointments` or `tblappointments`.

            // Regex: /`([a-zA-Z0-9_]*)(tblappointments)`/
            // If we find `tenant_a_tblappointments`, we replace with `tenant_b_tblappointments`.
        }

        // Re-read file and process line by line
        $handle = fopen($tmp_path, 'r');
        $temp_out = tempnam(sys_get_temp_dir(), 'ccx_import_');
        $handle_out = fopen($temp_out, 'w');

        $stats = [
            'tables' => [],
            'rows_inserted' => 0
        ];

        $current_statement = '';

        // Helper to strip prefix
        // We simply replace any table matching the module tables (ignoring prefix) with the correct Destination Table Name.

        // We need the list of simple table names (without 'tbl' if possible, or just 'tblfoo')
        // $expected_tables has 'tblfoo'.

        // Logic:
        // For each line, check if it contains a table name that ENDS WITH any of our expected tables.
        // Replace that full table string with `$dest_prefix . $tbl`.

        // Optimization: Build a big Regex?
        // pattern: /`([a-zA-Z0-9_]+)`/ 
        // Callback: if suffix of match equals an expected table, replace.

        while (!feof($handle)) {
            $line = fgets($handle);
            if ($line === false)
                break;

            // Security: Strip DROP TABLE to prevent data loss on existing tables
            // We checks if the line STARTS with DROP TABLE (ignoring whitespace) to avoid false positives in data values
            if (stripos(ltrim($line), 'DROP TABLE') === 0) {
                continue;
            }

            // Replace logic
            // We match everything in backticks
            $line = preg_replace_callback('/`([a-zA-Z0-9_]+)`/', function ($m) use ($expected_tables, $dest_prefix, &$stats, &$found_any) {
                $full_name = $m[1];
                foreach ($expected_tables as $exp) { // $exp = 'tblappointments'
                    // Check if full_name ends with exp
                    // And preceding char is _ or empty?
                    // If full_name is 'tblappointments', it ends with exp.
                    // If 'tenant_1_tblappointments', it ends with exp.

                    if (substr($full_name, -strlen($exp)) === $exp) {
                        $found_any = true;
                        $new_name = $dest_prefix . $exp;

                        // Clean up double prefix if $exp already had the prefix (unlikely but possible)
                        // But we just constructed new_name = prefix . exp.

                        if (!in_array($exp, $stats['tables'])) {
                            $stats['tables'][] = $exp;
                        }
                        return "`$new_name`";
                    }
                }
                return $m[0]; // No change
            }, $line);

            // Count inserts
            if (stripos($line, 'INSERT INTO') !== false) {
                // Rough count: count number of (...), (...) - simple heuristic
                // Or just count 1 for the statement?
                // Usually standard dumps are one INSERT per row or bulk.
                // Let's just count lines with INSERT for now, or match values.
                // Or bulk inserts: `),(` count.
                $stats['rows_inserted'] += substr_count($line, '),(') + 1;
            }

            fwrite($handle_out, $line);
        }

        fclose($handle);
        fclose($handle_out);

        if (!$found_any && !empty($module_name)) {
            unlink($temp_out);
            // Alert verification failed
            $msg = "Validation Failed: The uploaded file does not appear to contain tables for module '{$module_name}'.";
            echo json_encode(['success' => false, 'message' => $msg]);
            return;
        }

        // Now run the processed file
        $db->query('SET FOREIGN_KEY_CHECKS=0');

        $handle_sql = fopen($temp_out, 'r');
        $sql = '';
        $errors = [];

        while (!feof($handle_sql)) {
            $line = fgets($handle_sql);
            if ($line === false)
                break;

            // Fix Collation Compatibility
            $line = str_replace(['utf8mb4_0900_ai_ci', 'utf8mb4_0900_as_cs'], 'utf8mb4_general_ci', $line);

            // Allow retry by ignoring duplicates
            $line = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $line);

            $trim = trim($line);
            if ($trim === '' || strpos($trim, '--') === 0)
                continue;

            $sql .= $line;
            if (substr(rtrim($trim), -1) === ';') {
                if (!$db->query($sql)) {
                    $errors[] = $db->error()['message'];
                }
                $sql = '';
            }
        }
        fclose($handle_sql);
        unlink($temp_out);

        $db->query('SET FOREIGN_KEY_CHECKS=1');

        if (is_object($db) && $dest_slug !== 'master')
            $db->close();

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => 'Errors: ' . implode(', ', $errors)]);
        } else {
            $msg = "Import Successful!<br>";
            $msg .= "Tables updated: " . count($stats['tables']) . "<br>";
            $msg .= "Approx records inserted: " . $stats['rows_inserted'] . "<br><hr>";

            // Preview Data
            $msg .= "<h4>Data Preview:</h4>";
            foreach ($stats['tables'] as $tbl) {
                // Prevent Double Prefix (e.g. celabs_tbl + tblfoo = celabs_tbltblfoo)
                if (substr($dest_prefix, -3) === 'tbl' && substr($tbl, 0, 3) === 'tbl') {
                    $real_table = $dest_prefix . substr($tbl, 3);
                } else {
                    $real_table = $dest_prefix . $tbl;
                }

                $msg .= "<strong>Table: {$real_table}</strong><br>";
                $res = $db->query("SELECT * FROM `{$real_table}` LIMIT 3");
                if ($res) {
                    $rows = $res->result_array();
                    if (!empty($rows)) {
                        $msg .= "<div class='table-responsive'><table class='table table-striped table-bordered table-xs'>";
                        // Header
                        $msg .= "<thead><tr>";
                        foreach (array_keys($rows[0]) as $col) {
                            $msg .= "<th>{$col}</th>";
                        }
                        $msg .= "</tr></thead>";
                        // Body
                        $msg .= "<tbody>";
                        foreach ($rows as $row) {
                            $msg .= "<tr>";
                            foreach ($row as $val) {
                                // Truncate long values
                                $disp = strip_tags((string) $val);
                                if (strlen($disp) > 50)
                                    $disp = substr($disp, 0, 50) . '...';
                                $msg .= "<td>{$disp}</td>";
                            }
                            $msg .= "</tr>";
                        }
                        $msg .= "</tbody></table></div>";
                    } else {
                        $msg .= "<em>No records found.</em><br>";
                    }
                }
                $msg .= "<br>";
            }

            // Post-Import Fix for Tests Master
            if ($module_name === 'tests_master') {
                // 1. Get or Create 'Tests' group in destination
                $groups_tbl = $dest_prefix . 'items_groups';
                $items_tbl = $dest_prefix . 'items';

                $res = $db->query("SELECT id FROM `$groups_tbl` WHERE name = 'Tests'");
                $group_row = $res ? $res->row() : null;

                $tests_group_id = 0;
                if ($group_row) {
                    $tests_group_id = $group_row->id;
                } else {
                    $db->query("INSERT INTO `$groups_tbl` (name) VALUES ('Tests')");
                    $tests_group_id = $db->insert_id();
                }

                if ($tests_group_id) {
                    // 2. Update items to belong to this group if they are linked to templates
                    // or have test-specific flags

                    // Table names
                    $tmpl_word = $dest_prefix . 'tests_word_templates';
                    $tmpl_fixed = $dest_prefix . 'tests_fixed_templates';

                    // Flags: test_method_id > 0, or linked in templates
                    $sql_fix = "UPDATE `$items_tbl` 
                                SET group_id = $tests_group_id 
                                WHERE id IN (SELECT test_id FROM `$tmpl_word`) 
                                   OR id IN (SELECT test_id FROM `$tmpl_fixed`)
                                   OR test_method_id > 0
                                   OR is_blood_sample_required = 1";

                    $db->query($sql_fix);
                    $affected = $db->affected_rows();
                    if ($affected > 0) {
                        $msg .= "<br><strong>Post-Import Fix:</strong> Assigned $affected items to 'Tests' group (ID: $tests_group_id).<br>";
                    }
                }
            }

            echo json_encode(['success' => true, 'message' => $msg]);
        }
    }
}
