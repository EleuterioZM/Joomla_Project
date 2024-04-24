<script>
document.addEventListener('DOMContentLoaded', function(){
    app.loadMinicolors();
});
</script>
<div id="color-variables-dialog" class="modal hide ba-modal-picker picker-modal-arrow" style="display: none;">
    <div class="modal-header">
        <i class="zmdi zmdi-eyedropper"></i>
    </div>
    <div class="modal-body">
        <div id="color-picker-cell">
            <input type="hidden" data-dismiss="modal">
            <input type="text" class="variables-color-picker">
            <span class="minicolors-opacity-wrapper">
                <input type="number" class="minicolors-opacity" min="0" max="1" step="0.01">
                <span class="ba-tooltip ba-top ba-hide-element"><?php echo JText::_('OPACITY'); ?></span>
            </span>
        </div>
    </div>
</div>