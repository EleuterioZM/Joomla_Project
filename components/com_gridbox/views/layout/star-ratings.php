<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
$obj->items->{'item-'.$now} = gridboxHelper::getOptions('star-ratings');
?>
<div class="ba-item-star-ratings ba-item" id="item-<?php echo $now; ?>">
    <div itemscope itemtype="http://schema.org/CreativeWorkSeries" >
        <meta itemprop="name" content="">
        <div class="star-ratings-wrapper">
            <div class="stars-wrapper">
                <i class="zmdi zmdi-star active" data-rating="1"></i>
                <i class="zmdi zmdi-star active" data-rating="2"></i>
                <i class="zmdi zmdi-star active" data-rating="3"></i>
                <i class="zmdi zmdi-star active" data-rating="4"></i>
                <i class="zmdi zmdi-star active" data-rating="5"></i>
            </div>
            <div class="info-wrapper" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                <span class="rating-wrapper">
                    <span class="rating-title"><?php echo JText::_('RATING'); ?> </span>
                    <span class="rating-value" itemprop="ratingValue">0.00</span>
                </span>
                <span class="votes-wrapper">
                    (<span class="votes-count" itemprop="reviewCount">0</span>
                    <span class="votes-title"> <?php echo JText::_('VOTES'); ?></span>)
                </span>
            </div>
        </div>
    </div>
    <div class="ba-edit-item">
        <span class="ba-edit-wrapper edit-settings">
            <i class="zmdi zmdi-settings"></i>
            <span class="ba-tooltip tooltip-delay">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </span>
        <div class="ba-buttons-wrapper">
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-edit edit-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("EDIT"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-copy copy-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("COPY_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-globe add-library"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("ADD_TO_LIBRARY"); ?>
                </span>
            </span>
            <span class="ba-edit-wrapper">
                <i class="zmdi zmdi-delete delete-item"></i>
                <span class="ba-tooltip tooltip-delay settings-tooltip">
                    <?php echo JText::_("DELETE_ITEM"); ?>
                </span>
            </span>
            <span class="ba-edit-text">
                <?php echo JText::_("ITEM"); ?>
            </span>
        </div>
    </div>
    <div class="ba-box-model">
        
    </div>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();