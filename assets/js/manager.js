import $ from 'jquery';
import './common';
import 'bootstrap-datepicker';
import 'bootstrap-datepicker/dist/locales/bootstrap-datepicker.fr.min';
import 'chart.js';
import './data-tables';

import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css';
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css';
import 'chart.js/dist/Chart.min.css';
import '../css/admin.css';
import axios from "axios";


$(document).ready(function () {
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    $('.date-picker').datepicker({
        language: 'fr',
        autoclose: true,
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    });

    // Configure select2
    $('select').select2({
        language: 'fr'
    });

    $('#board_minute_category').select2({
        language: 'fr',
        placeholder: 'Choisissez une catégorie'
    });


    // Add video
    $('#btnAddVideo').on('click', function (e) {
        let videos = $('.elected-member-videos'),
            size = videos.children('.elected-member-video').length,
            videoItem = `
            <div class="elected-member-video">
                <div class="form-group">
                    <label for="videoLink${size}">
                        Video
                    </label>
                    <textarea name="videoLink[]" id="videoLink${size}" cols="30" rows="9"
                                                          class="form-control" placeholder="Coller ici le code du video" required></textarea>
                </div>
                <div class="form-group">
                    <label for="videoDescription${size}">
                        Description
                    </label>
                    <textarea name="videoDescription[]" id="videoDescription${size}" cols="30" rows="5"
                              class="form-control"></textarea>
                </div>
            </div>
        `;
        e.preventDefault();
        videos.append(videoItem);
        size++;

        if (size > 1) {
            $('#btnRemoveVideo').removeClass('d-none');
        }
    });
    // Remove video
    $('#btnRemoveVideo').on('click', function (e) {
        let videos = $('.elected-member-videos'),
            size = videos.children('.elected-member-video').length;

        e.preventDefault();
        size--;

        videos.children('.elected-member-video').last().remove();

        if (size === 1) {
            $('#btnRemoveVideo').addClass('d-none');
        }
    })


    // Add timeline
    $('#btnAddTimeline').on('click', function (e) {
        let timelines = $('.elected-member-timelines'),
            size = timelines.children('.elected-member-timeline').length,
            timelineItem = `
            <div class="elected-member-timeline">
                <div class="form-group">
                    <label for="timelineSourceCode${size}">
                        Code source de fil d'actualité
                    </label>
                    <textarea name="timelineSourceCode[]" id="timelineSourceCode${size}" cols="30" rows="9"
                              class="form-control" required></textarea>
                </div>
            </div>
        `;
        e.preventDefault();
        timelines.append(timelineItem);
        size++;

        if (size > 1) {
            $('#btnRemoveTimeline').removeClass('d-none');
        }
    });

    // Add time line
    $('#btnRemoveTimeline').on('click', function (e) {
        let timelines = $('.elected-member-timelines'),
            size = timelines.children('.elected-member-timeline').length;

        e.preventDefault();
        size--;

        timelines.children('.elected-member-timeline').last().remove();

        if (size === 1) {
            $('#btnRemoveTimeline').addClass('d-none');
        }
    })

    $('form').on('submit', function () {
        $(this).children('button[type="submit"]').attr('disabled', 'disabled');
    })

    $('input[type="file"]').on('change', function (e) {
        let fileName = e.target.files[0].name, id = $(this).attr('id');

        $('label[for="' + id + '"]').text(fileName);
    })

    $('input[type="file"][multiple="multiple"]').on('change', function (e) {
        let files = e.target.files, id = $(this).attr('id'), fileName = '';

        for (let i = 0; i < files.length; i++) {
            fileName += files[i].name;
            if(i < files.length - 1) {
                fileName += ', ';
            }
        }

        $('label[for="' + id + '"]').text(fileName);
    })


    $('#board_minute_category').on("change", function () {
        let val = $(this).val(), url = '/manager/' + val;

        if($('#board_minute_target').attr('disabled')) {
            $('#board_minute_target').removeAttr('disabled');
        }

        $('#board_minute_target').val(null).trigger('change');

        $('#board_minute_target').select2({
            language: 'fr',
            ajax: {
                url,
                dataType: 'json',
                type: 'GET',
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            if(item.codeInsee) {
                                return {
                                    text: item.codeInsee + ' - ' + item.name,
                                    id: item.id
                                }
                            }
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                }
            },
            templateSelection: function (data, container) {
                // Add custom attributes to the <option> tag for the selected option
                let text = data.text, codeInsee;

                if(val === 'commune') {
                    codeInsee = text.substring(0, text.indexOf('-'));
                    $('#board_minute_targetCode').val(codeInsee);
                }
                else {
                    $('#board_minute_targetCode').val('');
                }

                return text;
            }
        });
    });
})