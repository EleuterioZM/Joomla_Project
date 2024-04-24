<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework', true);

?> 
<link rel="stylesheet" href="components/com_baforms/assets/css/ba-admin.css" type="text/css"/>

<div class="modal-shortcode">
    <form action="<?php echo JRoute::_('index.php?option=com_baforms&view=forms&layout=modal&tmpl=component&function=formsSelectForm'); ?>"
          method="post" name="adminForm" id="adminForm" class="form-inline">
        <fieldset id="modal-filter">
    		<input type="text" name="filter_search" placeholder="Enter form name" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>"/>
    		<button type="submit" class="ba-btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>		
        </fieldset>
        <div class="forms-table">
            <table class="forms-list">
                 <tbody>
                      <?php foreach ($this->items as $i => $item) : ?>
                      <tr>
                            <th class="form-title">
                                <a href="javascript:void(0)" onclick="if (window.parent) window.parent.formsSelectForm(<?php echo $item->id; ?>)"><?php echo $item->title; ?></a>
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