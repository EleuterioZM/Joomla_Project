<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

$url = 'index.php?option=com_baforms&view=forms&layout=modal&tmpl=component';
?> 
<link rel="stylesheet" href="<?php echo JUri::root().'components/com_baforms/assets/css/ba-shortcode.css'; ?>" type="text/css"/>
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
                        <th><?php echo JText::_('TITLE'); ?></th>
                        <th><?php echo JText::_('ID'); ?></th>
                    </tr>
                </thead>
                 <tbody>
                      <?php foreach ($this->items as $i => $item) { ?>
                      <tr onclick="window.parent.formsSelectForm(<?php echo $item->id; ?>);">
                            <th class="page-title">
                                <a href="#" onclick="return false;">
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