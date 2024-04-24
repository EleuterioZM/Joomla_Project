(function () {
  'use strict';

  /**
   * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
   * @license     GNU General Public License version 2 or later; see LICENSE.txt
   */
  /**
   * JavaScript behavior to allow shift select in administrator grids
   */
  var JMultiSelect = /*#__PURE__*/function () {
    function JMultiSelect(container) {
      var _this = this;
      this.tableEl = container;
      this.formEl = container.closest('form');
      this.rowSelector = 'tr[class^="row"]';
      this.boxSelector = 'input[type="checkbox"][name="cid[]"]';
      this.checkallToggle = this.tableEl.querySelector('[name="checkall-toggle"]');
      this.prevRow = null;

      // Use delegation listener, to allow dynamic tables
      this.tableEl.addEventListener('click', function (event) {
        if (!event.target.closest(_this.rowSelector)) {
          return;
        }
        _this.onRowClick(event);
      });
      if (this.checkallToggle) {
        this.checkallToggle.addEventListener('click', function (_ref) {
          var target = _ref.target;
          var isChecked = target.checked;
          _this.getRows().forEach(function (row) {
            _this.changeBg(row, isChecked);
          });
        });
      }
    }
    var _proto = JMultiSelect.prototype;
    _proto.getRows = function getRows() {
      return Array.from(this.tableEl.querySelectorAll(this.rowSelector));
    }

    // Changes the row class depends on selection
    // eslint-disable-next-line class-methods-use-this
    ;
    _proto.changeBg = function changeBg(row, isChecked) {
      row.classList.toggle('row-selected', isChecked);
    }

    // Handle click on a row
    ;
    _proto.onRowClick = function onRowClick(_ref2) {
      var _this2 = this;
      var target = _ref2.target,
        shiftKey = _ref2.shiftKey;
      // Do not interfere with links, buttons, inputs
      if (target.tagName && (target.tagName === 'A' || target.tagName === 'BUTTON' || target.tagName === 'SELECT' || target.tagName === 'TEXTAREA' || target.tagName === 'INPUT' && !target.matches(this.boxSelector))) {
        return;
      }

      // Get clicked row and checkbox in it
      var currentRow = target.closest(this.rowSelector);
      var currentBox = target.matches(this.boxSelector) ? target : currentRow.querySelector(this.boxSelector);
      if (!currentBox) {
        return;
      }
      var isChecked = currentBox !== target ? !currentBox.checked : currentBox.checked;
      if (isChecked !== currentBox.checked) {
        currentBox.checked = isChecked;
        Joomla.isChecked(isChecked, this.formEl);
      }
      this.changeBg(currentRow, isChecked);

      // Select rows in range
      if (shiftKey && this.prevRow) {
        // Prevent text selection
        document.getSelection().removeAllRanges();

        // Re-query all rows, because they may be modified during sort operations
        var rows = this.getRows();
        var idxStart = rows.indexOf(this.prevRow);
        var idxEnd = rows.indexOf(currentRow);

        // Check for more than 2 row selected
        if (idxStart >= 0 && idxEnd >= 0 && Math.abs(idxStart - idxEnd) > 1) {
          var slice = idxStart < idxEnd ? rows.slice(idxStart, idxEnd + 1) : rows.slice(idxEnd, idxStart + 1);
          slice.forEach(function (row) {
            if (row === currentRow) {
              return;
            }
            var rowBox = row.querySelector(_this2.boxSelector);
            if (rowBox && rowBox.checked !== isChecked) {
              rowBox.checked = isChecked;
              _this2.changeBg(row, isChecked);
              Joomla.isChecked(isChecked, _this2.formEl);
            }
          });
        }
      }
      this.prevRow = currentRow;
    };
    return JMultiSelect;
  }();
  var onBoot = function onBoot(container) {
    var selector = '#adminForm';
    var confSelector = window.Joomla ? Joomla.getOptions('js-multiselect', {}).formName : '';
    if (confSelector) {
      var pref = confSelector[0];
      selector = pref !== '.' && pref !== '#' ? "#" + confSelector : confSelector;
    }
    container.querySelectorAll(selector).forEach(function (formElement) {
      if (formElement && !('multiselect' in formElement.dataset)) {
        formElement.dataset.multiselect = '';
        // eslint-disable-next-line no-new
        new JMultiSelect(formElement);
      }
    });
  };
  onBoot(document);
  document.addEventListener('joomla:updated', function (_ref3) {
    var target = _ref3.target;
    return onBoot(target);
  });

})();
