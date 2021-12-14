"use strict";
var options = [];
var qty = 1;
var cartIndex = null;
var couponOffer = $('#couponOffer').text().replace(/,/g, '');
var couponDiscountType = $('#couponDiscountType').val();
var couponDiscouintAmount = $('#couponDiscountAmount').val();
if ($('.main-body .page-wrapper').find('#cart-details-container').length) {
    $(".cart-shop").each(function () {
        let shopId = $(this).attr('data-shop_id');
        let index = $(this).attr('data-index');
        let shopClass = ".cart-shop-" + shopId;
        isShopAllItemChecked(shopClass, index, shopId);
    })
    checkingCheckbox();
}
$(document).on('click', '.add-to-cart', function() {
    let itemId = $(this).attr('data-itemId');
     options = $('input[name="option[]"]:checked').map(function(){return $(this).val();}).get().length > 0 ? $('input[name="option[]"]:checked').map(function(){return $(this).val();}).get() : [];
        $('select[name="option[]"] option:selected').each(function() {
            $(this).val() != '' ? options.push($(this).val()) : '';
        });
    ajaxCall("/cart-store", itemId, options, true, 'add');
});

function getSelectedIndex()
{
    let index = [];
    $('input[name="items[]"]:checked').each(function() {
        index.push($(this).val());
    });
    return index;
}

$(document).on('click', '#delete-selected-item', function() {
    let items = getSelectedIndex();
    if (items.length > 0) {
        ajaxCall("/cart-selected-delete", items, options, false, 'selectedRemove');
    }
})

$(document).on('click', '.delete-cart-item', function() {
    cartIndex = $(this).attr('data-index');
    ajaxCall("/cart-delete", null, options, false, 'remove');
})

function deleteShopBox()
{
    $(".shop-box").each(function() {
        let hasItem = 0;
        $(".shop-box .cart-shop").each(function() {
        let shopId = $(this).attr('data-shop_id');
        let shopClass = ".cart-shop-"+shopId;
            $(shopClass).each(function() {
                hasItem++;
            });
        });
        if (hasItem == 0) {
            $(this).remove();
        }
    });
}

$(document).on('click', '.cart-item-qty-inc', function() {
    let itemId = $(this).attr('data-itemId');
    qty = parseFloat($('#cart-item-details-'+itemId+' .cart-item-quantity').text()) + 1;
    $('#cart-item-details-'+itemId+' .cart-item-quantity').text(qty);
})

$(document).on('click', '.cart-item-qty-dec', function() {
    let itemId = $(this).attr('data-itemId');
    if (parseFloat($('#cart-item-details-'+itemId+' .cart-item-quantity').text()) > 1) {
        qty = parseFloat($('#cart-item-details-'+itemId+' .cart-item-quantity').text()) - 1;
        $('#cart-item-details-'+itemId+' .cart-item-quantity').text(qty);
    }
})

$("#cart-select-all").on('click', function() {
    $('input:checkbox').not(this).prop('checked', this.checked);
    checkingCheckbox();
});

$(document).on('click', '.cart-shop', function() {
    let shopId = $(this).attr('data-shop_id');
    let shopClass = ".cart-shop-"+shopId;
    $(shopClass+':checkbox').not(this).prop('checked', this.checked);
    isShopAllChecked();
    checkingCheckbox();
});

function isShopAllChecked()
{
    let flag = true;
    $('.cart-shop').each(function() {
        if ($(this).prop("checked")) {

        } else {
            flag = false;
        }
    })

    if (flag == true) {
        $("#cart-select-all").prop('checked', true);
    } else {
        $("#cart-select-all").prop('checked', false);
    }
}

function checkingCheckbox()
{
    let totalSubPrice = 0;
    $(".cart-shop").each(function() {
        let shopId = $(this).attr('data-shop_id');
        let shopClass = ".cart-shop-"+shopId;
        $(shopClass).each(function() {
            let itemPrice = parseFloat($(this).attr('data-price'));
            let itemQuantity = parseFloat($(this).attr('data-quantity'));
            if ($(this).prop("checked")) {
                totalSubPrice = totalSubPrice + (itemPrice * itemQuantity);
            }
        });
    });
    totalPriceByChecked(totalSubPrice);
}

$(document).on('click', '.cart-item-single', function() {
    let shopId = $(this).attr('data-shop_id');
    let index = $(this).attr('data-index');
    let shopClass = ".cart-shop-"+shopId;
    isShopAllItemChecked(shopClass, index, shopId);
    checkingCheckbox();
});

function isShopAllItemChecked(shopClass, index, shopId)
{
    let flag = true;
    $(shopClass).each(function() {
        if ($(this).prop("checked")) {

        } else {
            flag = false;
        }
    });
    if (flag == true) {
        $('.cart-shop').each(function() {
            if($(this).attr('data-shop_id') == shopId) {
                $(this).prop('checked', true);
            }
        })
    } else {
        $('.cart-shop').each(function() {
            if($(this).attr('data-shop_id') == shopId) {
                $(this).prop('checked', false);
            }
        })
    }
    isShopAllChecked();
}
$(document).on('click', '.cart-page-item-qty-inc', function() {
    cartIndex = $(this).attr('data-index');
    let itemId = $(this).attr('data-itemId');
    let price = $(this).attr('data-price');
    qty = parseFloat($('#cart-item-'+cartIndex+' .cart-item-quantity').text()) + 1;
    ajaxCall("/cart-store", itemId, options, false,'qtyIncrement');
})

$(document).on('click', '.cart-page-item-qty-dec', function() {
    cartIndex = $(this).attr('data-index');
    let itemId = $(this).attr('data-itemId');
    let price = $(this).attr('data-price');
    if (parseFloat($('#cart-item-'+cartIndex+' .cart-item-quantity').text()) > 1) {
        qty = parseFloat($('#cart-item-'+cartIndex+' .cart-item-quantity').text()) - 1;
        ajaxCall("/cart-reduce-qty", itemId, options, false,'qtyDecrement');
    }
})

function ajaxCall(url, itemId, ItemOption, msgShow = false, action = null)
{
    $.ajax({
        url: SITE_URL + url,
        data: {
            item_id: itemId,
            options : ItemOption,
            qty : qty,
            cartIndex : cartIndex,
            "_token": token
        },
        type: 'POST',
        dataType: 'JSON',
        success: function (data) {
            if (msgShow == true) {
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
                    updateCart(data.totalItem, data.totalPrice, data.carts, itemId, action)
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: jsLang(data.message)
                    })
                }
            } else {
                if (data.status == 1) {
                    updateCart(data.totalItem, data.totalPrice, data.carts, itemId, action)
                }
            }
        }
    });
}

function updateCart(totalItem, totalPrice, carts = [], itemId = null, action = null)
{
    let qty;
    let cartHeader = '';
    $('#totalCartItem').text(totalItem);
    $('#totalCartitemPage').text(totalItem);
    if (action == 'add') {
        cartHeader += `<div class="shadow-xl w-full p-4" id="cart-header">`;
        $.each(carts, function (index, value) {
            let optionNames = null;
            let options = null;
            let optionHtml = '';
            if (value['option_name'] != null && value['option'] != null) {
                optionNames = JSON.parse(value['option_name']);
                options = JSON.parse(value['option']);
                if (optionNames != null) {
                    $.each(optionNames, function (i, v) {
                    optionHtml += `
                          <div class="text-gray-400 cart-item-options">
                          ${v + " : " + options[i]}
                          </div>
                         `;
                    });
                }
            }
            cartHeader += `
            <div class="p-2 flex cursor-pointer border-b border-gray-100" id="cart-item-header-${index}">
                <div class="p-2 w-12">
                    <img src="${value['photo']}" alt="img product">
                </div>
                <div class="flex-auto text-sm w-32 p-1">
                    <div class="font-bold">${value['name']}</div>
                    ${optionHtml}
                    <div class="text-gray-400 cart-item-quantity">${value['quantity']} × ${getDecimalNumberFormat(value['price'])}</div>
                </div>
                <div class="flex flex-col w-18 font-medium items-end">
                    <a href="${`javascript:void(0)`}" class="w-4 h-4 mb-6 rounded-full cursor-pointer text-red-700 delete-cart-item" data-index="${index}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100%"
                            height="100%" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-trash-2 ">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path
                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                            </path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </a>
                    <span>${currencySymbol}<span class="cart-item-price">${getDecimalNumberFormat(value['price'] * value['quantity'])}</span></span>
                </div>
            </div>
          `;
        });
        cartHeader += `
             <div class="p-4 justify-center flex">
                    <a href="${ SITE_URL+'/carts' }" class="text-base  undefined  hover:scale-110 focus:outline-none flex justify-center px-4 py-2 rounded font-bold cursor-pointer
                        hover:bg-teal-700 hover:text-teal-100
                        bg-teal-100
                        text-teal-700
                        border duration-200 ease-in-out
                        border-teal-600 transition">${jsLang('Checkout')} ${currencySymbol}<span id="cart-item-total-price">${getDecimalNumberFormat(totalPrice)}</span>
                    </a>
                </div>
            </div>
        `;

        $('#cart-header').replaceWith(cartHeader);
    }
    if (action == 'remove') {
        $('#cart-item-'+cartIndex).remove();
        $('#cart-item-header-'+cartIndex).remove();
        if (carts.length == 0) {
            $('#cart-items').append(`<h3 class="text-xl mt-4 font-bold dark:text-gray-2 text-center" class="cart-empty">${jsLang('Empty!')}</h3>`);
            $('#checkOut').hide();
            $('#selecAllBox').hide();
        }
        totalPriceUpdate(totalPrice);
        checkingCheckbox();
        deleteShopBox();
    }
    if (action == 'selectedRemove') {
        $.each(itemId, function (i, v){
            $('#cart-item-'+v).remove();
            $('#cart-item-header-'+v).remove();
        });
        if (carts.length == 0) {
            $('#cart-items').append(`<h3 class="text-xl mt-4 font-bold dark:text-gray-2 text-center" class="cart-empty">${jsLang('Empty!')}</h3>`);
            $('#checkOut').hide();
            $('#selecAllBox').hide();
        }
        totalPriceUpdate(totalPrice);
        checkingCheckbox();
        deleteShopBox();

    }
    if (action == 'qtyIncrement') {
         qty = parseFloat($('#cart-item-'+cartIndex+' .cart-item-quantity').text()) + 1;
        quantityPriceUpdate(qty, parseFloat(carts[cartIndex]['price']), totalPrice, parseFloat(carts[cartIndex]['discount_amount']), carts[cartIndex]['discount_type'], carts[cartIndex]['actual_price']);
    }
    if (action == 'qtyDecrement') {
         qty = parseFloat($('#cart-item-'+cartIndex+' .cart-item-quantity').text()) - 1;
        quantityPriceUpdate(qty, parseFloat(carts[cartIndex]['price']), totalPrice, parseFloat(carts[cartIndex]['discount_amount']), carts[cartIndex]['discount_type'], carts[cartIndex]['actual_price']);
    }
}

function quantityPriceUpdate(qty, price, totalPrice, discountAmount, discountType, actualPrice)
{
    $('#cart-item-'+cartIndex+' .cart-item-quantity').text(qty);
    $('#cart-item-'+cartIndex+' .cart-item-quantity').text(qty);

    $('#cart-item-'+cartIndex+' .cart-item-single').removeAttr("data-quantity");
    $('#cart-item-'+cartIndex+' .cart-item-single').attr("data-quantity", qty);

    discountType != "Percent" ? $('#discount-amount-'+cartIndex).text(getDecimalNumberFormat(discountAmount * qty)) : '';

    $('#cart-item-header-'+cartIndex+' .cart-item-quantity').text(qty+ " × "+getDecimalNumberFormat(price));
    $('#cart-item-header-'+cartIndex+' .cart-item-price').text(getDecimalNumberFormat(price * qty));
    totalPriceUpdate(totalPrice);
    checkingCheckbox();
}

function totalPriceUpdate(totalPrice)
{
    $('#cart-item-total-price').text(getDecimalNumberFormat(totalPrice));
}

function totalPriceByChecked(totalPrice)
{
    couponOffer = 0;
    $('#couponOffer').text(getDecimalNumberFormat(couponOffer));
    $('#cart-subtotal').text(getDecimalNumberFormat(totalPrice));
    $('#cart-total').text(getDecimalNumberFormat(totalPrice - couponOffer));
    let index = getSelectedIndex();
    ajaxCall("/cart-selected-store", index, options, false, null);
}

$("#checkCoupon").on('click', function(event) {
    let disCountCode = $('#discount_code').val();
    $.ajax({
        url: SITE_URL + "/check-coupon",
        data: {
            discount_code: disCountCode,
            "_token": token
        },
        type: 'POST',
        dataType: 'JSON',
        success: function (data) {
            let msg = `<img src="${SITE_URL}/public/frontend/assets/img/product/congratulation.svg" class="w-10 block pr-2 coupon-img">`;
            if (data.status == 1) {
                 msg += `
                         <div class="text-sm rtl-direction-space coupon-msg">${jsLang('Congrats you are eligible for this coupon in this order.')}
                         </div>`;
                 let totalAm = $('#cart-total').text().replace(/,/g, '');
                 couponOffer = data.data['discount_type'] == 'Percentage' ? (totalAm * parseFloat(data.data['discount_amount'])) / 100 : data.data['discount_amount'];
                couponDiscountType = data.data['discount_type'];
                couponDiscouintAmount = data.data['discount_amount'];
                $('#couponOffer').text(getDecimalNumberFormat(couponOffer));
                $('#cart-total').text(getDecimalNumberFormat(totalAm - couponOffer));
            } else {
                 msg += `
                         <div class="text-sm rtl-direction-space coupon-msg">${jsLang(data.message)}
                         </div>`;
            }
            $('#couponMsg .coupon-msg').remove();
            $('#couponMsg .coupon-img').remove();
            $('#couponMsg').append(msg);
        }
    });
});




