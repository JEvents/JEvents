<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
$value = ".jeviso_itemcontainer:after {
    content: '';
    display: block;
    clear: both;
}
#jeviso_module .jeviso_item  .jfloat-event {
    max-width: 100%;
    width: 100%;
    box-sizing: border-box;
    border-radius: 3px;
    cursor: pointer;
    border: 1px solid #f0f0f1;
    display: inline-block;
    margin-right: 1%;
    margin-bottom: 10px;
}
#jeviso_module .jeviso_itemcontainer .jeviso_item .jfloat-event:hover {
    box-shadow: 0 0 8px 2px rgba(0, 0, 0, 0.4);
}

#jeviso_module .jeviso_item .jeviso_item_image img {
    max-width: 100%;
    border-radius: 3px;
}
#jeviso_module .jeviso_item .jeviso_item_title {
    padding: 5px;
}
#jeviso_module .jeviso_item .jeviso_item_date {
    padding: 5px;
}
#jeviso_module .jeviso_item .jeviso_item_body {
    word-wrap: break-word;
    padding: 5px;
}
#jeviso_module .jeviso_item .jeviso_item_footer {
    width: 100%;
    max-width: 100%;
}
#jeviso_module .jeviso_item .jeviso_item_footer .jeviso_item_catcolor {
    border-left-width: 10px;
    border-left-style: solid;
    display: inline-block;
    padding-left: 2px;
    text-align: left;
    width: 35%;
}
#jeviso_module .jeviso_item .jeviso_item_footer .jeviso_item_rmlink {
    display: inline-block;
    text-align: right;
    width: 55%;
}
.uk-button-primary > .ev_link_row,
.uk-button-primary > .ev_link_row:hover {
   color:inherit;
}
#jeviso-modal {
cursor:auto;
}
.jeviso_modal_image > img {
  margin: 0 auto;
  display:block;
}

/* Media Queries */
@media (max-width: 762px) {
    #jeviso_module .jeviso_item {
    width: 45.5%;
    }
}
/* Float module specific */
#jeviso_module .jeviso_itemcontainer .jeviso_item .jeviso_item_image img {
    width: 100%;
    max-width: 100%;
    border-top-right-radius: 3px;
    border-top-left-radius: 3px;
}
#jeviso_module .jeviso_itemcontainer .jeviso_item {
}
#jeviso_module .jeviso_itemcontainer .jeviso_item .jeviso_item_image {
    overflow: hidden;
    position: relative;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
}
#jeviso_module .jeviso_itemcontainer .jeviso_item .jeviso_item_image img {
    position: absolute;
    top: -50%;
    bottom: -50%;
    width: 100%;
    margin: auto;
    height: auto;
}
#jeviso_module .jeviso_itemcontainer .jeviso_item.listv {
    width: 100%;
}
#jeviso_module .jeviso_itemcontainer .jeviso_item.listv .noleftpadding {
    padding-left: 0;
}
#jeviso_module .jeviso_itemcontainer .jeviso_item h3.eventtitle {
    font-size: 1.3rem !important;
    font-weight: bold;
    text-overflow: ellipsis;
}
#jeviso_module .jeviso_itemcontainer .jeviso_item h3.eventtitle .eventcategory {
    display: block;
    font-size: 1.0rem !important;
    margin-top: 10px;
    font-weight: normal;
    text-overflow: ellipsis;
}
#jeviso_module .jeviso_itemcontainer .jeviso_item div.startdate {
    text-align: center;
}
.jeviso_modal {
    cursor: initial;
}
.jeviso_modal .uk-modal-dialog {
    width: 800px;
}
.jeviso_modal .uk-modal-dialog .uk-modal-header {
    margin: 0;
}
.jeviso_modal .uk-modal-dialog .uk-button-primary a {
    color: #fff;
}
.jeviso_modal .uk-modal-dialog .eventtime,
.jeviso_modal .uk-modal-dialog .eventdetails,
.jeviso_modal .uk-modal-dialog .calendarlinks {
    padding-bottom: 5px;
    border-bottom: 1px solid #e5e5e5;
    margin-bottom: 5px;
}
.jeviso_modal .uk-modal-dialog .startdate {
    font-size: 1.5rem !important;
    padding-left: 10px;
    text-align: center;
}
.jeviso_modal .uk-modal-dialog .startdate .startmonth {
    height: 40px;
    line-height: 40px;
}
.jeviso_modal .uk-modal-dialog .startdate .startday {
    height: 25px;
    line-height: 25px;
}
.jeviso_modal .uk-modal-dialog .uk-modal-title {
    padding-left: 40px;
    text-overflow: ellipsis;
}
.jeviso_modal .uk-modal-dialog .uk-modal-title a {
    font-size: 1.5rem !important;
    height: 40px;
    line-height: 40px;
}
.jeviso_modal .uk-modal-dialog .uk-modal-title .eventcategory {
    display: block;
    height: 25px;
    line-height: 25px;
    font-size: 1.3rem !important;
    opacity: 0.6;
    text-overflow: ellipsis;
}";

$lines = explode("\n", $value);

for ($i = 0; $i < count($lines) ; $i ++)
{
	$line = $lines[$i];
	if ($i < count($lines) -1)
	{
		echo "'" . addslashes($line) . "' + \n";
	}
	else
	{
		echo "'" . addslashes($line) . "',";
	}
}
