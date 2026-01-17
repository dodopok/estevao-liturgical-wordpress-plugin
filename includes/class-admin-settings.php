<?php
/**
 * Admin Settings for Liturgical Calendar
 *
 * @package Estevao_Liturgical_Calendar
 */

if (!defined('ABSPATH')) {
    exit;
}

class Estevao_Liturgical_Admin {

    /**
     * API Client instance
     */
    private $api_client;

    /**
     * Option group name
     */
    const OPTION_GROUP = 'estevao_liturgical_settings';

    /**
     * Available banner styles
     */
    const BANNER_STYLES = array(
        'simple' => 'Simples',
        'elegant' => 'Elegante/Clássico',
        'modern' => 'Moderno/Glass',
        'compact' => 'Compacto',
    );

    /**
     * Constructor
     *
     * @param Estevao_Liturgical_API_Client $api_client API client instance
     */
    public function __construct($api_client) {
        $this->api_client = $api_client;

        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // AJAX handlers
        add_action('wp_ajax_estevao_liturgical_preview', array($this, 'ajax_preview'));
        add_action('wp_ajax_estevao_liturgical_clear_cache', array($this, 'ajax_clear_cache'));
    }

    /**
     * Add settings page to admin menu
     */
    public function add_settings_page() {
        add_options_page(
            __('Calendário Litúrgico', 'estevao-liturgical-calendar'),
            __('Calendário Litúrgico', 'estevao-liturgical-calendar'),
            'manage_options',
            'estevao-liturgical-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        // Prayer book
        register_setting(self::OPTION_GROUP, 'estevao_liturgical_prayer_book_code', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'loc_2015',
        ));

        // Bible version
        register_setting(self::OPTION_GROUP, 'estevao_liturgical_bible_version', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'nvi',
        ));

        // Banner style
        register_setting(self::OPTION_GROUP, 'estevao_liturgical_banner_style', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'simple',
        ));

        // Banner elements
        register_setting(self::OPTION_GROUP, 'estevao_liturgical_banner_elements', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_banner_elements'),
            'default' => array('title', 'year', 'readings'),
        ));

        // API Settings Section
        add_settings_section(
            'estevao_liturgical_api_section',
            __('Configurações da API', 'estevao-liturgical-calendar'),
            array($this, 'render_api_section_description'),
            'estevao-liturgical-settings'
        );

        add_settings_field(
            'estevao_liturgical_prayer_book_code',
            __('Livro de Oração', 'estevao-liturgical-calendar'),
            array($this, 'render_prayer_book_field'),
            'estevao-liturgical-settings',
            'estevao_liturgical_api_section'
        );

        add_settings_field(
            'estevao_liturgical_bible_version',
            __('Versão da Bíblia', 'estevao-liturgical-calendar'),
            array($this, 'render_bible_version_field'),
            'estevao-liturgical-settings',
            'estevao_liturgical_api_section'
        );

        // Banner Settings Section
        add_settings_section(
            'estevao_liturgical_banner_section',
            __('Configurações do Banner', 'estevao-liturgical-calendar'),
            array($this, 'render_banner_section_description'),
            'estevao-liturgical-settings'
        );

        add_settings_field(
            'estevao_liturgical_banner_style',
            __('Estilo do Banner', 'estevao-liturgical-calendar'),
            array($this, 'render_banner_style_field'),
            'estevao-liturgical-settings',
            'estevao_liturgical_banner_section'
        );

        add_settings_field(
            'estevao_liturgical_banner_elements',
            __('Elementos do Banner', 'estevao-liturgical-calendar'),
            array($this, 'render_banner_elements_field'),
            'estevao-liturgical-settings',
            'estevao_liturgical_banner_section'
        );
    }

    /**
     * Sanitize banner elements
     */
    public function sanitize_banner_elements($input) {
        if (!is_array($input)) {
            return array('title', 'year', 'readings');
        }
        $valid = array('title', 'year', 'readings', 'date');
        return array_intersect($input, $valid);
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('settings_page_estevao-liturgical-settings' !== $hook) {
            return;
        }

        // Enqueue frontend CSS for preview
        wp_enqueue_style(
            'estevao-liturgical-frontend',
            ESTEVAO_LITURGICAL_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            ESTEVAO_LITURGICAL_VERSION
        );

        // Admin specific styles
        wp_enqueue_style(
            'estevao-liturgical-admin',
            ESTEVAO_LITURGICAL_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ESTEVAO_LITURGICAL_VERSION
        );

        // Admin JavaScript
        wp_enqueue_script(
            'estevao-liturgical-admin',
            ESTEVAO_LITURGICAL_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            ESTEVAO_LITURGICAL_VERSION,
            true
        );

        wp_localize_script('estevao-liturgical-admin', 'estevaoLiturgical', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('estevao_liturgical_preview'),
            'strings' => array(
                'loading' => __('Carregando...', 'estevao-liturgical-calendar'),
                'error' => __('Erro ao carregar preview', 'estevao-liturgical-calendar'),
                'cacheCleared' => __('Cache limpo com sucesso!', 'estevao-liturgical-calendar'),
            ),
        ));
    }

    /**
     * AJAX handler for preview
     */
    public function ajax_preview() {
        check_ajax_referer('estevao_liturgical_preview', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $prayer_book = sanitize_text_field($_POST['prayer_book'] ?? 'loc_2015');
        $bible_version = sanitize_text_field($_POST['bible_version'] ?? 'nvi');
        $style = sanitize_text_field($_POST['style'] ?? 'simple');
        $elements = isset($_POST['elements']) ? array_map('sanitize_text_field', $_POST['elements']) : array('title', 'year', 'readings');
        $date_type = sanitize_text_field($_POST['date_type'] ?? 'today');

        // Temporarily set options for preview
        $old_prayer_book = get_option('estevao_liturgical_prayer_book_code');
        $old_bible_version = get_option('estevao_liturgical_bible_version');

        update_option('estevao_liturgical_prayer_book_code', $prayer_book);
        update_option('estevao_liturgical_bible_version', $bible_version);

        // Clear cache to get fresh data
        $this->api_client->clear_cache();

        // Get date
        switch ($date_type) {
            case 'last_sunday':
                $date = $this->api_client->get_last_sunday_date();
                break;
            case 'next_sunday':
                $date = $this->api_client->get_next_sunday_date();
                break;
            default:
                $date = $this->api_client->get_today_date();
        }

        // Get calendar data
        $data = $this->api_client->get_calendar($date);

        // Restore original options
        update_option('estevao_liturgical_prayer_book_code', $old_prayer_book);
        update_option('estevao_liturgical_bible_version', $old_bible_version);

        if (is_wp_error($data)) {
            wp_send_json_error($data->get_error_message());
        }

        // Render banner
        $html = $this->render_banner_preview($data, $style, $elements);

        wp_send_json_success(array(
            'html' => $html,
            'data' => $data,
        ));
    }

    /**
     * AJAX handler for clearing cache
     */
    public function ajax_clear_cache() {
        check_ajax_referer('estevao_liturgical_preview', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $this->api_client->clear_cache();
        wp_send_json_success();
    }

    /**
     * Render banner preview HTML
     */
    private function render_banner_preview($data, $style, $elements) {
        $color = $data['liturgical_color'] ?? 'verde';
        if (!empty($data['celebration']['color'])) {
            $color = $data['celebration']['color'];
        }

        $style_class = 'liturgical-banner-style-' . sanitize_html_class($style);
        $color_class = 'liturgical-banner-' . sanitize_html_class($color);

        // Build title
        $title_parts = array();
        if (!empty($data['celebration']['name'])) {
            $title_parts[] = $data['celebration']['name'];
        }
        if (!empty($data['liturgical_season'])) {
            $title_parts[] = $data['liturgical_season'];
        }
        $title = implode(' - ', $title_parts);
        if (empty($title) && !empty($data['sunday_name'])) {
            $title = $data['sunday_name'];
        }

        // Build readings
        $readings_refs = array();
        $reading_types = array(
            'first_reading' => '1a Leit.',
            'psalm' => 'Sl',
            'second_reading' => '2a Leit.',
            'gospel' => 'Ev.',
        );
        if (!empty($data['readings']) && is_array($data['readings'])) {
            foreach ($reading_types as $key => $label) {
                if (!empty($data['readings'][$key]['reference'])) {
                    $readings_refs[] = $label . ': ' . $data['readings'][$key]['reference'];
                }
            }
        }

        ob_start();
        ?>
        <div class="estevao-liturgical-banner <?php echo esc_attr($style_class . ' ' . $color_class); ?>" data-color="<?php echo esc_attr($color); ?>">
            <?php if (in_array('date', $elements) && !empty($data['date'])): ?>
                <div class="liturgical-banner-date"><?php echo esc_html($data['date']); ?></div>
            <?php endif; ?>

            <?php if (in_array('title', $elements) && !empty($title)): ?>
                <div class="liturgical-banner-title"><?php echo esc_html($title); ?></div>
            <?php endif; ?>

            <?php if (in_array('year', $elements) && !empty($data['liturgical_year'])): ?>
                <div class="liturgical-banner-year">
                    <?php echo esc_html__('Ano Litúrgico', 'estevao-liturgical-calendar') . ' ' . esc_html($data['liturgical_year']); ?>
                </div>
            <?php endif; ?>

            <?php if (in_array('readings', $elements) && !empty($readings_refs)): ?>
                <div class="liturgical-banner-readings">
                    <?php echo esc_html(implode(' | ', $readings_refs)); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render API section description
     */
    public function render_api_section_description() {
        echo '<p>' . esc_html__('Configure as preferências do calendário litúrgico.', 'estevao-liturgical-calendar') . '</p>';
    }

    /**
     * Render banner section description
     */
    public function render_banner_section_description() {
        echo '<p>' . esc_html__('Personalize a aparência do banner litúrgico.', 'estevao-liturgical-calendar') . '</p>';
    }

    /**
     * Render prayer book field
     */
    public function render_prayer_book_field() {
        $current = get_option('estevao_liturgical_prayer_book_code', 'loc_2015');
        $prayer_books = $this->api_client->get_prayer_books();

        if (is_wp_error($prayer_books)) {
            echo '<p class="description" style="color: #d63638;">' . esc_html__('Erro ao carregar livros de oração da API.', 'estevao-liturgical-calendar') . '</p>';
            echo '<input type="text" name="estevao_liturgical_prayer_book_code" value="' . esc_attr($current) . '" class="preview-trigger" />';
            return;
        }

        echo '<select name="estevao_liturgical_prayer_book_code" id="estevao_liturgical_prayer_book_code" class="preview-trigger">';
        foreach ($prayer_books as $book) {
            $selected = selected($current, $book['code'], false);
            $label = $book['name'];
            if (!empty($book['jurisdiction'])) {
                $label .= ' - ' . $book['jurisdiction'];
            }
            echo '<option value="' . esc_attr($book['code']) . '"' . $selected . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('Livro de Oração para coletas e lecionário.', 'estevao-liturgical-calendar') . '</p>';
    }

    /**
     * Render bible version field
     */
    public function render_bible_version_field() {
        $current = get_option('estevao_liturgical_bible_version', 'nvi');
        $versions = $this->api_client->get_bible_versions();

        if (is_wp_error($versions)) {
            echo '<p class="description" style="color: #d63638;">' . esc_html__('Erro ao carregar versões da Bíblia.', 'estevao-liturgical-calendar') . '</p>';
            echo '<input type="text" name="estevao_liturgical_bible_version" value="' . esc_attr($current) . '" class="preview-trigger" />';
            return;
        }

        $pt_versions = array_filter($versions, function($v) { return ($v['language'] ?? '') === 'pt-BR'; });
        $en_versions = array_filter($versions, function($v) { return ($v['language'] ?? '') === 'en'; });

        echo '<select name="estevao_liturgical_bible_version" id="estevao_liturgical_bible_version" class="preview-trigger">';

        if (!empty($pt_versions)) {
            echo '<optgroup label="Português">';
            foreach ($pt_versions as $version) {
                $selected = selected($current, strtolower($version['code']), false);
                echo '<option value="' . esc_attr(strtolower($version['code'])) . '"' . $selected . '>' . esc_html($version['code'] . ' - ' . ($version['full_name'] ?? $version['name'])) . '</option>';
            }
            echo '</optgroup>';
        }

        if (!empty($en_versions)) {
            echo '<optgroup label="Inglês">';
            foreach ($en_versions as $version) {
                $selected = selected($current, strtolower($version['code']), false);
                echo '<option value="' . esc_attr(strtolower($version['code'])) . '"' . $selected . '>' . esc_html($version['code'] . ' - ' . ($version['full_name'] ?? $version['name'])) . '</option>';
            }
            echo '</optgroup>';
        }

        echo '</select>';
        echo '<p class="description">' . esc_html__('Versão da Bíblia para as leituras.', 'estevao-liturgical-calendar') . '</p>';
    }

    /**
     * Render banner style field
     */
    public function render_banner_style_field() {
        $current = get_option('estevao_liturgical_banner_style', 'simple');
        ?>
        <div class="banner-style-selector">
            <?php foreach (self::BANNER_STYLES as $value => $label): ?>
                <label class="banner-style-option <?php echo $current === $value ? 'selected' : ''; ?>">
                    <input type="radio" name="estevao_liturgical_banner_style" value="<?php echo esc_attr($value); ?>" <?php checked($current, $value); ?> class="preview-trigger" />
                    <span class="style-preview style-preview-<?php echo esc_attr($value); ?>"></span>
                    <span class="style-label"><?php echo esc_html($label); ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <p class="description"><?php esc_html_e('Estilo visual padrão do banner. Pode ser sobrescrito com style="..." no shortcode.', 'estevao-liturgical-calendar'); ?></p>
        <?php
    }

    /**
     * Render banner elements field
     */
    public function render_banner_elements_field() {
        $current = get_option('estevao_liturgical_banner_elements', array('title', 'year', 'readings'));
        $elements = array(
            'date' => __('Data', 'estevao-liturgical-calendar'),
            'title' => __('Título (Estação/Celebração)', 'estevao-liturgical-calendar'),
            'year' => __('Ano Litúrgico', 'estevao-liturgical-calendar'),
            'readings' => __('Referências das Leituras', 'estevao-liturgical-calendar'),
        );
        ?>
        <div class="banner-elements-selector">
            <?php foreach ($elements as $value => $label): ?>
                <label class="banner-element-option">
                    <input type="checkbox" name="estevao_liturgical_banner_elements[]" value="<?php echo esc_attr($value); ?>" <?php checked(in_array($value, $current)); ?> class="preview-trigger" />
                    <?php echo esc_html($label); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <p class="description"><?php esc_html_e('Elementos exibidos no banner por padrão. Pode ser sobrescrito com show="..." no shortcode.', 'estevao-liturgical-calendar'); ?></p>
        <?php
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            $this->api_client->clear_cache();
            add_settings_error(
                'estevao_liturgical_messages',
                'estevao_liturgical_message',
                __('Configurações salvas. Cache limpo.', 'estevao-liturgical-calendar'),
                'updated'
            );
        }
        ?>
        <div class="wrap estevao-liturgical-admin">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <?php settings_errors('estevao_liturgical_messages'); ?>

            <div class="estevao-admin-layout">
                <div class="estevao-admin-settings">
                    <form action="options.php" method="post" id="estevao-settings-form">
                        <?php
                        settings_fields(self::OPTION_GROUP);
                        do_settings_sections('estevao-liturgical-settings');
                        submit_button(__('Salvar Configurações', 'estevao-liturgical-calendar'));
                        ?>
                    </form>

                    <div class="estevao-cache-actions">
                        <button type="button" id="clear-cache-btn" class="button button-secondary">
                            <?php esc_html_e('Limpar Cache', 'estevao-liturgical-calendar'); ?>
                        </button>
                        <span id="cache-status"></span>
                    </div>
                </div>

                <div class="estevao-admin-preview">
                    <h2><?php esc_html_e('Preview do Banner', 'estevao-liturgical-calendar'); ?></h2>

                    <div class="preview-date-selector">
                        <label>
                            <input type="radio" name="preview_date" value="today" checked /> <?php esc_html_e('Hoje', 'estevao-liturgical-calendar'); ?>
                        </label>
                        <label>
                            <input type="radio" name="preview_date" value="next_sunday" /> <?php esc_html_e('Próximo Domingo', 'estevao-liturgical-calendar'); ?>
                        </label>
                        <label>
                            <input type="radio" name="preview_date" value="last_sunday" /> <?php esc_html_e('Domingo Anterior', 'estevao-liturgical-calendar'); ?>
                        </label>
                    </div>

                    <div id="banner-preview-container" class="preview-container">
                        <div class="preview-loading"><?php esc_html_e('Carregando preview...', 'estevao-liturgical-calendar'); ?></div>
                    </div>

                    <div class="preview-shortcode">
                        <h4><?php esc_html_e('Shortcode:', 'estevao-liturgical-calendar'); ?></h4>
                        <code id="generated-shortcode">[liturgical_banner]</code>
                        <button type="button" class="button button-small copy-shortcode" title="<?php esc_attr_e('Copiar', 'estevao-liturgical-calendar'); ?>">
                            <?php esc_html_e('Copiar', 'estevao-liturgical-calendar'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <div class="estevao-liturgical-shortcode-examples">
                <h3><?php esc_html_e('Documentação dos Shortcodes', 'estevao-liturgical-calendar'); ?></h3>

                <div class="shortcode-docs-grid">
                    <div class="shortcode-doc">
                        <h4>[liturgical_calendar]</h4>
                        <p><?php esc_html_e('Exibe informações detalhadas do calendário litúrgico.', 'estevao-liturgical-calendar'); ?></p>
                        <table class="shortcode-attrs">
                            <tr><th>date</th><td>today, next_sunday, last_sunday, ou Y-m-d</td></tr>
                            <tr><th>show</th><td>date, day_name, season, color, year, collect, readings, readings_full, celebration</td></tr>
                        </table>
                    </div>

                    <div class="shortcode-doc">
                        <h4>[liturgical_banner]</h4>
                        <p><?php esc_html_e('Exibe um banner centralizado com a cor litúrgica.', 'estevao-liturgical-calendar'); ?></p>
                        <table class="shortcode-attrs">
                            <tr><th>date</th><td>today, next_sunday, last_sunday, ou Y-m-d</td></tr>
                            <tr><th>style</th><td>simple, elegant, modern, compact</td></tr>
                            <tr><th>show</th><td>date, title, year, readings</td></tr>
                        </table>
                    </div>
                </div>

                <h4><?php esc_html_e('Exemplos:', 'estevao-liturgical-calendar'); ?></h4>
                <code>[liturgical_banner date="next_sunday" style="elegant"]</code>
                <code>[liturgical_banner style="modern" show="title,readings"]</code>
                <code>[liturgical_calendar date="today" show="day_name,readings_full"]</code>
            </div>
        </div>
        <?php
    }
}
