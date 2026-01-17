<?php
/**
 * Shortcodes for Liturgical Calendar
 *
 * @package Estevao_Liturgical_Calendar
 */

if (!defined('ABSPATH')) {
    exit;
}

class Estevao_Liturgical_Shortcodes {

    /**
     * API Client instance
     */
    private $api_client;

    /**
     * Available fields for display
     */
    private $available_fields = array(
        'date',
        'day_name',
        'season',
        'color',
        'year',
        'collect',
        'readings',
        'readings_full',
        'celebration',
    );

    /**
     * Constructor
     *
     * @param Estevao_Liturgical_API_Client $api_client API client instance
     */
    public function __construct($api_client) {
        $this->api_client = $api_client;
        add_shortcode('liturgical_calendar', array($this, 'render_shortcode'));
        add_shortcode('liturgical_banner', array($this, 'render_banner_shortcode'));
    }

    /**
     * Render the liturgical_calendar shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'date' => 'today',
            'show' => '',
        ), $atts, 'liturgical_calendar');

        // Get the date
        $date = $this->resolve_date($atts['date']);
        if (is_wp_error($date)) {
            return $this->render_error($date->get_error_message());
        }

        // Get calendar data
        $data = $this->api_client->get_calendar($date);
        if (is_wp_error($data)) {
            return $this->render_error($data->get_error_message());
        }

        // Determine which fields to show
        $fields_to_show = $this->parse_show_attribute($atts['show']);

        // Render output
        return $this->render_calendar($data, $fields_to_show);
    }

    /**
     * Resolve date attribute to actual date
     *
     * @param string $date_attr Date attribute value
     * @return string|WP_Error Date in Y-m-d format or error
     */
    private function resolve_date($date_attr) {
        switch ($date_attr) {
            case 'today':
                return $this->api_client->get_today_date();

            case 'last_sunday':
                return $this->api_client->get_last_sunday_date();

            case 'next_sunday':
                return $this->api_client->get_next_sunday_date();

            default:
                // Check if it's a valid date format
                $parsed = DateTime::createFromFormat('Y-m-d', $date_attr);
                if ($parsed && $parsed->format('Y-m-d') === $date_attr) {
                    return $date_attr;
                }
                return new WP_Error('invalid_date', __('Data inválida. Use: today, last_sunday, next_sunday ou Y-m-d', 'estevao-liturgical-calendar'));
        }
    }

    /**
     * Parse the show attribute into an array of fields
     *
     * @param string $show_attr Show attribute value
     * @return array Array of field names to display
     */
    private function parse_show_attribute($show_attr) {
        if (empty($show_attr)) {
            return $this->available_fields;
        }

        $requested = array_map('trim', explode(',', $show_attr));
        $valid = array_intersect($requested, $this->available_fields);

        return !empty($valid) ? $valid : $this->available_fields;
    }

    /**
     * Render calendar HTML
     *
     * @param array $data Calendar data
     * @param array $fields Fields to display
     * @return string HTML output
     */
    private function render_calendar($data, $fields) {
        $color_class = !empty($data['liturgical_color']) ? 'liturgical-color-' . sanitize_html_class($data['liturgical_color']) : '';

        $output = '<div class="estevao-liturgical-calendar ' . esc_attr($color_class) . '">';

        foreach ($fields as $field) {
            $output .= $this->render_field($field, $data);
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Render a single field
     *
     * @param string $field Field name
     * @param array $data Calendar data
     * @return string HTML output
     */
    private function render_field($field, $data) {
        $output = '';

        switch ($field) {
            case 'date':
                if (!empty($data['date'])) {
                    $output = '<div class="liturgical-date">' . esc_html($data['date']) . '</div>';
                }
                break;

            case 'day_name':
                $name = $this->get_day_name($data);
                if (!empty($name)) {
                    $output = '<div class="liturgical-day-name">' . esc_html($name) . '</div>';
                }
                break;

            case 'season':
                if (!empty($data['liturgical_season'])) {
                    $output = '<div class="liturgical-season">';
                    $output .= '<span class="liturgical-label">' . esc_html__('Estação:', 'estevao-liturgical-calendar') . '</span> ';
                    $output .= '<span class="liturgical-value">' . esc_html($data['liturgical_season']) . '</span>';
                    $output .= '</div>';
                }
                break;

            case 'color':
                if (!empty($data['liturgical_color'])) {
                    $output = '<div class="liturgical-color">';
                    $output .= '<span class="liturgical-color-indicator" data-color="' . esc_attr($data['liturgical_color']) . '"></span>';
                    $output .= '<span class="liturgical-value">' . esc_html(ucfirst($data['liturgical_color'])) . '</span>';
                    $output .= '</div>';
                }
                break;

            case 'year':
                if (!empty($data['liturgical_year'])) {
                    $output = '<div class="liturgical-year">';
                    $output .= '<span class="liturgical-label">' . esc_html__('Ano Litúrgico:', 'estevao-liturgical-calendar') . '</span> ';
                    $output .= '<span class="liturgical-value">' . esc_html($data['liturgical_year']) . '</span>';
                    $output .= '</div>';
                }
                break;

            case 'collect':
                $output = $this->render_collects($data);
                break;

            case 'readings':
                $output = $this->render_readings($data, false);
                break;

            case 'readings_full':
                $output = $this->render_readings($data, true);
                break;

            case 'celebration':
                $output = $this->render_celebration($data);
                break;
        }

        return $output;
    }

    /**
     * Get the day name from data
     *
     * @param array $data Calendar data
     * @return string Day name
     */
    private function get_day_name($data) {
        if (!empty($data['sunday_name'])) {
            return $data['sunday_name'];
        }
        if (!empty($data['celebration']['name'])) {
            return $data['celebration']['name'];
        }
        if (!empty($data['day_of_week'])) {
            return $data['day_of_week'];
        }
        return '';
    }

    /**
     * Render collects
     *
     * @param array $data Calendar data
     * @return string HTML output
     */
    private function render_collects($data) {
        if (empty($data['collect']) || !is_array($data['collect'])) {
            return '';
        }

        $output = '<div class="liturgical-collects">';

        foreach ($data['collect'] as $collect) {
            $output .= '<div class="liturgical-collect">';

            if (!empty($collect['title'])) {
                $output .= '<h4 class="liturgical-collect-title">' . esc_html($collect['title']);
                if (!empty($collect['subtitle'])) {
                    $output .= ' <span class="liturgical-collect-subtitle">(' . esc_html($collect['subtitle']) . ')</span>';
                }
                $output .= '</h4>';
            }

            if (!empty($collect['text'])) {
                $output .= '<div class="liturgical-collect-text">' . wp_kses_post(nl2br($collect['text'])) . '</div>';
            }

            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Render readings
     *
     * @param array $data Calendar data
     * @param bool $full Whether to show full text
     * @return string HTML output
     */
    private function render_readings($data, $full = false) {
        if (empty($data['readings']) || !is_array($data['readings'])) {
            return '';
        }

        $reading_types = array(
            'first_reading' => __('Primeira Leitura', 'estevao-liturgical-calendar'),
            'psalm' => __('Salmo', 'estevao-liturgical-calendar'),
            'second_reading' => __('Segunda Leitura', 'estevao-liturgical-calendar'),
            'gospel' => __('Evangelho', 'estevao-liturgical-calendar'),
        );

        $output = '<div class="liturgical-readings">';
        $output .= '<h4 class="liturgical-readings-title">' . esc_html__('Leituras', 'estevao-liturgical-calendar') . '</h4>';

        foreach ($reading_types as $key => $label) {
            if (empty($data['readings'][$key])) {
                continue;
            }

            $reading = $data['readings'][$key];
            $output .= '<div class="liturgical-reading liturgical-reading-' . esc_attr($key) . '">';
            $output .= '<span class="liturgical-reading-label">' . esc_html($label) . ':</span> ';
            $output .= '<span class="liturgical-reading-reference">' . esc_html($reading['reference'] ?? '') . '</span>';

            if ($full && !empty($reading['content']['verses'])) {
                $output .= '<div class="liturgical-reading-text">';
                foreach ($reading['content']['verses'] as $verse) {
                    $output .= '<span class="liturgical-verse">';
                    $output .= '<sup class="liturgical-verse-number">' . esc_html($verse['number']) . '</sup> ';
                    $output .= esc_html($verse['text']);
                    $output .= '</span> ';
                }
                $output .= '</div>';
            }

            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Render celebration/saint
     *
     * @param array $data Calendar data
     * @return string HTML output
     */
    private function render_celebration($data) {
        if (empty($data['celebration'])) {
            return '';
        }

        $celebration = $data['celebration'];
        $output = '<div class="liturgical-celebration">';

        if (!empty($celebration['name'])) {
            $output .= '<div class="liturgical-celebration-name">' . esc_html($celebration['name']) . '</div>';
        }

        if (!empty($celebration['description'])) {
            $output .= '<div class="liturgical-celebration-description">' . esc_html($celebration['description']) . '</div>';
        }

        if (!empty($celebration['color'])) {
            $output .= '<div class="liturgical-celebration-color">';
            $output .= '<span class="liturgical-color-indicator" data-color="' . esc_attr($celebration['color']) . '"></span>';
            $output .= '<span class="liturgical-value">' . esc_html(ucfirst($celebration['color'])) . '</span>';
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Available banner elements
     */
    private $banner_elements = array('date', 'title', 'year', 'readings');

    /**
     * Available banner styles
     */
    private $banner_styles = array('simple', 'elegant', 'modern', 'compact');

    /**
     * Render the liturgical_banner shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_banner_shortcode($atts) {
        $atts = shortcode_atts(array(
            'date' => 'today',
            'style' => '',
            'show' => '',
        ), $atts, 'liturgical_banner');

        // Get the date
        $date = $this->resolve_date($atts['date']);
        if (is_wp_error($date)) {
            return $this->render_error($date->get_error_message());
        }

        // Get calendar data
        $data = $this->api_client->get_calendar($date);
        if (is_wp_error($data)) {
            return $this->render_error($data->get_error_message());
        }

        // Determine style (shortcode overrides option)
        $style = $atts['style'];
        if (empty($style) || !in_array($style, $this->banner_styles)) {
            $style = get_option('estevao_liturgical_banner_style', 'simple');
        }

        // Determine elements to show (shortcode overrides option)
        $elements = array();
        if (!empty($atts['show'])) {
            $requested = array_map('trim', explode(',', $atts['show']));
            $elements = array_intersect($requested, $this->banner_elements);
        }
        if (empty($elements)) {
            $elements = get_option('estevao_liturgical_banner_elements', array('title', 'year', 'readings'));
        }

        return $this->render_banner($data, $style, $elements);
    }

    /**
     * Render banner HTML
     *
     * @param array $data Calendar data
     * @param string $style Banner style
     * @param array $elements Elements to display
     * @return string HTML output
     */
    private function render_banner($data, $style = 'simple', $elements = array()) {
        if (empty($elements)) {
            $elements = array('title', 'year', 'readings');
        }

        // Determine the color (celebration color takes precedence if exists)
        $color = $data['liturgical_color'] ?? 'verde';
        if (!empty($data['celebration']['color'])) {
            $color = $data['celebration']['color'];
        }

        $style_class = 'liturgical-banner-style-' . sanitize_html_class($style);
        $color_class = 'liturgical-banner-' . sanitize_html_class($color);

        // Build the title: celebration name + season, or just season
        $title_parts = array();
        if (!empty($data['celebration']['name'])) {
            $title_parts[] = $data['celebration']['name'];
        }
        if (!empty($data['liturgical_season'])) {
            $title_parts[] = $data['liturgical_season'];
        }
        $title = implode(' - ', $title_parts);

        // Fallback to sunday_name if no title
        if (empty($title) && !empty($data['sunday_name'])) {
            $title = $data['sunday_name'];
        }

        // Build readings references
        $readings_refs = array();
        $reading_types = array(
            'first_reading' => __('1a Leit.', 'estevao-liturgical-calendar'),
            'psalm' => __('Sl', 'estevao-liturgical-calendar'),
            'second_reading' => __('2a Leit.', 'estevao-liturgical-calendar'),
            'gospel' => __('Ev.', 'estevao-liturgical-calendar'),
        );

        if (!empty($data['readings']) && is_array($data['readings'])) {
            foreach ($reading_types as $key => $label) {
                if (!empty($data['readings'][$key]['reference'])) {
                    $readings_refs[] = $label . ': ' . $data['readings'][$key]['reference'];
                }
            }
        }

        // Build output
        $output = '<div class="estevao-liturgical-banner ' . esc_attr($style_class . ' ' . $color_class) . '" data-color="' . esc_attr($color) . '">';

        // Date
        if (in_array('date', $elements) && !empty($data['date'])) {
            $output .= '<div class="liturgical-banner-date">' . esc_html($data['date']) . '</div>';
        }

        // Title (season/celebration)
        if (in_array('title', $elements) && !empty($title)) {
            $output .= '<div class="liturgical-banner-title">' . esc_html($title) . '</div>';
        }

        // Liturgical year
        if (in_array('year', $elements) && !empty($data['liturgical_year'])) {
            $output .= '<div class="liturgical-banner-year">';
            $output .= esc_html__('Ano Litúrgico', 'estevao-liturgical-calendar') . ' ' . esc_html($data['liturgical_year']);
            $output .= '</div>';
        }

        // Readings references
        if (in_array('readings', $elements) && !empty($readings_refs)) {
            $output .= '<div class="liturgical-banner-readings">';
            $output .= esc_html(implode(' | ', $readings_refs));
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Render error message
     *
     * @param string $message Error message
     * @return string HTML output
     */
    private function render_error($message) {
        return '<div class="estevao-liturgical-error">' . esc_html($message) . '</div>';
    }
}
