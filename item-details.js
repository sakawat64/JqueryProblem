"use strict";
if ($('.main-body .page-wrapper').find('#item-details-container').length) {
    var ratingValue = 0;
    var mainPrice = $('#item_price').text().replace(/,/g, '');
    var optionIdentify = [];
    var amount = [];
    var count = 0;
    var tempAmount = 0;
    var multipleSelectPrice = [];
    var multipleSelectPriceType = [];
    var globalOptionBox = [];
    var lastOptionBox = null;
    $(document).ready(function(){

        /* 1. Visualizing things on Hover - See next part for action on click */
        $('#stars li').on('mouseover', function() {
            var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

            // Now highlight all the stars that's not after the current hovered star
            $(this).parent().children('li.star').each(function(e){
                if (e < onStar) {
                    $(this).addClass('hover');
                }
                else {
                    $(this).removeClass('hover');
                }
            });

        }).on('mouseout', function(){
            $(this).parent().children('li.star').each(function(e){
                $(this).removeClass('hover');
            });
        });


        /* 2. Action to perform on click */
        $('#stars li').on('click', function(){
            var onStar = parseInt($(this).data('value'), 10); // The star currently selected
            var stars = $(this).parent().children('li.star');

            for (let i = 0; i < stars.length; i++) {
                $(stars[i]).removeClass('selected');
            }

            for (let i = 0; i < onStar; i++) {
                $(stars[i]).addClass('selected');
            }

            // JUST RESPONSE (Not needed)
            ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);

        });


    });


    $("#reviewFrom").on('submit', function(event) {
        event.preventDefault();
        let rate = ratingValue;
        let comments = $('#comments').val();
        let itemId = $('#item_id').val();
        $.ajax({
            url: SITE_URL + "/review-store",
            data: {
                rating: rate,
                comments: comments,
                item_id : itemId,
                "_token": token
            },
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })
                if (data.status == 1) {
                    Toast.fire({
                        icon: 'success',
                        title: jsLang(data.message)
                    });
                    $('#comments').val(null);
                    $(".star").removeClass("selected");
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: jsLang(data.message)
                    })
                }
            }
        });
    });

    $(".singleCheckBox").on('click', function() {
        // in the handler, 'this' refers to the box clicked on
        var $box = $(this);
        if ($box.is(":checked")) {
            // the name of the box is retrieved using the .attr() method
            // as it is assumed and expected to be immutable
            var group = ".singleCheckBox:checkbox[name='" + $box.attr("name") + "']";
            // the checked state of the group/box on the other hand will change
            // and the current value is retrieved using .prop() method
            $(group).prop("checked", false);
            $box.prop("checked", true);
        } else {
            $box.prop("checked", false);
        }
    });

    $(document).on('change', '.option_price', function() {
        let price, priceType, optionId, optionBox, inputType, reducePrice;
        inputType = typeof ($(this).find(':selected').attr('data-inputType')) != 'undefined' ? $(this).find(':selected').attr('data-inputType') : $(this).attr('data-inputType');
        optionBox = typeof ($(this).find(':selected').attr('data-option')) != 'undefined' ? $(this).find(':selected').attr('data-option') : $(this).attr('data-option');
        if (inputType == 'checkbox' || inputType == 'checkbox_custom' || inputType == 'radio' || inputType == 'radio_custom') {
            if ($(this).prop("checked")) {
                price = typeof ($(this).find(':selected').attr('data-price')) != 'undefined' ? $(this).find(':selected').attr('data-price') : $(this).attr('data-price');
                priceType = typeof ($(this).find(':selected').attr('data-type')) != 'undefined' ? $(this).find(':selected').attr('data-type') : $(this).attr('data-type');
                optionId = typeof ($(this).find(':selected').attr('data-optionId')) != 'undefined' ? $(this).find(':selected').attr('data-optionId') : $(this).attr('data-optionId');
                changePrice(price, priceType, optionBox, optionId, inputType);
            } else {
                reducePrice = null;
                if ($(this).prop("checked", false)) {
                    reducePrice = typeof ($(this).find(':selected').attr('data-price')) != 'undefined' ? $(this).find(':selected').attr('data-price') : $(this).attr('data-price');
                }
                changePrice(price, priceType, optionBox, optionId, inputType, reducePrice);
            }
        } else {
            price = typeof ($(this).find(':selected').attr('data-price')) != 'undefined' ? $(this).find(':selected').attr('data-price') : $(this).attr('data-price');
            priceType = typeof ($(this).find(':selected').attr('data-type')) != 'undefined' ? $(this).find(':selected').attr('data-type') : $(this).attr('data-type');
            optionId = typeof ($(this).find(':selected').attr('data-optionId')) != 'undefined' ? $(this).find(':selected').attr('data-optionId') : $(this).attr('data-optionId');
            changePrice(price, priceType, optionBox, optionId, inputType);
        }
    });

    function changePrice(price, priceType, optionBox, optionId, inputType, reducePrice = null)
    {
        let getData = null;
        if (typeof price == 'undefined' && typeof amount[optionBox] != 'undefined') {
               if (reducePrice != null) {
                   amount[optionBox] -= reducePrice;
                   getData = multipleCalc(".customCheckBox-"+optionBox, inputType);
                   amount[optionBox] = getData['extra'];
                   tempAmount = calculate() + mainPrice;
                   $('#item_price').text(null);
                   $('#item_price').text(getDecimalNumberFormat(tempAmount));
               } else {
                   $('#item_price').text(null);
                   $('#item_price').text(getDecimalNumberFormat(tempAmount - amount[optionBox]));
                   delete amount[optionBox];
               }
        } else {
            price = parseFloat(price);
            mainPrice = parseFloat(mainPrice);
            let extraAmount = 0;
            if (priceType == 'Percent') {
                extraAmount = (mainPrice*price)/100;
            } else {
                extraAmount = price;
            }
            if (inputType == 'checkbox_custom' || inputType == 'multiple_select' || inputType == 'radio_custom') {
                if (inputType == 'checkbox_custom' || inputType == 'radio_custom') {
                     getData = multipleCalc(".customCheckBox-"+optionBox, inputType);
                } else {
                     getData = multipleCalc("#multiple-"+optionBox, inputType);
                }
                amount[optionBox] = getData['extra'];
                tempAmount = calculate() + mainPrice;
            } else {
                amount[optionBox] = extraAmount;
                tempAmount = calculate() + mainPrice;
            }
            $('#item_price').text(null);
            $('#item_price').text(getDecimalNumberFormat(tempAmount));
        }
    }

    function multipleCalc(identify, type)
    {
        let cntPrice = 0, exAm = 0, dtPrice, dtPriceType;
        if (type == 'multiple_select') {
            dtPrice = multipleSelectPrice
            dtPriceType = multipleSelectPriceType
        } else {
            let checkPrice = [];
            let checkPriceType = [];
            $(identify+':checkbox:checked').each(function(i) {
                checkPrice[i] = $(this).attr('data-price');
                checkPriceType[i] = $(this).attr('data-type');
            });
             dtPrice = checkPrice;
             dtPriceType = checkPriceType;
        }

        $.each(dtPrice, function (i, v) {
            if (dtPriceType[i] == 'Percent') {
                exAm += (mainPrice*parseFloat(v))/100;
            } else {
                exAm += parseFloat(v);
            }
            cntPrice += mainPrice;
        })
        return {
            'total' : cntPrice,
            'extra' : exAm
        }
    }

    function calculate()
    {
        let total = 0;
        for (let i = 0; i < amount.length ; i++) {
            if(typeof(amount[i]) != "undefined") {
                total += amount[i];
            }
        }
        return total;
    }

    function removeArrayElement(type, itemId)
    {
        if (type == "relate") {
            delete preVDuplicateRelate[itemId];
        } else if(type == "cross") {
            delete preVDuplicateCross[itemId];
        } else if(type == "up") {
            delete preVDuplicateUp[itemId];
        }
    }




    // select multiple dropdown
    let mulInc = 0;
    $('.multiple_select').each(function () {
        let multiId = $(this).attr('id');
        window.dropdown = function () {
            return {
                options: [],
                selected: [],
                show: false,
                open() { this.show = true },
                close() { this.show = false },
                isOpen() { return this.show === true },
                select(index, event) {

                    if (!this.options[index].selected) {

                        this.options[index].selected = true;
                        this.options[index].element = event.target;
                        this.selected.push(index);

                    } else {
                        this.selected.splice(this.selected.lastIndexOf(index), 1);
                        this.options[index].selected = false
                    }
                },
                remove(index, option) {
                    let i = 0,box;
                    this.options[option].selected = false;
                    this.selected.splice(index, 1);
                    multipleSelectPrice.splice(index, 1);
                    multipleSelectPriceType.splice(index, 1);
                    box = multipleSelectPrice.length == 1 ? globalOptionBox[0] : null;
                    box != null ? lastOptionBox = box : '';
                    globalOptionBox.splice(index, 1);
                    multipleSelectPrice.length == 0 ? changePrice(null, null, lastOptionBox, null, "multiple_select") : null;

                },
                loadOptions() {
                    const options = document.getElementById(multiId).options;
                    for (let i = 0; i < options.length; i++) {
                        this.options.push({
                        value: options[i].value,
                        text: options[i].innerText,
                        price: options[i].getAttribute('data-price'),
                        inputType: options[i].getAttribute('data-inputType'),
                        optionBox: options[i].getAttribute('data-option'),
                        priceType: options[i].getAttribute('data-type'),
                        optionId: options[i].getAttribute('data-optionId'),
                        selected: options[i].getAttribute('selected') != null ? options[i].getAttribute('selected') : false
                    });
                }


                },
                selectedValues() {
                    let i = 0;
                    return this.selected.map((option) => {
                        multipleSelectPrice[i] = this.options[option].price;
                        multipleSelectPriceType[i] = this.options[option].priceType;
                        globalOptionBox[i] = this.options[option].optionBox;
                        changePrice(this.options[option].price, this.options[option].priceType, this.options[option].optionBox, this.options[option].optionId, this.options[option].inputType);
                        i++;
                        return this.options[option].value;
                    })
                }
            }
        }
    });
}




