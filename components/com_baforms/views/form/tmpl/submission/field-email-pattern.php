<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
?>
<div style="max-width: 100%;  width: 550px; margin: 0 auto;">
    <span style="color:#333;font-weight: bold;line-height:28px;font-size:16px;white-space: normal;display:block;">
        <?php echo $field->title; ?>:
    </span>
<?php
if ($field->type == 'signature') {
?>
    <img src="<?php echo JUri::root().$value; ?>">
<?php
} else {
?>
    <span style="color: #999;text-align: left;line-height: 28px;font-size: 16px;word-break: break-word;margin-bottom: 30px;display:block;">
        <?php echo $value; ?>
    </span>
<?php
}
?>
</div>
<?php
if ($field->type == 'poll') {
?>
<div style="max-width: 100%;  width: 550px; margin: 0 auto;">
    <span style="color:#333;font-weight: bold;line-height:28px;font-size:16px;white-space: normal;display:block;"><?php echo JText::_('POLL_RESULTS'); ?></span>
    <div style="color: #999;text-align: left;line-height: 28px;font-size: 16px;word-break: break-word;margin-bottom: 30px;display:block;">
<?php
    $pollResults = (array)$field->results;
    usort($pollResults, function($a, $b){
        if ($a->order == $b->order) {
            return 0;
        }

        return ($a->order < $b->order) ? -1 : 1;
    });
    $lastPoll = end($pollResults);
    foreach ($pollResults as $result) {
?>
        <div style="<?php echo $lastPoll != $result ? 'border-bottom: 1px solid #f3f3f3; ' : ''; ?>padding: 10px 0;">
            <span style="width: 400px; display: inline-block;"><?php echo $result->title; ?></span>
            <span style="width: 100px; display: inline-block;"><?php echo $result->votes.' '.JText::_('VOTES'); ?></span>
            <span style=" font-weight: bold;color: #333;"><?php echo $result->percent; ?>%</span>
        </div>
<?php
    }
?>
    </div>
</div>
<?php
}
?>

<?php
$out = ob_get_contents();
ob_end_clean();