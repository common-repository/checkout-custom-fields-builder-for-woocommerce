(()=>{function e(){var e=jQuery;if("undefined"==typeof wc_country_select_params)return!1;if(e().selectWoo){var t=function(){e("select.country_select:visible, select.state_select:visible").each((function(){var t=e(this),n=e.extend({placeholder:t.attr("data-placeholder")||t.attr("placeholder")||"",label:t.attr("data-label")||null,width:"100%"},{language:{errorLoading:function(){return wc_country_select_params.i18n_searching},inputTooLong:function(e){var t=e.input.length-e.maximum;return 1===t?wc_country_select_params.i18n_input_too_long_1:wc_country_select_params.i18n_input_too_long_n.replace("%qty%",t)},inputTooShort:function(e){var t=e.minimum-e.input.length;return 1===t?wc_country_select_params.i18n_input_too_short_1:wc_country_select_params.i18n_input_too_short_n.replace("%qty%",t)},loadingMore:function(){return wc_country_select_params.i18n_load_more},maximumSelected:function(e){return 1===e.maximum?wc_country_select_params.i18n_selection_too_long_1:wc_country_select_params.i18n_selection_too_long_n.replace("%qty%",e.maximum)},noResults:function(){return wc_country_select_params.i18n_no_matches},searching:function(){return wc_country_select_params.i18n_searching}}});e(this).on("select2:select",(function(){e(this).trigger("focus")})).selectWoo(n)}))};t(),e(document.body).on("country_to_state_changed",(function(){t()})),e(document.body).on("change","#ship-to-different-address input",(function(){t()}))}var n=wc_country_select_params.countries.replace(/&quot;/g,'"'),a=JSON.parse(n),o=".woocommerce-billing-fields,.woocommerce-shipping-fields,.woocommerce-address-fields,.woocommerce-shipping-calculator";e(document.body).on("change refresh","select.country_to_state, input.country_to_state",(function(){var t=e(this).closest(o);t.length||(t=e(this).closest(".form-row").parent());var n,i=e(this).val(),r=t.find("#billing_state, #shipping_state, #calc_shipping_state"),c=r.closest(".form-row"),s=r.attr("name"),l=r.attr("id"),d=r.attr("data-input-classes"),p=r.val(),_=r.attr("placeholder")||r.attr("data-placeholder")||"";if(a[i])if(e.isEmptyObject(a[i]))n=e('<input type="hidden" />').prop("id",l).prop("name",s).prop("placeholder",_).attr("data-input-classes",d).addClass("hidden "+d),c.hide().find(".select2-container").remove(),r.replaceWith(n),e(document.body).trigger("country_to_state_changed",[i,t]);else{var u=a[i],m=e('<option value=""></option>').text(wc_country_select_params.i18n_select_state_text);_||(_=wc_country_select_params.i18n_select_state_text),c.show(),r.is("input")&&(n=e("<select></select>").prop("id",l).prop("name",s).data("placeholder",_).attr("data-input-classes",d).addClass("state_select "+d),r.replaceWith(n),r=t.find("#billing_state, #shipping_state, #calc_shipping_state")),r.empty().append(m),e.each(u,(function(t){var n=e("<option></option>").prop("value",t).text(u[t]);r.append(n)})),r.val(p).trigger("change"),e(document.body).trigger("country_to_state_changed",[i,t])}else r.is('select, input[type="hidden"]')&&(n=e('<input type="text" />').prop("id",l).prop("name",s).prop("placeholder",_).attr("data-input-classes",d).addClass("input-text  "+d),c.show().find(".select2-container").remove(),r.replaceWith(n),e(document.body).trigger("country_to_state_changed",[i,t]));e(document.body).trigger("country_to_state_changing",[i,t])})),e(document.body).on("wc_address_i18n_ready",(function(){e(o).each((function(){var t=e(this).find("#billing_country, #shipping_country, #calc_shipping_country");0!==t.length&&0!==t.val().length&&t.trigger("refresh")}))}))}function t(e,t){t?(e.find("label .optional").remove(),e.addClass("validate-required"),0===e.find("label .required").length&&e.find("label").append('&nbsp;<abbr class="required" title="'+ccfbw_address_i18n_params.i18n_required_text+'">*</abbr>')):(e.find("label .required").remove(),e.removeClass("validate-required woocommerce-invalid woocommerce-invalid-required-field"),0===e.find("label .optional").length&&e.find("label").append('&nbsp;<span class="optional">('+ccfbw_address_i18n_params.i18n_optional_text+")</span>"))}function n(){var e=jQuery,t=".wc_payment_method";e(document).on("change",".wc_payment_methods .input-radio",(function(){var n=e(this).parents(t);n.siblings().removeClass("opened"),n.addClass("opened")})),e(t).length&&e(t).each((function(){var t=e(this);t.find(".input-radio").is(":checked")&&t.addClass("opened")})),e(document).on("updated_checkout",(function(){n()}))}var a='input[name="coupon_code"]',o='button[name="apply_coupon"]',i=".showcoupon",r=".checkout_coupon",c="#ccfbw-form-coupon-message";function s(e){var t=jQuery,n={security:wc_checkout_params.apply_coupon_nonce,coupon_code:t(a,e).val(),billing_email:t('form[name="checkout"]').find('input[name="billing_email"]').val()};t.ajax({type:"POST",url:wc_checkout_params.wc_ajax_url.toString().replace("%%endpoint%%","ccfbw_apply_coupon"),data:n,success:function(o){e.removeClass("processing"),o&&("error"===o.status?(t(c).text(o.message),t(r).addClass("woocommerce-invalid")):(e.slideUp(),t(a,e).val("")),t(document.body).trigger("applied_coupon_in_checkout",[n.coupon_code]),t(document.body).trigger("update_checkout",{update_shipping_method:!1}))},dataType:"json"})}jQuery(window).on("elementor/frontend/init",(function(l){l.target.elementorFrontend.hooks.addAction("frontend/element_ready/ccfbw_form.default",(function(){var l;!function(){if("undefined"==typeof ccfbw_address_i18n_params)return!1;var e=jQuery,n=ccfbw_address_i18n_params.locale.replace(/&quot;/g,'"'),a=JSON.parse(n);e(document.body).on("country_to_state_changing",(function(n,o,i){var r,c=i;r=void 0!==a[o]?a[o]:a.default;var s=c.find("#billing_postcode_field, #shipping_postcode_field"),l=c.find("#billing_city_field, #shipping_city_field"),d=c.find("#billing_state_field, #shipping_state_field");s.attr("data-o_class")||(s.attr("data-o_class",s.attr("class")),l.attr("data-o_class",l.attr("class")),d.attr("data-o_class",d.attr("class")));var p=JSON.parse(ccfbw_address_i18n_params.locale_fields);e.each(p,(function(n,o){var i=c.find(o),s=e.extend(!0,{},a.default[n],r[n]);void 0!==s.label&&i.find("label").html(s.label),void 0!==s.placeholder&&(i.find(":input").attr("placeholder",s.placeholder),i.find(":input").attr("data-placeholder",s.placeholder),i.find(".select2-selection__placeholder").text(s.placeholder)),void 0!==s.placeholder||void 0===s.label||i.find("label").length||(i.find(":input").attr("placeholder",s.label),i.find(":input").attr("data-placeholder",s.label),i.find(".select2-selection__placeholder").text(s.label)),void 0!==s.required?t(i,s.required):t(i,!1),void 0!==s.priority&&i.data("priority",s.priority),"state"!==n&&(void 0!==s.hidden&&!0===s.hidden?i.hide().find(":input").val(""):i.show()),Array.isArray(s.class)&&(i.removeClass("form-row-first form-row-last form-row-wide"),i.addClass(s.class.join(" ")))}))})).trigger("wc_address_i18n_ready")}(),e(),n(),(l=jQuery)(document).on("keydown",a,(function(e){if(13===(e.keyCode||e.charCode)){e.preventDefault();var t=l(this).parents(r);return t.is(".processing")||(t.addClass("processing"),s(t)),!1}})),l(document).on("click keyup",i+", "+a,(function(){l(c).text(""),l(r).removeClass("woocommerce-invalid")})),l(document).on("click",o,(function(){var e=l(this).parents(r);return e.is(".processing")||(e.addClass("processing"),s(e)),!1}))}))}))})();