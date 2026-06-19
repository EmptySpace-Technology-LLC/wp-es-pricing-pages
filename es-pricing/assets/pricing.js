/* ES StageStock Pricing — front-end logic */
(function () {
  'use strict';

  var data     = window.esPricingData || {};
  var plans    = data.plans    || [];
  var period   = 'monthly';
  var discount = 0;

  /* ---- helpers ---- */

  function applyDisc(price) {
    return Math.round(price * (1 - discount) * 100) / 100;
  }

  function fmtDollars(n) {
    var s = n.toFixed(2).split('.');
    return { dollars: s[0], cents: s[1] === '00' ? '' : '.' + s[1] };
  }

  function fmtFull(n) {
    return n === Math.floor(n) ? '$' + n : '$' + n.toFixed(2);
  }

  function priceHtml(plan) {
    if (plan.isFree) {
      return '<div class="es-amount"><sup>$</sup>0</div><div class="es-period">forever</div>';
    }
    if (plan.isEnterprise) {
      return '<div class="es-contact-cta">Contact<br>Us</div>';
    }

    var base      = period === 'monthly' ? plan.monthly : plan.annualPerMonth;
    var discPrice = applyDisc(base);
    var p         = fmtDollars(discPrice);
    var hasDisc   = discount > 0;
    var html      = '';

    if (hasDisc) {
      html += '<div class="es-original">' + fmtFull(base) + '/mo</div>';
    }

    html += '<div class="es-amount"><sup>$</sup>' + p.dollars;
    if (p.cents) html += '<span class="cents">' + p.cents + '</span>';
    html += '</div>';

    if (period === 'monthly') {
      html += '<div class="es-period">/month</div>';
    } else {
      html += '<div class="es-period">/mo &mdash; billed annually</div>';
      var annualDisc = applyDisc(plan.annualTotal);
      if (hasDisc) {
        html += '<div class="es-annual-total">' + fmtFull(annualDisc) + '/year</div>';
        html += '<div class="es-annual-total-orig">' + fmtFull(plan.annualTotal) + '/year</div>';
      } else {
        html += '<div class="es-annual-total">' + fmtFull(plan.annualTotal) + '/year</div>';
      }
    }

    return html;
  }

  /* ---- render ---- */

  function render() {
    var container = document.getElementById('es-cards');
    if (!container) return;
    container.innerHTML = '';

    plans.forEach(function (plan) {
      var classes = 'es-card';
      if (plan.isFree)       classes += ' es-card-free';
      if (plan.isEnterprise) classes += ' es-card-enterprise';

      var features = '<li><strong>' + escHtml(plan.limit) + '</strong></li>' +
        plan.features.map(function (f) {
          return '<li>' + escHtml(f) + '</li>';
        }).join('');

      var card = document.createElement('div');
      card.className = classes;
      card.innerHTML =
        '<div class="es-card-header">' +
          '<div class="es-card-tagline">' + escHtml(plan.tagline) + '</div>' +
          '<div class="es-card-name">'    + escHtml(plan.name)    + '</div>' +
        '</div>' +
        '<div class="es-card-price">' + priceHtml(plan) + '</div>' +
        '<ul class="es-card-features">' + features + '</ul>';

      container.appendChild(card);
    });
  }

  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function updateNote() {
    var sel = document.getElementById('es-who-select');
    if (!sel) return;
    var opt  = sel.options[sel.selectedIndex];
    var note = opt ? (opt.getAttribute('data-note') || '') : '';
    var el   = document.getElementById('es-discount-note');
    var ast  = document.getElementById('es-discount-asterisk');
    if (el) el.textContent = (discount > 0 && note) ? '✔ ' + note : '';
    if (ast) ast.style.display = (discount > 0) ? 'block' : 'none';
  }

  /* ---- events ---- */

  function init() {
    var toggleBtns = document.querySelectorAll('#es-pricing .es-toggle-btn');
    toggleBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        toggleBtns.forEach(function (b) { b.classList.remove('active'); });
        this.classList.add('active');
        period = this.dataset ? this.dataset.period : this.getAttribute('data-period');
        render();
      });
    });

    var select = document.getElementById('es-who-select');
    if (select) {
      select.addEventListener('change', function () {
        discount = parseFloat(this.value) || 0;
        updateNote();
        render();
      });
    }

    /* modal — library chosen in WP Admin → ES Pricing settings */
    var modalLib = (data.modalLibrary === 'fancybox') ? 'fancybox' : 'magnific';

    if (typeof jQuery !== 'undefined') {
      jQuery(function ($) {
        if (modalLib === 'fancybox' && $.fn.fancybox) {
          $('.fancybox-signup').fancybox({ type: 'iframe', width: '90%', height: '85%' });
        } else if ($.fn.magnificPopup) {
          $('.fancybox-signup').magnificPopup({
            type: 'iframe',
            mainClass: 'es-pricing-popup',   /* scoped CSS for 90% × 85vh sizing */
            iframe: {
              markup: '<div class="mfp-iframe-scaler">' +
                        '<div class="mfp-close"></div>' +
                        '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                      '</div>'
            }
          });
        }
      });
    }

    render();
    updateNote();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
