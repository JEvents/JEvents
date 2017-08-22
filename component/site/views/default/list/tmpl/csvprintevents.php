<?php

defined('_JEXEC') or die('Restricted access');

$jinput = Jfactory::getApplication()->input;

if ($jinput->getInt("limit", 0) < 1000)
{
	$uri = JUri::getInstance();
	$uri->setVar("limit", 99999);
	global $mainframe;
	$url = $uri->toString();
	JFactory::getApplication()->redirect($url);
	//echo $url;
	exit();
}
ob_end_clean();

$data = $this->data;

//var_dump($data);exit();

$num_events = count($data['rows']);

$rows = array();

$fields = array();

$compparams = JComponentHelper::getParams("com_jevents");
$infields = explode("||", $compparams->get("columns", "TITLE_LINK|Title Link|Title"));
$cols = array();
$titles = array();

foreach ($infields as $infield)
{
	$parts = explode("|", $infield);
	$cols[] = $parts[0];
	$titles[] = $parts[2];
        $fields[$parts[2]] = $parts[0];
}

$rows[] = array_keys($fields);


$template = "";
foreach ($cols as $col)
{
    if (strlen($template)>0){
        $template .= "##@@##";
    }
    $template .= "{{xx:$col}}";
}
if ($num_events > 0)
{
        for ($r = 0; $r < $num_events; $r++)
        {
            ob_start();
            $this->loadedFromTemplate('icalevent.list_row', $data['rows'][$r], 0, $template);
            $rowdata = ob_get_clean();
	        if ($compparams->get("csvexportfiler", 0) == 1)
	        {
		        $rows[] = explode("##@@##", strip_tags($rowdata));

	        } elseif($compparams->get("csvexportfiler", 0) == 2) {

		        $rows[] = explode("##@@##", htmlentities($rowdata));

	        } else {
		        $rows[] = explode("##@@##", $rowdata);
	        }
        }
}

$document = JFactory::getDocument();
$document->setMimeEncoding("text/csv");


$data = exportAsCSV($rows);

// Finally, generate a file
$size = strlen($data);

@ob_end_clean();

@ini_set("zlib.output_compression", "Off");
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: private");
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment;filename=extract_" . date("Y_m_d") . ".csv");
header("Accept-Ranges: bytes");
header("Content-Length: $size");
// force UTF-8 BOM headers in file - see http://stackoverflow.com/questions/5368150/php-header-excel-and-utf-8
echo pack('CCC', 0xef, 0xbb, 0xbf);
echo $data;
exit();


outputCSV($rows);

function outputCSV($data)
{
	ob_start();
	$outstream = fopen("php://output", 'w');

	function __outputCSV(&$row, $key, $filehandler)
	{
		if (is_object($row))
		{
			$data = array();
			global $fields;
			foreach ($fields as $key => $field)
			{
                                if (isset($row->$field))
				{
					$data[$key] = $row->$field;
				}				
				if (is_array($data[$key]))
				{
					$data[$key] = implode(", ", $data[$key]);
				}				
			}
		}
		else
		{
			$data = $row;
		}

		fputcsv($filehandler, $data, ',', '"');

	}

	array_walk($data, '__outputCSV', $outstream);

	fclose($outstream);
	return ob_get_clean();

}

function exportAsCSV(&$exportData)
{
	return outputCSV($exportData);

}
