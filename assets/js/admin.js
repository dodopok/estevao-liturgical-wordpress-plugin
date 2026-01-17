(function($) {
    'use strict';

    var previewTimeout = null;
    var isLoading = false;

    /**
     * Initialize admin functionality
     */
    function init() {
        bindEvents();
        loadPreview();
    }

    /**
     * Bind event handlers
     */
    function bindEvents() {
        // Preview triggers (inputs that should update preview)
        $(document).on('change', '.preview-trigger', debounce(loadPreview, 300));
        $(document).on('change', 'input[name="preview_date"]', loadPreview);

        // Style selector visual feedback
        $(document).on('change', 'input[name="estevao_liturgical_banner_style"]', function() {
            $('.banner-style-option').removeClass('selected');
            $(this).closest('.banner-style-option').addClass('selected');
        });

        // Clear cache button
        $('#clear-cache-btn').on('click', clearCache);

        // Copy shortcode button
        $('.copy-shortcode').on('click', copyShortcode);
    }

    /**
     * Load preview via AJAX
     */
    function loadPreview() {
        if (isLoading) return;

        var $container = $('#banner-preview-container');
        var data = getFormData();

        isLoading = true;
        $container.addClass('loading');
        $container.html('<div class="preview-loading">' + estevaoLiturgical.strings.loading + '</div>');

        $.ajax({
            url: estevaoLiturgical.ajaxUrl,
            type: 'POST',
            data: {
                action: 'estevao_liturgical_preview',
                nonce: estevaoLiturgical.nonce,
                prayer_book: data.prayerBook,
                bible_version: data.bibleVersion,
                style: data.style,
                elements: data.elements,
                date_type: data.dateType
            },
            success: function(response) {
                if (response.success) {
                    $container.html(response.data.html);
                    updateGeneratedShortcode(data);
                } else {
                    $container.html('<div class="preview-error">' + (response.data || estevaoLiturgical.strings.error) + '</div>');
                }
            },
            error: function() {
                $container.html('<div class="preview-error">' + estevaoLiturgical.strings.error + '</div>');
            },
            complete: function() {
                isLoading = false;
                $container.removeClass('loading');
            }
        });
    }

    /**
     * Get form data for preview
     */
    function getFormData() {
        var elements = [];
        $('input[name="estevao_liturgical_banner_elements[]"]:checked').each(function() {
            elements.push($(this).val());
        });

        return {
            prayerBook: $('#estevao_liturgical_prayer_book_code').val() || $('input[name="estevao_liturgical_prayer_book_code"]').val(),
            bibleVersion: $('#estevao_liturgical_bible_version').val() || $('input[name="estevao_liturgical_bible_version"]').val(),
            style: $('input[name="estevao_liturgical_banner_style"]:checked').val() || 'simple',
            elements: elements.length > 0 ? elements : ['title', 'year', 'readings'],
            dateType: $('input[name="preview_date"]:checked').val() || 'today'
        };
    }

    /**
     * Update generated shortcode display
     */
    function updateGeneratedShortcode(data) {
        var shortcode = '[liturgical_banner';
        var attrs = [];

        // Add date if not today
        if (data.dateType !== 'today') {
            attrs.push('date="' + data.dateType + '"');
        }

        // Add style if not simple
        if (data.style !== 'simple') {
            attrs.push('style="' + data.style + '"');
        }

        // Add show if not default
        var defaultElements = ['title', 'year', 'readings'];
        var elementsChanged = data.elements.length !== defaultElements.length ||
            !data.elements.every(function(el) { return defaultElements.includes(el); });

        if (elementsChanged) {
            attrs.push('show="' + data.elements.join(',') + '"');
        }

        if (attrs.length > 0) {
            shortcode += ' ' + attrs.join(' ');
        }

        shortcode += ']';

        $('#generated-shortcode').text(shortcode);
    }

    /**
     * Clear cache via AJAX
     */
    function clearCache() {
        var $btn = $('#clear-cache-btn');
        var $status = $('#cache-status');

        $btn.prop('disabled', true);
        $status.text('...');

        $.ajax({
            url: estevaoLiturgical.ajaxUrl,
            type: 'POST',
            data: {
                action: 'estevao_liturgical_clear_cache',
                nonce: estevaoLiturgical.nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.text(estevaoLiturgical.strings.cacheCleared).addClass('success');
                    setTimeout(function() {
                        $status.text('').removeClass('success');
                    }, 3000);
                    // Reload preview with fresh data
                    loadPreview();
                }
            },
            error: function() {
                $status.text('Erro').addClass('error');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    }

    /**
     * Copy shortcode to clipboard
     */
    function copyShortcode() {
        var shortcode = $('#generated-shortcode').text();
        var $btn = $(this);

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(shortcode).then(function() {
                showCopyFeedback($btn);
            });
        } else {
            // Fallback for older browsers
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(shortcode).select();
            document.execCommand('copy');
            $temp.remove();
            showCopyFeedback($btn);
        }
    }

    /**
     * Show copy feedback
     */
    function showCopyFeedback($btn) {
        var originalText = $btn.text();
        $btn.text('Copiado!').addClass('copied');
        setTimeout(function() {
            $btn.text(originalText).removeClass('copied');
        }, 2000);
    }

    /**
     * Debounce function
     */
    function debounce(func, wait) {
        return function() {
            var context = this, args = arguments;
            clearTimeout(previewTimeout);
            previewTimeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    // Initialize on document ready
    $(document).ready(init);

})(jQuery);
