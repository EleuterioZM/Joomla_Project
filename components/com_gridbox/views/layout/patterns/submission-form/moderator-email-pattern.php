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
                                            "><?php echo JText::_('THE_NEW_ITEM_SUBMITTED_ON'); ?>
                                            <a href="<?php echo JUri::root(); ?>" style="
                                            color: #1da6f4;
                                            font-family:Arial,sans-serif;
                                            font-size:21px;
                                            font-weight: bold;
                                            margin:0;
                                            text-decoration: none;
                                            "><?php echo $sitename; ?></a>
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
                                            width: 100%;
                                            ">                                                
                                                <p style="
                                                font-family:Arial,sans-serif;
                                                font-size:16px;
                                                font-weight: bold;
                                                margin:0;
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
                                                <a href="<?php echo JUri::root(); ?>" style="
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
                                                "><?php echo $sitename; ?></a>
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