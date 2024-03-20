(function ($) {
    $(document).ready(function () {

        $('#run-cli-import').on('click', function () {
            jQuery.ajax({
                url: $('#run-cli-import').data('action'),
                type: 'post',
            });

            $(this).remove();
            $('.success-msg-cli').show();
        });

        $('body').on('submit', '#backflow-data-import', function (e) {
            e.preventDefault();
            $('.success-msg').css('display', 'none');
            $('.import-data-status').css('display', 'block');
            $('.section-form-wrap').addClass('loading');
            importData();
        });

        function importData(response) {

            let form = $('#backflow-data-import'),
                formData = new FormData(),
                paged,
                total_count,
                column_index,
                columns_count,
                user_id;

            if (typeof response !== 'undefined') {
                paged = response.hasOwnProperty('paged') ? response.paged : 1;
                total_count = response.hasOwnProperty('total_count') ? response.total_count : false;
                column_index = response.hasOwnProperty('column_index') ? response.column_index : false;
                columns_count = response.hasOwnProperty('columns_count') ? response.columns_count : false;
                user_id = response.hasOwnProperty('user_id') ? response.user_id : false;
            } else {
                paged = 1;
                total_count = false;
                column_index = false;
                columns_count = false;
                user_id = false;
            }

            $.each($(':input', form), function (i, fileds) {
                formData.append($(fileds).attr('name'), $(fileds).val());
            });

            formData.append('csv', $('input[type=file]', form)[0].files[0]);
            formData.append('paged', paged);

            if (total_count) {
                formData.append('total_count', total_count);
            }

            if (column_index) {
                formData.append('column_index', column_index);
            }

            if (user_id) {
                formData.append('user_id', user_id);
            }

            if (columns_count) {
                formData.append('columns_count', columns_count);
            }

            jQuery.ajax({
                url: $('#backflow-data-import').attr('action'),
                type: 'post',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (response) {

                    if (response.status == 'done') {

                        $('.section-form-wrap').removeClass('loading');
                        $('.success-msg').css('display', 'block');
                        $('.import-data-status').css('display', 'none');

                        updResponseMsg(response);
                    } else if (response.paged > 0) {
                        console.log(response);
                        updResponseMsg(response);
                        importData(response);
                    }

                    function updResponseMsg(response) {

                        let column_index = response.hasOwnProperty('column_index') ? response.column_index : false;
                        let columns_count = response.hasOwnProperty('columns_count') ? response.columns_count : false;

                        // custom structure for pricing import.
                        if (column_index && columns_count) {

                            if ($('#import-data-index-' + column_index).length > 0) {

                                let percent = getPercent(response.imported_count, response.total_count);
                                let html_percent = '<strong>' + percent + '</strong> (' + response.imported_count + '/' + response.total_count + ')';
                                $('#import-data-index-' + column_index + ' .cssProgress-bar').css('width', percent);
                                $('#import-data-index-' + column_index + ' .cssProgress-bar-label').html(html_percent);

                            } else {

                                if ($('#import-data-index-' + (column_index - 1)).length > 0) {
                                    let percent = '100%';
                                    let html_percent = '<strong>' + percent + '</strong> (' + response.total_count + '/' + response.total_count + ')';

                                    $('#import-data-index-' + (column_index - 1) + ' .cssProgress-bar').css('width', percent);
                                    $('#import-data-index-' + (column_index - 1) + ' .cssProgress-bar-label').html(html_percent);
                                } else {
                                    $('#import-data').hide();
                                }

                                $(".import-data-status").append(
                                    $('#import-data').clone()
                                        .prop('id', 'import-data-index-' + column_index)
                                        .css('display', 'block')
                                );

                                let html_user_id = 'User ID: <strong>' + response.user_id + '</strong> (' + response.column_index + '/' + response.columns_count + ')';
                                let percent = getPercent(response.imported_count, response.total_count);
                                let html_percent = '<strong>' + percent + '</strong> (' + response.imported_count + '/' + response.total_count + ')';

                                $('#import-data-index-' + column_index).prepend(html_user_id);
                                $('#import-data-index-' + column_index + ' .cssProgress-bar').css('width', percent);
                                $('#import-data-index-' + column_index + ' .cssProgress-bar-label').html(html_percent);
                            }

                            if (response.status == 'done') {
                                $("div[id^='import-data-index']").remove();
                            }

                        } else {
                            // default structure
                            let percent = getPercent(response.imported_count, response.total_count);
                            $('#import-data .cssProgress-bar').css('width', percent);
                            let html_percent = '<strong>' + percent + '</strong> (' + response.imported_count + '/' + response.total_count + ')';
                            $('#import-data .cssProgress-bar-label').html(html_percent);
                        }

                    }
                }
            });

        }

        function getPercent(num, amount) {
            return per(num, amount) + '%';
        }

        function per(num, amount) {
            return parseInt((num / amount) * 100);
        }

    });
})(jQuery);