<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

?>
<link rel="stylesheet" href="components/com_baforms/assets/css/ba-admin.css" type="text/css"/>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){
    window.print();
});
</script>
<div id="pdf-wrapper" style="background: #ffffff; padding: 25px; pointer-events: none;">
    <div style="display: flex; justify-content: space-between;margin-right: 100%;align-items: center;width: 100%;">
            <h1 style="padding: 25px 0; font: 700 18px/32px 'Roboto', sans-serif;"><?php echo $this->submission->title; ?></h1>
            <br>
            <p style="font-size: 14px;"><?php echo date('Y-m-d', strtotime($this->submission->date_time)); ?></p>
    </div>
    <div class="row-fluid">
        <div style="width: 100%;">
            <table id="submission-data" class="table table-striped">
<?php
        foreach ($this->submission->message as $message) {
            if (!empty($message->message)) {
?>
                <tr>
                    <td style="font-size: 16px;background: transparent;box-shadow: none;border: none;padding-top: 25px;line-height: 32px;padding: 25px 0 0;font-weight: bold;"><?php echo $message->title; ?>:</td>
                </tr>    
                <tr>    
                    <td style="background: transparent; box-shadow: none; border: none;padding: 0; color: #999;">
<?php
                    if ($message->type == 'total') {
                        $object = json_decode($message->message);
                        include(JPATH_COMPONENT.'/views/layout/pdf-total-submission.php');
                    } else if ($message->type == 'signature') {
?>
                        <img src="<?php echo JUri::root().$message->message; ?>">
<?php
                    } else if ($message->type != 'upload') {
                        $text = str_replace(';', '', $message->message);
?>
                        <p><?php echo $text; ?></p>
<?php
                    } else if (!empty($message->message)) {
?>
                        <a target="_blank" href="<?php echo JUri::root().$this->uploaded_path; ?>/baforms/<?php echo $message->message; ?>">
                            View Uploaded File
                        </a>
<?php
                    }
?>
                    </td>
                </tr>
<?php
            }
        }
?>
            </table>
        </div>
    </div>
</div>    