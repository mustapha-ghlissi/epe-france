// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import React from "react";
import ReactDOM from 'react-dom';
import $ from 'jquery';
import TypeIt from "typeit";
import './common';
import 'pdfmake';
import _ from 'lodash';

import FilterForm from "./React/components/FilterForm";
import '../js/lib/jquery.star-rating-svg';

// any CSS you import will output into a single css file (app.css in this case)
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import '../css/star-rating-svg.css';
import '../css/app.css';


if ($('#filterForm').length > 0) {
    ReactDOM.render(<FilterForm/>, document.getElementById('filterForm'));
}

// ScrollSpy
$(document).on('click', '.epe-categories a', function (event) {
    // Make sure this.hash has a value before overriding default behavior
    if (this.hash !== "") {
        // Prevent default anchor click behavior
        event.preventDefault();
        // Store hash
        var hash = this.hash;
        $('html, body').animate({
            scrollTop: $(hash).offset().top - 250
        }, 800, function () {
        });
    }  // End if
})

// Go to vote page
$(document).on('click', '.form-vote', function () {
    $(this).parent().submit();
})

$(document).ready(function (){
    // DatatTable configuration
    $('.dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
        }
    });
    $("a#btn-back-top").on('click', function (e) {
        e.preventDefault();
        $("html, body").animate({scrollTop: 0}, "slow");
        return false;
    });
    $('.rating').starRating({
        totalStars: 5,
        initialRating: 0,
        emptyColor: 'lightgrey',
        hoverColor: '#FDCC0D',
        activeColor: '#FDCC0D',
        ratedColors: ['#FDCC0D', '#FDCC0D', '#FDCC0D', '#FDCC0D', '#FDCC0D'],
        strokeWidth: 0,
        starShape: 'rounded',
        starSize: 26,
        useGradient: false,
        callback: function (currentRating, $el) {
            let inputs = $el.closest('.evaluation-form').find('input[type="hidden"]'),
                checkboxes = $el.closest('.evaluation-form').find('input[type="checkbox"]'),
                isChecked, isEvaluated;

            $el.parent().next().val(currentRating);

            isChecked = isCheckedBoxes(checkboxes);
            isEvaluated = isEvaluatedInputs(inputs);


            if (isChecked && isEvaluated) {
                $el.closest('.evaluation-form').find('button').removeAttr('disabled');
            } else {
                if (!$el.closest('.evaluation-form').find('button').attr('disabled')) {
                    $el.closest('.evaluation-form').find('button').attr('disabled', 'disabled');
                }
            }

        },
        onHover: function (currentIndex, currentRating, $el) {
            $el.next().text(currentIndex + ' / 5');

            if (currentIndex < 2.5) {
                $el.next().removeClass('badge-success');
                $el.next().removeClass('badge-warning');
                $el.next().addClass('badge-danger');
            } else if (currentIndex < 4) {
                $el.next().removeClass('badge-success');
                $el.next().removeClass('badge-danger');
                $el.next().addClass('badge-warning');
            } else {
                $el.next().removeClass('badge-danger');
                $el.next().removeClass('badge-warning');
                $el.next().addClass('badge-success');
            }
        },
        onLeave: function (currentIndex, currentRating, $el) {
            $el.next().text(currentRating + ' / 5');

            if (currentRating < 2.5) {
                $el.next().removeClass('badge-success');
                $el.next().removeClass('badge-warning');
                $el.next().addClass('badge-danger');
            } else if (currentRating < 4) {
                $el.next().removeClass('badge-success');
                $el.next().removeClass('badge-danger');
                $el.next().addClass('badge-warning');
            } else {
                $el.next().removeClass('badge-danger');
                $el.next().removeClass('badge-warning');
                $el.next().addClass('badge-success');
            }
        }
    })
    $('input[type="checkbox"][id="evaluation"], input[type="checkbox"][id="cgu"]').on('click', function () {

        let inputs = $(this).closest('.evaluation-form').find('input[type="hidden"]'),
            checkboxes = $(this).closest('.evaluation-form').find('input[type="checkbox"]'),
            isChecked, isEvaluated;

        isChecked = isCheckedBoxes(checkboxes);
        isEvaluated = isEvaluatedInputs(inputs);

        if (isChecked && isEvaluated) {
            $(this).closest('.evaluation-form').find('button').removeAttr('disabled');
        } else {
            if (!$(this).closest('.evaluation-form').find('button').attr('disabled')) {
                $(this).closest('.evaluation-form').find('button').attr('disabled', 'disabled');
            }
        }
    })
    // Check if the evaluation form checkboxes are checked or not
    function isCheckedBoxes(boxes) {
        let allChecked = 0;

        boxes.each(function (index, box) {
            if ($(box).is(':checked')) {
                allChecked++;
            }
        })

        return allChecked === boxes.length;
    }
    // Check if the evaluation criteria fields are set or not
    function isEvaluatedInputs(inputs) {
        let mostEvaluated = 0;

        inputs.each(function (index, input) {
            if (parseFloat($(input).val()) > 0) {
                mostEvaluated++;
            }
        })

        return mostEvaluated >= (inputs.length - 1) / 2;
    }
})
