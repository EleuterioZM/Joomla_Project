<?php
/**
* @package   Gridbox
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

ob_start();
?>
<table style="
margin:0 auto;
width:600px;
">
    <tbody>
        <tr>
            <td>
                <table style="
                width: 100%;
                padding: 25px;
                border-collapse: collapse;
                ">
                    <tbody>
                        <tr>
                            <td>
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td>  
                                            <h3 style="
                                            font-family:Arial,sans-serif;
                                            font-size:21px;
                                            line-height: 32px;
                                            font-weight: bold;
                                            margin:0;
                                            padding-top: 50px;
                                            padding-bottom: 25px;
                                            color: #000000;
                                            "><?php echo JText::_('A_NEW_COMMENT_POSTED_ON'); ?>
                                            <a href="<?php echo JUri::root().'index.php/commentID-'.$data->id; ?>" style="
                                            color: #1da6f4;
                                            font-family:Arial,sans-serif;
                                            font-size:21px;
                                            font-weight: bold;
                                            margin:0;
                                            text-decoration: none;
                                            "><?php echo $data->title; ?></a>
                                            </h3>                                           
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr style="
                        border-top: 1px solid #eeeeee;
                        border-right: 1px solid #eeeeee;
                        border-left: 1px solid #eeeeee;
                        ">
                            <td style="
                            padding-top: 25px;
                            padding-right: 25px;
                            padding-left: 25px;
                            ">
                                <table style="
                                width: 100%;
                                ">
                                    <tbody>
                                        <tr >
                                            <td style="
                                            width: 10%;
                                            min-width: 50px;
                                            ">
                                                <img src="<?php echo $avatar; ?>" title="author-avatar" width="50" height="50" style="
                                                height:50px;
                                                outline:none;
                                                display:block;
                                                max-width:100%;
                                                min-width: 50px;
                                                border:none;
                                                border-radius:50px;
                                                ">
                                            </td>
                                            <td style="
                                            width: 90%;
                                            ">                                                
                                                <h4 style="
                                                font-family:Arial,sans-serif;
                                                font-size:16px;
                                                font-weight: bold;
                                                margin:0;
                                                margin-left: 25px;
                                                "><?php echo $data->name; ?></h4>
                                                <h5 style="
                                                font-family:Arial,sans-serif;
                                                font-size:12px;
                                                font-weight:normal;
                                                margin:0; 
                                                color: #999999;
                                                line-height: 24px;
                                                text-decoration: none;
                                                margin-left: 25px;
                                                "><?php echo $date; ?></h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr style="
                        border-right: 1px solid #eeeeee;
                        border-left: 1px solid #eeeeee;
                        ">
                            <td style="
                            padding-right: 25px;
                            padding-left: 25px;
                            ">
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td style="
                                            width: 10%;
                                            min-width: 50px;
                                            ">                                              
                                            </td>
                                            <td style="
                                            width: 90%;
                                            ">
                                                <p style="
                                                font-family:Arial,sans-serif;
                                                font-size:14px;
                                                font-weight:normal;
                                                margin:0;
                                                line-height: 28px;
                                                margin-left: 25px;
                                                "><?php echo $message; ?></p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr style="
                        border-right: 1px solid #eeeeee;
                        border-left: 1px solid #eeeeee;
                        border-bottom: 1px solid #eeeeee;
                        ">
                            <td style="
                            padding-right: 25px;
                            padding-left: 25px;
                            padding-bottom: 25px;
                            ">
                                <table style="
                                width: 100%;
                                ">
                                    <tbody>
                                        <tr>
                                            <td style="
                                            width: 10%;
                                            min-width: 50px;
                                            ">                                              
                                            </td>
                                            <td style="
                                            padding-top:25px;
                                            text-align:center;
                                            width: 90%;
                                            ">
                                                <a href="<?php echo JUri::root().'index.php/commentID-'.$data->id; ?>" style="
                                                white-space: nowrap;
                                                background-color: #1da6f4;
                                                border-radius: 50px;
                                                color: #ffffff;
                                                padding: 15px 35px;
                                                font-family: Arial,sans-serif;
                                                font-size: 14px;
                                                font-weight: bold;
                                                display: inline-block;
                                                margin: 0 auto;
                                                margin-left: 25px;
                                                text-decoration: none;
                                                "><?php echo JText::_('REPLY_TO_THIS_COMMENT'); ?></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<?php
$out = ob_get_contents();
ob_end_clean();