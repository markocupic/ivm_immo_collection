/*
 * This file is part of Contao Ivm Immo Collection.
 *
 * (c) Marko Cupic, april 2019
 * @author Marko Cupic <https://github.com/markocupic/ivm_immo_collection>
 * @license MIT
 *
 *
 *
 * Helper class
 * @type {{getCookie: (function(*): *), setCookie: IvmImmoCollection.setCookie, delCookie: IvmImmoCollection.delCookie, removeItem: (function(*, *): *)}}
 */
var IvmImmoCollection = {

    getCookie: function (name) {
        var cookie = " " + document.cookie;
        var search = " " + name + "=";
        var setStr = null;
        var offset = 0;
        var end = 0;
        if (cookie.length > 0) {
            offset = cookie.indexOf(search);
            if (offset != -1) {
                offset += search.length;
                end = cookie.indexOf(";", offset)
                if (end == -1) {
                    end = cookie.length;
                }
                setStr = cookie.substring(offset, end);
            }
        }
        return setStr;
    },

    setCookie: function (name, value, expires, path, domain, secure) {
        document.cookie = name + "=" + value +
            ((expires) ? "; expires=" + expires : "") +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            ((secure) ? "; secure" : "");
    },

    delCookie: function (name) {
        document.cookie = name + "=" + "; expires=Thu, 01 Jan 1970 00:00:01 GMT";
    },

    removeItem: function (item, array) {
        for (index in array) {
            if (array[index] == item) {
                array.splice(index, 1);
            }
        }
        return array;
    }
}

/**
 * Script zur Ansteuerung der feature und unfeature buttons
 */
$(document).ready(function () {
    jQuery('.ivm-collection-toggle-button').on('click', function (e) {

        e.preventDefault();
        e.stopPropagation();
        var button = $(this);
        var icon = $(button).find('img');
        var iconSRC = $(icon).prop('src');

        // Wohnungs-Id
        var wid = $(button).data('wid');

        // Catch the cookie
        var cookie = IvmImmoCollection.getCookie('ivm-collection');
        var arrCollection = [];
        if (cookie !== null) {
            // Base 64 decode cookie string
            //var strCookie = atob(cookie);
            var strCookie = cookie;

            arrCollection = strCookie.split(',');
        }

        // Toggle featured/unfeatured
        if ($(button).hasClass('featured')) {
            arrCollection = IvmImmoCollection.removeItem(wid, arrCollection);
            $(button).removeClass('featured');
            $(icon).prop('src', iconSRC.replace('featured', 'unfeatured'));
            $(button).attr('aria-checked', 'false');
        } else {
            arrCollection = IvmImmoCollection.removeItem(wid, arrCollection);
            arrCollection.push(wid);
            $(button).addClass('featured');
            $(icon).prop('src', iconSRC.replace('unfeatured', 'featured'));
            $(button).attr('aria-checked', 'true');
        }

        // Set cookie
        var strCollection = arrCollection.join(',');
        strCollection = strCollection.replace(',,', ',');

        // Set expiration
        var d = new Date();
        d.setTime(d.getTime() + (2 * 24 * 60 * 60 * 1000));
        var expires = d.toUTCString();
        // Base64 encode cookie string
        //var strCookie = strCollection != '' ? btoa(strCollection) : '';
        var strCookie = strCollection != '' ? strCollection : '';
        IvmImmoCollection.setCookie('ivm-collection', strCookie, expires);

        // Remove item from DOM in collection list view only data-itemselector property needed on the button element
        if ($(button).data('itemselector') !== '' && !$(this).hasClass('featured')) {
            $(button).closest($(button).data('itemselector')).remove();
        }
        return false;
    });
});







