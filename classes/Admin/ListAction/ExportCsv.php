<?php
class Admin_ListAction_ExportCsv extends Admin_ListAction_Base
{
    protected $_id = 'export_csv';
    protected $_displayName = 'wyeksportuj wyfiltrowane rekordy do CSV';
    
    protected $_records;
    
    protected function _perform($model, $filters, $params)
    {
        $this->_prepareRecords($model, $filters, $params);
        $this->_download("data_export_" . date("Y-m-d") . ".csv");
        echo $this->_prepareFile($this->_records);
        exit;
    }
    
    protected function _prepareRecords($model, $filters, $params)
    {
        $this->_records = array();
        foreach ($model->getAll(1, 9999999999, $filters) as $r) {
            $record = array();
            foreach ($r as $k => $v) {
                if (in_array($k, $params['fields'])) {
                    $record[$k] = $v;
                }
            }
            $this->_records[] = $record;
        }
    }
    
    /** below from reply http://stackoverflow.com/questions/4249432/export-to-csv-via-php */
    protected function _prepareFile(array &$array)
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }
    
    function _download($filename)
    {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
    
        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
    
        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
}