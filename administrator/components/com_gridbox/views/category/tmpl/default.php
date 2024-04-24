<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$function  = $app->input->getCmd('function', 'jSelectPage');
$input = JFactory::getApplication()->input;
$id = $input->get('id', 0, 'int');
?> 
<link rel="stylesheet" href="components/com_gridbox/assets/css/ba-admin.css" type="text/css"/>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function(){
        var items = document.querySelectorAll('th.page-title a');
        for (var i = 0; i < items.length; i++) {
            items[i].addEventListener('click', function(event){
                event.preventDefault();
                window.parent.document.querySelector('#jform_request_id_name').value = this.dataset.title;
                window.parent.document.querySelector('#jform_request_id_id').value = this.dataset.id;
                window.parent.jQuery('#gridbox-category-modal').modal('hide');
            });
        }
    });
</script>
<div class="modal-shortcode">
    <form action="<?php echo JRoute::_('index.php?option=com_gridbox&view=category&tmpl=component&id='.$id); ?>"
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
                        <th><?php echo JText::_('CATEGORY'); ?></th>
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