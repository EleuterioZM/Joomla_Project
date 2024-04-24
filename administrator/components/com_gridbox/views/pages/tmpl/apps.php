<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$url = 'index.php?option=com_gridbox&view=pages&layout=apps&tmpl=component';
$input = JFactory::getApplication()->input;
$edit_type = $input->get('edit_type', '', 'string');
?> 
<link rel="stylesheet" href="components/com_gridbox/assets/css/ba-admin.css" type="text/css"/>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function(){
        var items = document.querySelectorAll('th.page-title a');
        for (var i = 0; i < items.length; i++) {
            items[i].addEventListener('click', function(event){
                event.preventDefault();
                var id = this.dataset.id,
                    d = window.top.document,
                    q = window.top.jQuery;
<?php
            if (empty($edit_type)) {
?>
                var url = 'index.php?option=com_gridbox&view=pages&layout=modal&tmpl=component&id='+this.dataset.id,
                    str = '<iframe class="iframe jviewport-height70" src="'+url+'"></iframe>';
                d.querySelector('#gridbox_app_name').value = this.dataset.title;
                d.querySelector('#gridbox_app_id').value = this.dataset.id;
                d.querySelector('#select-app').nextElementSibling.style.display = '';
                d.querySelector('#jform_request_id_name').value = '';
                d.querySelector('#jform_request_id_id').value = '';
                q('#gridbox_app_modal').modal('hide');
                q('#gridbox-page-modal').each(function(){
                    if (this.dataset.iframe) {
                        let div = document.createElement('div');
                        div.innerHTML = this.dataset.iframe;
                        div.querySelector('iframe').src = url;
                        this.dataset.iframe = div.innerHTML;
                        q(this).data('iframe', this.dataset.iframe);
                    }
                    this.dataset.url = url;
                    this.querySelector('.modal-body').innerHTML = str;
                });
<?php
            } else {
?>
                d.querySelector('#jform_request_id_name').value = this.dataset.title;
                d.querySelector('#jform_request_id_id').value = this.dataset.id;
                q('#gridbox-app-modal').modal('hide');
<?php
            }
?>
            });
        }
    });
</script>
<div class="modal-shortcode">
    <form action="<?php echo JRoute::_($url); ?>"
          method="post" name="adminForm" id="adminForm" class="form-inline">
        <fieldset id="modal-filter">
            <input type="text" name="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER') ?>" id="filter_search"
                   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"/>
        <i class="zmdi zmdi-search"></i>           
            <button type="submit" class="ba-btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
        </fieldset>
        <div class="page-table">
            <table class="page-list">
                <thead>
                    <tr>
                        <th><?php echo JText::_('APP'); ?></th>
                        <th><?php echo JText::_('ID'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $i => $item) { ?>
                        <tr>
                            <th class="page-title">
                                <a href="#" data-id="<?php echo $item->id; ?>" data-title="<?php echo addslashes($item->title); ?>">
                                    <?php echo $item->title; ?>
                                </a>
                            </th>
                            <td><?php echo $item->id; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </form>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</div>