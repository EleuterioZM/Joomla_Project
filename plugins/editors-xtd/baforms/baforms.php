<?php
/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;


class PlgButtonBaforms extends JPlugin
{
    public function onDisplay($name)
    {
        $js = "
            function formsSelectForm(id) {
                if ('jInsertEditorText' in window) {
                    jInsertEditorText('[forms ID='+id+']', '".$name."');
                    SqueezeBox.close();
                    jModalClose();
                } else {
                    for (var ind in Joomla.editors.instances) {
                        Joomla.editors.instances[ind].replaceSelection('[forms ID='+id+']', '".$name."');
                        break;
                    }
                    if (window.jQuery) {
                        jQuery(Joomla.currentModal).modal('hide');
                    }
                }
                if (window.Joomla.Modal) {
                    window.parent.Joomla.Modal.getCurrent().close();
                }
            }";
        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);
        $button = new JObject;
        $button->modal = true;
        $button->class = 'btn';
        $button->link = 'index.php?option=com_baforms&amp;view=forms&amp;layout=modal&amp;tmpl=component';
        $button->text = 'Forms';
        $button->name = 'star';
        $button->options = "{handler: 'iframe', size: {x: 740, y: 545}}";
        $button->iconSVG = '<svg width="24" height="24" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path></svg>';

        return $button;
    }
}
