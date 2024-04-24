<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$url = 'index.php?option=com_gridbox&view=apps&layout=modal&tmpl=component';
?> 
<link rel="stylesheet" href="components/com_gridbox/assets/css/ba-admin.css" type="text/css"/>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function(){
        var items = document.querySelectorAll('th.page-title a');
        for (var i = 0; i < items.length; i++) {
            items[i].addEventListener('click', function(event){
                event.preventDefault();
                var id = this.dataset.id,
                    url = 'index.php?option=com_gridbox&view=category&tmpl=component&id='+id,
                    str = '<iframe class="iframe jviewport-height70" src="'+url+'"></iframe>',
                    d = window.top.document,
                    q = window.top.jQuery;
                d.querySelector('#gridbox_app_name').value = this.dataset.title;
                d.querySelector('#gridbox_app_id').value = id;
                d.querySelector('#select-app').nextElementSibling.style.display = '';
                d.querySelector('#jform_request_id_name').value = this.dataset.title;
                d.querySelector('#jform_request_id_id').value = 0;
                q('#gridbox_app_modal').modal('hide');
                q('#gridbox-category-modal').each(function(){
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
                url = 'index.php?option=com_gridbox&view=blog&app='+id;
                d.querySelector('#jform_link').value = url;
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
                      <?php foreach ($this->items as $i => $item) : ?>
                      <tr>
                            <th class="page-title">
                                <a href="#" data-id="<?php echo $item->id; ?>" data-title="<?php echo addslashes($item->title); ?>">
                                    <?php echo $item->title; ?>
                                </a>
                            </th>
                            <td><?php echo $item->id; ?></td>
                        </tr>
                      <?php endforeach; ?>
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